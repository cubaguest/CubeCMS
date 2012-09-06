<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class Mails_Controller extends Controller {
   const SESSION_MAIL_RECIPIENTS = 'mails_recipients';

   const RECIPIENTS_MAILS_SEPARATOR = ',';

   const MSG_FILE_NAME = 'mmails_msg.cache';
   const NUM_MAIL_IN_LIST = 20;

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

//    public function listMailsController() {
//       $this->checkWritebleRights();
//       $model = new NewsLetter_Model_Mails();
//       $this->view()->mails = $model->getMails();
//    }

//    public function listMailsExportController() {
//       $this->checkWritebleRights();
//       $model = new Mails_Model_Addressbook();
//       $this->view()->mails = $model->getMails();
//       $this->view()->type = $this->getRequest('output', 'txt');
//    }

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
   
   public function addressListController() {
      // objekt komponenty JGrid
      $modelAddresBook = new MailsAddressBook_Model_Addressbook();
   
      $idGrp = (int)$this->getRequestParam('idgrp', MailsAddressBook_Model_Groups::GROUP_ID_ALL);
   
      // search
      if($idGrp != 0){
         $modelAddresBook->where(MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP, $idGrp);
      }
   
      $items = $modelAddresBook->records();
   
      // out
      $rows = array();
      foreach ($items as $mail) {
         array_push($rows,
               array(
                     'id' => $mail->{MailsAddressBook_Model_Addressbook::COLUMN_ID},
                     MailsAddressBook_Model_Addressbook::COLUMN_MAIL => $mail->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL},
                     MailsAddressBook_Model_Addressbook::COLUMN_NAME => $mail->{MailsAddressBook_Model_Addressbook::COLUMN_NAME},
                     MailsAddressBook_Model_Addressbook::COLUMN_SURNAME => $mail->{MailsAddressBook_Model_Addressbook::COLUMN_SURNAME},
                     ));
      }
      $this->view()->rows = $rows;
   }
   
}
?>
