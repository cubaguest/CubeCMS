<?php
/*
 * Třída modelu s listem Novinek
 */
class NewsListModel extends DbModel {
	/**
	 * Názvy sloupců v databázi
	 * @var string
	 */
	const COLUM_NEWS_LABEL = 'label';
	const COLUM_NEWS_LABEL_LANG_PREFIX = 'label_';
	const COLUM_NEWS_TEXT = 'text';
	const COLUM_NEWS_TEXT_LANG_PREFIX = 'text_';
	const COLUM_NEWS_URLKEY = 'urlkey';
	const COLUM_NEWS_TIME = 'time';
	const COLUM_NEWS_ID_USER = 'id_user';
	const COLUM_NEWS_ID_ITEM = 'id_item';
	const COLUM_NEWS_ID_NEW = 'id_new';
	const COLUM_NEWS_DELETED = 'deleted';
	
	/**
	 * Speciální imageinární sloupce
	 * @var string
	 */
	const COLUM_NEWS_LANG = 'lang';
//	const COLUM_NEWS_EDITABLE = 'editable';
//	const COLUM_NEWS_EDIT_LINK = 'editlink';	
	
	/**
	 * Celkový počet novinek
	 * @var integer
	 */
	private $allNewsCount = 0;
	private $countNewsLoaded = false;
	
	/**
	 * Tabulka s uživateli
	 * @var string
	 */
	private $tableUsers = null;
	
	/**
	 * Metoda vrací počet novinek
	 *
	 * @return integer -- počet novinek
	 */
	public function getCountNews() {
		if(!$this->countNewsLoaded){
			$sqlCount = $this->getDb()->select()->from($this->getModule()->getDbTable(), array("count"=>"COUNT(*)"))
											->where(self::COLUM_NEWS_ID_ITEM. ' = '.$this->getModule()->getId())
											->where(self::COLUM_NEWS_DELETED." = ".(int)false);
		
			$count = $this->getDb()->fetchObject($sqlCount);
			$this->allNewsCount = $count->count;
			$this->countNewsLoaded = true;
		}
		
		return $this->allNewsCount;
	}
	
	
	/**
	 * Metoda vrací pole s vybranými novinkami
	 * 
	 * @return array -- pole novinek
	 */
	public function getSelectedListNews($from, $count) {
		$sqlSelect = $this->getDb()->select()->from(array("news" => $this->getModule()->getDbTable()), array(self::COLUM_NEWS_LABEL => "IFNULL(".self::COLUM_NEWS_LABEL_LANG_PREFIX.Locale::getLang().", ".self::COLUM_NEWS_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
							self::COLUM_NEWS_LANG => "IF(`".self::COLUM_NEWS_LABEL_LANG_PREFIX.Locale::getLang()."` != 'NULL', '".Locale::getLang()."', '".Locale::getDefaultLang()."')",
							self::COLUM_NEWS_TEXT => "IFNULL(".self::COLUM_NEWS_TEXT_LANG_PREFIX.Locale::getLang().", ".self::COLUM_NEWS_TEXT_LANG_PREFIX.Locale::getDefaultLang().")",
							self::COLUM_NEWS_URLKEY, self::COLUM_NEWS_ID_USER, self::COLUM_NEWS_ID_NEW))
						->where("news.".self::COLUM_NEWS_ID_ITEM." = ".$this->getModule()->getId())
						->where("news.".self::COLUM_NEWS_DELETED." = ".(int)false)
						->limit($from, $count)
						->order("news.".self::COLUM_NEWS_TIME, 'desc');
						
		if($this->tableUsers != null){				
			$sqlSelect=$sqlSelect->join(array("users" => $tableUsers), "users.".self::COLUM_NEWS_ID_USER." = news.".self::COLUM_NEWS_ID_USER, null, Auth::USER_NAME);
		}
		
		$returArray = $this->getDb()->fetchAssoc($sqlSelect);
		
		return $returArray;
	}
	
	/**
	 * Metoda nastaví tabulku s uživateli
	 *
	 * @param string -- název tanulky s uživateli
	 */
	public function setTableUsers($tableUsers) {
		$this->tableUsers = $tableUsers;
	}
	
	
}

?>