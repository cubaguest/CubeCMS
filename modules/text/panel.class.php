<?php
class Text_Panel extends Panel {
   const TEXT_PANEL_KEY = 'panel';
	
	public function panelController() {
	}
	
	public function panelView() {
      $textM = new Text_Model_Detail();
      $this->template()->text = $textM->getText($this->category()->getId(),self::TEXT_PANEL_KEY);
      if($this->template()->text == false) return false;
		$this->template()->addTplFile("panel.phtml");
	}
}
?>