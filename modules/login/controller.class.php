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

   protected $form;
   
   public function mainController() {
      $model = new Model_Users();

      $this->view()->user = $model->getUserById(Auth::getUserId());
	}
	
	/**
	 * Metoda pro úpravu hesla
	 */
	public function changePasswdController() {
		$this->checkWritebleRights();
      $model = new Model_Users();

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

         $user = $model->record(Auth::getUserId());
         // kontrola starého hesla
         if(Auth::cryptPassword($form->current->getValues()) != $user->{Model_Users::COLUMN_PASSWORD}){
            $form->current->setError($this->_('Špatně zadané aktuální heslo'));
         }

         if($form->new1->getValues() != $form->new2->getValues()){
            $form->new1->setError($this->_('Nová hesla se neshodují'));
         }
      }

      if($form->isValid()){
         $user = $model->record(Auth::getUserId());
         $user->{Model_Users::COLUMN_PASSWORD} = Auth::cryptPassword($form->new1->getValues());
         $model->save($user);
         $this->infoMsg()->addMessage($this->_("Heslo bylo úspěšně změněno"));
   		$this->link()->route()->reload();
      }

      $this->view()->form = $form;
	}

   public function changeUserController() {
      $this->checkWritebleRights();

      $model = new Model_Users();
      $user = $model->record(Auth::getUserId());

      $this->createEditUserForm(); // vytvoříme instanci formu
      $this->form->name->setValues($user->{Model_Users::COLUMN_NAME});
      $this->form->surname->setValues($user->{Model_Users::COLUMN_SURNAME});
      $this->form->email->setValues($user->{Model_Users::COLUMN_MAIL});
      $this->form->note->setValues($user->{Model_Users::COLUMN_NOTE});
      unset ($model);

      if($this->form->isSend() AND $this->form->save->getValues() == false){
         $this->link()->route()->reload();
      }

      if($this->form->isValid()){
         $this->saveUser();
         $this->infoMsg()->addMessage($this->_('Změny byly uloženy'));
         $this->link()->route()->reload();
      }
      $this->view()->form = $this->form;
   }

   protected function createEditUserForm() {
      $this->form = new Form('user_');

      $fGrpBase = $this->form->addGroup('base', $this->_('Základní informace'));

      $elemName = new Form_Element_Text('name', $this->_('Jméno'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $this->form->addElement($elemName, $fGrpBase);

      $elemSurName = new Form_Element_Text('surname', $this->_('Přijmení'));
      $elemSurName->addValidation(new Form_Validator_NotEmpty());
      $this->form->addElement($elemSurName, $fGrpBase);

      $elemEmails = new Form_Element_Text('email', $this->_('Email'));
      $elemEmails->addValidation(new Form_Validator_NotEmpty());
      $elemEmails->addValidation(new Form_Validator_Email());
      $this->form->addElement($elemEmails, $fGrpBase);

      $fGrpOther = $this->form->addGroup('other', $this->_('Ostatní'));
      $elemNote = new Form_Element_TextArea('note', $this->_('Poznámky'));
      $this->form->addElement($elemNote, $fGrpOther);

      $elemSubmit = new Form_Element_SaveCancel('save');
      $this->form->addElement($elemSubmit);
   }

   protected function saveUser() {
      $modelUser = new Model_Users();
      $user = $modelUser->record(Auth::getUserId());
      $user->{Model_Users::COLUMN_NAME} = $this->form->name->getValues();
      $user->{Model_Users::COLUMN_SURNAME} = $this->form->surname->getValues();
      $user->{Model_Users::COLUMN_MAIL} = $this->form->email->getValues();
      $user->{Model_Users::COLUMN_NOTE} = $this->form->note->getValues();
      $modelUser->save($user);
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
         $user = $modelUsr->where(Model_Users::COLUMN_USERNAME.' = :uname', array('uname' => $form->username->getValues()))->record();

         $mail = explode(';', $user->{Model_Users::COLUMN_MAIL});

         $email = new Email(false);
         $email->addAddress($mail[0], $user->{Model_Users::COLUMN_NAME}.' '.$user->{Model_Users::COLUMN_SURNAME});
         $email->setSubject($this->_('Obnova zapomenutého hesla'));

         $cnt = $this->_("Vazeny uzivateli,\nzasilame Vam vyzadanou zmenu hesla.\nPokud jste tento email nevygeneroval Vy, jedna se nejspise\no omyl jineho uzivatele a muzete tento email ignorovat.\n");
         $newPass = self::generatePassword();
         $cnt .= "\n".  $this->_('Heslo').': '.$newPass."\n\n";
         $cnt .= $this->_("S pozdravem\nTým").' '.VVE_WEB_NAME;
         $email->setContent($cnt);
         $email->send();

         if(defined('Model_Users::COLUMN_PASSWORD_RESTORE')){// need release 6.4 r4 or higer
            $user->{Model_Users::COLUMN_PASSWORD_RESTORE} = Auth::cryptPassword($newPass);
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
      $numLetters = round(rand(self::PASSWD_MIN_LENGTH, self::PASSWD_MAX_LENGTH));
      for ($index = 0; $index < $numLetters; $index++) {
         $string .= $letters[rand(0, count($letters)-1)];
      }
      return $string; 
   }
}

?>