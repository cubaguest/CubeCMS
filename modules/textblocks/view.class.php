<?php
class TextBlocks_View extends View {
   public function mainView() {
      $this->template()->addFile($this->getTemplate());

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();

         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('block_add', $this->tr('Přidat blok'), $this->link()->route('add'));
         $toolAdd->setIcon(Template_Toolbox2::ICON_ADD)->setTitle($this->tr("Přidat nový blok"));
         $toolbox->addTool($toolAdd);
         
         if($this->blocks && count($this->blocks) > 1){
            $toolOrder = new Template_Toolbox2_Tool_PostRedirect('block_edit_order', $this->tr('upravit pořadí'), $this->link()->route('editOrder'));
            $toolOrder->setIcon(Template_Toolbox2::ICON_MOVE_UP_DOWN)->setTitle($this->tr("upravit pořadí bloků"));
            $toolbox->addTool($toolOrder);
         }

         $this->toolbox = $toolbox;
      }
      if($this->category()->getRights()->isWritable()){
         $this->blocks = $this->createBlocksToolbox($this->blocks);
      }
      $this->imagePath = $this->category()->getModule()->getDataDir(true);
   }

   protected function createBlocksToolbox($blocks)
   {
      if(empty($blocks)){
         return $blocks;
      }
      $toolboxEdit = new Template_Toolbox2();
      $toolboxEdit->setIcon(Template_Toolbox2::ICON_PEN);

      $toolEdit = new Template_Toolbox2_Tool_PostRedirect('block_edit', $this->tr("Upravit blok"));
      $toolEdit->setIcon(Template_Toolbox2::ICON_PEN)->setTitle($this->tr('Upravit blok'));
      $toolboxEdit->addTool($toolEdit);

      $toolDelete = new Template_Toolbox2_Tool_Form($this->formDelete);
      $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE);
      $toolDelete->setConfirmMeassage($this->tr('Opravdu smazat blok?'));
      $toolDelete->setImportant(true);
      $toolboxEdit->addTool($toolDelete);
      
      foreach ($blocks as $block) {
         $toolboxEdit->block_edit->setAction($this->link()->route('edit', array('id' => $block->getPK())));
         $toolboxEdit->block_del_->getForm()->id->setValues($block->getPK());
         $block->toolbox = clone $toolboxEdit;
      }
      return $blocks;
   }
   
   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      Template_Module::setEdit(true);
      $this->template()->addFile("tpl://edit.phtml");
      $this->setTinyMCE($this->form->text, 'advanced');
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->addView();
      // cestak obrázků
      $this->imagePath = $this->category()->getModule()->getDataDir(true);
   }

   public function editOrderView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://edit_order.phtml');
   }


}
