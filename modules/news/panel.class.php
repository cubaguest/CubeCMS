<?php
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
	
//	/**
//	 * Pole novinek
//	 * @var array
//	 */
//	private $newsArray = array();
//
//	/**
//	 * Link do novinek
//	 * @var string
//	 */
//	private $newsLink = null;
	
	
	public function panelController() {
//      $newsM = new NewsListModel();
//      $this->newsArray = $newsM->getSelectedListNews(0, $this->module()->getParam(self::PARAM_NUMBER_OF_NEWS));
		
      //	Přidání odkazů přímo na detail novinky
//      if(!empty ($this->newsArray)){
//         foreach ($this->newsArray as $newKey => $new) {
//            $this->newsArray[$newKey][self::SHOW_LINK_NAME] = $this->link()
//            ->article($new[NewsListModel::COLUMN_NEWS_LABEL], $new[NewsListModel::COLUMN_NEWS_ID_NEW]);
//         }
//      }

		
//		$this->newsLink = $this->link();
	}
	
	public function panelView() {
      $this->template()->addTplFile("panel.phtml");
      $this->template()->addCssFile("style.css");
      $newsM = $this->createModel("NewsListModel");
      $this->template()->news = $newsM->getSelectedListNews(0, $this->module()->getParam(self::PARAM_NUMBER_OF_NEWS));
	}
}
?>