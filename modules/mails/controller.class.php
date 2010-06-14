<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Mails_Controller extends Controller {
   const SESSION_MAIL_RECIPIENTS = 'mails_recipients';

   const ACTION_COMPOSE = 'compose';
   const ACTION_DELETE  = 'delete';
   const ACTION_EXPORT  = 'export';

   const EXPORT_CSV     = 'cvs';
   const EXPORT_TXT     = 'txt';
   const EXPORT_JSON    = 'json';
   const EXPORT_VCARD   = 'vcard';

   const MAIL_TYPE_ADDRESSBOOK = 'addressbook';
   const MAIL_TYPE_USER = 'user';
   const MAIL_TYPE_NEWSLETTER = 'newsletter';

   const RECIPIENTS_MAILS_SEPARATOR = ';';


   public function mainController() {
      $this->checkControllRights();

      // modely
      $modelNewsLetter = new NewsLetter_Model_Mails();
      $modelUsers = new Model_Users();
      $modelAddresBook = new Mails_Model_Addressbook();

      // form vybraných emailů
      $formMailsSel = new Form('selmail_');

      $elemSel = new Form_Element_Checkbox('select');
      $elemSel->setDimensional();
      $formMailsSel->addElement($elemSel);

      $elemType = new Form_Element_Hidden('type');
      $formMailsSel->addElement($elemType);

      $elemMail = new Form_Element_Hidden('mail');
      $formMailsSel->addElement($elemMail);
      $elemName = new Form_Element_Hidden('name');
      $formMailsSel->addElement($elemName);
      $elemSurName = new Form_Element_Hidden('surname');
      $formMailsSel->addElement($elemSurName);

      $elemAction = new Form_Element_Hidden('action');
      $formMailsSel->addElement($elemAction);
      $elemAction = new Form_Element_Hidden('export');
      $formMailsSel->addElement($elemAction);

      if($formMailsSel->isValid()){
         $formmails = $formMailsSel->mail->getValues();
         $formnames = $formMailsSel->name->getValues();
         $formsurnames = $formMailsSel->surname->getValues();
         $type = $formMailsSel->type->getValues();
         $selecteds = $formMailsSel->select->getValues();

         $mails = array();
         foreach ($selecteds as $key => $id) {
            array_push($mails, array('mail' => $formmails[$key], 'name' => $formnames[$key],
                'surname' => $formsurnames[$key], 'id' => $id, 'type' => $type[$key]));
         }

         switch ($formMailsSel->action->getValues()) {
            case self::ACTION_EXPORT:
               $this->setView('export');
               $this->view()->mails = $mails;
               $this->view()->type = $this->getRequestParam('export');
               break;
            case self::ACTION_DELETE:
//               var_dump($mails);flush();
               foreach ($mails as $mail) {
                  switch ($mail['type']) {
                     case self::MAIL_TYPE_USER:
                        $this->errMsg()->addMessage(sprintf($this->_('E-mail uživatele %s %s v systému nelze smazat'),
                                $mail['name'], $mail['surname']), true);
                        break;
                     case self::MAIL_TYPE_ADDRESSBOOK:
                        $modelAddresBook->deleteMail((int)$mail['id']);
                        break;
                     case self::MAIL_TYPE_NEWSLETTER:
                        $modelNewsLetter->deleteMail($mail['mail']);
                        break;
                     default:
                        $this->errMsg()->addMessage($this->_('Předán nesprávný typ předaného e-mailu. Chyba při zpracování.'), true);
                        break;
                  }
               }
               if(count($mails) == 1){
                  $this->infoMsg()->addMessage($this->_('E-mail byl smazán'));
               } else {
                  $this->infoMsg()->addMessage($this->_('E-maily byly smazány'));
               }
               $this->link()->reload();

               break;
            case self::ACTION_COMPOSE:
            default:
               $_SESSION[self::SESSION_MAIL_RECIPIENTS] = $mails;
               $this->link()->route('composeMail')->reload();
               break;
         }
      }

      $this->view()->formSelMails = $formMailsSel;

      $formImport = new Form('mails_import_');

      $eFile = new Form_Element_File('file', $this->_('Soubor (*.csv)'));
      $eFile->addValidation(new Form_Validator_FileExtension('csv'));
      $formImport->addElement($eFile);

      $eTarget = new Form_Element_Select('target', $this->_('Uložit do'));
      $eTarget->setOptions(array('Newsletteru' => self::MAIL_TYPE_NEWSLETTER,
          'Adresáře' => self::MAIL_TYPE_ADDRESSBOOK));
      $formImport->addElement($eTarget);

      $eSeparator = new Form_Element_Text('separator', $this->_('Oddělovač'));
      $eSeparator->addValidation(new Form_Validator_NotEmpty());
      $eSeparator->setValues(';');
      $formImport->addElement($eSeparator);

      $eImport = new Form_Element_Submit('import', $this->_('Nahrát'));
      $formImport->addElement($eImport);

      if($formImport->isValid()){
         
      }

      $this->view()->formImport = $formImport;

      $groups = array();
      $mails = array();
      // načtení mailů z newsletteru
      $m = $modelNewsLetter->getMails();
      foreach ($m as $mail) {
         $group = 'newsletter';
         if($mail->{NewsLetter_Model_Mails::COLUMN_GROUP} != null)
            $group .= ' - '.$mail->{NewsLetter_Model_Mails::COLUMN_GROUP};
         if(!isset ($groups[$group]))
            $groups[$group] = 'newsletter'.$mail->{NewsLetter_Model_Mails::COLUMN_GROUP};

         array_push($mails, array('type' => self::MAIL_TYPE_NEWSLETTER, 'group' => $group,
             'name' => null, 'surname' => null, 'mail' => $mail->{NewsLetter_Model_Mails::COLUMN_MAIL},
             'id' => $mail->{NewsLetter_Model_Mails::COLUMN_ID}));
      }

      // načtení mailů z registrovaných uživatelů
      $u = $modelUsers->getUsersList();
      while ($user = $u->fetchObject()) {
         if($user->{Model_Users::COLUMN_GROUP_NAME} == 'guest'
            OR $user->{Model_Users::COLUMN_MAIL} == null) continue;
         $group = $user->{Model_Users::COLUMN_GROUP_NAME};
         if(!isset ($groups[$group])) $groups[$group] = $group;

         $mail = explode(';', $user->{Model_Users::COLUMN_MAIL});

         array_push($mails, array('type' => self::MAIL_TYPE_USER, 'group' => $group,
             'name' => $user->{Model_Users::COLUMN_NAME},
             'surname' => $user->{Model_Users::COLUMN_SURNAME},
             'mail' => $mail[0],
             'id' => $user->{Model_Users::COLUMN_ID}));
      }

      // načtení mailů z adresáře
      $book = $modelAddresBook->getMails();
      $groups['addressbook'] = 'addressbook';
      foreach ($book as $mail) {
         array_push($mails, array('type' => self::MAIL_TYPE_ADDRESSBOOK, 'group' => 'addressbook',
             'name' => $mail->{Mails_Model_Addressbook::COLUMN_NAME},
             'surname' => $mail->{Mails_Model_Addressbook::COLUMN_SURNAME},
             'mail' => $mail->{Mails_Model_Addressbook::COLUMN_MAIL},
             'id' => $mail->{Mails_Model_Addressbook::COLUMN_ID}));
      }

      // do viewru
      $this->view()->mails = $mails;
      $this->view()->groups = $groups;

   }

   public function addMailController(){
      $this->checkWritebleRights();

      $form = $this->createEditAddressbookMailForm();

      if($form->isValid()){
         $addresbookModel = new Mails_Model_Addressbook();
         $addresbookModel->saveMail($form->mail->getValues(), $form->name->getValues(),
                 $form->surname->getValues());
         $this->infoMsg()->addMessage($this->_('E-mail byl uložen do adresáře'));
         $this->link()->route(null)->reload();
      }

      $this->view()->form = $form;
   }

   public function editMailController() {
      $this->checkWritebleRights();
      $form = $this->createEditAddressbookMailForm();

      $adrModel = new Mails_Model_Addressbook();
      $mail = $adrModel->getMail($this->getRequest('id'));
      if($mail == false) return false;

      $form->name->setValues($mail->{Mails_Model_Addressbook::COLUMN_NAME});
      $form->surname->setValues($mail->{Mails_Model_Addressbook::COLUMN_SURNAME});
      $form->mail->setValues($mail->{Mails_Model_Addressbook::COLUMN_MAIL});

      if($form->isValid()){
         $adrModel->saveMail($form->mail->getValues(), $form->name->getValues(), 
                 $form->surname->getValues(), (int)$this->getRequest('id'));
         
         $this->infoMsg()->addMessage($this->_('E-mail v adresáři byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
   }

   /**
    * Metoda vytvří objekt formuláře pro editaci emailu
    * @return Form
    */
   private function createEditAddressbookMailForm() {
      $form = new Form('addressbook_mail_');

      $eName = new Form_Element_Text('name', $this->_('Jméno'));
      $form->addElement($eName);

      $eSurName = new Form_Element_Text('surname', $this->_('Přijmení'));
      $form->addElement($eSurName);

      $eMail = new Form_Element_Text('mail', $this->_('E-mail'));
      $eMail->addValidation(new Form_Validator_NotEmpty());
      $eMail->addValidation(new Form_Validator_Email());
      $form->addElement($eMail);

      $eSave = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($eSave);

      return $form;
   }

   public function listMailsController() {
      $this->checkWritebleRights();
      $model = new NewsLetter_Model_Mails();
      $this->view()->mails = $model->getMails();
   }

   public function listMailsExportController() {
      $this->checkWritebleRights();
      $model = new NewsLetter_Model_Mails();
      $this->view()->mails = $model->getMails();
      $this->view()->type = $this->getRequest('output', 'txt');
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings,Form &$form) {
      $form->addGroup('server', 'Nastavení serveru');
   }

   public function composeMailController() {
      $this->checkWritebleRights();

      $recipients = null;
      if(isset ($_SESSION[self::SESSION_MAIL_RECIPIENTS])){
         $recipients = null;
         foreach ($_SESSION[self::SESSION_MAIL_RECIPIENTS] as $mail) {
            $recipients .= $mail['mail'].self::RECIPIENTS_MAILS_SEPARATOR;
         }
         $recipients = substr($recipients, 0, strlen($recipients)-1);

      } else if($this->getRequestParam('mail') != null){
         $recipients = $this->getRequestParam('mail');
      } else {
         $this->errMsg()->addMessage($this->_('Nebyly předány žádné adresy, pravděpodobně chyba při zpracování'));
      }

      $formSendMail = new Form('sendmail_');
      $elemRecipients = new Form_Element_TextArea('recipients', $this->_('Příjemci'));
      $elemRecipients->setValues($recipients);

      $elemRecipients->addValidation(new Form_Validator_NotEmpty());
      $elemRecipients->addFilter(new Form_Filter_RemoveWhiteChars());
      $elemRecipients->setSubLabel($this->_('E-mailové adresy oddělené středníkem'));
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


      if($formSendMail->isValid()){
         $recipAddresses = explode(self::RECIPIENTS_MAILS_SEPARATOR, $formSendMail->recipients->getValues());

         $mailObj = new Email(true);
         $mailObj->setSubject($formSendMail->subject->getValues());
         $mailObj->setContent($formSendMail->text->getValues());

         // pokud je soubor bude připojen
         if($formSendMail->file->getValues() != null){
            $file = $formSendMail->file->createFileObject("Filesystem_File");
            $mailObj->addAttachment($file);
         }
         $mailObj->addAddress($recipAddresses);
         $mailObj->sendMail();
         $this->infoMsg()->addMessage($this->_('E-mail byl odeslán'));
         $this->link()->route()->rmParam()->reload();
      }
      $this->view()->form = $formSendMail;
   }
}

?>