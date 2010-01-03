<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class CinemaProgram_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }
}

?>