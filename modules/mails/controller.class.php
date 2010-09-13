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

   public function mainController() {
      $this->checkControllRights();
      $modelSMails = new Mails_Model_SendMails();

      $formSendMail = new Form('sendmail_');
      $elemRecipients = new Form_Element_TextArea('recipients', $this->_('Příjemci'));

      $elemRecipients->addValidation(new Form_Validator_NotEmpty());
//      $elemRecipients->addFilter(new Form_Filter_RemoveWhiteChars());
      $elemRecipients->setSubLabel($this->_('E-mailové adresy oddělené čárkou'));
      $formSendMail->addElement($elemRecipients);

      $elemSubject = new Form_Element_Text('subject', $this->_('Předmět'));
      $elemSubject->addValidation(new Form_Validator_NotEmpty());
      $formSendMail->addElement($elemSubject);

      $elemText = new Form_Element_TextArea('text', $this->_('Text'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $formSendMail->addElement($elemText);

      $elemFile = new Form_Element_File('file', $this->_('Příloha'));
      $elemFile->setUploadDir(AppCore::getAppCacheDir());
      $formSendMail->addElement($elemFile);

      $elemSendBatch = new Form_Element_Checkbox('sendBatch', $this->_('Odeslat každému příjemci zvlášť'));
      $elemSendBatch->setValues(false);
      $formSendMail->addElement($elemSendBatch);

      $elemSubmit = new Form_Element_Submit('send', $this->_('Odeslat'));
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
            throw new CoreException($this->_('Špatně předané id mailu'));
         }
         $formSendMail->recipients->setValues($mail->{Mails_Model_SendMails::COLUMN_RECIPIENTS});
         $formSendMail->subject->setValues($mail->{Mails_Model_SendMails::COLUMN_SUBJECT});
         $formSendMail->text->setValues($mail->{Mails_Model_SendMails::COLUMN_CONTENT});
      }

      if ($formSendMail->isValid()) {
         $recipAddresses = explode(self::RECIPIENTS_MAILS_SEPARATOR, $formSendMail->recipients->getValues());

         $mailObj = new Email(true);
         $mailObj->setSubject($formSendMail->subject->getValues());
         $mailObj->setContent($formSendMail->text->getValues());

         // pokud je soubor bude připojen
         $attachments = array();
         if ($formSendMail->file->getValues() != null) {
            $file = $formSendMail->file->createFileObject("Filesystem_File");
            $mailObj->addAttachment($file);
            array_push($attachments, $file->getName());
         }

         // adresy
         $recStr = $formSendMail->recipients->getValues();
         $matches = array();
         preg_match_all('/(?:"(?P<name>[^"]*)")?[< .,]*(?P<mail>[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4})/i', $recStr, $matches);
//         var_dump($recStr);var_dump($matches['mail']);flush();exit();
         foreach ($matches['mail'] as $key => $mail) {
            if($matches['name'][$key] == '' OR $matches['name'][$key] == null ){
               $mailObj->addAddress($mail);
            } else {
               $mailObj->addAddress($mail, $matches['name'][$key]);
            }
         }

         if($formSendMail->sendBatch->getValues() == true){
            $mailObj->batchSend();
         } else {
            $mailObj->send();
         }
         // uložíme email do db
         $modelSMails->saveMail($formSendMail->subject->getValues(), $formSendMail->text->getValues(),
                 $recipAddresses, Auth::getUserId(), $attachments);

         $this->infoMsg()->addMessage($this->_('E-mail byl odeslán'));
         $this->link()->route()->rmParam()->reload();
      }
      $this->view()->form = $formSendMail;

      // adresář
      $modelAddress = new Mails_Model_Addressbook();
      $this->view()->mailsAddressBook = $modelAddress->getMails();

      // newsletter
      $modelModules = new Model_Module();
      if ($modelModules->isModuleInstaled('newsletter') == true) {
         $modelNewsLetters = new NewsLetter_Model_Mails();
         $this->view()->mailsNewsLetter = $modelNewsLetters->getMails();
      }

      // guestbook
      if ($modelModules->isModuleInstaled('guestbook') == true) {
         $modelGuestBook = new GuestBook_Model_Detail();
         $this->view()->mailsGuestbook = $modelGuestBook->getListAll(0, 10000)->fetchAll(PDO::FETCH_OBJ);
      }

      // uživatelé systému
      $this->view()->mailsUsers = array();
      $modelUsers = new Model_Users();
      $users = array();
      $usrtTmp = $modelUsers->getUsersWithMails()->fetchAll(PDO::FETCH_OBJ);
      foreach ($usrtTmp as $user) {
         if(!isset ($users[(string)$user->{Model_Users::COLUMN_GROUP_NAME}]))
            $users[$user->{Model_Users::COLUMN_GROUP_NAME}] = array();
         array_push($users[$user->{Model_Users::COLUMN_GROUP_NAME}], $user);
      }
      unset ($usrtTmp);
      $this->view()->mailsUsers = $users;
      unset ($users);

      // načteme skupiny z adresář
      $modelGrps = new Mails_Model_Groups();
      $this->view()->mailsGroups = $modelGrps->getGroups();
      unset ($modelGrps);

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
            $jqGrid->request()->searchField());
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
               $this->infoMsg()->addMessage($this->_('Kontakt by uložen'));
            } else {
               $this->errMsg()->addMessage($this->_('Špatně zadaný e-mail'));
            }
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // validace mailu
            $validatorMail = new Validator_EMail($jqGridReq->mail);
            if ($validatorMail->isValid()) {
               $adrModel->saveMail($jqGridReq->mail, $jqGridReq->idg, $jqGridReq->name,
                  $jqGridReq->surname, $jqGridReq->note, $jqGridReq->id);
               $this->infoMsg()->addMessage($this->_('Kontakt by uložen'));
            } else {
               $this->errMsg()->addMessage($this->_('Špatně zadaný e-mail'));
            }
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            foreach ($jqGridReq->getIds() as $id) {
               $adrModel->deleteMail((int)$id);
            }
            $this->infoMsg()->addMessage($this->_('Vybrané kontakty byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->_('Nepodporovaný typ operace'));
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
            $this->infoMsg()->addMessage($this->_('Skupina byla uložena'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            if((int)$jqGridReq->id != 0){
               $adrModel->save($jqGridReq->name, $jqGridReq->note, $jqGridReq->id);
               $this->infoMsg()->addMessage($this->_('Skupina byla uložena'));
            } else {
               $this->errMsg()->addMessage($this->_('Skupinu všechny nelze upravovat'));
            }
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            if((int)$jqGridReq->id != 0){
               foreach ($jqGridReq->getIds() as $id) {
                  $model->deleteGroup((int)$id);
               }
               $this->infoMsg()->addMessage($this->_('Vybrané skupiny byly smazány'));
            } else {
               $this->errMsg()->addMessage($this->_('Skupinu všechny nelze smazat'));
            }
            break;
         default:
            $this->errMsg()->addMessage($this->_('Nepodporovaný typ operace'));
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

      /* IMPORT */
      $formImport = new Form('mails_import_');
      $formImportGrpBasic = $formImport->addGroup('basic', $this->_('Základní'));
      $formImportGrpAdv = $formImport->addGroup('advanced', $this->_('Pokročilé'));

      $eFile = new Form_Element_File('file', $this->_('Soubor (*.csv)'));
      $eFile->addValidation(new Form_Validator_FileExtension('csv'));
      $formImport->addElement($eFile, $formImportGrpBasic);


      $eGroup = new Form_Element_Select('group', $this->_('Skupina'));
      $formImport->addElement($eGroup, $formImportGrpBasic);

      $eImport = new Form_Element_Submit('import', $this->_('Nahrát'));
      $formImport->addElement($eImport, $formImportGrpBasic);

//      $eSeparator = new Form_Element_Text('separator', $this->_('Oddělovač'));
//      $eSeparator->addValidation(new Form_Validator_NotEmpty());
//      $eSeparator->setValues(';');
//      $formImport->addElement($eSeparator, $formImportGrpAdv);
      
//      $eSkipFirst = new Form_Element_Checkbox('skipfirst', $this->_('Přeskočit první řádek'));
//      $formImport->addElement($eSkipFirst, $formImportGrpAdv);

//      $eNumColls = new Form_Element_Text('cools', $this->_('počet sloupců'));
//      $eNumColls->setValues(1);
//      $formImport->addElement($eNumColls);
      
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
         $this->infoMsg()->addMessage(sprintf($this->_('Adresy byly importovány. Celkem bylo importováno %s záznamů.'),$numImports));
         $this->link()->reload();
      }
      $this->view()->formImport = $formImport;

      /* EXPORT */
      $formExport = new Form('mails_export_');
      $formExportGrpBasic = $formExport->addGroup('basic',  $this->_('Základní'));
      $formExportGrpAdv = $formExport->addGroup('advanced',  $this->_('Pokročilé'));

      $eGroup = new Form_Element_Select('group', $this->_('Skupina'));

      $eGroup->setOptions(array($this->_('Vše') => Mails_Model_Groups::GROUP_ID_ALL), true);
      foreach ($grps as $grp) {
         $eGroup->setOptions(array($grp->{Mails_Model_Groups::COLUMN_NAME} => $grp->{Mails_Model_Groups::COLUMN_ID}), true);
      }
      $formExport->addElement($eGroup, $formExportGrpBasic);


      $eSubmit = new Form_Element_Submit('export', $this->_('Export'));
      $formExport->addElement($eSubmit, $formExportGrpBasic);

      $eAddHeaders = new Form_Element_Checkbox('addheader', $this->_('Přidat názvy sloupců'));
      $eAddHeaders->setValues(true);
      $formExport->addElement($eAddHeaders, $formExportGrpAdv);

      $eExportType = new Form_Element_Select('type', $this->_('Typ'));
      $eExportType->setOptions(array('csv' => 'csv', 'txt' => 'txt'));
      $formExport->addElement($eExportType, $formExportGrpAdv);

      $eCsvSep = new Form_Element_Text('csvsep', $this->_('Oddělovač hodnot'));
      $eCsvSep->setValues(',');
      $eCsvSep->addValidation(new Form_Validator_NotEmpty());
      $formExport->addElement($eCsvSep, $formExportGrpAdv);

      if($formExport->isValid()){
         $modelMails = new Mails_Model_Addressbook();
         $mails = $modelMails->getMails($formExport->group->getValues());
         switch ($formExport->type->getValues()) {
            case 'csv':
               $comCsv = new Component_CSV();
               $comCsv->setConfig(Component_CSV::CFG_CELL_SEPARATOR, $formExport->csvsep->getValues());
               $comCsv->setConfig(Component_CSV::CFG_FLUSH_FILE, 'mails-'.date('Y-m-d').'.csv');
               if($formExport->addheader->getValues() == true){
                  $comCsv->setCellLabels(array($this->_('Jméno'),$this->_('Přijmení'),$this->_('E-mail'),$this->_('Poznámka')));
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
               $this->errMsg()->addMessage($this->_('Nepodporovaný typ exportu'));
               break;
         }

         $compCsv = new Component_CSV();



      }
      $this->view()->formExport = $formExport;

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


   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings, Form &$form) {
      $form->addGroup('server', 'Nastavení serveru');
   }

}
?>