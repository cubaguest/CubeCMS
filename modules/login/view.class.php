<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Login_View extends View {
	public function mainView() {
      if(Auth::isLoginStatic()){
         $this->template()->addTplFile("user.phtml");
      } else {
         $this->template()->addTplFile("login.phtml", 'login');
      }
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
		$this->template()->addTplFile("changepsswd.phtml", 'login');
	}

   public function changeUserView() {
      $this->template()->addTplFile("edituser.phtml");
   }

   public function newPasswordView() {
      $this->template()->addTplFile('newpass.phtml', 'login');
   }
}

?>