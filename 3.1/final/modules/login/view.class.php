<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class LoginView extends View {
	public function mainView() {
		
		
		if($this->getRights()->isWritable()){
			$this->template()->addTpl("buttoneditpasswd.tpl");
			$this->template()->addVar("LINK_TO_CHANGE_PASSWD", $this->container()->getLink('edit_passwd'));
			$this->template()->addTpl("logout.tpl");
			$this->assignLogoutLabels();
		} else {
			$this->template()->addTpl("login.tpl");
			$this->assignLoginLabels();
		}


		$this->template()->addCss("style.css");
	}
	
	private function assignLogoutLabels() {
		$this->template()->addVar("LOGOUT_BUTTON_NAME", _("Odhlásit"));
		$this->template()->addVar("LOGOUT_BUTTON_CHANGE_PASSWD", _("Změnit heslo"));

	}
	
	private function assignLoginLabels() {
		$this->template()->addVar("LOGIN_USER_NAME", _("Uživatelské jméno"));
		$this->template()->addVar("LOGIN_USER_PASSWORD", _("Heslo"));
		$this->template()->addVar("LOGIN_BUTTON", _("Přihlásit"));
		$this->template()->addVar("LOGIN_FORM_LABEL", _("Přihlášení"));
	}
	
	
	/**
	 * Viewer pro zobrazení detailu
	 */
	public function showView() {
		
	}
	
	public function editView() {
		
	}
	
	/**
	 * Viewwer pro zobrazení změny hesla
	 */
	public function editpasswdView() {
		$this->template()->addTpl("changepsswd.tpl");
		$this->template()->addJS("generatepswd.js");
		
		$this->template()->setTplSubLabel(_("Změna hesla"));
		$this->template()->addVar("OLD_PSSWD", _("Staré heslo"));
		$this->template()->addVar("NEW_PSSWD", _("Nové heslo"));
		$this->template()->addVar("NEW_PSSWD_CONFIRM", _("Nové heslo (potvrzení)"));
		$this->template()->addVar("GENERATE_PSSWD_BUTTON", _("Generovat"));
		$this->template()->addVar("SEND_PASSWD_BUTTON", _("Změnit"));
	}
	
}

?>