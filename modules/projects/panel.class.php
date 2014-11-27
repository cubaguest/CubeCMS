<?php
class Projects_Panel extends Panel {

   public function panelController() {
      $m = new Projects_Model_Projects();
      
      $this->template()->projects = $m
          ->joinFK(Projects_Model_Projects::COLUMN_ID_SECTION)
          ->where(Projects_Model_Sections::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()))
          ->limit(0,20)
          ->order(Projects_Model_Projects::COLUMN_ORDER)
          ->records();
   }

   public function panelView() {
      if(!empty($this->template()->projects)){
         $this->template()->addFile($this->getTemplate());
      }
      $this->template()->dataDir = $this->category()->getModule()->getDataDir(false);
      $this->template()->imagesPath = $this->category()->getModule()->getDataDir(true);
   }
}