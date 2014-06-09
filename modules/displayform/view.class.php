<?php
class DisplayForm_View extends View {
   public function mainView() {
      $this->template()->addFile($this->getTemplate());
      
      if($this->rights()->isWritable()){
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_PEN);
         $toolEditText = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr("Upravit úvodní text"),
            $this->link()->route('edittext'));
         $toolEditText->setIcon('page_edit.png')->setTitle($this->tr('Upravit úvodní text formuláře'));
         $this->toolbox->addTool($toolEditText);
      }
   }
   
   public function edittextView() {
      Template_Module::setEdit(true);
      $this->setTinyMCE($this->form->text, $this->category()->getParam(Photogalery_Controller::PARAM_EDITOR_TYPE, 'advanced'));
      $this->template()->addFile("tpl://edittext.phtml");
      Template_Navigation::addItem($this->tr('Úprava úvodního textu'), $this->link());
   }
}