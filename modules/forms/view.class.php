<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Forms_View extends View {
   public function mainView() 
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://list.phtml');
      
      $toolbox = new Template_Toolbox2();

      $toolChangeStatus = new Template_Toolbox2_Tool_Form($this->formChangeStatus);
      $toolChangeStatus->setIcon('enable.png');
      $toolbox->addTool($toolChangeStatus);
      
      $toolAddEv = new Template_Toolbox2_Tool_Redirect('previewForm', $this->tr('Náhled formuláře'));
      $toolAddEv->setIcon('application_form_preview.png')->setAction($this->link()->route('previewForm'));
      $toolbox->addTool($toolAddEv);
      
      $toolHome = new Template_Toolbox2_Tool_Redirect('editForm', $this->tr('Upravit formulář'));
      $toolHome->setIcon('application_form_edit.png')->setAction($this->link()->route("editForm"));
      $toolbox->addTool($toolHome);

      $toolRemove = new Template_Toolbox2_Tool_Form($this->formDelete);
      $toolRemove->setIcon('application_form_delete.png')->setConfirmMeassage($this->tr('Opravdu smazat formulář?'));
      $toolbox->addTool($toolRemove);

      $this->toolboxItem = $toolbox;
   }
   
   public function createFormView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://edit.phtml');
      
   }
   
   public function editFormView()
   {
      $this->createFormView();
      
   }
   
   public function previewFormView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://preview.phtml');

   }
}

?>