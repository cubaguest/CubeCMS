<?php
class Text_Panel extends Panel {
	
	public function panelController() {
	}
	
	public function panelView() {
      $textM = new Text_Model_Detail();
      $this->template()->shortText = $textM->getText($this->category()->getId());
      if($this->template()->shortText == false) return false;
		$this->template()->addTplFile("panel.phtml");
	}
}
?>