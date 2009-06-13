<?php
/*
 * Třída modelu s listem Novinek
 */
class Articles_Model_List extends Model_Db {
	/**
	 * Celkový počet novinek
	 * @var integer
	 */
	private $allArticlesCount = 0;
	private $countArticlesLoaded = false;
	
	/**
	 * Tabulka s uživateli
	 * @var string
	 */
	private $tableUsers = null;
	
	/**
	 * Metoda vrací počet článků
	 *
	 * @return integer -- počet článků
	 */
	public function getCountArticles() {
		if(!$this->countArticlesLoaded){
         $sqlCount = $this->getDb()->select()->table($this->module()->getDbTable())
         ->colums(array("count"=>"COUNT(*)"))
         ->where(Articles_Model_Detail::COLUMN_ARTICLE_ID_ITEM, $this->module()->getId());
		
			$count = $this->getDb()->fetchObject($sqlCount);
			$this->allArticlesCount = $count->count;
			$this->countArticlesLoaded = true;
		}
		return $this->allArticlesCount;
	}
	
	
	/**
	 * Metoda vrací pole s vybranými články
	 * 
	 * @return array -- pole článků
	 */
   public function getSelectedListArticles($from, $count=5) {
      $sqlSelect = $this->getDb()->select()->table($this->module()->getDbTable(), 'article')
      ->colums(array(Articles_Model_Detail::COLUMN_ARTICLE_LABEL => "IFNULL(".Articles_Model_Detail::COLUMN_ARTICLE_LABEL
            .'_'.Locale::getLang().", ".Articles_Model_Detail::COLUMN_ARTICLE_LABEL.'_'.Locale::getDefaultLang().")",
            Articles_Model_Detail::COLUMN_ARTICLE_TEXT => "IFNULL(".Articles_Model_Detail::COLUMN_ARTICLE_TEXT.'_'.Locale::getLang()
            .", ".Articles_Model_Detail::COLUMN_ARTICLE_TEXT.'_'.Locale::getDefaultLang().")",
            Articles_Model_Detail::COLUMN_ARTICLE_ID_USER, Articles_Model_Detail::COLUMN_ARTICLE_ID,
            Articles_Model_Detail::COLUMN_ARTICLE_TIME))
      ->join(array('user' => $this->getUserTable()),
         array('article'=>Articles_Model_Detail::COLUMN_ARTICLE_ID_USER, Articles_Model_Detail::COLUMN_USER_ID),
         null, Articles_Model_Detail::COLUMN_USER_NAME)
//      ->join(array('user' => $this->getUserTable()), 'news.'.self::COLUMN_NEWS_ID_USER
//         .' = user.'.self::COLUMN_ISER_ID, null, self::COLUMN_USER_NAME)
      ->where("article.".Articles_Model_Detail::COLUMN_ARTICLE_ID_ITEM, $this->module()->getId())
      ->limit($from, $count)
      ->order("article.".Articles_Model_Detail::COLUMN_ARTICLE_TIME, Db::ORDER_DESC);
		$returArray = $this->getDb()->fetchAll($sqlSelect);
		return $returArray;
	}

/**
	 * Metoda vrací pole se všemi články
	 *
	 * @return array -- pole článků
	 */
	public function getListArticles() {
      $sqlSelect = $this->getDb()->select()->table($this->module()->getDbTable(), 'articletb')
      ->colums(array(Articles_Model_Detail::COLUMN_ARTICLE_LABEL => "IFNULL(".Articles_Model_Detail::COLUMN_ARTICLE_LABEL
            .'_'.Locale::getLang().", ".Articles_Model_Detail::COLUMN_ARTICLE_LABEL.'_'.Locale::getDefaultLang().")",
            Articles_Model_Detail::COLUMN_ARTICLE_TEXT => "IFNULL(".Articles_Model_Detail::COLUMN_ARTICLE_TEXT
            .'_'.Locale::getLang().", ".Articles_Model_Detail::COLUMN_ARTICLE_TEXT.'_'.Locale::getDefaultLang().")",
            Articles_Model_Detail::COLUMN_ARTICLE_ID_USER, Articles_Model_Detail::COLUMN_ARTICLE_ID,
            Articles_Model_Detail::COLUMN_ARTICLE_EDIT_TIME))
      ->where("articletb.".Articles_Model_Detail::COLUMN_ARTICLE_ID_ITEM, $this->module()->getId())
      ->order("articletb.".Articles_Model_Detail::COLUMN_ARTICLE_TIME, Db::ORDER_DESC);

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
      ->table($this->module()->getDbTable())
      ->colums(Articles_Model_Detail::COLUMN_ARTICLE_TIME)
      ->limit(0, 1)
      ->order(Articles_Model_Detail::COLUMN_ARTICLE_TIME, Db::ORDER_DESC);

      $returArray = $this->getDb()->fetchObject($sqlSelect);

      if(!empty ($returArray)){
         $returArray = $returArray->{Articles_Model_Detail::COLUMN_ARTICLE_TIME};
      }
		return $returArray;
   }
	
}

?>