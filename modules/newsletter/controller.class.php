<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class NewsLetter_Controller extends Controller {
   const TEXT_MAIN_KEY = 'main';

   const PARAM_NEW_MAIL_REG_NOTICE = 'newmail_registered_send_notice';
   const PARAM_NEW_MAIL_REG_SUBJECT = 'newmail_registered_subject';
   const PARAM_NEW_MAIL_REG_ADMIN_RECIPIENTS = 'newmail_reg_rec';
   const PARAM_NEW_MAIL_REG_SUBJECT_ADMIN = 'newmail_registered_subject_admin';
   const PARAM_NEW_MAIL_REG_TEXT_ADMIN = 'newmail_registered_text_admin';
   const PARAM_NEW_MAIL_REG_TEXT_USER = 'newmail_registered_text_user';
   const PARAM_ADMINS = 'newmail_admins';

   public function mainController() {
      $newMailForm = $this->createRegForm();

      if($newMailForm->isValid()) {
         $mailsM = new NewsLetter_Model_Mails();
         $newMail = $newMailForm->mail->getValues();
         // načtení existujících emailů pro okntrolu existence
         if(!$mailsM->isSavedMails($newMail)) {
            $mailsM->saveMail($newMail, $_SERVER['REMOTE_ADDR']);
            // pokud se má odeslat mail správci
            if($this->category()->getParam(self::PARAM_NEW_MAIL_REG_NOTICE, true) == true) {
               // email webmasterovi
               $emailComp = new Email();
               $emailComp->addAddress($this->getRecipientEmailAddress());
               $emailComp->setSubject($this->category()->getParam(self::PARAM_NEW_MAIL_REG_SUBJECT_ADMIN,
                       self::getNewMailText(self::PARAM_NEW_MAIL_REG_SUBJECT_ADMIN)).' '.VVE_WEB_NAME);
               // přepis hodnot
               $emailComp->setContent(str_replace(
                       array("%date%", '%email%', '%ip%', '%webname%', '%weblink%','%unregaddress%'),
                       array(vve_date("%x"), $newMail, $_SERVER['REMOTE_ADDR'],
                       VVE_WEB_NAME, Url_Request::getBaseWebDir(),
                       $this->link()->route('unregistration')->param('mail', $newMail)),
                       $this->category()->getParam(self::PARAM_NEW_MAIL_REG_TEXT_ADMIN,
                       self::getNewMailText(self::PARAM_NEW_MAIL_REG_TEXT_ADMIN))));
               $emailComp->sendMail();
            }
            // email registrovanému
            $emailComp = new Email();
            $emailComp->addAddress($newMail);
            $emailComp->setSubject($this->category()->getParam(self::PARAM_NEW_MAIL_REG_SUBJECT,
                    self::getNewMailText(self::PARAM_NEW_MAIL_REG_SUBJECT)).' '.VVE_WEB_NAME);
            // přepis hodnot
            $emailComp->setContent(str_replace(
                    array("%date%", '%email%', '%ip%', '%webname%', '%weblink%','%unregaddress%'),
                    array(vve_date("%x"), $newMail, $_SERVER['REMOTE_ADDR'],
                    VVE_WEB_NAME, Url_Request::getBaseWebDir(),
                    $this->link()->route('unregistration')->param('mail', $newMail)),
                    $this->category()->getParam(self::PARAM_NEW_MAIL_REG_TEXT_USER,
                    self::getNewMailText(self::PARAM_NEW_MAIL_REG_TEXT_USER))));
            $emailComp->sendMail();

            $this->infoMsg()->addMessage($this->_('Váš e-mail byl uložen'));
            $this->link()->reload();
         } else {
            $this->errMsg()->addMessage($this->_('Váš e-mail byl již jednou uložen'));
         }
      }

      $textModel = new Text_Model_Detail();
      $this->view()->text = $textModel->getText($this->category()->getId(), self::TEXT_MAIN_KEY);

      $this->view()->newMailForm = $newMailForm;
   }

   private function createRegForm(){
      $newMailForm = new Form('regmail_');

      $elemMail = new Form_Element_Text('mail', $this->_('E-mail'));
      $elemMail->addValidation(new Form_Validator_NotEmpty());
      $elemMail->addValidation(new Form_Validator_Email());
      $newMailForm->addElement($elemMail);

      $elemSend = new Form_Element_Submit('send', $this->_('Registrovat'));
      $newMailForm->addElement($elemSend);
      return $newMailForm;
   }

   private static function getNewMailText($type = self::PARAM_NEW_MAIL_REG_TEXT_USER) {
      switch ($type) {
         case self::PARAM_NEW_MAIL_REG_TEXT_ADMIN:
            return '%date% byla registrována nová e-mailová adresa "%email%" pro odběr novinek, '
                    .'ze stránek "%webname%"'."\r\n".' Registrace proběhla z IP adresy %ip%.';
            break;
         case self::PARAM_NEW_MAIL_REG_SUBJECT_ADMIN:
            return 'Nová registroce k odběru novinek';
            break;
         case self::PARAM_NEW_MAIL_REG_SUBJECT:
            return 'Registroce k odběru novinek ze stránek';
            break;
         case self::PARAM_NEW_MAIL_REG_TEXT_USER:
         default:
            return '%date% byla tato e-mailová adresa registrována k odběru novinek ze stránek %webname% (%weblink%).'."\r\n"
                    .'Registarci lze zrušit na adrese %unregaddress%.'."\r\n"
                    .'Registrace proběhla z IP adresy %ip%.';
            break;
      }

   }

   public function editTextController() {
      $this->checkWritebleRights();
      $textModel = new Text_Model_Detail();

      $form = new Form('edittext_');
      $text = $textModel->getText($this->category()->getId(), self::TEXT_MAIN_KEY);

      $elemLabel = new Form_Element_Text('label', $this->_('Nadpis'));
      $elemLabel->setLangs();
      if($text != false) {
         $elemLabel->setValues($text[Text_Model_Detail::COLUMN_LABEL]);
      }
      $form->addElement($elemLabel);

      $elemText = new Form_Element_TextArea('text',$this->_('úvodní text'));
      $elemText->setLangs();
      if($text != false) {
         $elemText->setValues($text[Text_Model_Detail::COLUMN_TEXT]);
      }
      $form->addElement($elemText);

      $elemSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemSubmit);

      if($form->isValid()) {
         $textModel->saveText($form->text->getValues(), $form->label->getValues(),
                 $this->category()->getId(), self::TEXT_MAIN_KEY);

         $this->infoMsg()->addMessage($this->_('Text byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
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
    * Metoda vrací emaily pro příjemce nových adres z nastaavení
    */
   private function getRecipientEmailAddress() {
      $mails = array();
      $str = $this->category()->getParam(self::PARAM_NEW_MAIL_REG_ADMIN_RECIPIENTS, null);
      $mails = explode(';', $str);

      $usersId = $this->category()->getParam(self::PARAM_ADMINS, array());

      $modelusers = new Model_Users();

      foreach ($usersId as $id) {
         $user = $modelusers->getUserById($id);
         $mails = array_merge($mails, explode(';', $user->{Model_Users::COLUMN_MAIL}));
      }

      return $mails;
   }

   public function unregistrationMailController() {
      $unregMailForm = new Form('unregmail_');

      $elemMail = new Form_Element_Text('mail', $this->_('E-mail'));
      $elemMail->addValidation(new Form_Validator_NotEmpty());
      $elemMail->addValidation(new Form_Validator_Email());
      $unregMailForm->addElement($elemMail);

      $elemSend = new Form_Element_Submit('send', $this->_('Zrušit registraci'));
      $unregMailForm->addElement($elemSend);

      if($unregMailForm->isValid()) {
         $model = new NewsLetter_Model_Mails();
         $model->deleteMail($unregMailForm->mail->getValues());
         $this->infoMsg()->addMessage(sprintf($this->_('E-mailové adrese %s byla zrušena registrace'), $unregMailForm->mail->getValues()));
         $this->link()->reload();
      }
      // odregistrace přes url adresu
      $email = $this->getRequestParam('mail', null);
      if($email !== null) {
         $model = new NewsLetter_Model_Mails();
         $model->deleteMail($email);
         $this->infoMsg()->addMessage(sprintf($this->_('E-mailové adrese %s byla zrušena registrace'), $email));
         $this->link()->rmParam()->reload();
      }
      $this->view()->unregMailForm = $unregMailForm;
   }

   public function registerController(){
      $newMailForm = $this->createRegForm();
      $data = array('code' => false, 'message' => null);

      if($newMailForm->isValid()) {
         $mailsM = new NewsLetter_Model_Mails();
         $newMail = $newMailForm->mail->getValues();
         // načtení existujících emailů pro okntrolu existence
         if(!$mailsM->isSavedMails($this->category()->getId(), $newMail)) {
            $mailsM->saveMail($newMail, $_SERVER['REMOTE_ADDR']);
            // pokud se má odeslat mail správci
            if($this->category()->getParam(self::PARAM_NEW_MAIL_REG_NOTICE, true) == true) {
               // email webmasterům
               $emailComp = new Email();
               $emailComp->addAddress($this->getRecipientEmailAddress());
               $emailComp->setSubject($this->category()->getParam(self::PARAM_NEW_MAIL_REG_SUBJECT_ADMIN,
                       self::getNewMailText(self::PARAM_NEW_MAIL_REG_SUBJECT_ADMIN)).' '.VVE_WEB_NAME);
               // přepis hodnot
               $emailComp->setContent(str_replace(
                       array("%date%", '%email%', '%ip%', '%webname%', '%weblink%','%unregaddress%'),
                       array(vve_date("%x"), $newMail, $_SERVER['REMOTE_ADDR'],
                       VVE_WEB_NAME, Url_Request::getBaseWebDir(),
                       $this->link()->route('unregistration')->param('mail', $newMail)),
                       $this->category()->getParam(self::PARAM_NEW_MAIL_REG_TEXT_ADMIN,
                       self::getNewMailText(self::PARAM_NEW_MAIL_REG_TEXT_ADMIN))));
               $emailComp->sendMail();
            }
            // email registrovanému
            $emailComp = new Email();
            $emailComp->addAddress($newMail);
            $emailComp->setSubject($this->category()->getParam(self::PARAM_NEW_MAIL_REG_SUBJECT,
                    self::getNewMailText(self::PARAM_NEW_MAIL_REG_SUBJECT)).' '.VVE_WEB_NAME);
            // přepis hodnot
            $emailComp->setContent(str_replace(
                    array("%date%", '%email%', '%ip%', '%webname%', '%weblink%','%unregaddress%'),
                    array(vve_date("%x"), $newMail, $_SERVER['REMOTE_ADDR'],
                    VVE_WEB_NAME, Url_Request::getBaseWebDir(),
                    $this->link()->route('unregistration')->param('mail', $newMail)),
                    $this->category()->getParam(self::PARAM_NEW_MAIL_REG_TEXT_USER,
                    self::getNewMailText(self::PARAM_NEW_MAIL_REG_TEXT_USER))));
            $emailComp->sendMail();
            $data['code'] = true;
            $data['message'] = $this->_('Váš e-mail byl uložen');
         } else {
            $data['code'] = false;
            $data['message'] = $this->_('Váš e-mail byl již jednou uložen');
         }
      } else {
         $data['code'] = false;
         $data['message'] = $this->errMsg()->getMessages();
      }

      $this->view()->data = $data;
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings,Form &$form) {
      $form->addGroup('users', 'Uživatelsáé nastavení');


      // předmět uživatel
      $elemNMSub = new Form_Element_Text('newm_reg_subject_user', 'Předmět');
      $elemNMSub->setSubLabel('Předmět emailu který je odeslán nově registrovanému.'
              .' Za text je automaticky doplněn název stránek');
      $elemNMSub->addValidation(new Form_Validator_NotEmpty());
      if(!isset ($settings[self::PARAM_NEW_MAIL_REG_SUBJECT])) {
         $elemNMSub->setValues(self::getNewMailText(self::PARAM_NEW_MAIL_REG_SUBJECT));
      } else {
         $elemNMSub->setValues($settings[self::PARAM_NEW_MAIL_REG_SUBJECT]);
      }
      $form->addElement($elemNMSub,'users');

      // text uživatel
      $elemNMTextUser = new Form_Element_TextArea('newm_reg_text_user', 'Text e-mailu pro uživatele');
      $elemNMTextUser->setSubLabel('Text emailu poslaný uživateli. Nahrazovaná slova: '
              .'(%date% = datum, %email% = email, %ip% = ip adresa, %webname% = název stránek,'
              .' %weblink% = odkaz stránek, %unregaddress% = odkaz na odregistrování)');
      $elemNMTextUser->addValidation(new Form_Validator_NotEmpty());
      $elemNMTextUser->html()->setAttrib('rows', 7)->setAttrib('cols', 40);
      if(!isset ($settings[self::PARAM_NEW_MAIL_REG_TEXT_USER])) {
         $elemNMTextUser->setValues(self::getNewMailText(self::PARAM_NEW_MAIL_REG_TEXT_USER));
      } else {
         $elemNMTextUser->setValues($settings[self::PARAM_NEW_MAIL_REG_TEXT_USER]);
      }
      $form->addElement($elemNMTextUser,'users');

      $form->addGroup('admins', 'Administrátorská nastavení');
      // odeslání upozornění
      $elemCheckAdminNotice = new Form_Element_Checkbox('admin_notice', 'Odesílat uporonění správci');
      if(!isset ($settings[self::PARAM_NEW_MAIL_REG_NOTICE])) {
         $elemCheckAdminNotice->setValues(true);
      } else {
         $elemCheckAdminNotice->setValues($settings[self::PARAM_NEW_MAIL_REG_NOTICE]);
      }
      $form->addElement($elemCheckAdminNotice,'admins');

      // maily správců
      $elemEamilRec = new Form_Element_TextArea('emails_rec', 'Adresy správců');
      $elemEamilRec->setSubLabel('E-mailové  adresy správců, kterým chodí upozornění
na nově registrované adresy. Může jich být více a jsou odděleny středníkem. Místo tohoto boxu
lze využít následující select s výběrem již existujících uživatelů.');
      $form->addElement($elemEamilRec,'admins');

      if(isset($settings[self::PARAM_NEW_MAIL_REG_ADMIN_RECIPIENTS])) {
         $form->emails_rec->setValues($settings[self::PARAM_NEW_MAIL_REG_ADMIN_RECIPIENTS]);
      }

      $elemAdmins = new Form_Element_Select('admins', 'Správci');
      // načtení uživatelů
      $modelUsers = new Model_Users();
      $users = $modelUsers->getUsersList();
      $usersIds = array();
      foreach ($users as $user) {
         $usersIds[$user[Model_Users::COLUMN_USERNAME]] = $user[Model_Users::COLUMN_ID];
      }
      $elemAdmins->setOptions($usersIds);
      $elemAdmins->setMultiple();
      $elemAdmins->html()->setAttrib('size', 4);
      
      $form->addElement($elemAdmins, 'admins');

      if(isset($settings[self::PARAM_ADMINS])) {
         $form->admins->setValues($settings[self::PARAM_ADMINS]);
      }

      // předmět admin
      $elemNMSubAdm = new Form_Element_Text('newm_reg_subject_admin', 'Předmět');
      $elemNMSubAdm->setSubLabel('Předmět emailu který je odeslán správci. Za text je automaticky doplněn název stránek');
      $elemNMSubAdm->addValidation(new Form_Validator_NotEmpty());
      if(!isset ($settings[self::PARAM_NEW_MAIL_REG_SUBJECT_ADMIN])) {
         $elemNMSubAdm->setValues(self::getNewMailText(self::PARAM_NEW_MAIL_REG_SUBJECT_ADMIN));
      } else {
         $elemNMSubAdm->setValues($settings[self::PARAM_NEW_MAIL_REG_SUBJECT_ADMIN]);
      }
      $form->addElement($elemNMSubAdm,'admins');
      // text admin
      $elemNMTextAdmin = new Form_Element_TextArea('newm_reg_text_admin', 'Text e-mailu pro admina');
      $elemNMTextAdmin->setSubLabel('Text emailu poslaný správci. Nahrazovaná slova: '
              .'(%date% = datum, %email% = email, %ip% = ip adresa, %webname% = název stránek,'
              .' %weblink% = odkaz stránek, %unregaddress% = odkaz na odregistrování)');
      $elemNMTextAdmin->addValidation(new Form_Validator_NotEmpty());
      $elemNMTextAdmin->html()->setAttrib('rows', 7)->setAttrib('cols', 40);
      if(!isset ($settings[self::PARAM_NEW_MAIL_REG_TEXT_ADMIN])) {
         $elemNMTextAdmin->setValues(self::getNewMailText(self::PARAM_NEW_MAIL_REG_TEXT_ADMIN));
      } else {
         $elemNMTextAdmin->setValues($settings[self::PARAM_NEW_MAIL_REG_TEXT_ADMIN]);
      }
      $form->addElement($elemNMTextAdmin,'admins');

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_NEW_MAIL_REG_ADMIN_RECIPIENTS] = $form->emails_rec->getValues();
         $settings[self::PARAM_NEW_MAIL_REG_SUBJECT] = $form->newm_reg_subject_user->getValues();
         $settings[self::PARAM_NEW_MAIL_REG_TEXT_USER] = $form->newm_reg_text_user->getValues();
         $settings[self::PARAM_NEW_MAIL_REG_NOTICE] = $form->admin_notice->getValues();
         $settings[self::PARAM_NEW_MAIL_REG_SUBJECT_ADMIN] = $form->newm_reg_subject_admin->getValues();
         $settings[self::PARAM_NEW_MAIL_REG_TEXT_ADMIN] = $form->newm_reg_text_admin->getValues();
         $settings[self::PARAM_ADMINS] = $form->admins->getValues();
      }
   }

   public function deleteMailsController() {
      $this->checkWritebleRights();
      if(isset($_REQUEST['select_item'])){
         $model = new NewsLetter_Model_Mails();
         $model->deleteMails(array_keys($_REQUEST['select_item']));
         $this->infoMsg()->addMessage($this->_('Označené adresy byly smazány'));
      } else {
         $this->errMsg()->addMessage($this->_('Nebyla vabrána žádná položka'));
      }
   }

   public function sendMailController() {
      $this->checkWritebleRights();
      $model = new NewsLetter_Model_Mails();
      $formSendMail = new Form('sendmail_');
      $elemRecipients = new Form_Element_TextArea('recipients', $this->_('Příjemci'));
      if(isset($_REQUEST['select_item'])){
         $rec = null;
         $mails = $model->getMailsByIds(array_keys($_REQUEST['select_item']));
         foreach ($mails as $value) {
            $rec .= $value->{NewsLetter_Model_Mails::COLUMN_MAIL}.';';
         }
         $elemRecipients->setValues(substr($rec, 0, strlen($rec)-1));
      }

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

      $elemSubmit = new Form_Element_Submit('send', $this->_('Odeslat'));
      $formSendMail->addElement($elemSubmit);

      if($formSendMail->isValid()){
         $mails = explode(';', $formSendMail->recipients->getValues());
         $mailObj = new Email(true);
         $mailObj->setSubject($formSendMail->subject->getValues());
         $mailObj->setContent($formSendMail->text->getValues());
         $mailObj->addAddress($mails);
         $mailObj->sendMail();
         $this->infoMsg()->addMessage($this->_('E-mail byl odeslán'));
         $this->link()->route('list')->reload();
      }
      $this->view()->form = $formSendMail;
   }
}

?>