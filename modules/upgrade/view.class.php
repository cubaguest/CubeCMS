<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Upgrade_View extends View {
	public function mainView() {
      $this->template()->addTplFile('main.phtml');
      Template_Module::setEdit(true);
	}
}

?>