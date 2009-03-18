<?php
/*
 * Třída modelu s listem Novinek
 */
class NewsListModel extends DbModel {
	/**
	 * Názvy sloupců v databázi
	 * @var string
	 */
	const COLUMN_NEWS_LABEL = 'label';
	const COLUMN_NEWS_LABEL_LANG_PREFIX = 'label_';
	const COLUMN_NEWS_TEXT = 'text';
	const COLUMN_NEWS_TEXT_LANG_PREFIX = 'text_';
	const COLUMN_NEWS_TIME = 'time';
	const COLUMN_NEWS_ID_USER = 'id_user';
	const COLUMN_NEWS_ID_ITEM = 'id_item';
	const COLUMN_NEWS_ID_NEW = 'id_new';
	const COLUMN_NEWS_DELETED = 'deleted';
	
	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
	const COLUMN_USER_NAME = 'username';
	const COLUMN_ISER_ID =	 'id_user';	
	
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
         $sqlCount = $this->getDb()->select()->table($this->getModule()->getDbTable())
         ->colums(array("count"=>"COUNT(*)"))
         ->where(self::COLUMN_NEWS_ID_ITEM, $this->getModule()->getId())
			->where(self::COLUMN_NEWS_DELETED, (int)false);
		
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
   public function getSelectedListNews($from, $count=5) {
      $sqlSelect = $this->getDb()->select()->table($this->getModule()->getDbTable(), 'news')
      ->colums(array(self::COLUMN_NEWS_LABEL => "IFNULL(".self::COLUMN_NEWS_LABEL.'_'.Locale::getLang().", ".self::COLUMN_NEWS_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_NEWS_TEXT => "IFNULL(".self::COLUMN_NEWS_TEXT_LANG_PREFIX.Locale::getLang()
            .", ".self::COLUMN_NEWS_TEXT_LANG_PREFIX.Locale::getDefaultLang().")",
            self::COLUMN_NEWS_ID_USER, self::COLUMN_NEWS_ID_NEW, self::COLUMN_NEWS_TIME))
      ->join(array('user' => $this->getUserTable()),
         array('news'=>self::COLUMN_NEWS_ID_USER, self::COLUMN_ISER_ID), null, self::COLUMN_USER_NAME)
//      ->join(array('user' => $this->getUserTable()), 'news.'.self::COLUMN_NEWS_ID_USER
//         .' = user.'.self::COLUMN_ISER_ID, null, self::COLUMN_USER_NAME)
      ->where("news.".self::COLUMN_NEWS_ID_ITEM, $this->getModule()->getId())
      ->where("news.".self::COLUMN_NEWS_DELETED, (int)false)
      ->limit($from, $count)
      ->order("news.".self::COLUMN_NEWS_TIME, Db::ORDER_DESC);

      // jestli se mají sledovat uživatelé, tak tady něco musí být
//		if($this->tableUsers != null){
//			$sqlSelect=$sqlSelect->join(array("users" => $tableUsers), "users.".self::COLUMN_NEWS_ID_USER." = news.".self::COLUMN_NEWS_ID_USER, null, Auth::USER_NAME);
//		}
//
		$returArray = $this->getDb()->fetchAll($sqlSelect);
//
		return $returArray;
	}

	/**
	 * Metoda vrací pole s vybranými novinkami
	 *
	 * @return array -- pole novinek
	 */
	public function getListNews() {
      $sqlSelect = $this->getDb()->select()->table($this->getModule()->getDbTable(), 'news')
      ->colums(array(self::COLUMN_NEWS_LABEL => "IFNULL(".self::COLUMN_NEWS_LABEL_LANG_PREFIX.Locale::getLang().", ".self::COLUMN_NEWS_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
            self::COLUMN_NEWS_TEXT => "IFNULL(".self::COLUMN_NEWS_TEXT_LANG_PREFIX.Locale::getLang().", ".self::COLUMN_NEWS_TEXT_LANG_PREFIX.Locale::getDefaultLang().")",
            self::COLUMN_NEWS_ID_USER, self::COLUMN_NEWS_ID_NEW, self::COLUMN_NEWS_TIME))
      ->where("news.".self::COLUMN_NEWS_ID_ITEM, $this->getModule()->getId())
      ->where("news.".self::COLUMN_NEWS_DELETED, (int)false)
      ->order("news.".self::COLUMN_NEWS_TIME, Db::ORDER_DESC);

//		if($this->tableUsers != null){
//			$sqlSelect=$sqlSelect->join(array("users" => $tableUsers), "users.".self::COLUMN_NEWS_ID_USER." = news.".self::COLUMN_NEWS_ID_USER, null, Auth::USER_NAME);
//		}

		$returArray = $this->getDb()->fetchAll($sqlSelect);

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
	
	
	private function getUserTable() {
		$tableUsers = AppCore::sysConfig()->getOptionValue(Auth::CONFIG_USERS_TABLE_NAME, Config::SECTION_DB_TABLES);
		
		return $tableUsers;
	}

   public function getLastChange() {
      $sqlSelect = $this->getDb()->select()
      ->table($this->getModule()->getDbTable())
      ->colums(self::COLUMN_NEWS_TIME)
      ->limit(0, 1)
      ->order(self::COLUMN_NEWS_TIME, Db::ORDER_DESC);

      $returArray = $this->getDb()->fetchObject($sqlSelect);

      if(!empty ($returArray)){
         $returArray = $returArray->{self::COLUMN_NEWS_TIME};
      }
		return $returArray;
   }
	
}

?>