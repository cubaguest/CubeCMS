<?php
class MapLocations_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://main.phtml');
      $this->createMapToolbox();
   }
   
   public function listView() {
      $this->template()->addFile('tpl://maplocations:list.phtml');
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
         $toolEdit->setIcon(Template_Toolbox2::ICON_PEN)->setTitle($this->tr('Upravit místa na mapě'));
         $this->toolbox->addTool($toolEdit);
      }
   }

   protected function createListToolbox() {
      if($this->rights()->isWritable()) {
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_location', $this->tr("Přidat místo"),
         $this->link()->route('add'));
         $toolAdd->setIcon(Template_Toolbox2::ICON_ADD)->setTitle($this->tr('Přidat nové místo'));
         $this->toolbox->addTool($toolAdd);
         
         $toolClose = new Template_Toolbox2_Tool_PostRedirect('close_location_list', $this->tr("Zavřít seznam"), $this->link()->route());
         $toolClose->setIcon(Template_Toolbox2::ICON_CLOSE)->setTitle($this->tr('zavřít seznam míst'));
         $this->toolbox->addTool($toolClose);
         
         if(!empty($this->locations)){
            foreach ($this->locations as $location) {
               $toolboxItem = new Template_Toolbox2();
               $toolboxItem->setIcon(Template_Toolbox2::ICON_ADD);
               $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_location', $this->tr("Upravit místo"),
               $this->link()->route('edit', array('id' => $location->getPK())));
               $toolEdit->setIcon(Template_Toolbox2::ICON_PEN)->setTitle($this->tr('Upravit vybrané místo'));
               $toolboxItem->addTool($toolEdit);

               $toolDelete = new Template_Toolbox2_Tool_Form($this->formDel);
               $toolDelete->setImportant(true);
               $toolDelete->setIcon(Template_Toolbox2::ICON_DELETE)->setTitle($this->tr('Smazat vybrané místo'))->setConfirmMeassage($this->tr('Opravdu smazat tuto položku?'));
               $toolDelete->getForm()->id->setValues($location->getPK());
               $toolboxItem->addTool($toolDelete);
               
               $location->toolboxItem = clone $toolboxItem;
            }
         }
      }
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      Template_Module::setEdit(true);
      $this->setTinyMCE($this->form->text, 'advanced');
      $this->template()->addFile('tpl://maplocations:edit.phtml');
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->addView();
   }

   public function editTextView() {
      $this->setTinyMCE($this->form->text, 'advanced');
      $this->template()->addFile('tpl://maplocations:edittext.phtml');
   }
}