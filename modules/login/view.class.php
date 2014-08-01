<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Login_View extends View {
	public function mainView() {
      if(Auth::isLoginStatic()){
         $this->template()->addFile("tpl://login:user.phtml");
      } else {
         $this->template()->addFile("tpl://login:login.phtml");
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
		$this->template()->addFile("tpl://login:changepsswd.phtml");
	}

   public function changeUserView() {
      $this->template()->addFile("tpl://login:edituser.phtml");
   }

   public function newPasswordView() {
      $this->template()->addFile("tpl://login:newpass.phtml");
   }
}