<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class PhpInfo_Controller extends Controller {
/**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      // viewer
      $this->view()->template()->addTplFile("info.phtml");
   }
}

?>