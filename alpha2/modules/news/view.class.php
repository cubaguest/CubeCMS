<?php
class NewsView extends View {
	public function mainView() {
		$this->template()->addTpl("list.tpl");
//		$this->template()->addTpl("scroll.tpl");
//		$this->template()->addCss("pokus.css");
//		$this->template()->addJS("pokus.js");
//		
		$this->template()->addVar("NEWS_LIST_ARRAY", $this->getModel()->allNewsArray);
		$this->template()->addCss("style.css");
		
		//TODO korektní cestu
		$this->template()->addTpl($this->getModel()->scroll->getTpl(), true);
		$this->getModel()->scroll->assignToTpl($this->template());
//		$this->getModel()->scroll->getTpl();

		//JS Plugins
		$tinymce = new TinyMce();
		
//		echo "<pre>";
//		print_r($tinymce);
//		echo "</pre>";
		
		$this->addJsPlugin($tinymce);
	}

	public function showView()
	{
		;
	}
	
//	public function testView() {
//		echo "pokus test";
//	}
	
	
}

?>