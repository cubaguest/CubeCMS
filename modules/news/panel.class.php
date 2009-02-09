<?php
class NewsPanel extends Panel {
	
	/**
	 * Názva sloupců v db
	 * @var string
	 */
	const COLUMN_ID_ITEM 	= 'id_item';
	const COLUMN_ID_NEW 		= 'id_new';
	const COLUMN_NEWS_URLKEY 		= 'urlkey';
	const COLUMN_NEWS_LABEL = 'label';
	const COLUMN_NEWS_LABEL_LANG_PREFIX = 'label_';
	const COLUMN_NEWS_TEXT = 'text';
	const COLUMN_NEWS_TEXT_LANG_PREFIX = 'text_';
	const COLUMN_TIME 		= 'time';
	const COLUMN_NEWS_LANG = 'lang';
	const COLUMN_NEWS_DELETED = 'deleted';
	
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
	
	/**
	 * Pole novinek
	 * @var array
	 */
	private $newsArray = array();
	
	/**
	 * Link do novinek
	 * @var string
	 */
	private $newsLink = null;
	
	
	public function panelController() {
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(), 
            array(self::COLUMN_NEWS_LABEL => "IFNULL(".self::COLUMN_NEWS_LABEL_LANG_PREFIX
                .Locale::getLang().", ".self::COLUMN_NEWS_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
                self::COLUMN_NEWS_LANG => "IF(`".self::COLUMN_NEWS_LABEL_LANG_PREFIX
                .Locale::getLang()."` != 'NULL', '".Locale::getLang()."', '".Locale::getDefaultLang()
                ."')", self::COLUMN_NEWS_TEXT => "IFNULL(".self::COLUMN_NEWS_TEXT_LANG_PREFIX
                .Locale::getLang().", ".self::COLUMN_NEWS_TEXT_LANG_PREFIX.Locale::getDefaultLang()
                .")", self::COLUMN_ID_NEW))
			->where(self::COLUMN_ID_ITEM." = ".$this->getModule()->getId())
			->where(self::COLUMN_NEWS_DELETED." = ".(int)false)
         ->limit(0,$this->getModule()->getParam(self::PARAM_NUMBER_OF_NEWS))
			->order(self::COLUMN_TIME, 'desc');
											
		
		$this->newsArray = $this->getDb()->fetchAssoc($sqlSelect);
		
//		Přidání odkazů přímo na detail novinky
		foreach ($this->newsArray as $newKey => $new) {
			$this->newsArray[$newKey][self::SHOW_LINK_NAME] = $this->getLink()->article($new[self::COLUMN_NEWS_LABEL], $new[self::COLUMN_ID_NEW]);
		}
		
		
		$this->newsLink = $this->getLink();
	}
	
	public function panelView() {
		$this->template()->addTpl("panel.tpl");
		$this->template()->addCss("style.css");
		
		$this->template()->addVar("NEWS_ARRAY", $this->newsArray);
		$this->template()->addVar("NEWS_LINK", $this->newsLink);
		$this->template()->addVar("NEWS_LINK_NAME", _("Další novinky"));
		$this->template()->addVar("NEWS_MORE", _("Více"));
	}
}
?>