<?php

class UserReg_Controller extends Controller {
   const PARAM_COND_AGREE = 'cond_agree';
   const PARAM_TARGET_ID_GROUP = 'target_id_grp';
   const PARAM_CREATE_USER_NOW = 'create_now';
   const PARAM_REG_LINK_EXPIRE = 'reg_link_expire';

   const DEFAULT_REG_LINK_EXPIRE = 24;

   const PARAM_ADMIN_RECIPIENTS = 'admin_rec';
   const PARAM_OTHER_RECIPIENTS = 'other_rec';

   const TEXT_KEY_MAIN = 'main';
   const TEXT_KEY_MAIL_REG = 'mail_reg';
   const TEXT_KEY_WELCOME = 'welcome';

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $modelUsers = new Model_Users();

      $model = new UserReg_Model_Queue();
      $model->clearExpired($this->category()->getId(), $this->category()->getParam(self::PARAM_REG_LINK_EXPIRE, self::DEFAULT_REG_LINK_EXPIRE));

      // načtení textů
      $modelText = new Text_Model();
      $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_MAIN);
      if ($text != false) {
         $this->view()->text = $text->{Text_Model::COLUMN_TEXT};
      }

      $formReg = new Form('reg');
      $formGrpAccount = $formReg->addGroup('account', $this->_('Účet'));

      $elemUsername = new Form_Element_Text('username', $this->_('Uživatelské jméno'));
      $elemUsername->addValidation(new Form_Validator_NotEmpty());
      $elemUsername->addValidation(new Form_Validator_MinLength(5));
      $elemUsername->addValidation(new Form_Validator_Regexp('/^[a-zA-Z0-9_-]+$/', $this->_('Uživatelské jméno obsahuje nepovolené znaky.')));
      $elemUsername->setSubLabel($this->_('pouze písmena, číslice a znaky: "_-"'));


      $formReg->addElement($elemUsername, $formGrpAccount);

      $elemPass = new Form_Element_Password('pass', $this->_('Heslo'));
      $elemPass->addValidation(new Form_Validator_NotEmpty());
      $elemPass->addValidation(new Form_Validator_MinLength(5));
      $formReg->addElement($elemPass, $formGrpAccount);

      $elemPassControll = new Form_Element_Password('passctrl', $this->_('Heslo (kontrola)'));
      $elemPassControll->addValidation(new Form_Validator_NotEmpty());
