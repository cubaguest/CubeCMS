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
	
	public function editView() {
	}
	
	public function addView() {
	}
	
}

?>