<?php
class MapLocations_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://main.phtml');
      $this->createMapToolbox();
   }
   
   public function listView() {
      $this->template()->addFile('tpl://list.phtml');
      $this->createListToolbox();
   }

   /**
    * Vytvoření toolboxů v detailu
    */
   protected function createMapToolbox() {
      if($this->category()->getRights()->isWritable()) {
         $this->toolbox = new Template_Toolbox2();

         $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('list_palces', 
                 $this->tr("Upravit místa"), $this->link()->route('list'));
         $toolEdit->setIcon('house_edit.png')->setTitle($this->tr('Upravit místa na mapě'));
         $this->toolbox->addTool($toolEdit);
      }
   }

   protected function createListToolbox() {
      if($this->rights()->isWritable()) {
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_location', $this->tr("Přidat místo"),
         $this->link()->route('add'));
         $toolAdd->setIcon('house_add.png')->setTitle($this->tr('Přidat nové místo'));
         $this->toolbox->addTool($toolAdd);
         
         $toolClose = new Template_Toolbox2_Tool_PostRedirect('close_location_list', $this->tr("Zavřít seznam"), $this->link()->route());
         $toolClose->setIcon('cancel.png')->setTitle($this->tr('zavřít seznam míst'));
         $this->toolbox->addTool($toolClose);
         
         $this->toolboxItem = new Template_Toolbox2();
         $this->toolboxItem->setIcon(Template_Toolbox2::ICON_ADD);
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_location', $this->tr("Upravit místo"),
         $this->link()->route('edit'));
         $toolEdit->setIcon('house_edit.png')->setTitle($this->tr('Upravit vybrané místo'));
         $this->toolboxItem->addTool($toolEdit);
         
         $toolDelete = new Template_Toolbox2_Tool_Form($this->formDel);
         $toolDelete->setIcon('house_delete.png')->setTitle($this->tr('Smazat vybrané místo'))->setConfirmMeassage($this->tr('Opravdu smazat tuto položku?'));
         $this->toolboxItem->addTool($toolDelete);
      }
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      Template_Module::setEdit(true);
      $this->addTinyMCE();
      $this->template()->addFile('tpl://edit.phtml');
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->addView();
   }

   public function editTextView() {
      $this->addTinyMCE('simple');
      $this->template()->addFile('tpl://edittext.phtml');
   }
   
   private function addTinyMCE($theme = 'advanced') {
      if($this->form->haveElement('text')){
         $this->form->text->html()->addClass("mceEditor");
      }
      $this->tinyMCE = new Component_TinyMCE();
      switch ($this->category()->getParam(Articles_Controller::PARAM_EDITOR_TYPE, $theme)) {
         case 'simple':
            $settings = new Component_TinyMCE_Settings_AdvSimple();
            $settings->setSetting('editor_selector', 'mceEditor');
            break;
         case 'full':
            // TinyMCE
            $settings = new Component_TinyMCE_Settings_Full();
            break;
         case 'advanced':
         default:
            $settings = new Component_TinyMCE_Settings_Advanced();
            break;
      }
      $settings->setSetting('height', '600');
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
   }

}

?>
