<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Panels_View extends View {
   public function mainView() {
      $this->template()->addTplFile('list.phtml');

      if($this->category()->getRights()->isWritable()) {
//          $toolbox = new Template_Toolbox2();
//          $toolbox->setIcon(Template_Toolbox2::ICON_ADD);
//          $toolAdd = new Template_Toolbox2_Tool_Redirect('add_panel', $this->tr("Přidat panel"),
//          $this->link()->route('add'));
//          $toolAdd->setIcon('page_add.png')->setTitle($this->tr('Přidat nový panel'));
//          $toolbox->addTool($toolAdd);
//          $this->toolbox = $toolbox;
      }
      Template_Module::setEdit(true);
   }

   public function addView() {
      Template_Navigation::addItem($this->tr('Přidání panelu'), $this->link(), null, null, null, true);
      $this->template()->addTplFile('edit.phtml');
      Template_Module::setEdit(true);
   }

   public function editView() {
      Template_Navigation::addItem($this->tr('Úprava panelu'), $this->link(), null, null, null, true);
      $this->template()->addTplFile('edit.phtml');
      Template_Module::setEdit(true);
      $this->edit=true;
   }

   public function getPanelsView() {
      print (json_encode($this->data));
   }

   public function getPanelInfoView() {
      print (json_encode($this->data));
   }

   public function panelSettingsView() {
      $this->template()->addTplFile('panel_settings.phtml');
      if(file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
      .$this->moduleName.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR
      .DIRECTORY_SEPARATOR.'panel_settings.phtml')) {
         $tpl = new Template_Module($this->link(), $this->category());
         $tpl->addTplFile('panel_settings.phtml', $this->moduleName);
         $this->includeTpl = $tpl;
      }
      Template_Navigation::addItem($this->tr('Nastavení panelu'), $this->link(), null, null, null, true);
      Template_Module::setEdit(true);
   }

   public function getListPanelsView(){
      $this->template()->addTplFile('listtpl.phtml');
      Template_Module::setEdit(true);
   }
}

?>