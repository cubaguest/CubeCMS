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

   public function mainController() {
      $newMailForm = new Form('regmail_');

      $elemMail = new Form_Element_Text('mail', $this->_('E-mail'));
      $elemMail->addValidation(new Form_Validator_NotEmpty());
      $elemMail->addValidation(new Form_Validator_Email());
      $newMailForm->addElement($elemMail);

      $elemSend = new Form_Element_Submit('send', $this->_('Registrovat'));
      $newMailForm->addElement($elemSend);

      if($newMailForm->isValid()) {
         $mailsM = new NewsLetter_Model_Mails();
         $newMail = $newMailForm->mail->getValues();
         // načtení existujících emailů pro okntrolu existence
         if(!$mailsM->isSavedMails($this->category()->getId(), $newMail)) {
            $mailsM->saveMail($newMail, $_SERVER['REMOTE_ADDR'], $this->category()->getId());
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

      $formRem = new Form('removeMails_');

      $elemCheckbox = new Form_Element_Checkbox('id');
      $elemCheckbox->setDimensional();
      $formRem->addElement($elemCheckbox);

      $elemRemove = new Form_Element_Submit('remove',$this->_('odstranit označené'));
      $formRem->addElement($elemRemove);

      if($formRem->isValid()) {
         if($formRem->id->getValues() != false) {
            $model->deleteMails(array_keys($formRem->id->getValues()));
         }
         $this->infoMsg()->addMessage($this->_('Označené adresy byly smazány'));
         $this->link()->reload();
      }

      $this->view()->formRemove = $formRem;
      $this->view()->mails = $model->getMails($this->category()->getId());
   }

   public function listMailsExportController() {
      $this->checkWritebleRights();
      $model = new NewsLetter_Model_Mails();
      $this->view()->mails = $model->getMails($this->category()->getId());
      $this->view()->type = $this->getRequest('output', 'txt');
   }

   /**
    * Metoda vrací emaily pro příjemce nových adres z nastaavení
    */
   private function getRecipientEmailAddress() {
      $str = $this->category()->getParam(self::PARAM_NEW_MAIL_REG_ADMIN_RECIPIENTS, null);
      return explode(';', $str);
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
         $model->deleteMail($this->category()->getId(), $unregMailForm->mail->getValues());
         $this->infoMsg()->addMessage(sprintf($this->_('E-mailové adrese %s byla zrušena registrace'), $unregMailForm->mail->getValues()));
         $this->link()->reload();
      }

      $email = $this->getRequestParam('mail', null);
      if($email !== null) {
         $model = new NewsLetter_Model_Mails();
         $model->deleteMail($this->category()->getId(), $email);
         $this->infoMsg()->addMessage(sprintf($this->_('E-mailové adrese %s byla zrušena registrace'), $email));
         $this->link()->rmParam()->reload();
      }

      $this->view()->unregMailForm = $unregMailForm;
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings,Form &$form) {
      $form->addGroup('basic', 'Základní nasatvení');

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
      $form->addElement($elemNMSub,'basic');

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
      $form->addElement($elemNMTextUser,'basic');

      // odeslání upozornění
      $elemCheckAdminNotice = new Form_Element_Checkbox('admin_notice', 'Odesílat uporonění správci');
      if(!isset ($settings[self::PARAM_NEW_MAIL_REG_NOTICE])) {
         $elemCheckAdminNotice->setValues(true);
      } else {
         $elemCheckAdminNotice->setValues($settings[self::PARAM_NEW_MAIL_REG_NOTICE]);
      }
      $form->addElement($elemCheckAdminNotice,'basic');

      // maily správců
      $elemEamilRec = new Form_Element_TextArea('emails_rec', 'Adresy správců');
      $elemEamilRec->setSubLabel('E-mailové  adresy příjemcu, kterým chodí nově registrované adresy. Může jich být více a jsou odděleny středníkem.');
      $form->addElement($elemEamilRec,'basic');

      if(isset($settings['emails_rec'])) {
         $form->emails_rec->setValues($settings['emails_rec']);
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
      $form->addElement($elemNMSubAdm,'basic');
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
      $form->addElement($elemNMTextAdmin,'basic');

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_NEW_MAIL_REG_ADMIN_RECIPIENTS] = $form->emails_rec->getValues();
         $settings[self::PARAM_NEW_MAIL_REG_SUBJECT] = $form->newm_reg_subject_user->getValues();
         $settings[self::PARAM_NEW_MAIL_REG_TEXT_USER] = $form->newm_reg_text_user->getValues();
         $settings[self::PARAM_NEW_MAIL_REG_NOTICE] = $form->admin_notice->getValues();
         $settings[self::PARAM_NEW_MAIL_REG_SUBJECT_ADMIN] = $form->newm_reg_subject_admin->getValues();
         $settings[self::PARAM_NEW_MAIL_REG_TEXT_ADMIN] = $form->newm_reg_text_admin->getValues();
      }
   }
}

?>