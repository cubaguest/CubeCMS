<?php
class Partners_View extends View {
   public function mainView() {
      $this->template()->addFile("tpl://list.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('partner_add', $this->tr('Přidat partnera'), $this->link()->route('add'));
         $toolAdd->setIcon('user_add.png')->setTitle($this->tr("Přidat nového partnera"));
         $toolbox->addTool($toolAdd);
         
         if($this->partners != false && count($this->partners) > 1){
            $toolOrder = new Template_Toolbox2_Tool_PostRedirect('partners_edit_order', $this->tr('Upravit pořadí'), $this->link()->route('editOrder'));
            $toolOrder->setIcon('arrow_up_down.png')->setTitle($this->tr("Upravit pořadí partnerů"));
            $toolbox->addTool($toolOrder);
         }

         $this->toolbox = $toolbox;

         $toolboxEdit = new Template_Toolbox2();
         $toolboxEdit->setIcon('user_edit.png');

         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('partner_edit', $this->tr("Upravit partnera"));
         $toolEdit->setIcon('user_edit.png')->setTitle($this->tr('Upravit partnera'));
         $toolboxEdit->addTool($toolEdit);
         
         $toolChangeVis = new Template_Toolbox2_Tool_Form($this->formVisibility);
         $toolChangeVis->setIcon('user_eye.png');
         $toolboxEdit->addTool($toolChangeVis);

         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
         $toolDelete->setIcon('user_delete.png');
         $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat partnera?'));
         $toolboxEdit->addTool($toolDelete);
         
         $this->toolboxEdit = $toolboxEdit;
      }
      
      $this->imagePath = $this->category()->getModule()->getDataDir(true);
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      $this->template()->addFile("tpl://edit.phtml");
//      $this->setTinyMCE($formElement);
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->edit = true;
      $this->addView();
      // cestak obrázků
      $this->imagePath = $this->category()->getModule()->getDataDir(true);
   }

   public function editOrderView()
   {
      $this->template()->addFile('tpl://edit_order.phtml');
   }
}

?>
