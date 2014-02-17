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
      $modelLogins = new Model_UsersLogins;

      $this->view()->user = $model->joinFK(Model_Users::COLUMN_GROUP_ID)->record(Auth::getUserId());
      
      $this->view()->lastLogin = $modelLogins
         ->where(Model_UsersLogins::COLUMN_ID_USER." = :idu", array('idu' => Auth::getUserId()))
         ->order(array( Model_UsersLogins::COLUMN_TIME => Model_ORM::ORDER_DESC ) )
         ->limit(1, 1)
         ->record(); 
	}
	
	/**
	 * Metoda pro úpravu hesla
	 */
	public function changePasswdController() {
      if(!Auth::isLogin()){return false;}
      
      $model = new Model_Users();

      $form = new Form('pass_', true);
      $form->html()->setAttrib('autocomplete', 'off');

      $elemPassCur = new Form_Element_Password('current', $this->tr('Staré heslo'));
      $elemPassCur->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemPassCur);

      $elemPassN1 = new Form_Element_Password('new1', $this->tr('Nové heslo'));
      $elemPassN1->addValidation(new Form_Validator_NotEmpty());
      $elemPassN1->addValidation(new Form_Validator_MinLength(5));
      $form->addElement($elemPassN1);

      $elemPassN2 = new Form_Element_Password('new2', $this->tr('Nové heslo'));
      $elemPassN2->addValidation(new Form_Validator_NotEmpty());
      $elemPassN2->addValidation(new Form_Validator_MinLength(5));
      $elemPassN2->setSubLabel($this->tr('Potvrzení hesla'));
      $form->addElement($elemPassN2);

      $elemPassGener = new Form_Element_Text('gener', $this->tr('Generované'));
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
            $form->current->setError($this->tr('Špatně zadané aktuální heslo'));
         }

         if($form->new1->getValues() != $form->new2->getValues()){
            $form->new1->setError($this->tr('Nová hesla se neshodují'));
         }
      }

      if($form->isValid()){
         $user = $model->record(Auth::getUserId());
         $user->{Model_Users::COLUMN_PASSWORD} = Auth::cryptPassword($form->new1->getValues());
         $model->save($user);
         $this->infoMsg()->addMessage($this->tr("Heslo bylo úspěšně změněno"));
   		$this->link()->route()->reload();
      }

      $this->view()->form = $form;
	}

   public function changeUserController() {
      if(!Auth::isLogin()){return false;}
      $model = new Model_Users();
      $user = $model->record(Auth::getUserId());

      $form = $this->createEditUserForm($user); // vytvoříme instanci formu

      if($form->isSend() ){
         if(!$form->save->getValues()){
            $this->link()->route()->redirect();
         }
         
//         $reserver = $model->where(Model_Users::COLUMN_USERNAME." = :uname AND ".Model_Users::COLUMN_ID." != :uid",
//               array('uname' => $form->username->getValues(), 'uid' => Auth::getUserId()))
//            ->count();
//
//         if((bool)$reserver){
//            $form->username->setError($this->tr('Vybrané uživatelské jméno je obsazeno'));
//         }
      }

      if($form->isValid()){
         $this->saveUser($form);
         $this->infoMsg()->addMessage($this->tr('Změny byly uloženy'));
         $this->link()->route()->redirect();
      }
      $this->view()->form = $form;
   }

   /**
    * @return Form
    */
   protected function createEditUserForm(Model_ORM_Record $user) {
      $form = new Form('user_', true);

      $fGrpBase = $form->addGroup('base', $this->tr('Základní informace'));

//      $elemUserName = new Form_Element_Text('username', $this->tr('Uživatelské jméno'));
//      $elemUserName->addValidation(new Form_Validator_NotEmpty());
//      $form->addElement($elemUserName, $fGrpBase);
      
      $elemName = new Form_Element_Text('name', $this->tr('Jméno'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName, $fGrpBase);

      $elemSurName = new Form_Element_Text('surname', $this->tr('Přijmení'));
      $elemSurName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemSurName, $fGrpBase);

      $elemEmails = new Form_Element_Text('email', $this->tr('Email'));
      $elemEmails->addValidation(new Form_Validator_NotEmpty());
      $elemEmails->addValidation(new Form_Validator_Email());
      $form->addElement($elemEmails, $fGrpBase);

      $fGrpOther = $form->addGroup('other', $this->tr('Ostatní'));
      $elemNote = new Form_Element_TextArea('note', $this->tr('Poznámky'));
      $form->addElement($elemNote, $fGrpOther);

      if(count(Locales::getAppLangs()) > 1 && Auth::isAdmin()){
         $fGrpSettings = $form->addGroup('settings', $this->tr('Nastavení'));
         $elemLang = new Form_Element_Select('lang', $this->tr('Výchozí jazyk'));
         $elemLang->setOptions(array_flip(Locales::getAppLangsNames()));
         $form->addElement($elemLang, $fGrpSettings);
      }
      
      $elemSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($elemSubmit);

      $form->name->setValues($user->{Model_Users::COLUMN_NAME});
      $form->surname->setValues($user->{Model_Users::COLUMN_SURNAME});
      $form->email->setValues($user->{Model_Users::COLUMN_MAIL});
      $form->note->setValues($user->{Model_Users::COLUMN_NOTE});
      if(isset($form->lang)){
         $form->lang->setValues(Model_UsersSettings::getSettings('userlang', Locales::getDefaultLang()));
      }

      return $form;
   }

   protected function saveUser(Form $form) {
      $modelUser = new Model_Users();
      $user = $modelUser->record(Auth::getUserId());
//      $user->{Model_Users::COLUMN_USERNAME} = $form->username->getValues();
      $user->{Model_Users::COLUMN_NAME} = $form->name->getValues();
      $user->{Model_Users::COLUMN_SURNAME} = $form->surname->getValues();
      $user->{Model_Users::COLUMN_MAIL} = $form->email->getValues();
      $user->{Model_Users::COLUMN_NOTE} = $form->note->getValues();
      if(isset($form->lang)){
         Model_UsersSettings::setSettings('userlang', $form->lang->getValues());
      }
      $modelUser->save($user);
   }


   public function newPasswordController() {
      // redirect to account if login
      if(Auth::isLogin() == true)
         $this->link()->route()->reload();
      
      $modelUsr = new Model_Users();

      $form = new Form('newpass_', true);

      $eUsername = new Form_Element_Text('username', $this->tr('Uživatelské jméno'));
      $eUsername->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($eUsername);

      $elemSubmit = new Form_Element_SaveCancel('restore', array($this->tr('Zaslat'), $this->tr('Zrušit')));
      $form->addElement($elemSubmit);

      if($form->isSend()){
         if($form->restore->getValues() == false){
            $this->link()->route()->reload();
         }
         if($form->username->getValues() != null AND
            $modelUsr->where(Model_Users::COLUMN_USERNAME." = :username ||  ".Model_Users::COLUMN_MAIL." = :mail",
            array('username' => $form->username->getValues(), 'mail' => $form->username->getValues()))->record() == false){
            $eUsername->setError($this->tr('Zadané uživatelské jméno neexistuje'));
         }
      }

      if($form->isValid()){
         Auth::sendRestorePassword($form->username->getValues());
         $this->log(sprintf('Změna hesla uživatele %s', $form->username->getValues()));
         $this->infoMsg()->addMessage($this->tr('Nové heslo Vám bylo zasláno na Váš e-mail'));
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
