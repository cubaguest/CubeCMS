<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class House_View extends View {
   public function mainView() {
      $this->template()->addTplFile("page.phtml");
      $this->template()->addCssFile("style.css");
   }

}

?>