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
      $toolChangeStatus->setIcon(Template_Toolbox2::ICON_ENABLE);
      $toolbox->addTool($toolChangeStatus);
      
      $toolAddEv = new Template_Toolbox2_Tool_Redirect('previewForm', $this->tr('Náhled formuláře'));
      $toolAddEv->setIcon(Template_Toolbox2::ICON_PREVIEW)->setAction($this->link()->route('previewForm'));
      $toolbox->addTool($toolAddEv);
      
      $toolHome = new Template_Toolbox2_Tool_Redirect('editForm', $this->tr('Upravit formulář'));
      $toolHome->setIcon(Template_Toolbox2::ICON_PAGE_EDIT)->setAction($this->link()->route("editForm"));
      $toolbox->addTool($toolHome);

      $toolRemove = new Template_Toolbox2_Tool_Form($this->formDelete);
      $toolRemove
          ->setIcon(Template_Toolbox2::ICON_DELETE)
          ->setConfirmMeassage($this->tr('Opravdu smazat formulář?'))
          ->setImportant(true);
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