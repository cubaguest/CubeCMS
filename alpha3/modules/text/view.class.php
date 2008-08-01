<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class TextView extends View {
	public function mainView() {
		echo "mainview";
//		$this->template()->addTpl("list.tpl");
//		$this->template()->addTpl("scroll.tpl");
//		$this->template()->addCss("pokus.css");
//		$this->template()->addJS("pokus.js");
//		
//		$this->template()->addVar("NEWS_LIST_ARRAY", $this->getModel()->allNewsArray);
//		$this->template()->addCss("style.css");
	}
	
	public function edittextController() {
		echo "editview";
	}
	
}

?>