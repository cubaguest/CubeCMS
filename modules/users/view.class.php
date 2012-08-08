<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Users_View extends View {
	public function usersView() {
      $this->template()->addTplFile('users.phtml');
      Template_Module::setEdit(true);
      Template_Navigation::addItem($this->tr('Uživatelé'), $this->link(), null, null, null, true);
   }
   
	public function mainView() {
      $this->template()->addTplFile('create_user.phtml');
      Template_Module::setEdit(true);
   }

	public function groupsView() {
      $this->template()->addTplFile('groups.phtml');
      Template_Module::setEdit(true);
      Template_Navigation::addItem($this->tr('Skupiny'), $this->link(), null, null, null, true);
   }
   
   public function usersListView(){
      echo json_encode($this->respond);
   }

   public function groupsListView(){
      echo json_encode($this->respond);
   }
}

?>