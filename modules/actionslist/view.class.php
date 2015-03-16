<?php
class ActionsList_View extends Actions_View {

   public function mainView() {
      $this->template()->addFile("tpl://list.phtml");

      $this->createListToolbox();
   }

   protected function createListToolbox() 
   {
      if($this->rights()->isWritable()) {
         if( ($this->toolbox instanceof Template_Toolbox2 ) == false ){
            $this->toolbox = new Template_Toolbox2();
            $this->toolbox->setIcon(Template_Toolbox2::ICON_PEN);
         }
         
         $toolArchive = new Template_Toolbox2_Tool_PostRedirect('archive', $this->tr("Zobrazit archiv"),
            $this->link()->route('archive'));
         $toolArchive->setIcon('archive')->setTitle($this->tr('Zobrazit archiv událostí'));
         $this->toolbox->addTool($toolArchive);
         
         if($this->rights()->isControll() && !$this->category()->getParam(Actions_Controller::PARAM_SHOW_EVENT_DIRECTLY, false) ) {
            $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolAdd = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr("Upravit úvodní text"),
            $this->link()->route('editlabel'));
            $toolAdd->setIcon(Template_Toolbox2::ICON_PEN)->setTitle($this->tr('Upravit úvodní text'));
            $this->toolbox->addTool($toolAdd);
         }
      }
   }
  
}