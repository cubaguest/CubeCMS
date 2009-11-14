<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class NavigationMenu_Controller extends Controller {
   public function mainController() {
      $this->checkReadableRights();
      // nastavení viewru
      $this->view()->template()->addTplFile('list.phtml');
   }

   public function showController() {
   }

   public function editController() {

      $this->view()->template()->form = $form;
      $this->view()->template()->option = $opt;
      $this->view()->template()->addTplFile('edit.phtml');
   }
}

?>