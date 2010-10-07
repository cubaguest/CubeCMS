<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Login_Controller extends Controller {
	/**
	 * minimální délka hesla
	 * @var integer
	 */
	const PASSWD_MIN_LENGTH = 5;
	const PASSWD_MAX_LENGTH = 8;
	
	public function mainController() {
      $model = new Model_Users();

      $this->view()->user = $model->getUserById(Auth::getUserId());
	}
	
	/**
	 * Metoda pro úpravu hesla
	 */
	public function changePasswdController() {
		$this->checkWritebleRights();

      $form = new Form('pass_');
      $form->html()->setAttrib('autocomplete', 'off');

      $elemPassCur = new Form_Element_Password('current', $this->_('Staré heslo'));
      $elemPassCur->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemPassCur);

      $elemPassN1 = new Form_Element_Text('new1', $this->_('Nové heslo'));
      $elemPassN1->addValidation(new Form_Validator_NotEmpty());
      $elemPassN1->addValidation(new Form_Validator_MinLength(5));
      $form->addElement($elemPassN1);

      $elemPassN2 = new Form_Element_Text('new2', $this->_('Nové heslo'));
      $elemPassN2->addValidation(new Form_Validator_NotEmpty());
      $elemPassN2->addValidation(new Form_Validator_MinLength(5));
      $elemPassN2->setSubLabel($this->_('Potvrzení hesla'));
      $form->addElement($elemPassN2);

      $elemPassGener = new Form_Element_Text('gener', $this->_('Generované'));
      $form->addElement($elemPassGener);

      $elemSubmit = new Form_Element_SaveCancel('change');
      $form->addElement($elemSubmit);

      //        Pokud byl odeslán formulář
      if($form->isSend()){
         if($form->change->getValues() == false){
            $this->link()->route()->reload();
         }

         $model = new Model_Users();
         $user = $model->getUserById(Auth::getUserId());
         // kontrola starého hesla
         if(Auth::cryptPassword($form->current->getValues()) != $user->{Model_Users::COLUMN_PASSWORD}){
            $form->current->setError($this->_('Špatně zadané aktuální heslo'));
         }

         if($form->new1->getValues() != $form->new2->getValues()){
            $form->new1->setError($this->_('Nová hesla se neshodují'));
         }
      }

      if($form->isValid()){
         $model = new Model_Users();
         $model->changeUserPassword(Auth::getUserId(), $form->new1->getValues());
         $this->infoMsg()->addMessage($this->_("Heslo bylo úspěšně změněno"));
   		$this->link()->route()->reload();
      }

      $this->view()->form = $form;
	}

   public function changeUserController() {
      $this->checkWritebleRights();

      $model = new Model_Users();
      $user = $model->getUserById(Auth::getUserId());

      $form = new Form('user_');

      $elemName = new Form_Element_Text('name', $this->_('Jméno'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $elemName->setValues($user->{Model_Users::COLUMN_NAME});
      $form->addElement($elemName);

      $elemSurName = new Form_Element_Text('surname', $this->_('Přijmení'));
      $elemSurName->addValidation(new Form_Validator_NotEmpty());
      $elemSurName->setValues($user->{Model_Users::COLUMN_SURNAME});
      $form->addElement($elemSurName);

      $elemEmails = new Form_Element_Text('email', $this->_('Email'));
      $elemEmails->addValidation(new Form_Validator_NotEmpty());
      $elemEmails->addValidation(new Form_Validator_Email());
      $elemEmails->setValues($user->{Model_Users::COLUMN_MAIL});
      $form->addElement($elemEmails);

      $elemNote = new Form_Element_TextArea('note', $this->_('Poznámky'));
      $elemNote->setValues($user->{Model_Users::COLUMN_NOTE});
      $form->addElement($elemNote);

      $elemSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($elemSubmit);

      if($form->isSend() AND $form->save->getValues() == false){
         // kontrola emailových adres
//         $mailsClean = preg_replace('/[^a-z0-9;.@_-]*/i', '', $form->emails->getValues()); // bílé znaky
//         $mailsClean = preg_replace('/\s+/i', '', $form->emails->getValues()); // bílé znaky
//         $mails = explode(';', $mailsClean);
//
//         $validMails = true;
//         $validator = new Validator_EMail();
//         foreach ($mails as $mail) {
//            $validator->setValues($mail);
//            if(!$validator->isValid()){
//               $validMails = false;
//               break;
//            }
//         }
//         if($validMails == false){
//            $form->emails->setError($this->_('E-mailové adresy byly špatně zadány'));
//         }
         $this->link()->route()->reload();
      }


      if($form->isValid()){
         $model->saveUser(Auth::getUserName(), $form->name->getValues(),
                 $form->surname->getValues(), null,
                 $user->{Model_Users::COLUMN_ID_GROUP}, $form->email->getValues(),
                 $form->note->getValues(), $user->{Model_Users::COLUMN_BLOCKED}, Auth::getUserId());
         $this->infoMsg()->addMessage($this->_('Změny byly uloženy'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
   }

   public function newPasswordController() {
      $this->checkReadableRights();
      // redirect to account if login
      if(Auth::isLogin() == true)
         $this->link()->route()->reload ();
      
      $modelUsr = new Model_Users();

      $form = new Form('newpass_');

      $eUsername = new Form_Element_Text('username', $this->_('Uživatelské jméno'));
      $eUsername->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($eUsername);

      $elemSubmit = new Form_Element_SaveCancel('restore', array($this->_('Zaslat'), _('Zrušit')));
      $form->addElement($elemSubmit);

      if($form->isSend()){
         if($form->restore->getValues() == false){
            $this->link()->route()->reload();
         }
         if($form->username->getValues() != null AND
            $modelUsr->where(Model_Users::COLUMN_USERNAME, $form->username->getValues())->record() == false){
            $eUsername->setError($this->_('Zadané uživatelské jméno neexistuje'));
         }
      }

      if($form->isValid()){
         $user = $modelUsr->where(Model_Users::COLUMN_USERNAME, $form->username->getValues())->record();

         $mail = explode(';', $user->{Model_Users::COLUMN_MAIL});

         $email = new Email(false);
         $email->setFrom($mail[0], $user->{Model_Users::COLUMN_NAME}.' '.$user->{Model_Users::COLUMN_SURNAME});
         $email->setSubject($this->_('Obnova zapomenutého hesla'));

         $cnt = $this->_("Vazeny uzivateli,\n zasilame Vam vyzadanou zmenu hesla.\n
Pokud jste tento email nevygeneroval Vy, jedna se nejspise\n
o omyl jineho uzivatele a muzete tedy tento email klidne\n
ignorovat.\n");
         $newPass = self::generatePassword();
         $cnt .= "\n".  $this->_('Heslo').': '.$newPass."\n\n";
         $cnt .= $this->_("S pozdravem\ntým").' '.VVE_WEB_NAME;
         $email->setContent($cnt);
         $email->send();

         // need release 6.4 r4 or higer
         if(defined('Model_Users::COLUMN_PASSWORD_RESTORE')){
            $user->{Model_Users::COLUMN_PASSWORD_RESTORE} = $newPass;
         } else {
            $user->{Model_Users::COLUMN_PASSWORD} = Auth::cryptPassword($newPass);
         }
         $modelUsr->save($user);

         $this->log(sprintf('Změna hesla uživatele %s', $form->username->getValues()));
         $this->infoMsg()->addMessage($this->_('Nově vygenerované heslo bylo zasláno na Váš e-mail'));
         $this->link()->route()->reload();
      }
      
      $this->view()->form = $form;
   }

   public static function generatePassword() {
      $string = null;
      $letters = array_merge(range('A', 'Z'), range('a', 'z'),range(0, 9));
      for ($index = self::PASSWD_MIN_LENGTH; $index <= self::PASSWD_MAX_LENGTH; $index++) {
         $string .= $letters[rand(0, count($letters))];
      }
      return $string; 
   }
}

?>