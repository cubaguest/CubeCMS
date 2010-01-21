<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Pokus_View extends View {
   public function mainView() {
      $this->template()->addTplFile("pokusform.phtml");
   }
}

?>