<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Services_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }
}

?>