<?php

class UserReg_Controller extends Controller {
   const PARAM_COND_AGREE = 'cond_agree';
   const PARAM_TARGET_ID_GROUP = 'target_id_grp';
   const PARAM_CREATE_USER_NOW = 'create_now';
   const PARAM_REG_LINK_EXPIRE = 'reg_link_expire';
   const PARAM_PASSWORD_MIN_LEN = 'pml';

   const DEFAULT_REG_LINK_EXPIRE = 24;

   const PARAM_ADMIN_RECIPIENTS = 'a_rec';
   const PARAM_OTHER_RECIPIENTS = 'o_rec';

   const TEXT_KEY_MAIN = 'main';
   const TEXT_KEY_MAIL_REG = 'mail_reg';
   const TEXT_KEY_WELCOME = 'welcome';

   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $modelUsers = new Model_Users();

      // načtení textů
      $modelText = new Text_Model();
      $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_MAIN);
      if ($text != false) {
         $this->view()->text = $text->{Text_Model::COLUMN_TEXT};
      }

      $formReg = new Form('reg');
      $formGrpAccount = $formReg->addGroup('account', $this->tr('Účet'));

      $elemUsername = new Form_Element_Text('username', $this->tr('Uživatelské jméno / e-mail'));
      $elemUsername->addValidation(new Form_Validator_NotEmpty());
      $elemUsername->addValidation(new Form_Validator_MinLength(5));
      $elemUsername->addValidation(new Form_Validator_Regexp('/^[a-zA-Z0-9@._-]+$/', $this->tr('Uživatelské jméno obsahuje nepovolené znaky.')));
      $elemUsername->setSubLabel($this->tr('Pouze písmena bez diakritiky, číslice a znaky: "@._-" (např. Váš e-mail)'));


      $formReg->addElement($elemUsername, $formGrpAccount);

      $elemPass = new Form_Element_Password('pass', $this->tr('Heslo'));
      $elemPass->addValidation(new Form_Validator_NotEmpty());
      $elemPass->addValidation(new Form_Validator_MinLength(5));
      $formReg->addElement($elemPass, $formGrpAccount);

      $elemPassControll = new Form_Element_Password('passctrl', $this->tr('Heslo (kontrola)'));
      $elemPassControll->addValidation(new Form_Validator_NotEmpty());
