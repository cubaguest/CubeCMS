<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class TextAction extends Action {
	

	public function actions() {
		$this->addAction("edittext", "ed");
	}
	
}
?>