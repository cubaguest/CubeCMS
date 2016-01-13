<?php
class People_View extends View {
   public function mainView() {
      $this->template()->addFile("tpl://list.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolEditText = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr('Upravit text'), $this->link()->route('editText'));
         $toolEditText->setIcon(Template_Toolbox2::ICON_PAGE_EDIT)->setTitle($this->tr("Upravit úvodní text"));
         $toolbox->addTool($toolEditText);
         
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('person_add', $this->tr('Přidat osobu'), $this->link()->route('add'));
         $toolAdd->setIcon(Template_Toolbox2::ICON_ADD)->setTitle($this->tr("Přidat novou osobu"));
         $toolbox->addTool($toolAdd);
         
         if($this->people != false){
            $toolOrder = new Template_Toolbox2_Tool_PostRedirect('person_edit_order', $this->tr('upravit pořadí'), $this->link()->route('editOrder'));
            $toolOrder->setIcon(Template_Toolbox2::ICON_MOVE_UP_DOWN)->setTitle($this->tr("upravit pořadí osob"));
            $toolbox->addTool($toolOrder);
         }

         $this->toolbox = $toolbox;

         $toolboxEdit = new Template_Toolbox2();
         $toolboxEdit->setIcon(Template_Toolbox2::ICON_PEN);

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('person_edit', $this->tr("Upravit osobu"));
         $toolEdit->setIcon(Template_Toolbox2::ICON_PEN)->setTitle($this->tr('Upravit osobu'));
         $toolboxEdit->addTool($toolEdit);

         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
         $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE);
         $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat osobu?'));
         $toolboxEdit->addTool($toolDelete);
         
         $this->toolboxEdit = $toolboxEdit;
      }
      
      $this->imagePath = $this->category()->getModule()->getDataDir(true);
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      Template_Module::setFullWidth(true);
      $this->template()->addFile("tpl://edit.phtml");
      $this->setTinyMCE($this->form->text, 'advanced');
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      Template_Module::setFullWidth(true);
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

   public function editTextView() {
      $this->setTinyMCE($this->form->text, 'advanced');
      $this->template()->addFile('tpl://edittext.phtml');
      Template::setFullWidth(true);
   }
}
