<?php
class Teams_View extends View {
   
   public function init()
   {
      parent::init();
      $this->imagePath = $this->category()->getModule()->getDataDir(true);
   }

   public function mainView() {
      $this->template()->addFile("tpl://list.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('person_add', $this->tr('Přidat osobu'), $this->link()->route('add'));
         $toolAdd->setIcon('user_add.png')->setTitle($this->tr("Přidat novou osobu"));
         $toolbox->addTool($toolAdd);
         
         if($this->teams != false){
            $toolOrder = new Template_Toolbox2_Tool_PostRedirect('person_edit_order', $this->tr('upravit pořadí'), $this->link()->route('editOrder'));
            $toolOrder->setIcon('arrow_up_down.png')->setTitle($this->tr("upravit pořadí osob"));
            $toolbox->addTool($toolOrder);
         }

         $this->toolbox = $toolbox;

         $toolboxEdit = new Template_Toolbox2();
         $toolboxEdit->setIcon('user_edit.png');

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('person_edit', $this->tr("Upravit osobu"));
         $toolEdit->setIcon('user_edit.png')->setTitle($this->tr('Upravit osobu'));
         $toolboxEdit->addTool($toolEdit);

         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
         $toolDelete->setIcon('user_delete.png');
         $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat osobu?'));
         $toolboxEdit->addTool($toolDelete);
         
         $this->toolboxEdit = $toolboxEdit;
      }
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      $this->template()->addFile("tpl://edit.phtml");
      $this->setTinyMCE($this->form->text, 'advanced');
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->edit = true;
      $this->addView();
   }

   public function editOrderView()
   {
      $this->template()->addFile('tpl://edit_order.phtml');
   }

}

?>
