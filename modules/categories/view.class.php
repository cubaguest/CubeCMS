<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Categories_View extends View {
   public function mainView() {
      $this->template()->addTplFile('list.phtml');
      $this->template()->addCssFile('style.css');
   }

   public function showView() {
      $this->template()->addTplFile('detail.phtml');
   }

   public function editView(){
      $this->template()->addTplFile('edit.phtml');
   }

   public function addView(){
      $this->editView();
   }

   public function moduleDocView() {
      print ($this->doc);
   }

   public function catSettingsView(){

      $this->template()->addTplFile('settings.phtml');
      if(file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
              .$this->moduleName.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR
              .DIRECTORY_SEPARATOR.'settings.phtml')){
         $tpl = new Template_Module($this->link(), $this->category());
         $tpl->addTplFile('settings.phtml', $this->moduleName);
         $this->includeTpl = $tpl;
      }

   }
}

?>