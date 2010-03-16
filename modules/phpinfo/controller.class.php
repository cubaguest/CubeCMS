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
   }

   public function infoController() {}
}

?>