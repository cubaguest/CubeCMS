<?php
class MessagesBoard_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://list.phtml');
      $this->createListToolbox();
      $this->createMsgToolbox();
      if($this->form instanceof Form){
         $this->setTinyMCE($this->form->text, $this->category()->getParam(Articles_Controller::PARAM_EDITOR_TYPE, 'simple'));
      }
   }

   /**
    * Vytvoření toolboxů v detailu
    */
   protected function createMsgToolbox() {
      if(!Auth::isLogin() OR 
         ($this->category()->getRights()->isControll() == false AND $this->category()->getRights()->isWritable() == false) ){
         return;
      }
      
      $toolbox = new Template_Toolbox2();
      $toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);

      $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_msg', $this->tr("Upravit položku"));
      $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit položku'));
      $toolbox->addTool($toolEdit);


      $tooldel = new Template_Toolbox2_Tool_Form($this->formDelete);
      $tooldel->setIcon('page_delete.png')->setTitle($this->tr('Smazat položku'))
         ->setConfirmMeassage($this->tr('Opravdu smazat položku?'));
      $toolbox->addTool($tooldel);
      
      foreach ($this->messages as $key => $msg) {
         if($this->category()->getRights()->isControll() OR $msg->{MessagesBoard_Model::COLUMN_ID_USER} == Auth::getUserId()) {
            
            $this->formDelete->id->setValues($msg->{MessagesBoard_Model::COLUMN_ID});
            
            $toolEdit->setAction($this->link()->route('edit', array('id' => $msg->{MessagesBoard_Model::COLUMN_ID})));
            $tooldel->getForm()->id->setValues($msg->{MessagesBoard_Model::COLUMN_ID});
            $this->formDelete->id->setValues($msg->{MessagesBoard_Model::COLUMN_ID});
            
            $this->template()->messages[$key]->toolbox = clone $toolbox;
         }
      }
   }

   protected function createListToolbox() {
      if($this->rights()->isControll()) {
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
      }
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      Template_Module::setEdit(true);
      
      $this->setTinyMCE($this->form->text);
      $this->template()->addFile('tpl://edit.phtml');
   }

   public function editTextView() {
      $this->addTinyMCE('simple');
      $this->template()->addFile('tpl://articles:edittext.phtml');
   }
}

?>
