<?php
class DayMenu_Panel extends Panel {
   private $text = null;

   public function panelController() {
      $date = new DateTime();
      if(date("H") > 14){
         $date->modify("+1 day");
      }
      
      $model = new DayMenu_Model();
      $model->where(DayMenu_Model::COLUMN_DATE.' = :d AND '.DayMenu_Model::COLUMN_CONCEPT.' = 0', array('d' => $date->format("Y-m-d")));
      
      $this->text = $model->record();
	}
	
	public function panelView() {
      if($this->text == false || ($this->text->{DayMenu_Model::COLUMN_TEXT_PANEL} == null && $this->text->{DayMenu_Model::COLUMN_TEXT} == null ) ) return false;
      
      $this->template()->text = (string)$this->text->{DayMenu_Model::COLUMN_TEXT_PANEL} != null 
         ? $this->text->{DayMenu_Model::COLUMN_TEXT_PANEL} : $this->text->{DayMenu_Model::COLUMN_TEXT};
      $this->template()->addFile('tpl://panel.phtml');
	}
}
?>