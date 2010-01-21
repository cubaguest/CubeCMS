<?php
class NavigationMenu_Panel extends Panel {
	
	public function panelController() {
	}
	
	public function panelView() {
     	$this->template()->addTplFile("list_only.phtml");
		$this->template()->addCssFile("style.css");
	}
}
?>