//      $elemPassControll->addValidation(new Form_Validator_MinLength(5));
      $formReg->addElement($elemPassControll, $formGrpAccount);


      $formGrpContact = $formReg->addGroup('contact', $this->tr('Kontaktní údaje'));
      $elemName = new Form_Element_Text('name', $this->tr('Jméno'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $formReg->addElement($elemName, $formGrpContact);

      $elemSurName = new Form_Element_Text('surname', $this->tr('Přijmení'));
      $elemSurName->addValidation(new Form_Validator_NotEmpty());
      $formReg->addElement($elemSurName, $formGrpContact);

      $elemMail = new Form_Element_Text('mail', $this->tr('E-mail'));
      $elemMail->addValidation(new Form_Validator_NotEmpty());
      $elemMail->addValidation(new Form_Validator_Email());
      $formReg->addElement($elemMail, $formGrpContact);
      
      $elemNote = new Form_Element_TextArea('note', $this->tr('Váš popis'));
      $elemNote->setSubLabel($this->tr('Charakteristika Vaší osoby, čím se zabýváte, kde pracujete, co je pro vás důležité, politická příslušnost atd.'
            .' <em>Text je zobrazen u Vašeho uživatelského účtu a můžete jej kdykoliv změnit.</em>'));
      $elemNote->addFilter(new Form_Filter_StripTags());
      $formReg->addElement($elemNote, $formGrpContact);

      $elemCondAgree = new Form_Element_Checkbox('condAgree', $this->category()->getParam(self::PARAM_COND_AGREE, $this->tr('Potvrzení')));
      $elemCondAgree->setSubLabel( sprintf( $this->tr('Potvrzením souhlasíte se zpracováním Vašich osobních údajů pro potřeby stránek %s.'), VVE_WEB_NAME ));
      $elemCondAgree->addValidation(new Form_Validator_NotEmpty($this->tr('Musíte souhlasit s podmínkami')));
      $elemCondAgree->setValues(true);
      $formReg->addElement($elemCondAgree);

      $elemSubmit = new Form_Element_Submit('send', $this->tr('Registrovat'));
      $formReg->addElement($elemSubmit);

      // kontrola stejného hesla
      if ($formReg->isSend()) {
         if ($formReg->pass->getValues() != $formReg->passctrl->getValues()) {
            $elemPassControll->setError($this->tr('Hesla se neshodují'));
         }
         $this->checkUserNameController($formReg->username->getValues());
         if ($this->view()->isFree == false) {
            $elemUsername->setError($this->tr('Zvolené uživatelské jméno je již obsazeno.'));
         }
      }

      if ($formReg->isValid()) {
         // vytvoření uživatele okamžitě
         if ($this->category()->getParam(self::PARAM_CREATE_USER_NOW, false) === true) {
            $newUser = $modelUsers->newRecord();
            $newUser->{Model_Users::COLUMN_USERNAME} = $formReg->username->getValues(); 
            $newUser->{Model_Users::COLUMN_NAME} = $formReg->name->getValues(); 
            $newUser->{Model_Users::COLUMN_SURNAME} = $formReg->surname->getValues(); 
            $newUser->{Model_Users::COLUMN_PASSWORD} = Auth::cryptPassword($formReg->pass->getValues()); 
            $newUser->{Model_Users::COLUMN_GROUP_ID} = $this->category()->getParam(self::PARAM_TARGET_ID_GROUP, VVE_DEFAULT_ID_GROUP); 
            $newUser->{Model_Users::COLUMN_MAIL} = $formReg->mail->getValues(); 
            $newUser->{Model_Users::COLUMN_NOTE} = $formReg->note->getValues(); 
            
            $modelUsers->save($newUser);

            // ===================== NEW 
            $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_MAIL_REG);
            $note = null;
            if ($text != false) {
               $note = $text->{Text_Model::COLUMN_TEXT};
            }
            
            $expire = new DateTime();
            $expire->modify('+' . $this->category()->getParam(self::PARAM_REG_LINK_EXPIRE, self::DEFAULT_REG_LINK_EXPIRE) . ' hours');
            
            $mail = new Email(true);
            $mail->setSubject($this->tr('Registrace na stránkách') . ' ' . VVE_WEB_NAME);
            $mail->addAddress($formReg->mail->getValues());
            
            
            $data = null;
            
            $data .= $this->_getMailTbRow($this->tr('Jméno a přijmení'), $formReg->name->getValues()." ".$formReg->surname->getValues());
            $data .= $this->_getMailTbRow($this->tr('Uživatelské jméno'), $formReg->username->getValues());
            $data .= $this->_getMailTbRow($this->tr('E-mail'), $formReg->mail->getValues());
            $data .= $this->_getMailTbRow($this->tr('Heslo'), $formReg->pass->getValues());
            
            $replacements = array(
                  '{NOTE}' => $note,
                  '{WEB_LINK}' => '<a href="'.$this->link()->clear(true).'">{WEB_NAME}</a>',
                  '{WEB_NAME}' => VVE_WEB_NAME,
                  '{REG_COMPLETE_LINK}' => null,
                  '{REG_LINK_EXPIRE}' => null,
                  '{USER_NAME}' => $formReg->username->getValues(),
                  '{DATA}' => $data,
                  '{DATE_TIME}' => vve_date('%x %X'),
            );
            
            $mail->setReplacements($replacements);
            
            $msg =
               '<h1>'.$this->tr('Na stránkách {WEB_LINK} byla provedena nová registrace uživatelského účtu "{USER_NAME}"').'</h1>'
               . '<h2>'.$this->tr('Informace o registraci účtu').': </h2>'
               . '<p><table cellpadding="5" border="1">'
               . '{DATA}'
               . ' </table></p>'
               . '<hr />'
               .'<div>{NOTE}</div>';
            $mail->setContent(Email::getBaseHtmlMail($msg));
            $mail->send();

            $this->sendAdminNotification($newUser);
            
            $this->infoMsg()->addMessage($this->tr('Registrace proběhla úspěšně.'));
            $this->link()->clear()->route('welcome')->reload();
         } else {
            /*
             * generace hashe pro pozdější ověření
             * Bude předáno v url jako paramter, zabrání se tak DoS útoku na registrace
             */
            $hash = md5(time() . $_SERVER['REMOTE_ADDR'] . $formReg->username->getValues());

            $model = new UserReg_Model_Queue();

            $newUserQ = $model->newRecord();
            
            $newUserQ->{UserReg_Model_Queue::COLUMN_ID_CAT} = $this->category()->getId(); 
            $newUserQ->{UserReg_Model_Queue::COLUMN_USERNAME} = $formReg->username->getValues(); 
            $newUserQ->{UserReg_Model_Queue::COLUMN_PASS} = $formReg->pass->getValues(); 
            $newUserQ->{UserReg_Model_Queue::COLUMN_MAIL} = $formReg->mail->getValues(); 
            $newUserQ->{UserReg_Model_Queue::COLUMN_NAME} = $formReg->name->getValues(); 
            $newUserQ->{UserReg_Model_Queue::COLUMN_SURNAME} = $formReg->surname->getValues(); 
            $newUserQ->{UserReg_Model_Queue::COLUMN_HASH} = $hash; 
            $newUserQ->{UserReg_Model_Queue::COLUMN_IP} = $_SERVER['REMOTE_ADDR']; 
            $newUserQ->{UserReg_Model_Queue::COLUMN_NOTE} = $formReg->note->getValues(); 
            
            $model->save($newUserQ);

            $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_MAIL_REG);
            $note = null;
            if ($text != false) {
               $note = $text->{Text_Model::COLUMN_TEXT};
            }
            
            $expire = new DateTime();
            $expire->modify('+' . $this->category()->getParam(self::PARAM_REG_LINK_EXPIRE, self::DEFAULT_REG_LINK_EXPIRE) . ' hours');

            $mail = new Email(true);
            $mail->setSubject($this->tr('Registrace na stránkách') . ' ' . VVE_WEB_NAME);
            $mail->addAddress($formReg->mail->getValues());
            
            
            $data = null;
            
            $data .= $this->_getMailTbRow($this->tr('Jméno a přijmení'), $formReg->name->getValues()." ".$formReg->surname->getValues());
            $data .= $this->_getMailTbRow($this->tr('Uživatelské jméno'), $formReg->username->getValues());
            $data .= $this->_getMailTbRow($this->tr('E-mail'), $formReg->mail->getValues());
            $data .= $this->_getMailTbRow($this->tr('Heslo'), $formReg->pass->getValues());
            
            $replacements = array(
                  '{NOTE}' => $note,
                  '{WEB_LINK}' => '<a href="'.$this->link()->clear(true).'">{WEB_NAME}</a>',
                  '{WEB_NAME}' => VVE_WEB_NAME,
                  '{REG_COMPLETE_LINK}' => '<a href="' . $this->link()->route('completeReg')->param('id', $hash)
                     . '" title="' . $this->tr('Dokončení registrace') . '">'.$this->tr('dokončení registrace').'</a>',
                  '{REG_LINK_EXPIRE}' => vve_date('%x %X', $expire),
                  '{USER_NAME}' => $formReg->username->getValues(),
                  '{DATA}' => $data,
                  '{DATE_TIME}' => vve_date('%x %X'),
            );
            
            $mail->setReplacements($replacements);
            
            $msg =
               '<h1>'.$this->tr('Na stránkách {WEB_LINK} byla provedena nová registrace uživatelského účtu "{USER_NAME}"').'</h1>'
               . '<p>'.$this->tr('Pro potvrzení registrace je nutné kliknout na následující odkaz <strong>{REG_COMPLETE_LINK}</strong>. Tento odkaz vyprší {REG_LINK_EXPIRE}.').': </p>'
               . '<h2>'.$this->tr('Informace o registraci účtu').': </h2>'
               . '<p><table cellpadding="5" border="1">'
               . '{DATA}'
               . ' </table></p>'
               . '<hr />'
               .'<div>{NOTE}</div>';
            
            $mail->setContent(Email::getBaseHtmlMail($msg));
            $mail->send();

            $this->infoMsg()->addMessage(sprintf($this->tr('Registrace byla zařazena. Na zadané emailové adrese "%s" nalezenete informace pro dokončení registrace.'), $formReg->mail->getValues()));
            $this->link()->reload();
         }
      }

      $this->view()->formReg = $formReg;
   }

   /**
    * Dokončení registrace
    */
   public function completeRegController() {
      $this->checkReadableRights();

      $model = new UserReg_Model_Queue();

      self::removeExpired($this->category()->getId(),
            $this->category()->getParam(self::PARAM_REG_LINK_EXPIRE, self::DEFAULT_REG_LINK_EXPIRE));

      $registration = $model->where(UserReg_Model_Queue::COLUMN_HASH." = :uhash",
            array('uhash' => $this->getRequestParam('id')))
            ->record();

      if ($registration == false) {
         $this->errMsg()->addMessage(
               $this->tr('Požadované registraci již vypršela platnost nebo nebyla vytvořena. Pokuste se zaregistrovat znovu.'));
      } else {
         $modelUsers = new Model_Users();

         $username = $registration->{UserReg_Model_Queue::COLUMN_USERNAME};
         $count = 1;
         while ($modelUsers->getUser($username) != false){
            $username = (string)$registration->{UserReg_Model_Queue::COLUMN_USERNAME}.$count;
            $count++;
            if($count > 100){
               $this->errMsg()->addMessage(
                     $this->tr('Toto uživatelské jméno a všech jeho 100 podob je již obsazeno. Vyplňte prosím znovu registraci s jiným uživatelským jménem.'));
               return;
            }
         }

         $newUser = $modelUsers->newRecord();

         $newUser->{Model_Users::COLUMN_USERNAME} = $registration->{UserReg_Model_Queue::COLUMN_USERNAME};
         $newUser->{Model_Users::COLUMN_NAME} = $registration->{UserReg_Model_Queue::COLUMN_NAME};
         $newUser->{Model_Users::COLUMN_SURNAME} = $registration->{UserReg_Model_Queue::COLUMN_SURNAME};
         $newUser->{Model_Users::COLUMN_PASSWORD} = Auth::cryptPassword($registration->{UserReg_Model_Queue::COLUMN_PASS});
         $newUser->{Model_Users::COLUMN_GROUP_ID} = $this->category()->getParam(self::PARAM_TARGET_ID_GROUP, VVE_DEFAULT_ID_GROUP);
         $newUser->{Model_Users::COLUMN_MAIL} = $registration->{UserReg_Model_Queue::COLUMN_MAIL};
         $newUser->{Model_Users::COLUMN_NOTE} = $registration->{UserReg_Model_Queue::COLUMN_NOTE};
         
         $modelUsers->save($newUser);
         
         $this->sendAdminNotification($newUser);
         
         $model->delete($registration);

         $this->infoMsg()->addMessage($this->tr('Registrace proběhla úspěšně.'));
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
      $elemTextEmail = new Form_Element_TextArea('text_mail', $this->tr('Text emailu'));
      $elemTextEmail->setLangs();
      $elemTextEmail->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      // naplníme pokud je čím
      $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_MAIL_REG);
      if ($text != false) {
         $elemTextEmail->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $formEdit->addElement($elemTextEmail);

      $elemSave = new Form_Element_SaveCancel('save');
      $formEdit->addElement($elemSave);

      if($formEdit->isSend() AND $formEdit->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if ($formEdit->isValid()) {
         $modelText->saveText($formEdit->text_mail->getValues(), null,
            $this->category()->getId(), self::TEXT_KEY_MAIL_REG);
         $this->infoMsg()->addMessage($this->tr('Text registračního e-mailu byl uložen'));
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

      $elemText = new Form_Element_TextArea('text', $this->tr('Text přihlášky'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->setLangs();

      // naplníme pokud je čím
      $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_MAIN);
      if ($text != false) {
         $elemText->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $formEdit->addElement($elemText);

      $elemSave = new Form_Element_SaveCancel('save');
      $formEdit->addElement($elemSave);

      if($formEdit->isSend() AND $formEdit->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      if ($formEdit->isValid()) {
         $modelText->saveText($formEdit->text->getValues(), null, $this->category()->getId(), self::TEXT_KEY_MAIN);
         $this->infoMsg()->addMessage($this->tr('Text byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->formEdit = $formEdit;
   }

   public function editWelcomeController() {
      $this->checkWritebleRights();
      $modelText = new Text_Model();

      $formEdit = new Form('contact_edit');

      $elemText = new Form_Element_TextArea('text', $this->tr('Text'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->setLangs();

      // naplníme pokud je čím
      $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_WELCOME);
      if ($text != false) {
         $elemText->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $formEdit->addElement($elemText);

      $elemSave = new Form_Element_SaveCancel('save');
      $formEdit->addElement($elemSave);

      if($formEdit->isSend() AND $formEdit->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      if ($formEdit->isValid()) {
         $modelText->saveText($formEdit->text->getValues(), null, $this->category()->getId(), self::TEXT_KEY_WELCOME);
         $this->infoMsg()->addMessage($this->tr('Text byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->formEdit = $formEdit;
   }

   public function checkUserNameController($username = null) 
   {
      $this->checkReadableRights();
      $this->view()->isFree = false;

      if($username === null) $username = isset($_POST['username']) ? $_POST['username'] : null;
      
      if($username == null){
         return; 
      }
      
      $modelUsers = new Model_Users();
      $modelQueue = new UserReg_Model_Queue();

      $user = $modelUsers->getUser($username, true);
      $userQ = $modelQueue->getUser($username);

      $this->view()->msg = $this->tr('obsazené');
      if($user === false AND $userQ === false){
         $this->view()->isFree = true;
         $this->view()->msg = $this->tr('volné');
      }
   }

   protected function sendAdminNotification($userRecord) 
   {
      // maily adminů - z uživatelů
      $usersId = $this->category()->getParam(self::PARAM_ADMIN_RECIPIENTS, array());
      if(empty($usersId)){
         return;
      }
      
      $mail = new Email(true);
      
      $adminMails = array();
      $modelusers = new Model_Users();
      foreach ($usersId as $id) {
         $user = $modelusers->record($id);
         $adminMails = array_merge($adminMails, explode(';', $user->{Model_Users::COLUMN_MAIL}));
      }
      $mail->addAddress($adminMails);
      $name = $userRecord->{Model_Users::COLUMN_NAME}." ".$userRecord->{Model_Users::COLUMN_SURNAME};
      $mail->setSubject( sprintf($this->tr('Registrace nového uživatele %s na stránkách'), $name ) . ' ' . VVE_WEB_NAME);
      
      $data = null;
            
      $data .= $this->_getMailTbRow($this->tr('Jméno a přijmení'), $name);
      $data .= $this->_getMailTbRow($this->tr('Uživatelské jméno'), $userRecord->{Model_Users::COLUMN_USERNAME});
      $data .= $this->_getMailTbRow($this->tr('E-mail'), $userRecord->{Model_Users::COLUMN_MAIL});
      $data .= $this->_getMailTbRow($this->tr('ID skupiny'), $userRecord->{Model_Users::COLUMN_GROUP_ID});
            
      $replacements = array(
         '{WEB_LINK}' => '<a href="'.$this->link()->clear(true).'">{WEB_NAME}</a>',
         '{WEB_NAME}' => VVE_WEB_NAME,
         '{USER_NAME}' => $userRecord->{Model_Users::COLUMN_USERNAME},
         '{DATA}' => $data,
         '{DATE_TIME}' => vve_date('%x %X'),
      );
            
      $mail->setReplacements($replacements);
            
      $msg =
         '<h1>'.$this->tr('Na stránkách {WEB_LINK} byla provedena nová registrace uživatelského účtu "{USER_NAME}"').'</h1>'
         . '<h2>'.$this->tr('Informace o registraci účtu').': </h2>'
         . '<p><table cellpadding="5" border="1">'
         . '{DATA}'
         . ' </table></p>'
         . '<hr />';
      $mail->setContent(Email::getBaseHtmlMail($msg));
      $mail->send();
   } 
   
   private function _getMailTbRow($col1, $col2) {
      $r = '<tr>';
      $r .= '<th style="text-align: left;">'.$col1.'</th>';
      $r .= '<td>'.nl2br($col2).'</td>';
      $r .= '</tr>';
      return $r;
   }
   
   /**
    * Metoda pro nastavení modulu
    */
   public function settings(&$settings, Form &$form) {

      /* Nastavení formuláře */
      $grpForm = $form->addGroup('form', $this->tr('Nastavení formuláře'));

      $elemCreateNow = new Form_Element_Checkbox('createNow', $this->tr('Vytvořit účet okamžitě'));
      $elemCreateNow->setValues(false);
      $elemCreateNow->setSubLabel($this->tr('Pokud je vytvářen účet okamžitě, není generován odkaz na dokončení účtu. Proto není potřebná volba pro vypršení odkazu dokončení registrace.'));
      $form->addElement($elemCreateNow, $grpForm);
      if (isset($settings[self::PARAM_CREATE_USER_NOW])) {
         $form->createNow->setValues($settings[self::PARAM_CREATE_USER_NOW]);
      }

      // maily správců
      $elemTGroups = new Form_Element_Select('groupId', $this->tr('Cílová skupina'));
      // načtení uživatelů
      VVE_SUB_SITE_DOMAIN == null ? $domain = 'www' : $domain = VVE_SUB_SITE_DOMAIN;
      $groupsModel = new Model_Groups();
      $groupsModel->join(Model_Groups::COLUMN_ID, array('t_sg' => 'Model_SitesGroups'), Model_SitesGroups::COLUMN_ID_GROUP, array())
         ->join(array('t_sg' => Model_SitesGroups::COLUMN_ID_SITE), array('t_s' => 'Model_Sites'), Model_Sites::COLUMN_ID)
         ->where('t_s.'.Model_Sites::COLUMN_ID.' IS NULL OR t_s.'.Model_Sites::COLUMN_DOMAIN.' = :domain', array('domain' => $domain));
      $grpsIds = array();
      
      foreach ($groupsModel->records() as $grp) {
         if ($grp->{Model_Groups::COLUMN_ID} == VVE_DEFAULT_ID_GROUP) {
            $elemTGroups->setValues(VVE_DEFAULT_ID_GROUP);
         }
         $grpsIds[$grp->{Model_Groups::COLUMN_NAME} . ' - ' . $grp->{Model_Groups::COLUMN_LABEL}] = $grp->{Model_Groups::COLUMN_ID};
      }
      $elemTGroups->setOptions($grpsIds);
      if (isset($settings[self::PARAM_TARGET_ID_GROUP])) {
         $elemTGroups->setValues($settings[self::PARAM_TARGET_ID_GROUP]);
      }
      $form->addElement($elemTGroups, $grpForm);
      
      $elemRegExpire = new Form_Element_Text('expire', $this->tr('Platnost dokončení'));
      $elemRegExpire->setSubLabel($this->tr('Za kolik hodin vyprší odkaz pro dokončení registrace'));
      if (isset($settings[self::PARAM_REG_LINK_EXPIRE])) {
         $elemRegExpire->setValues($settings[self::PARAM_REG_LINK_EXPIRE]);
      }
      $elemRegExpire->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemRegExpire, $grpForm);

      $elemPassMinL = new Form_Element_Text('passMinL', $this->tr('Minimální délka hesla'));
      $elemPassMinL->setValues(5);
      if (isset($settings[self::PARAM_PASSWORD_MIN_LEN])) {
         $elemPassMinL->setValues($settings[self::PARAM_PASSWORD_MIN_LEN]);
      }
      $elemPassMinL->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemPassMinL, $grpForm);

      /* Nootifikace */
      $grpNotify = $form->addGroup('notify', $this->tr('Oznámení o registraci'));
      $elemAdmins = new Form_Element_Select('notifyAdmins', 'Adresy uživatelů v systému');
      // načtení uživatelů
      $modelUsers = new Model_Users();
      $users = $modelUsers->usersForThisWeb(true)->records(PDO::FETCH_OBJ);
      $usersIds = array();
      foreach ($users as $user) {
         if($user->{Model_Users::COLUMN_MAIL} != null){
            $usersIds[$user->{Model_Users::COLUMN_NAME} ." ".$user->{Model_Users::COLUMN_SURNAME}
            .' ('.$user->{Model_Users::COLUMN_USERNAME}.') - '.$user->{Model_Users::COLUMN_GROUP_LABEL}
            .' ('.$user->{Model_Users::COLUMN_GROUP_NAME}.')'] = $user->{Model_Users::COLUMN_ID};
         }
      }
      $elemAdmins->setOptions($usersIds);
      $elemAdmins->setMultiple();
      $elemAdmins->html()->setAttrib('size', 4);
      if (isset($settings[self::PARAM_ADMIN_RECIPIENTS])) {
         $elemAdmins->setValues($settings[self::PARAM_ADMIN_RECIPIENTS]);
      }
      $form->addElement($elemAdmins, $grpNotify);
      
      if ($form->isSend() AND $form->createNow->getValues() != true) {
         $form->expire->addValidation(new Form_Validator_NotEmpty());
      }
      
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
         $settings[self::PARAM_CREATE_USER_NOW] = $form->createNow->getValues();
         $settings[self::PARAM_TARGET_ID_GROUP] = $form->groupId->getValues();
         $settings[self::PARAM_REG_LINK_EXPIRE] = $form->expire->getValues();
         $settings[self::PARAM_PASSWORD_MIN_LEN] = $form->passMinL->getValues();
         $settings[self::PARAM_ADMIN_RECIPIENTS] = $form->notifyAdmins->getValues();
      }
   }

   /* Autorun metody */
    
   public static function AutoRunDaily()
   {
      $modelCats = new Model_Category();
      $model = new UserReg_Model_Queue();
      $cats = $modelCats->where(Model_Category::COLUMN_MODULE." = :module", array('module' => 'userreg'));

      foreach ($cats as $cObj) {
         $cat = new Category($cObj->{Model_Category::COLUMN_ID}, false, $cObj);
         self::removeExpired($cat->getId(), $cat->getParam(self::PARAM_REG_LINK_EXPIRE, self::DEFAULT_REG_LINK_EXPIRE));
         
      }
   }
   
   protected static function removeExpired($idCat, $hours)
   {
      $model = new UserReg_Model_Queue();
      $model
      ->where(UserReg_Model_Queue::COLUMN_ID_CAT." = :idc AND TIMESTAMPDIFF(HOUR,".UserReg_Model_Queue::COLUMN_TIME_ADD.",NOW()) > :inter",
            array('idc' => $idCat, 'inter' => $hours))
            ->delete();
   }
   
}
?>