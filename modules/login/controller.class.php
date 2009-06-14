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
	}
	
	/**
	 * Metoda pro úpravu hesla
	 */
	public function changepasswdController() {
		$this->checkWritebleRights();

      $form = new Form(self::FORM_PREFIX);

      $form->crInputPassword(self::FORM_PASSWD_OLD, true, Form::VALIDATE_NONE, Form::CODE_HTMLENCODE, 50, self::PASSWD_MIN_LENGTH)
      ->crInputPassword(self::FORM_PASSWD_NEW, true, Form::VALIDATE_NONE, Form::CODE_HTMLENCODE, 50, self::PASSWD_MIN_LENGTH)
      ->crInputPassword(self::FORM_PASSWD_NEW_CONFIRM, true, Form::VALIDATE_NONE, Form::CODE_HTMLENCODE, 50, self::PASSWD_MIN_LENGTH)
      ->crSubmit(self::FORM_BUTTON_CHANGE);

      //        Pokud byl odeslán formulář
      if($form->checkForm()){
         $userObj = new Login_Model_UserDetail($this->sys());
         if($userObj->getPasswd($this->getRights()->getAuth()->getUserId()) !=
            $this->cryptPasswd($form->getValue(self::FORM_PASSWD_OLD))){
            $this->errMsg()->addMessage($this->_m('Staré heslo nebylo vyplněno správně'));
         } else if($form->getValue(self::FORM_PASSWD_NEW) != $form->getValue(self::FORM_PASSWD_NEW_CONFIRM)){
            $this->errMsg()->addMessage($this->_m('Nová hesla se neshodují'));
         } else {
            if(!$userObj->setPasswd($this->getRights()->getAuth()->getUserId(),$this->cryptPasswd($form->getValue(self::FORM_PASSWD_NEW)))){
					new ModuleException($this->_m('Heslo se nepodařilo uložit'), 1);
				} else {
					$this->infoMsg()->addMessage($this->_m("Heslo bylo úspěšně změněno"));
					$this->getLink()->action()->article()->reload();
				}
         }
      }
	}
	
	/**
	 * Metoda zašifruje heslo
	 */
	private function cryptPasswd($passwd) {
		return md5($passwd);
	}
}

?>