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
	const FOMR_PREFIX 				= 'passwd_';
	const FOMR_PASSWD_OLD 			= 'old';
	const FOMR_PASSWD_NEW 			= 'new';
	const FOMR_PASSWD_NEW_CONFIRM 	= 'new_confirm';
	const FOMR_BUTTON_CHANGE 		= 'change';
	
	/**
	 * minimální délka hesla
	 * @var integer
	 */
	const PASSWD_MIN_LENGTH = 5;
	
	
	public function mainController() {
		if($this->getRights()->isWritable()){
			$this->createModel("detail");
			
			$this->getModel()->linkToEditPasswd = $this->getLink()->action($this->getAction()->actionEditpasswd());
			
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
		if(!$this->getRights()->isWritable()){
			$this->getLink()->category()->action()->article()->reload();
		}
		
		
		if(isset($_POST[self::FOMR_PREFIX.self::FOMR_BUTTON_CHANGE])){
//			načtení strého hesla z db
			$sqlSelPasswd = $this->getDb()->select()->from(array("user"=>$this->getModule()->getDbTable()), self::COLUM_USER_PASSWORD)
													->where(self::COLUM_ID." = ".$this->getRights()->getAuth()->getUserId());
													
			$oldPasswd = $this->getDb()->fetchAssoc($sqlSelPasswd, true);
			
			if($_POST[self::FOMR_PREFIX.self::FOMR_PASSWD_OLD] == null OR $_POST[self::FOMR_PREFIX.self::FOMR_PASSWD_NEW] == null OR 
				$_POST[self::FOMR_PREFIX.self::FOMR_PASSWD_NEW_CONFIRM] == null){
				$this->errMsg()->addMessage(_("Nebyly zadány všechny potřebné parametry"));	
			} else if(md5($_POST[self::FOMR_PREFIX.self::FOMR_PASSWD_OLD]) != $oldPasswd[self::COLUM_USER_PASSWORD]){
				$this->errMsg()->addMessage(_("Staré heslo bylo nesprávně zadáno"));
			} else if($_POST[self::FOMR_PREFIX.self::FOMR_PASSWD_NEW] != $_POST[self::FOMR_PREFIX.self::FOMR_PASSWD_NEW_CONFIRM]){
				$this->errMsg()->addMessage(_("Nově zadaná hesla nesouhlasí"));
			} else if(strlen($_POST[self::FOMR_PREFIX.self::FOMR_PASSWD_NEW]) < self::PASSWD_MIN_LENGTH) {
				$this->errMsg()->addMessage(_("Nové heslo je příliš krátké"));
			} else {
				$sqlUpdate = $this->getDb()->update()->table($this->getModule()->getDbTable())
													 ->set(array(self::COLUM_USER_PASSWORD=>md5(htmlspecialchars($_POST[self::FOMR_PREFIX.self::FOMR_PASSWD_NEW], ENT_QUOTES))));
				$this->getDb()->query($sqlUpdate);

				$this->infoMsg()->addMessage(_("Heslo bylo úspěšně změněno"));
				$this->getLink()->action()->article()->reload();
			}
			
			
		}
		
		$this->createModel("detail");
		$this->getModel()->linkToBack = $this->getLink()->action()->article();
		
	}
	
	

}

?>