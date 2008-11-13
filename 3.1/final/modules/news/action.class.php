<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class NewsAction extends Action {
	

	public function actions(){
		$this->addAction("addparent", "addp");
	}
	
}
?>