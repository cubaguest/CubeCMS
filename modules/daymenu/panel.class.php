<?php
class DayMenu_Panel extends Panel {
   private $text = null;

   public function panelController() {
      $model = new Text_Model();
      $this->text = $model
         ->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :dk', 
                 array('idc' => $this->category()->getId(), 'dk' => 'p_'.date('N')))
         ->record();
	}
	
	public function panelView() {
      if($this->text == false || $this->text->{Text_Model::COLUMN_TEXT} == null) return false;
      $this->template()->text = (string)$this->text->{Text_Model::COLUMN_TEXT};
      $this->template()->addFile('tpl://panel.phtml');
	}
}
?>