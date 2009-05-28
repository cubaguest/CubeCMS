<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class LoginController extends Controller {
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
		if($this->getRights()->isWritable()){
			$this->container()->addLink('edit_passwd', $this->getLink()->action($this->getAction()->changePasswd()));
		}
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
         $userObj = new UserDetailModel();
         if($userObj->getPasswd($this->getRights()->getAuth()->getUserId()) !=
            $this->cryptPasswd($form->getValue(self::FORM_PASSWD_OLD))){
            $this->errMsg()->addMessage(_('Staré heslo nebylo vyplněno správně'));
         } else if($form->getValue(self::FORM_PASSWD_NEW) != $form->getValue(self::FORM_PASSWD_NEW_CONFIRM)){
            $this->errMsg()->addMessage(_('Nová hesla se neshodují'));
         } else {
            if(!$userObj->setPasswd($this->getRights()->getAuth()->getUserId(),$this->cryptPasswd($form->getValue(self::FORM_PASSWD_NEW)))){
					new CoreException(_('Heslo se nepodařilo uložit'), 1);
				} else {
					$this->infoMsg()->addMessage(_("Heslo bylo úspěšně změněno"));
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