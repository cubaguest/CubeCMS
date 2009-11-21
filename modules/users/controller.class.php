<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Users_Controller extends Controller {
	/**
	 * Minimální délka hesla
	 * @var integer
	 */
	const PASSWORD_MIN_LENGHT = 5;
	
	public function mainController() {
      $this->checkControllRights();
      $model = new Model_Users();

      $formRemove = new Form('user_');

      $elemId = new Form_Element_Hidden('id');
      $formRemove->addElement($elemId);

      $elemSub = new Form_Element_SubmitImage('remove', $this->_('Odstranit'));
      $formRemove->addElement($elemSub);

      if($formRemove->isValid()){
         $user = $model->getUserById($formRemove->id->getValues());
         $model->deleteUser($formRemove->id->getValues());
         $this->infoMsg()->addMessage($this->_(sprintf('Uživatel "%s" byl smazán', $user->{Model_Users::COLUMN_USERNAME})));
         $this->link()->reload();
      }

      $formEnable = new Form('userstatus_');

      $elemId = new Form_Element_Hidden('id');
      $formEnable->addElement($elemId);
      $elemStat = new Form_Element_Hidden('status');
      $formEnable->addElement($elemStat);

      $elemSub = new Form_Element_SubmitImage('change', $this->_('Změnit'));
      $formEnable->addElement($elemSub);

      if($formEnable->isValid()) {
         if($formEnable->status->getValues() == 'enable') {
            $model->enableUser($formEnable->id->getValues());
            $this->infoMsg()->addMessage($this->_('Uživatel byl aktivován'));
         } else if($formEnable->status->getValues() == 'disable') {
            $model->disableUser($formEnable->id->getValues());
            $this->infoMsg()->addMessage($this->_('Uživatel byl deaktivován'));
         }
         $this->link()->reload();
      }


      $this->view()->template()->addTplFile('listUsers.phtml');
	}
	
	/**
	 * Metoda pro zobrazení detailu zástupce
	 */
	public function showController() {
	}
	
	/**
	 * Metoda pro úpravu
	 */
	public function edituserController() {
      $this->checkControllRights();
      $mUser = new Model_Users();
      $user = $mUser->getUserById($this->getRequest('id'));


      $form = $this->createUserForm($user->{Model_Users::COLUMN_USERNAME});

      $elemID = new Form_Element_Hidden('iduser');
      $elemID->setValues($this->getRequest('id'));
      $form->addElement($elemID);


      $form->username->setValues($user->{Model_Users::COLUMN_USERNAME});
      $form->name->setValues($user->{Model_Users::COLUMN_NAME});
      $form->surname->setValues($user->{Model_Users::COLUMN_SURNAME});
      $form->group->setValues($user->{Model_Users::COLUMN_ID_GROUP});
      $form->email->setValues($user->{Model_Users::COLUMN_MAIL});
      $form->note->setValues($user->{Model_Users::COLUMN_NOTE});
      $form->blocked->setValues($user->{Model_Users::COLUMN_BLOCKED});

      if($form->isValid()){
         $m = new Model_Users();
         $m->saveUser($form->username->getValues(),$form->name->getValues(),
            $form->surname->getValues(), $form->password->getValues(),$form->group->getValues(),
            $form->email->getValues(),$form->note->getValues(),$form->blocked->getValues(),$this->getRequest('id'));

         $this->infoMsg()->addMessage($this->_(sprintf('Uživatel %s byl uložen', $form->username->getValues())));
         $this->link()->route()->reload();
      }

      $this->view()->template()->userName = $user->{Model_Users::COLUMN_USERNAME};
      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile('edituser.phtml');
	}
	
	/**
	 * Metoda pro přidávání uživatelů
	 */
	public function adduserController() {
      $this->checkControllRights();
      $form = $this->createUserForm();
      // heslo je nutné
      $form->password->addValidation(new Form_Validator_NotEmpty());


      if($form->isValid()){
         $m = new Model_Users();
         $m->saveUser($form->username->getValues(),$form->name->getValues(),
            $form->surname->getValues(), $form->password->getValues(),$form->group->getValues(),
            $form->email->getValues(),$form->note->getValues(),$form->blocked->getValues());

         $this->infoMsg()->addMessage($this->_(sprintf('Uživatel %s byl uložen', $form->username->getValues())));
         $this->link()->route()->reload();
      }

      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile('edituser.phtml');
	}

   /**
    * Metoda vytvoří formulář pro úpravu uživatele
    * @return Form
    */
   private function createUserForm($username = null) {
      $model = new Model_Users();
      $form = new Form('user_');

      $elemUsername = new Form_Element_Text('username', $this->_('Uživatelské jméno'));
      $elemUsername->addValidation(new Form_Validator_NotEmpty());

      $users = $model->getUsersList();
      $usedUsers = array();
      while ($u = $users->fetchObject()) {
         if($username != $u->{Model_Users::COLUMN_USERNAME}){
            array_push($usedUsers, $u->{Model_Users::COLUMN_USERNAME});
         }
      }
      $elemUsername->addValidation(new Form_Validator_NotInArray($usedUsers));
      $form->addElement($elemUsername);

      $elemName = new Form_Element_Text('name', $this->_('Jméno'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName);

      $elemSurName = new Form_Element_Text('surname', $this->_('Přijmení'));
      $elemSurName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemSurName);

      $elemPass1 = new Form_Element_Password('password', $this->_('Heslo'));
      $elemPass1->addValidation(new Form_Validator_Length(5));
      $form->addElement($elemPass1);

      $groups = $model->getGroups();

      $grps = array();
      while ($group = $groups->fetchObject()) {
         $grps[$group->{Model_Users::COLUMN_GROUP_LABEL}] = $group->{Model_Users::COLUMN_ID_GROUP};
      }

      $elemGrp = new Form_Element_Select('group', $this->_('Skupina'));
      $elemGrp->addValidation(new Form_Validator_NotEmpty());
      $elemGrp->setOptions($grps);
      $form->addElement($elemGrp);

      $elemEmail = new Form_Element_Text('email', $this->_('E-mail'));
      $elemEmail->addValidation(new Form_Validator_Email());
      $form->addElement($elemEmail);

      $elemNote = new Form_Element_TextArea('note', $this->_('Poznámky'));
      $form->addElement($elemNote);

      $elemBlock = new Form_Element_Checkbox('blocked', $this->_('Blokován'));
      $form->addElement($elemBlock);


      $submit = new Form_Element_Submit('send', $this->_('Odeslat'));
      $form->addElement($submit);

      return $form;
   }


   public function addGroupController() {
      $uModel = new Model_Users();
      $form = $this->createGroupForm();

      if($form->isValid()){
         $uModel->saveGroup($form->name->getValues(), $form->label->getValues(), $form->def_status->getValues());
         $this->infoMsg()->addMessage($this->_('Skupina byla uložena'));
         $this->link()->route()->reload();
      }

      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile('editgroup.phtml');
   }

   /**
    * Metoda pro vytvoření formuláře skupiny
    * @return Form
    */
   private function createGroupForm() {
      $form = new Form('group_');

      $elemName = new Form_Element_Text('name', $this->_('Název skupiny'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemName);

      $elemLabel = new Form_Element_TextArea('label', $this->_('Popis skupiny'));
      $form->addElement($elemLabel);

      // pole s typy práv
      $rightsTypes = array('r--'=>'r--', '-w-'=>'-w-', '--c'=>'--c', 'rw-'=>'rw-',
          'r-c'=>'r-c', '-wc'=>'-wc', 'rwc'=>'rwc', '---' => '---');
      $catGrpRigths = new Form_Element_Select('def_status', $this->_("Výchozí práva"));
      $catGrpRigths->setOptions($rightsTypes);
      $form->addElement($catGrpRigths);

      $elemSubmit = new Form_Element_Submit('send', $this->_('odeslat'));
      $form->addElement($elemSubmit);
      
      return $form;
   }
}

?>