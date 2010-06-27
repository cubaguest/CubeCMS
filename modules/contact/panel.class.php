<?php
class Contact_Panel extends Panel {
   const TEXT_PANEL_KEY = 'panel';
	
	public function panelController() {
	}
	
	public function panelView() {
      $textM = new Text_Model();
      $text = $textM->getText($this->category()->getId(),self::TEXT_PANEL_KEY);
      if($text != false){
         $this->template()->text = $text->{Text_Model::COLUMN_TEXT};
      } else {
         return false;
      }
		$this->template()->addTplFile("panel.phtml");
	}
}
?>