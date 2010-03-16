<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class PhpInfo_View extends View {
   public function mainView() {
      $this->template()->addTplFile("info.phtml");
   }

   public function infoView() {
      phpinfo();
   }
}

?>