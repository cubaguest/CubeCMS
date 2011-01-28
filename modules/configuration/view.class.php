<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Configuration_View extends View {
   public function mainView() {
      $this->template()->addTplFile('list.phtml');
   }

   public function editView() {
      $this->template()->addTplFile('edit.phtml');
   }
}

?>