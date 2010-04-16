<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Login_Controller extends Controller {
	/**
	 * KOnstanty s názvy prvků formulářů
	 * @var atring
	 */
	const FORM_PREFIX 				= 'passwd_';
	const FORM_PASSWD_OLD 			= 'old';
	const FORM_PASSWD_NEW 			= 'new';
	const FORM_PASSWD_NEW_CONFIRM 	= 'new_confirm';
	const FORM_BUTTON_CHANGE 		= 'change';
	
	/**
	 * minimální délka hesla
	 * @var integer
	 */
	const PASSWD_MIN_LENGTH = 5;
	
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

      $elemSubmit = new Form_Element_Submit('change', $this->_('Změnit'));
      $form->addElement($elemSubmit);

      //        Pokud byl odeslán formulář
      if($form->isSend()){
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
      $this->view()->linkBack = $this->link()->route();
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

      $elemEmails = new Form_Element_TextArea('emails', $this->_('Emaily'));
      $elemEmails->addValidation(new Form_Validator_NotEmpty());
      $elemEmails->setSubLabel($this->_('E-mailové adresy oddělené středníkem'));
      $elemEmails->setValues($user->{Model_Users::COLUMN_MAIL});
      $form->addElement($elemEmails);

      $elemNote = new Form_Element_TextArea('note', $this->_('Poznámky'));
      $elemNote->setValues($user->{Model_Users::COLUMN_NOTE});
      $form->addElement($elemNote);

      $elemSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemSubmit);

      if($form->isSend()){
         // kontrola emailových adres
         $mails = preg_replace('/[^a-z0-9;.@_-]*/i', '', $form->emails->getValues()); // bílé znaky
         $mails = explode(';', $mails);

         $validMails = true;
         $validator = new Validator_EMail();
         foreach ($mails as $mail) {
            $validator->setValues($mail);
            if(!$validator->isValid()){
               $validMails = false;
               break;
            }
         }
         if($validMails == false){
            $form->emails->setError($this->_('E-mailové adresy byly špatně zadány'));
         }
      }

      if($form->isValid()){
         $model->saveUser(Auth::getUserName(), $form->name->getValues(),
                 $form->surname->getValues(), $user->{Model_Users::COLUMN_PASSWORD},
                 $user->{Model_Users::COLUMN_ID_GROUP}, $form->emails->getValues(),
                 $form->note->getValues(), $user->{Model_Users::COLUMN_BLOCKED}, Auth::getUserId());
         $this->infoMsg()->addMessage($this->_('Změny byly uloženy'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
   }
	
	/**
	 * Metoda zašifruje heslo
	 */
	private function cryptPasswd($passwd) {
		return md5($passwd);
	}
}

?>