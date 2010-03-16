<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class CinameProgramFk_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }
}

?>