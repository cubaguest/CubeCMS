<?php
class Login_Panel extends Panel {
	
	public function panelController() {
	}
	
	public function panelView() {
      $this->template()->currentLink = (string)new Url_Link();
      $this->template()->addTplFile("panel.phtml");
	}
}
?>