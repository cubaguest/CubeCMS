<?php

/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */
class Categories_View extends View {

   public function mainView() {
      $this->template()->addTplFile('list.phtml');
      if($this->rights()->isControll()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_ADD);
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_category', $this->tr("Přidat kategorii"),
         $this->link()->route('add'));
         $toolAdd->setIcon('application_add.png')->setTitle($this->tr('Přidat novou kategorii'));
         $toolbox->addTool($toolAdd);
         $this->toolbox = $toolbox;
      }
   }

   public function adminMenuView() {
      $this->mainView();
   }

   public function showView() {
      $this->template()->addTplFile('detail.phtml');
   }

   public function editView() {
      Template_Module::setEdit(true);
      $this->template()->addTplFile('edit.phtml');
   }

   public function addView() {
      Template_Module::setEdit(true);
      $this->editView();
   }

   public function moduleDocView() {
      print ($this->doc);
   }

   public function catSettingsView() {
      Template_Module::setEdit(true);
      $this->mview->viewSettingsView();
   }

}
?>