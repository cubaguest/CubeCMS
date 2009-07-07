<?php
class News_Panel extends Panel {
	/**
	 * Počet novinek v panelu
	 * @var integer
	 */
	const PARAM_NUMBER_OF_NEWS = 'scrollpanel';

	public function panelController() {

	}
	
	public function panelView() {
      $this->template()->addTplFile("panel.phtml");
      $this->template()->addCssFile("style.css");
      $newsM = new News_Model_List($this->sys());
      $this->template()->news = $newsM->getSelectedListNews(0, $this->module()->getParam(self::PARAM_NUMBER_OF_NEWS,5));
	}
}
?>