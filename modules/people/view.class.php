<?php
class People_View extends View {
   public function mainView() {
      $this->template()->addFile("tpl://list.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('person_add', $this->_('Přidat osobu'), $this->link()->route('add'));
         $toolAdd->setIcon('user_add.png')->setTitle($this->_("Přidat novou osobu"));
         $toolbox->addTool($toolAdd);
         
         if($this->people != false){
            $toolOrder = new Template_Toolbox2_Tool_PostRedirect('person_edit_order', $this->_('upravit pořadí'), $this->link()->route('editOrder'));
            $toolOrder->setIcon('arrow_up_down.png')->setTitle($this->_("upravit pořadí osob"));
            $toolbox->addTool($toolOrder);
         }

         $this->toolbox = $toolbox;

         $toolboxEdit = new Template_Toolbox2();
         $toolboxEdit->setIcon('user_edit.png');

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('person_edit', $this->_("Upravit osobu"));
         $toolEdit->setIcon('user_edit.png')->setTitle($this->_('Upravit osobu'));
         $toolboxEdit->addTool($toolEdit);

         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
         $toolDelete->setIcon('user_delete.png');
         $toolDelete->setConfirmMeassage($this->_('Opravdu smazat osobu?'));
         $toolboxEdit->addTool($toolDelete);
         
         $this->toolboxEdit = $toolboxEdit;
      }
      
      $this->imagePath = $this->category()->getModule()->getDataDir(true);
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      Template_Module::setEdit(true);
      $this->template()->addFile("tpl://edit.phtml");
      $this->addTinyMCE();
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      Template_Module::setEdit(true);
      $this->edit = true;
      $this->addView();
      // cestak obrázků
      $this->imagePath = $this->category()->getModule()->getDataDir(true);
   }

   public function editOrderView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://edit_order.phtml');
   }


   private function addTinyMCE() {
      if($this->form->haveElement('text')){
         $this->form->text->html()->addClass("mceEditor");
      }
      $this->tinyMCE = new Component_TinyMCE();
      $settings = new Component_TinyMCE_Settings_Advanced();
      $settings->setSetting('height', '600');
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
   }
}

?>
