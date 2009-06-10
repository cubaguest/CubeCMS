<?php
/*
 * Třída modelu s listem Novinek
 */
class ArticlesListModel extends DbModel {
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
         ->where(ArticleDetailModel::COLUMN_ARTICLE_ID_ITEM, $this->module()->getId());
		
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
      ->colums(array(ArticleDetailModel::COLUMN_ARTICLE_LABEL => "IFNULL(".ArticleDetailModel::COLUMN_ARTICLE_LABEL
            .'_'.Locale::getLang().", ".ArticleDetailModel::COLUMN_ARTICLE_LABEL.'_'.Locale::getDefaultLang().")",
            ArticleDetailModel::COLUMN_ARTICLE_TEXT => "IFNULL(".ArticleDetailModel::COLUMN_ARTICLE_TEXT.'_'.Locale::getLang()
            .", ".ArticleDetailModel::COLUMN_ARTICLE_TEXT.'_'.Locale::getDefaultLang().")",
            ArticleDetailModel::COLUMN_ARTICLE_ID_USER, ArticleDetailModel::COLUMN_ARTICLE_ID,
            ArticleDetailModel::COLUMN_ARTICLE_TIME))
      ->join(array('user' => $this->getUserTable()),
         array('article'=>ArticleDetailModel::COLUMN_ARTICLE_ID_USER, ArticleDetailModel::COLUMN_USER_ID),
         null, ArticleDetailModel::COLUMN_USER_NAME)
//      ->join(array('user' => $this->getUserTable()), 'news.'.self::COLUMN_NEWS_ID_USER
//         .' = user.'.self::COLUMN_ISER_ID, null, self::COLUMN_USER_NAME)
      ->where("article.".ArticleDetailModel::COLUMN_ARTICLE_ID_ITEM, $this->module()->getId())
      ->limit($from, $count)
      ->order("article.".ArticleDetailModel::COLUMN_ARTICLE_TIME, Db::ORDER_DESC);
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
      ->colums(array(ArticleDetailModel::COLUMN_ARTICLE_LABEL => "IFNULL(".ArticleDetailModel::COLUMN_ARTICLE_LABEL
            .'_'.Locale::getLang().", ".ArticleDetailModel::COLUMN_ARTICLE_LABEL.'_'.Locale::getDefaultLang().")",
            ArticleDetailModel::COLUMN_ARTICLE_TEXT => "IFNULL(".ArticleDetailModel::COLUMN_ARTICLE_TEXT
            .'_'.Locale::getLang().", ".ArticleDetailModel::COLUMN_ARTICLE_TEXT.'_'.Locale::getDefaultLang().")",
            ArticleDetailModel::COLUMN_ARTICLE_ID_USER, ArticleDetailModel::COLUMN_ARTICLE_ID,
            ArticleDetailModel::COLUMN_ARTICLE_EDIT_TIME))
      ->where("articletb.".ArticleDetailModel::COLUMN_ARTICLE_ID_ITEM, $this->module()->getId())
      ->order("articletb.".ArticleDetailModel::COLUMN_ARTICLE_TIME, Db::ORDER_DESC);

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
      ->colums(ArticleDetailModel::COLUMN_ARTICLE_TIME)
      ->limit(0, 1)
      ->order(ArticleDetailModel::COLUMN_ARTICLE_TIME, Db::ORDER_DESC);

      $returArray = $this->getDb()->fetchObject($sqlSelect);

      if(!empty ($returArray)){
         $returArray = $returArray->{ArticleDetailModel::COLUMN_ARTICLE_TIME};
      }
		return $returArray;
   }
	
}

?>