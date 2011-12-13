<?php
class ProjectsSimple_View extends Projects_View {
   public function mainView() 
   {
      $this->template()->addFile('tpl://list.phtml');
      $this->toolbox = $this->createListToolbox();
   }

   public function projectView() 
   {
      $this->createProjectToolbox();
      $pView = new Photogalery_View($this->pCtrl);
      $pView->addImagesToolbox();
      $this->template()->addFile('tpl://project.phtml');
   }
   
   public function addProjectView()
   {
      $this->template()->addFile('tpl://projects:edit_project.phtml');
      $this->setTinyMCE($this->form->text, 
         $this->category()->getParam(Photogalery_Controller::PARAM_EDITOR_TYPE, 'advanced'));
   }
   
   protected function createProjectToolbox()
   {
      if ($this->rights()->isControll() OR 
         Auth::getUserId() == $this->project->{Projects_Model_Projects::COLUMN_ID_USER}) {
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         // edit
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_project', $this->tr("Upravit projekt"),
               $this->link()->route('editProject'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit text projektu'));
         $this->toolbox->addTool($toolEdit);
         // delete
         if(isset ($this->formDelete)){
            $toolDel = new Template_Toolbox2_Tool_Form($this->formDelete);
            $toolDel->setIcon('page_delete.png')->setTitle($this->tr('Smazat projekt'))
               ->setConfirmMeassage($this->tr('Opravdu smazat tento projekt?'));
            $this->toolbox->addTool($toolDel);
         }
      }
   }

   protected function createListToolbox()
   {
      if(!$this->category()->getRights()->isWritable()){
         return null;
      }
      $toolbox = new Template_Toolbox2();
      $toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
      // add project
      $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_project', $this->tr("Přidat projekt"),
            $this->link()->route('addProject'));
      $toolAdd->setIcon('page_add.png')->setTitle($this->tr('Přidat nový projekt'));
      $toolbox->addTool($toolAdd);
      
      $toolEditText = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr("Upravit úvodní text"),
            $this->link()->route('editText'));
      $toolEditText->setIcon('page_edit.png')->setTitle($this->tr('Upravit úvodní text seznamu'));
      $toolbox->addTool($toolEditText);

      return $toolbox;
   }
}

?>
