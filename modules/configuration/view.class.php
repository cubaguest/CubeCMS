<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Configuration_View extends View {
   public function mainView() {
      $this->template()->addTplFile('list.phtml');
      Template_Module::setEdit(true);
   }

   public function editView() {
      $this->template()->addFile('tpl://edit.phtml');
      Template_Module::setEdit(true);
   }
   public function editGlobalView() {
      $this->editView();
      $this->template()->editGlobalOption = true;
   }
}

?>