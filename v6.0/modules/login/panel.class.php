<?php
class Login_Panel extends Panel {
	
	public function panelController() {
	}
	
	public function panelView() {
		$this->template()->addTplFile("panel.phtml");
	}
}
?>