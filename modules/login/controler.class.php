<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class LoginController extends Controller {
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUM_ID 				= 'id_user';
	const COLUM_USER_NAME		= 'name';
	const COLUM_USER_SURNAME	= 'surname';
	const COLUM_USER_USERNAME	= 'username';
	const COLUM_USER_PASSWORD	= 'password';

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
			$this->container()->addLink('edit_passwd', $this->getLink()->action($this->getAction()->actionEditpasswd()));
		}
	}
	
	/**
	 * Metoda pro zobrazení detailu zástupce
	 */
	public function showController() {
		
	}
	
	/**
	 * Metoda pro úpravu
	 */
	public function editController() {
		
	}
	
	/**
	 * Metoda pro úpravu hesla
	 */
	public function editpasswdController() {
		$this->checkWritebleRights();
		
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_CHANGE])){
//			načtení strého hesla z db
			$userObj = new UserDetailModel();													
			$oldPasswd = $userObj->getPasswd($this->getRights()->getAuth()->getUserId());
			
			$oPasswd = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_PASSWD_OLD]);
			$nPasswd = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_PASSWD_NEW]);
			$nPasswdConfirm = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_PASSWD_NEW_CONFIRM]);
			
			if($oPasswd == null OR $nPasswd == null OR $nPasswdConfirm == null){
				$this->errMsg()->addMessage(_("Nebyly zadány všechny potřebné parametry"));	
			} else if($this->cryptPasswd($oPasswd) != $oldPasswd){
				$this->errMsg()->addMessage(_("Staré heslo nebylo zadáno správně"));
			} else if($nPasswd != $nPasswdConfirm){
				$this->errMsg()->addMessage(_("Nově zadaná hesla se neshodují"));
			} else if(strlen($nPasswd) < self::PASSWD_MIN_LENGTH) {
				$this->errMsg()->addMessage(_("Nové heslo je příliš krátké"));
			} else {
				
				if(!$userObj->setPasswd($this->getRights()->getAuth()->getUserId(),$this->cryptPasswd($nPasswd))){
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