<?php
include_once './lib/panel.class.php';
class NewsPanel extends Panel {
	/**
	 * Počet novinek v panelu
	 * @var integer
	 */
	const PARAM_NUMBER_OF_NEWS = 'scrollpanel';

	/**
	 * Název proměné s linkem na detail
	 * @var string
	 */
	const SHOW_LINK_NAME = 'show_link';
	
	public function panelController() {
	}
	
	public function panelView() {
      $this->template()->addTplFile("panel.phtml");
      $this->template()->addCssFile("style.css");
      $newsM = $this->createModel("NewsListModel");
      $this->template()->news = $newsM->getSelectedListNews(0, $this->module()->getParam(self::PARAM_NUMBER_OF_NEWS));
	}
}
?>