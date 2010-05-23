<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Users_View extends View {
	public function mainView() {
      $this->template()->addTplFile('listUsers.phtml');
   }
	/**
	 * Viewer pro zobrazení detailu
	 */
	public function showView() {
	}
	
	public function edituserView() {
      $this->template()->addTplFile('edituser.phtml');
	}
	
	public function adduserView() {
      $this->template()->addTplFile('edituser.phtml');
	}

   public function addGroupView() {
      $this->template()->addTplFile('editgroup.phtml');
   }
   
   public function editGroupView() {
      $this->template()->addTplFile('editgroup.phtml');
   }
	
}

?>