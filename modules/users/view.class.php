<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Users_View extends View {
	public function mainView() {
      $this->template()->addTplFile('users.phtml');
   }

	public function groupsView() {
      $this->template()->addTplFile('groups.phtml');
   }
   
   public function usersListView(){
      echo json_encode($this->respond);
   }

   public function groupsListView(){
      echo json_encode($this->respond);
   }

	/**
	 * Viewer pro zobrazení detailu
	 */
//	public function showView() {
//	}
	
//	public function editUserView() {
//      echo json_encode($this->respond);
//	}
//
//	public function adduserView() {
//      $this->template()->addTplFile('edituser.phtml');
//	}
//
//   public function addGroupView() {
//      $this->template()->addTplFile('editgroup.phtml');
//   }
//
//   public function editGroupView() {
//      $this->template()->addTplFile('editgroup.phtml');
//   }
	
}

?>