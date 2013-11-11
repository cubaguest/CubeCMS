<?php
class Text_Panel extends Panel {
   const TEXT_PANEL_KEY = 'panel';
   const PARAM_TPL_PANEL = 'tpl';


   public function panelController() {
      $textM = new Text_Model();
      $rec = $textM->setSelectAllLangs(false)
         ->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :sk',
         array('idc' => $this->category()->getId(), 'sk' => self::TEXT_PANEL_KEY))
         ->record();
      if($rec != false AND !$rec->isNew()){
         $this->template()->text = $rec;
      }
	}
	
	public function panelView() {
      if($this->template()->text != null){
         $this->template()->addFile($this->getTemplate());
      }
	}
}