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
   const NUM_MAIL_IN_LIST = 30;

   public function mainController() {
      $this->checkControllRights();
      $modelSMails = new Mails_Model_SendMails();

      $formSendMail = new Form('sendmail_');
      $elemRecipients = new Form_Element_TextArea('recipients', $this->_('Příjemci'));

      $elemRecipients->addValidation(new Form_Validator_NotEmpty());
      $elemRecipients->addFilter(new Form_Filter_RemoveWhiteChars());
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
         $mailObj->addAddress($recipAddresses);
         $mailObj->sendMail();
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

   }

   public function addressListController() {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $modelAddresBook = new Mails_Model_Addressbook();

      if ($jqGrid->request()->isSearch()) {
         
      } else {
         $jqGrid->respond()->setRecords($modelAddresBook->getCount());

         $book = $modelAddresBook->getMails(($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(),
                         $jqGrid->request()->rows, $jqGrid->request()->orderField, $jqGrid->request()->order);
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
               $adrModel->saveMail($jqGridReq->mail, $jqGridReq->name, $jqGridReq->surname,
                       $jqGridReq->note);
               $this->infoMsg()->addMessage($this->_('Kontakt by uložen'));
            } else {
               $this->errMsg()->addMessage($this->_('Špatně zadaný e-mail'));
            }
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // validace mailu
            $validatorMail = new Validator_EMail($jqGridReq->mail);
            if ($validatorMail->isValid()) {
               $adrModel->saveMail($jqGridReq->mail, $jqGridReq->name, $jqGridReq->surname,
                       $jqGridReq->note, $jqGridReq->id);
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

   public function addressBookController() {
      $this->checkWritebleRights();

      // IMPORT
      $formImport = new Form('mails_import_');

      $eFile = new Form_Element_File('file', $this->_('Soubor (*.csv)'));
      $eFile->addValidation(new Form_Validator_FileExtension('csv'));
      $formImport->addElement($eFile);

      $eSeparator = new Form_Element_Text('separator', $this->_('Oddělovač'));
      $eSeparator->addValidation(new Form_Validator_NotEmpty());
      $eSeparator->setValues(';');
      $formImport->addElement($eSeparator);

      $eImport = new Form_Element_Submit('import', $this->_('Nahrát'));
      $formImport->addElement($eImport);

      if ($formImport->isValid()) {

      }
      $this->view()->formImport = $formImport;



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

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings, Form &$form) {
      $form->addGroup('server', 'Nastavení serveru');
   }

}
?>