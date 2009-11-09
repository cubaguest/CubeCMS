<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Login_View extends View {
	public function mainView() {
      if($this->rights()->getAuth()->isLogin()){
         $this->template()->addTplFile("buttoneditpasswd.phtml");
         $this->template()->addTplFile("logout.phtml");
      } else {
         $this->template()->addTplFile("login.phtml");
      }
		$this->template()->addCssFile("style.css");
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
	public function changepasswdView() {
		$this->template()->addTplFile("changepsswd.phtml");
	}
	
}

?>