//      $elemPassControll->addValidation(new Form_Validator_MinLength(5));
      $formReg->addElement($elemPassControll, $formGrpAccount);


      $formGrpContact = $formReg->addGroup('contact', $this->_('Kontaktní údaje'));
      $elemName = new Form_Element_Text('name', $this->_('Jméno'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $formReg->addElement($elemName, $formGrpContact);

      $elemName = new Form_Element_Text('surname', $this->_('Přijmení'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $formReg->addElement($elemName, $formGrpContact);

      $elemMail = new Form_Element_Text('mail', $this->_('E-mail'));
      $elemMail->addValidation(new Form_Validator_NotEmpty());
      $elemMail->addValidation(new Form_Validator_Email());
      $formReg->addElement($elemMail, $formGrpContact);

      $elemPhone = new Form_Element_Text('phone', $this->_('Telefon'));
      $elemPhone->addValidation(new Form_Validator_Regexp(Form_Validator_Regexp::REGEXP_PHONE_CZSK, $this->_('Špatně zadané telefonní číslo')));
      $formReg->addElement($elemPhone, $formGrpContact);

      $elemCondAgree = new Form_Element_Checkbox('condAgree', $this->category()->getParam(self::PARAM_COND_AGREE, $this->_('Souhlasím se zpracováním údajů')));
      $elemCondAgree->addValidation(new Form_Validator_Match(true, $this->_('Musíte souhlasit s podmínkami')));
      $elemCondAgree->setValues(true);
      $formReg->addElement($elemCondAgree);

      $elemSubmit = new Form_Element_Submit('send', $this->_('Registrovat'));
      $formReg->addElement($elemSubmit);

      // kontrola stejného hesla
      if ($formReg->isSend()) {
         if ($formReg->pass->getValues() != $formReg->passctrl->getValues()) {
            $elemPassControll->setError($this->_('Hesla se neshodují'));
         }

         $this->checkUserNameController($formReg->username->getValues());
         if ($this->view()->isFree == false) {
            $elemUsername->setError($this->_('Zvolené uživatelské jméno je již obsazeno.'));
         }
      }

      if ($formReg->isValid()) {
         // vytvoření uživatele okamžitě
         if ($this->category()->getParam(self::PARAM_CREATE_USER_NOW, false) === true) {
            $modelUsers->saveUser($formReg->username->getValues(),
               $formReg->name->getValues(), $formReg->surname->getValues(),
               $formReg->pass->getValues(), $this->category()->getParam(self::PARAM_TARGET_ID_GROUP),
               $formReg->mail->getValues(), $formReg->phone->getValues(), false);

            $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_MAIL_REG);
            if ($text != false) {
               $text = $text->{Text_Model::COLUMN_TEXT};
            } else {
               $text = 'Registraci je možné dokončit na adrese {REG_COMPLETE_LINK}';
            }

            $userData = null;
            $userData .= $this->_('Login') . ': ' . $formReg->username->getValues() . '<br/>';
            $userData .= $this->_('Jméno') . ': ' . $formReg->name->getValues() . '<br/>';
            $userData .= $this->_('Přijmení') . ': ' . $formReg->surname->getValues() . '<br/>';
            $userData .= $this->_('Mail') . ': ' . $formReg->mail->getValues() . '<br/>';

            $text = str_replace(
                  array('{REG_COMPLETE_LINK}',
                     '{REG_LINK_EXPIRE}',
                     '{WEB_NAME}',
                     '{USER_DATA}',
                     '{USER_USERNAME}',
                     '{DATE}',
                     '{WEB_LINK}'),
                  array(null,null,
                     VVE_WEB_NAME,
                     $userData,
                     $formReg->username->getValues(),
                     vve_date('%x %X'),
                     '<a href="' . Url_Link::getMainWebDir() . '" title="' . VVE_WEB_NAME . '">' . VVE_WEB_NAME . '</a>'),
                  $text);

            $mail = new Email(true);
            $mail->setSubject($this->_('Registrace na stránkách') . ' ' . VVE_WEB_NAME);
            $mail->addAddress($formReg->mail->getValues());
            $mail->setContent($text);
            $mail->send();

            $this->infoMsg()->addMessage($this->_('Registrace proběhla úspěšně.'));
            $this->link()->clear()->route('welcome')->reload();
         } else {
            /*
             * generace hashe pro pozdější ověření
             * Bude předáno v url jako paramter, zabrání se tak DoS útoku na registrace
             */
            $hash = md5(time() . $_SERVER['REMOTE_ADDR'] . $formReg->username->getValues());

            $model = new UserReg_Model_Queue();

            $id = $model->save($this->category()->getId(),
                  $formReg->username->getValues(), $formReg->pass->getValues(), $hash,
                  $formReg->mail->getValues(), $formReg->name->getValues(), $formReg->surname->getValues(),
                  $formReg->phone->getValues());

            $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_MAIL_REG);
            if ($text != false) {
               $text = $text->{Text_Model::COLUMN_TEXT};
            } else {
               $text = 'Registraci je možné dokončit na adrese {REG_COMPLETE_LINK}';
            }

            $userData = null;
            $userData .= $this->_('Login') . ': ' . $formReg->username->getValues() . '<br/>';
            $userData .= $this->_('Jméno') . ': ' . $formReg->name->getValues() . '<br/>';
            $userData .= $this->_('Přijmení') . ': ' . $formReg->surname->getValues() . '<br/>';
            $userData .= $this->_('Mail') . ': ' . $formReg->mail->getValues() . '<br/>';

            $expire = new DateTime();
            $expire->modify('+' . $this->category()->getParam(self::PARAM_REG_LINK_EXPIRE, self::DEFAULT_REG_LINK_EXPIRE) . ' hours');

            $text = str_replace(
                  array('{REG_COMPLETE_LINK}',
                     '{REG_LINK_EXPIRE}',
                     '{WEB_NAME}',
                     '{USER_DATA}',
                     '{USER_USERNAME}',
                     '{DATE}',
                     '{WEB_LINK}'),
                  array('<a href="' . $this->link()->route('completeReg')->param('id', $hash)
                     . '" title="' . $this->_('Dokončení registrace') . '">'
                     . $this->link()->route('completeReg')->param('id', $hash) . '</a>',
                     vve_date('%x %X', $expire),
                     VVE_WEB_NAME,
                     $userData,
                     $formReg->username->getValues(),
                     vve_date('%x %X'),
                     '<a href="' . Url_Link::getMainWebDir() . '" title="' . VVE_WEB_NAME . '">' . VVE_WEB_NAME . '</a>'),
                  $text);

            $mail = new Email(true);
            $mail->setSubject($this->_('Registrace na stránkách') . ' ' . VVE_WEB_NAME);
            $mail->addAddress($formReg->mail->getValues());
            $mail->setContent($text);
            $mail->send();

            $this->infoMsg()->addMessage(sprintf($this->_('Registrace byla zařazena. Na zadané emailové adrese "%s" nalezenete informace pro dokončení registrace.'), $formReg->mail->getValues()));
            $this->link()->reload();
         }
      }

      $this->view()->formReg = $formReg;
   }

   public function completeRegController() {
      $this->checkReadableRights();

      $model = new UserReg_Model_Queue();
      $model->clearExpired($this->category()->getId(), $this->category()->getParam(self::PARAM_REG_LINK_EXPIRE, self::DEFAULT_REG_LINK_EXPIRE));

      $data = $model->getRegistration($this->getRequestParam('id'));

      if ($data == false) {
         $this->errMsg()->addMessage($this->_('Požadované registraci již vypršela platnost nebo nebyla vytvořena. Pokuste se zaregistrovat znovu.'));
      } else {
         $modelUsers = new Model_Users();

         $modelUsers->saveUser($data->{UserReg_Model_Queue::COLUMN_USERNAME},
            $data->{UserReg_Model_Queue::COLUMN_NAME}, $data->{UserReg_Model_Queue::COLUMN_SURNAME},
            $data->{UserReg_Model_Queue::COLUMN_PASS}, $this->category()->getParam(self::PARAM_TARGET_ID_GROUP),
            $data->{UserReg_Model_Queue::COLUMN_MAIL}, $data->{UserReg_Model_Queue::COLUMN_PHONE_NUMBER}, false);

         $model->remove($this->getRequestParam('id'));

         $this->infoMsg()->addMessage($this->_('Registrace proběhla úspěšně.'));
         $this->link()->clear()->route('welcome')->reload();
      }
   }

   public function welcomeController() {
      $this->checkReadableRights();
      // načtení textů
      $modelText = new Text_Model();
      $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_WELCOME);
      if ($text != false) {
         $this->view()->text = $text->{Text_Model::COLUMN_TEXT};
      }
   }

   /* ------------------------------------------------------------------------------- */

   public function editRegMailController() {
      $this->checkWritebleRights();
      $modelText = new Text_Model();
      $formEdit = new Form('mailreg_edit');
      // text emailu
      $elemTextEmail = new Form_Element_TextArea('text_email', $this->_('Text emailu'));
      $elemTextEmail->addValidation(new Form_Validator_NotEmpty());
      $elemTextEmail->setLangs();
      // naplníme pokud je čím
      $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_MAIL_REG);
      if ($text != false) {
         $elemTextEmail->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $formEdit->addElement($elemTextEmail);

      $eS = new Form_Element_Submit('save', $this->_('Uložit'));
      $formEdit->addElement($eS);

      if ($formEdit->isValid()) {
         $modelText->saveText($formEdit->text_email->getValues(), null,
            $this->category()->getId(), self::TEXT_KEY_MAIL_REG);
         $this->infoMsg()->addMessage($this->_('Text registračního e-mailu byl uložen'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $formEdit;
   }

   /**
    * controller pro úpravu textu
    */
   public function editTextController() {
      $this->checkWritebleRights();
      $modelText = new Text_Model();

      $formEdit = new Form('text_edit');

      $elemText = new Form_Element_TextArea('text', $this->_('Text přihlášky'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->setLangs();

      // naplníme pokud je čím
      $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_MAIN);
      if ($text != false) {
         $elemText->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $formEdit->addElement($elemText);

      $elemSave = new Form_Element_Submit('save', $this->_('Uložit'));
      $formEdit->addElement($elemSave);

      if ($formEdit->isValid()) {
         $modelText->saveText($formEdit->text->getValues(), null, $this->category()->getId(), self::TEXT_KEY_MAIN);
         $this->infoMsg()->addMessage($this->_('Text byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->formEdit = $formEdit;
   }

   public function editWelcomeController() {
      $this->checkWritebleRights();
      $modelText = new Text_Model();

      $formEdit = new Form('contact_edit');

      $elemText = new Form_Element_TextArea('text', $this->_('Text přihlášky'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->setLangs();

      // naplníme pokud je čím
      $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_WELCOME);
      if ($text != false) {
         $elemText->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $formEdit->addElement($elemText);

      $elemSave = new Form_Element_Submit('save', $this->_('Uložit'));
      $formEdit->addElement($elemSave);

      if ($formEdit->isValid()) {
         $modelText->saveText($formEdit->text->getValues(), null, $this->category()->getId(), self::TEXT_KEY_WELCOME);
         $this->infoMsg()->addMessage($this->_('Text byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->formEdit = $formEdit;
   }

   public function checkUserNameController($username = null) {
      $this->checkReadableRights();

      if($username == null) $username = $_POST['username'];

      $modelUsers = new Model_Users();
      $modelQueue = new UserReg_Model_Queue();

      $user = $modelUsers->getUser($username, true);
      $userQ = $modelQueue->getUser($username);

      $this->view()->isFree = false;
         $this->view()->msg = $this->_('obsazené');
      if($user === false AND $userQ === false){
         $this->view()->isFree = true;
         $this->view()->msg = $this->_('volné');
      }
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings, Form &$form) {

      $grpForm = $form->addGroup('form', 'Nastavení formuláře');

      $elemCreateNow = new Form_Element_Checkbox('createNow', 'Vytvořit účet okamžitě');
      $elemCreateNow->setValues(false);
      $elemCreateNow->setSubLabel('Pokud je vytvářen účet okamžitě, není generován odkaz na dokončení účtu. Proto není potřebná volba pro vypršení odkazu dokončení registrace.');
      $form->addElement($elemCreateNow, $grpForm);
      if (isset($settings[self::PARAM_CREATE_USER_NOW])) {
         $form->createNow->setValues($settings[self::PARAM_CREATE_USER_NOW]);
      }

      // maily správců
      $elemTGroups = new Form_Element_Select('groupId', 'Cílová skupina');
      // načtení uživatelů
      $modelUsers = new Model_Users();
      $groups = $modelUsers->getGroups();
      $grpsIds = array();
      foreach ($groups as $grp) {
         if ($grp[Model_Users::COLUMN_GROUP_ID] == VVE_DEFAULT_ID_GROUP) {
            $elemTGroups->setValues(VVE_DEFAULT_ID_GROUP);
         }
         $grpsIds[$grp[Model_Users::COLUMN_GROUP_NAME] . ' - ' . $grp[Model_Users::COLUMN_GROUP_LABEL]] = $grp[Model_Users::COLUMN_GROUP_ID];
      }
      $elemTGroups->setOptions($grpsIds);
      if (isset($settings[self::PARAM_TARGET_ID_GROUP])) {
         $elemTGroups->setValues($settings[self::PARAM_TARGET_ID_GROUP]);
      }

      $form->addElement($elemTGroups, $grpForm);

      $elemRegExpire = new Form_Element_Text('expire', 'Za kolik hodin vyprší odkaz pro dokončení registrace');

      if (isset($settings[self::PARAM_REG_LINK_EXPIRE])) {
         $elemRegExpire->setValues($settings[self::PARAM_REG_LINK_EXPIRE]);
      }
      $elemRegExpire->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
//      $elemRegExpire->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemRegExpire, $grpForm);

      if ($form->isSend() AND $form->createNow->getValues() != true) {
//         $form->expire->removeValidation('Form_Validator_NotEmpty');
         $form->expire->addValidation(new Form_Validator_NotEmpty());
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
         $settings[self::PARAM_CREATE_USER_NOW] = $form->createNow->getValues();
         $settings[self::PARAM_TARGET_ID_GROUP] = $form->groupId->getValues();
         $settings[self::PARAM_REG_LINK_EXPIRE] = $form->expire->getValues();
      }
   }

}
?>