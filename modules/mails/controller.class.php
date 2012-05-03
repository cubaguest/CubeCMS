<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class Mails_Controller extends Controller {
   const SESSION_MAIL_RECIPIENTS = 'mails_recipients';

   const EXPORT_CSV = 'cvs';
   const EXPORT_TXT = 'txt';
   const EXPORT_JSON = 'json';
   const EXPORT_VCARD = 'vcard';

   const RECIPIENTS_MAILS_SEPARATOR = ',';

   const NUM_ROWS_IN_TABLE = 15;
   const NUM_ROWS_IN_GRPTABLE = 5;
   const NUM_MAIL_IN_LIST = 30;

   const MSG_FILE_NAME = 'mmails_msg.cache';

   public function mainController()
   {
      $this->checkControllRights();
      $isHtmlMail = true;

      $modelSMails = new Mails_Model_SendMails();

      $formSendMail = new Form('sendmail_', true);
      $elemRecipients = new Form_Element_TextArea('recipients', $this->tr('Příjemci'));

      $elemRecipients->addValidation(new Form_Validator_NotEmpty());
//      $elemRecipients->addFilter(new Form_Filter_RemoveWhiteChars());
      $elemRecipients->setSubLabel($this->tr('E-mailové adresy oddělené čárkou'));
      $formSendMail->addElement($elemRecipients);

      $elemSubject = new Form_Element_Text('subject', $this->tr('Předmět'));
      $elemSubject->addValidation(new Form_Validator_NotEmpty());
      $formSendMail->addElement($elemSubject);

      $elemText = new Form_Element_TextArea('text', $this->tr('Text'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $formSendMail->addElement($elemText);

      $elemFile = new Form_Element_File('file', $this->tr('Příloha'));
      $elemFile->setUploadDir(AppCore::getAppCacheDir());
      $formSendMail->addElement($elemFile);

      $elemSendBatch = new Form_Element_Checkbox('sendBatch', $this->tr('Odeslat každému příjemci zvlášť'));
      $elemSendBatch->setValues(true);
      $formSendMail->addElement($elemSendBatch);

      $elemSendQueue = new Form_Element_Checkbox('sendQueue', $this->tr('Zařadit odesílání do fronty'));
      $elemSendQueue->setValues(true);
      $elemSendQueue->setSubLabel($this->tr('Obchází vypršení doby zpracování stránky a umožní odeslání velkému počtu příjemcům. Mail je odesílán vždy jen jednomu příjemci.'));
      $formSendMail->addElement($elemSendQueue);

      $elemSubmit = new Form_Element_Submit('send', $this->tr('Odeslat'));
      $formSendMail->addElement($elemSubmit);

      if (isset($_SESSION[self::SESSION_MAIL_RECIPIENTS])) {
         $formSendMail->recipients->setValues(implode(self::RECIPIENTS_MAILS_SEPARATOR, $_SESSION[self::SESSION_MAIL_RECIPIENTS]));
      } else if (isset($_GET['mail'])) {
         $recipients = $_GET['mail'];
         if (is_array($recipients))
            $recipients = implode(self::RECIPIENTS_MAILS_SEPARATOR, $recipients);
         $formSendMail->recipients->setValues($recipients);
      } else if ($this->getRequestParam('sendmail') != null) {
         // z uložených emailů
         $mail = $modelSMails->getMail($this->getRequestParam('sendmail'));
         if ($mail == false) {
            throw new CoreException($this->tr('Špatně předané id mailu'));
         }
         $formSendMail->recipients->setValues($mail->{Mails_Model_SendMails::COLUMN_RECIPIENTS});
         $formSendMail->subject->setValues($mail->{Mails_Model_SendMails::COLUMN_SUBJECT});
         $formSendMail->text->setValues($mail->{Mails_Model_SendMails::COLUMN_CONTENT});
      }

      if ($formSendMail->isValid()) {
         $mailObj = new Email($isHtmlMail);

         $recipAddresses = explode(self::RECIPIENTS_MAILS_SEPARATOR, $formSendMail->recipients->getValues());

         // pokud je soubor bude připojen
         $attachments = array();
         $attachFile = null;
         if ($formSendMail->file->getValues() != null) {
            $file = $formSendMail->file->createFileObject("Filesystem_File");
            array_push($attachments, $file->getName());
            $attachFile = $file->getName();
            unset ($file);
         }

         // adresy
         $recStr = $formSendMail->recipients->getValues();
         $matches = array();
         preg_match_all('/(?:"(?P<name>[^"]*)")?[< .,]*(?P<mail>[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4})/i', $recStr, $matches);
         foreach ($matches['mail'] as $key => $mail) {
            if($matches['name'][$key] == '' OR $matches['name'][$key] == null OR $matches['name'][$key] == ' ' ){
               $mailObj->addAddress($mail);
            } else {
               $mailObj->addAddress($mail, $matches['name'][$key]);
            }
         }

         // uložíme email do db
         $modelSMails->saveMail($formSendMail->subject->getValues(), $formSendMail->text->getValues(),
                 $recipAddresses, Auth::getUserId(), $attachments);

         if($formSendMail->sendQueue->getValues() != true){
            $mailObj->setSubject($formSendMail->subject->getValues());
            $mailObj->setContent($formSendMail->text->getValues());

            $mailObj->addAttachment(AppCore::getAppCacheDir().$attachFile);

            if($formSendMail->sendBatch->getValues() == true){
               $mailObj->batchSend(); // UNCOMMENT
            } else {
               $mailObj->send(); // UNCOMMENT
            }
            $this->infoMsg()->addMessage($this->tr('E-mail byl odeslán'));
            $this->link()->route()->rmParam()->reload();
         } else {
            $mailData = array(
               'message' => $formSendMail->text->getValues(),
               'subject' => $formSendMail->subject->getValues(),
               'ishtml' => $isHtmlMail,
               'attachment' => $attachFile,
               'sendbatch' => $formSendMail->sendBatch->getValues(),
            );
            $mailDataSer = serialize($mailData);
            
            // uložení adrwes do fronty v db
            $modelQ = new Mails_Model_SendQueue();
            $modelQ->where(Mails_Model_SendQueue::COLUMN_ID_USER." = :idu", array('idu' => Auth::getUserId()))->delete() ;
            
            foreach ($mailObj->getAddresses() as $key => $value) {
               $mailQR = $modelQ->newRecord();
               $mailQR->{Mails_Model_SendQueue::COLUMN_ID_USER} = Auth::getUserId();
               $mailQR->{Mails_Model_SendQueue::COLUMN_DATA} = $mailDataSer;
               if(is_int($key)){
                  $mailQR->{Mails_Model_SendQueue::COLUMN_MAIL} = $value;
               } else {
                  $mailQR->{Mails_Model_SendQueue::COLUMN_MAIL} = $key;
                  $mailQR->{Mails_Model_SendQueue::COLUMN_NAME} = $value;
               }
               $modelQ->save($mailQR);
            }

            $this->infoMsg()->addMessage($this->tr('E-mail byl zařazen do fronty pro odeslání'));
            $this->link()->route('sendMailsQueue')->rmParam()->reload();
         }
      }
      $this->view()->form = $formSendMail;

      // adresář
      $modelAddress = new Mails_Model_Addressbook();
      $address = $modelAddress->records(PDO::FETCH_OBJ);
      $this->view()->mailsAddressBook = $address;
      // newsletter
      $modelModules = new Model_Module();
      if ($modelModules->isModuleInstaled('newsletter') == true) {
         $modelNewsLetters = new NewsLetter_Model_Mails();
         $this->view()->mailsNewsLetter = $modelNewsLetters->getMails();
      }

      // guestbook
      if ($modelModules->isModuleInstaled('guestbook') == true) {
         $modelGuestBook = new GuestBook_Model();
         $this->view()->mailsGuestbook = $modelGuestBook->groupBy(GuestBook_Model::COLUMN_EMAIL)->records(PDO::FETCH_OBJ);
      }

      // uživatelé systému
      $this->view()->mailsUsers = array();
      $modelUsers = new Model_Users();
      $users = array();
      $usrtTmp = $modelUsers->joinFK(Model_Users::COLUMN_GROUP_ID)
         ->where(Model_Users::COLUMN_MAIL.' IS NOT NULL AND '.Model_Users::COLUMN_MAIL.' != \'\' ', array())
         ->records(PDO::FETCH_OBJ);
      foreach ($usrtTmp as $user) {
         $gKey = $user->{Model_Groups::COLUMN_LABEL} .' ('.$user->{Model_Groups::COLUMN_NAME}.')';
         if(!isset ($users[$gKey])){
            $users[$gKey] = array();
         }
         array_push($users[$gKey], $user);
      }
      unset ($usrtTmp);
      $this->view()->mailsUsers = $users;
//      unset ($users);

      // načteme skupiny z adresář
      $modelGrps = new Mails_Model_Groups();
      $this->view()->mailsGroups = $modelGrps->getGroups();
      unset ($modelGrps);

   }

   public function sendMailsQueueController()
   {
      $this->checkReadableRights();

//    if($sData['attachment'] != null AND file_exists(AppCore::getAppCacheDir().$sData['attachment'])){
//       unlink(AppCore::getAppCacheDir().$sData['attachment']);
//    }

      $modelQ = new Mails_Model_SendQueue();

      $this->view()->queue = $modelQ
         ->where(Mails_Model_SendQueue::COLUMN_ID_USER . " = :idu", array('idu' => Auth::getUserId()))
         ->order(array(Mails_Model_SendQueue::COLUMN_MAIL => Model_ORM::ORDER_ASC))
         ->records();

      /* FORM odeslání fronty */
      $formSend = new Form('send-queue');
      $eSend = new Form_Element_Submit('send', $this->tr('spustit odesílání'));
      $formSend->addElement($eSend);
      if ($formSend->isValid()) {
         $mailObj = new Email(true);
         // odeslání bez ajaxu
      }
      $this->view()->formSend = $formSend;
      // data pro odeslání

      /* FORM vyčištění fronty */
      $formClear = new Form('clear-queue');
      $eSend = new Form_Element_Submit('clear', $this->tr('vyčistit'));
      $formClear->addElement($eSend);
      if ($formClear->isValid()) {
         $modelQ->where(Mails_Model_SendQueue::COLUMN_ID_USER . " = :idu", array('idu' => Auth::getUserId()))->delete();
         $this->infoMsg()->addMessage($this->tr('Fronta byla vyčištěna'));
         $this->link()->route()->rmParam()->reload();
      }
      $this->view()->formClear = $formClear;

      /* FORM odstranění mailů na které se nedaří doručit */
      $formRemoveUndeliverable = new Form('remove-ndeliverable');
      $eRemove = new Form_Element_Submit('remove', $this->tr('odstranit'));
      $formRemoveUndeliverable->addElement($eRemove);
      if ($formRemoveUndeliverable->isValid()) {
         $mails = $modelQ
            ->where(Mails_Model_SendQueue::COLUMN_ID_USER . " = :idu AND " . Mails_Model_SendQueue::COLUMN_UNDELIVERABLE . " = 1", array('idu' => Auth::getUserId()))
            ->records();

         $modelA = new Mails_Model_Addressbook();
         foreach ($mails as $umail) {
            $modelA->deleteMail($umail->{Mails_Model_SendQueue::COLUMN_MAIL});
         }
         $modelQ->where(Mails_Model_SendQueue::COLUMN_ID_USER . " = :idu", array('idu' => Auth::getUserId()))->delete();
         $this->infoMsg()->addMessage($this->tr('Adresy na které se nepodařilo doručit byly odstraněny z adresáře.'));
         $this->link()->reload();
      }
      $this->view()->formRemUnedlivered = $formRemoveUndeliverable;
   }

   public function sendMailController()
   {
      $this->checkReadableRights();
      $modelQ = new Mails_Model_SendQueue();

      $mailItem = $modelQ->record($this->getRequestParam('id')); 
      
      if($mailItem == false || $mailItem->{Mails_Model_SendQueue::COLUMN_DATA} == null){
         $this->view()->msg = $this->tr('Odeslání neexistujícího mailu z fronty');
         $this->view()->status = 'ERR';
         $this->errMsg()->addMessage($this->view()->msg);
         return true;
      }
      
      $sData = unserialize($mailItem->{Mails_Model_SendQueue::COLUMN_DATA});
      $mailObj = new Email($sData['ishtml']);
      $mailObj->setContent($sData['message']);
      $mailObj->setSubject($sData['subject']);

      if($sData['attachment'] != null){
         $mailObj->addAttachment(AppCore::getAppCacheDir().$sData['attachment']);
      }

      $mailObj->addAddress($mailItem->{Mails_Model_SendQueue::COLUMN_MAIL}, $mailItem->{Mails_Model_SendQueue::COLUMN_NAME});

      $failures = array();
      if(!$mailObj->send($failures)){ // UNCOMMENT
//      if(true == false){
         $this->view()->msg = 'Nelze odeslat';
         $this->view()->status = 'ERR';
         $mailItem->{Mails_Model_SendQueue::COLUMN_UNDELIVERABLE} = true;
         $modelQ->save($mailItem);
         $this->errMsg()->addMessage(sprintf($this->tr('Zpráva s adresou %s byla odmítnuta.'), $failures[0]));
      } else {
         $this->view()->msg = 'Odesláno';
         $this->view()->status = 'OK';
         // odstranění mailu z fronty
         $modelQ->delete($mailItem);
         $this->infoMsg()->addMessage(sprintf($this->tr('Zpráva s adresou %s byla odeslána.'), $mailItem->{Mails_Model_SendQueue::COLUMN_MAIL}));
      }
   }

   public function addressListController() {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Mails_Model_Addressbook::COLUMN_MAIL);
      $modelAddresBook = new Mails_Model_Addressbook();
      $count = 0;
      // search
      if ($jqGrid->request()->isSearch()) {
         $count = $modelAddresBook->searchCount($jqGrid->request()->searchString(),
            (int)$this->getRequestParam('idgrp', Mails_Model_Groups::GROUP_ID_ALL),
            $jqGrid->request()->searchField(),$jqGrid->request()->searchType());
         $jqGrid->respond()->setRecords($count);

         $book = $modelAddresBook->search($jqGrid->request()->searchString(),
            (int)$this->getRequestParam('idgrp', Mails_Model_Groups::GROUP_ID_ALL),
            $jqGrid->request()->searchField(),$jqGrid->request()->searchType(),
            ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(),
            $jqGrid->request()->rows, $jqGrid->request()->orderField, $jqGrid->request()->order);
      } else {
      // list
         $count = $modelAddresBook->getCount((int)$this->getRequestParam('idgrp', Mails_Model_Groups::GROUP_ID_ALL));
         $jqGrid->respond()->setRecords($count);

         $book = $modelAddresBook->getMails((int)$this->getRequestParam('idgrp', Mails_Model_Groups::GROUP_ID_ALL),
            ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(),
            $jqGrid->request()->rows, $jqGrid->request()->orderField, $jqGrid->request()->order);
      }
      // out
      foreach ($book as $mail) {
         array_push($jqGrid->respond()->rows, array('id' => $mail->{Mails_Model_Addressbook::COLUMN_ID},
             'cell' => array(
                 $mail->{Mails_Model_Addressbook::COLUMN_MAIL},
                 $mail->{Mails_Model_Addressbook::COLUMN_NAME},
                 $mail->{Mails_Model_Addressbook::COLUMN_SURNAME},
                 $mail->{Mails_Model_Addressbook::COLUMN_NOTE}
                 ))
         );
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function groupsListController() {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $modelGroups = new Mails_Model_Groups();

      if ($jqGrid->request()->isSearch()) {

      } else {
         $jqGrid->respond()->setRecords($modelGroups->getCount());
         $fromRow = ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage();
         $groups = $modelGroups->getGroups($fromRow,$jqGrid->request()->rows, $jqGrid->request()->orderField, $jqGrid->request()->order);
         foreach ($groups as $grp) {
            array_push($jqGrid->respond()->rows, array('id' => $grp->{Mails_Model_Groups::COLUMN_ID},
                'cell' => array(
                    $grp->{Mails_Model_Groups::COLUMN_NAME},
                    $grp->{Mails_Model_Groups::COLUMN_NOTE}
                    ))
            );
         }
      }
      $this->view()->respond = $jqGrid->respond();
   }

   public function listMailsController() {
      $this->checkWritebleRights();
      $model = new NewsLetter_Model_Mails();
      $this->view()->mails = $model->getMails();
   }

   public function listMailsExportController() {
      $this->checkWritebleRights();
      $model = new Mails_Model_Addressbook();
      $this->view()->mails = $model->getMails();
      $this->view()->type = $this->getRequest('output', 'txt');
   }

   public function editMailController() {
      $this->checkWritebleRights();
      $adrModel = new Mails_Model_Addressbook();

      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            // validace mailu
            $validatorMail = new Validator_EMail($jqGridReq->mail);

            if ($validatorMail->isValid()) {
               $adrModel->saveMail($jqGridReq->mail, $jqGridReq->idg, $jqGridReq->name,
                  $jqGridReq->surname, $jqGridReq->note);
               $this->infoMsg()->addMessage($this->tr('Kontakt by uložen'));
            } else {
               $this->errMsg()->addMessage($this->tr('Špatně zadaný e-mail'));
            }
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // validace mailu
            $validatorMail = new Validator_EMail($jqGridReq->mail);
            if ($validatorMail->isValid()) {
               $adrModel->saveMail($jqGridReq->mail, $jqGridReq->idg, $jqGridReq->name,
                  $jqGridReq->surname, $jqGridReq->note, $jqGridReq->id);
               $this->infoMsg()->addMessage($this->tr('Kontakt by uložen'));
            } else {
               $this->errMsg()->addMessage($this->tr('Špatně zadaný e-mail'));
            }
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $adrModel->deleteMail((int)$id);
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané kontakty byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      } else {
         $this->view()->allOk = false;
      }
   }

   public function editGroupController() {
      $this->checkWritebleRights();
      $model = new Mails_Model_Groups();

      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $model->save($jqGridReq->name, $jqGridReq->note);
            $this->infoMsg()->addMessage($this->tr('Skupina byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            if((int)$jqGridReq->id != 0){
               $adrModel->save($jqGridReq->name, $jqGridReq->note, $jqGridReq->id);
               $this->infoMsg()->addMessage($this->tr('Skupina byla uložena'));
            } else {
               $this->errMsg()->addMessage($this->tr('Skupinu všechny nelze upravovat'));
            }
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            if((int)$jqGridReq->id != 0){
               foreach ($jqGridReq->getIds() as $id) {
                  $model->deleteGroup((int)$id);
               }
               $this->infoMsg()->addMessage($this->tr('Vybrané skupiny byly smazány'));
            } else {
               $this->errMsg()->addMessage($this->tr('Skupinu všechny nelze smazat'));
            }
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      } else {
         $this->view()->allOk = false;
      }
   }

   public function addressBookController() {
      $this->checkWritebleRights();

      $modelGrps = new Mails_Model_Groups();
      $grps = $modelGrps->getGroups();
      $this->view()->idSelGrp = $grps[0]->{Mails_Model_Groups::COLUMN_ID}; // kvůli načtení mailů z první skupiny

      $this->view()->linkBack = $this->link()->route()->rmParam();
   }

   public function sendMailsListController() {
      $this->checkControllRights();
      $model = new Mails_Model_SendMails();

      $compScroll = new Component_Scroll();
      $compScroll->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS,
              $model->getCount());
      $compScroll->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              self::NUM_MAIL_IN_LIST);
      $this->view()->mails = $model->getMails($compScroll->getStartRecord(), $compScroll->getRecordsOnPage());
      $this->view()->scrollComp = $compScroll;
      $this->view()->linkBack = $this->link()->route()->rmParam();
   }

   public function searchMailController() {
      $model = new Mails_Model_Addressbook();

      $this->view()->mails = $model->searchMail($this->getRequestParam('q'));
   }
   
   public function toolsController()
   {
      $this->checkWritebleRights();
      
      $modelGrps = new Mails_Model_Groups();
      $grps = $modelGrps->getGroups();
      
      /* IMPORT */
      $formImport = new Form('mails_import_');
      $formImportGrpBasic = $formImport->addGroup('basic', $this->tr('Základní'));
      $formImportGrpAdv = $formImport->addGroup('advanced', $this->tr('Pokročilé'));

      $eFile = new Form_Element_File('file', $this->tr('Soubor (*.csv)'));
      $eFile->addValidation(new Form_Validator_FileExtension('csv'));
      $formImport->addElement($eFile, $formImportGrpBasic);


      $eGroup = new Form_Element_Select('group', $this->tr('Skupina'));
      $formImport->addElement($eGroup, $formImportGrpBasic);

      $eImport = new Form_Element_Submit('import', $this->tr('Nahrát'));
      $formImport->addElement($eImport, $formImportGrpBasic);

      foreach ($grps as $grp) {
         $formImport->group->setOptions(array($grp->{Mails_Model_Groups::COLUMN_NAME} => $grp->{Mails_Model_Groups::COLUMN_ID}), true);
      }

      if ($formImport->isValid()) {
         $modelMails = new Mails_Model_Addressbook();
         $file = $formImport->file->createFileObject('Filesystem_File_Text');
         $mails = $file->getContent();
         
         $mailsArr = explode("\n", $mails);
         $numImports = 0;
         foreach ($mailsArr as $mail) {
            if(empty($mail)) continue;
            $modelMails->saveMail($mail, $formImport->group->getValues());
            $numImports++;
         }
         $this->infoMsg()->addMessage(sprintf($this->tr('Adresy byly importovány. Celkem bylo importováno %s záznamů.'),$numImports));
         $this->link()->reload();
      }
      $this->view()->formImport = $formImport;

      /* EXPORT */
      $formExport = new Form('mails_export_');
      $formExportGrpBasic = $formExport->addGroup('basic',  $this->tr('Základní'));
      $formExportGrpAdv = $formExport->addGroup('advanced',  $this->tr('Pokročilé'));

      $eGroup = new Form_Element_Select('group', $this->tr('Skupina adresáře'));

      $eGroup->setOptions(array($this->tr('Vše') => Mails_Model_Groups::GROUP_ID_ALL), true);
      foreach ($grps as $grp) {
         $eGroup->setOptions(array($grp->{Mails_Model_Groups::COLUMN_NAME} => $grp->{Mails_Model_Groups::COLUMN_ID}), true);
      }
      $formExport->addElement($eGroup, $formExportGrpBasic);


      $eSubmit = new Form_Element_Submit('export', $this->tr('Export'));
      $formExport->addElement($eSubmit);

      $eAddHeaders = new Form_Element_Checkbox('addheader', $this->tr('Přidat názvy sloupců'));
      $eAddHeaders->setValues(true);
      $formExport->addElement($eAddHeaders, $formExportGrpAdv);

      $eExportType = new Form_Element_Select('type', $this->tr('Typ'));
      $eExportType->setOptions(array('Excel (xls)' => 'xls', 'Excel (csv)' => 'csv', 'Prostý text (txt)' => 'txt'));
      $formExport->addElement($eExportType, $formExportGrpAdv);

      $eCsvSep = new Form_Element_Text('csvsep', $this->tr('Oddělovač hodnot'));
      $eCsvSep->setSubLabel($this->tr('Pouze csv formát'));
      $eCsvSep->setValues(',');
      $formExport->addElement($eCsvSep, $formExportGrpAdv);

      if($formExport->isSend() && $formExport->type->getValues() == "csv"){
         $formExport->csvsep->addValidation(new Form_Validator_NotEmpty());
      }
      if($formExport->isValid()){
         $modelMails = new Mails_Model_Addressbook();
         $mails = $modelMails->getMails($formExport->group->getValues());
         switch ($formExport->type->getValues()) {
            case 'csv':
               $comCsv = new Component_CSV();
               $comCsv->setConfig(Component_CSV::CFG_CELL_SEPARATOR, $formExport->csvsep->getValues());
               $comCsv->setConfig(Component_CSV::CFG_FLUSH_FILE, 'mails-'.date('Y-m-d').'.csv');
               if($formExport->addheader->getValues() == true){
                  $comCsv->setCellLabels(array($this->tr('Jméno'),$this->tr('Přijmení'),$this->tr('E-mail'),$this->tr('Poznámka')));
               }
               foreach ($mails as $mail) {
                  $comCsv->addRow(array(
                     $mail->{Mails_Model_Addressbook::COLUMN_NAME},
                     $mail->{Mails_Model_Addressbook::COLUMN_SURNAME},
                     $mail->{Mails_Model_Addressbook::COLUMN_MAIL},
                     $mail->{Mails_Model_Addressbook::COLUMN_NOTE}
                  ));
               }
               $comCsv->flush();
               break;
            case 'xls':
               include_once AppCore::getAppLibDir().'lib/nonvve/phpexcel/PHPExcel.php';
               $excelDoc = new PHPExcel();
               $excelDoc->setActiveSheetIndex(0);
               $list = $excelDoc->getActiveSheet();
               $currLine = 1;
               
               $list->getColumnDimension('A')->setWidth(10);
               $list->getColumnDimension('B')->setWidth(10);
               $list->getColumnDimension('C')->setWidth(45);
               
               if($formExport->addheader->getValues() == true){
                  $list->getStyle('A1:D1')->getFont()->setBold(true);
                  
                  $list->setCellValueByColumnAndRow(0, $currLine, $this->tr('Jméno'));
                  $list->setCellValueByColumnAndRow(1, $currLine, $this->tr('Přijmení'));
                  $list->setCellValueByColumnAndRow(2, $currLine, $this->tr('E-mail'));
                  $list->setCellValueByColumnAndRow(3, $currLine, $this->tr('Poznámka'));
                  $currLine++;
               }
               
               foreach ($mails as $mail) {
                  $list->setCellValueByColumnAndRow(0, $currLine, $mail->{Mails_Model_Addressbook::COLUMN_NAME});
                  $list->setCellValueByColumnAndRow(1, $currLine, $mail->{Mails_Model_Addressbook::COLUMN_SURNAME});
                  $list->setCellValueByColumnAndRow(2, $currLine, $mail->{Mails_Model_Addressbook::COLUMN_MAIL});
                  $list->setCellValueByColumnAndRow(3, $currLine, $mail->{Mails_Model_Addressbook::COLUMN_NOTE});
                  $currLine++;
               }
               $newExcelWriter = new PHPExcel_Writer_Excel5($excelDoc);
//               $newExcelWriter->save(AppCore::getAppCacheDir()."mail_export.xls");
               Template_Output::factory('xls');
               Template_Output::setDownload('mails-'.date('Y-m-d').'.xls');
               Template_Output::sendHeaders();
               header('Content-type: application/vnd.ms-excel');
               $newExcelWriter->save('php://output');
               exit();
               break;
            case 'txt':
               $buffer = null;
               foreach ($mails as $mail) {
                  $buffer .= $mail->{Mails_Model_Addressbook::COLUMN_MAIL}."\r\n";
               }
               Template_Output::factory('txt');
               Template_Output::setDownload('mails-'.date('Y-m-d').'.txt');
               Template_Output::sendHeaders();
               echo $buffer;
               flush();
               exit();
               break;
            default:
               $this->errMsg()->addMessage($this->tr('Nepodporovaný typ exportu'));
               break;
         }
      }
      $this->view()->formExport = $formExport;

      $formRemoveDuplicity = new Form('remduplicity');
      $eSubmit = new Form_Element_Submit('remove', $this->tr('Odstranit'));
      $formRemoveDuplicity->addElement($eSubmit);

      if($formRemoveDuplicity->isValid()){

      }
      $this->view()->formRemoveDuplicity = $formRemoveDuplicity;
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings, Form &$form) {
      $form->addGroup('server', 'Nastavení serveru');
   }

}
?>
