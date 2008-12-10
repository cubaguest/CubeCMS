<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class LoginAction extends Action {
	

	public function actions() {
		$this->addAction("editpasswd", "epw");
		
	}
	
}
?>