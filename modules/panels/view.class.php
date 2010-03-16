<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Panels_View extends View {
   public function mainView() {
      $this->template()->addTplFile('list.phtml');
   }

   public function addView(){
      $this->template()->addTplFile('edit.phtml');
   }

   public function editView(){
      $this->addView();
      $this->edit=true;
   }

   public function getPanelsView() {
      print (json_encode($this->data));
   }

   public function getPanelInfoView() {
      print (json_encode($this->data));
   }

   public function panelSettingsView(){
      $this->template()->addTplFile('panel_settings.phtml');
      if(file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
              .$this->moduleName.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR
              .DIRECTORY_SEPARATOR.'panel_settings.phtml')){
         $tpl = new Template_Module($this->link(), $this->category());
         $tpl->addTplFile('panel_settings.phtml', $this->moduleName);
         $this->includeTpl = $tpl;
      }

   }
}

?>