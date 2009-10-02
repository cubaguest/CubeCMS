<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Categories_Controller extends Controller {
	public function mainController() {
      $this->checkReadableRights();

      // nastavení viewru
      $this->view()->template()->addTplFile('list.phtml');
	}

   public function showController() {
      $this->checkReadableRights();


      // nastavení viewru
      $this->view()->template()->addTplFile('detail.phtml');
      $this->view()->template()->cat = $this->getRequest('category');
   }

	
}

?>