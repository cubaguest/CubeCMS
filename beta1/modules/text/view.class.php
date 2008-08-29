<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class TextView extends View {
	public function mainView() {
		
	if($this->getRights()->isWritable()){
			$this->template()->addTpl('addNewsButton.tpl');
			
			$this->template()->addVar('LINK_TO_EDIT_NEWS_NAME', _("Upravit"));
			$this->template()->addVar('LINK_TO_EDIT',$this->getModel()->link);
			
			
		}
		
		$this->template()->addTpl("text.tpl");
		$this->template()->addTpl("list.tpl");
		
//		$this->template()->addCss("pokus.css");
//		$this->template()->addJS("pokus.js");
//		
//		$this->template()->addVar("NEWS_LIST_ARRAY", $this->getModel()->allNewsArray);
//		$this->template()->addCss("style.css");
	}
	
	public function edittextView() {
		
		$tinymce = new TinyMce();
			$this->template()->addJsPlugin($tinymce);
	}
	
}

?>