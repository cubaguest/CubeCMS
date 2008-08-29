<?php
class NewsPanel extends Panel {
	
	/**
	 * Názva sloupců v db
	 * @var string
	 */
	const COLUM_ID_ITEM 	= 'id_item';
	const COLUM_ID_NEW 		= 'id_new';
	const COLUM_KEY 		= 'key';
	const COLUM_NEWS_LABEL = 'label';
	const COLUM_NEWS_LABEL_LANG_PREFIX = 'label_';
	const COLUM_NEWS_TEXT = 'text';
	const COLUM_NEWS_TEXT_LANG_PREFIX = 'text_';
	const COLUM_TIME 		= 'time';
	const COLUM_NEWS_LANG = 'lang';
	const COLUM_NEWS_DELETED = 'deleted';
	
	/**
	 * Počet novinek v panelu
	 * @var integer
	 */
	const NUMBER_OF_NEWS = 5;

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
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(), array(self::COLUM_NEWS_LABEL => "IFNULL(".self::COLUM_NEWS_LABEL_LANG_PREFIX.Locale::getLang().", ".self::COLUM_NEWS_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
													self::COLUM_NEWS_LANG => "IF(`".self::COLUM_NEWS_LABEL_LANG_PREFIX.Locale::getLang()."` != 'NULL', '".Locale::getLang()."', '".Locale::getDefaultLang()."')",
													self::COLUM_NEWS_TEXT => "IFNULL(".self::COLUM_NEWS_TEXT_LANG_PREFIX.Locale::getLang().", ".self::COLUM_NEWS_TEXT_LANG_PREFIX.Locale::getDefaultLang().")"))
											 ->where(self::COLUM_ID_ITEM." = ".$this->getModule()->getId())
											 ->where(self::COLUM_NEWS_DELETED." = ".(int)false)
											 ->limit(0,self::NUMBER_OF_NEWS)
											 ->order(self::COLUM_TIME, 'desc');
											
		
		$this->newsArray = $this->getDb()->fetchAssoc($sqlSelect);
		
		$this->newsLink = $this->getLink();
	}
	
	public function panelView() {
		$this->template()->addTpl("panel.tpl");
		
		$this->template()->addVar("NEWS_ARRAY", $this->newsArray);
		$this->template()->addVar("NEWS_LINK", $this->newsLink);
		$this->template()->addVar("NEWS_LINK_NAME", _("Další novinky"));
		$this->template()->addVar("NEWS_MORE", _("Více"));
	}
	
	
}
?>