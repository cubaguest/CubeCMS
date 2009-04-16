<?php
/*
 * Třída modelu s listem Novinek
 */
class ProductsListModel extends DbModel {
	/**
	 * Celkový počet novinek
	 * @var integer
	 */
	private $allProductsCount = 0;
	private $countProductsLoaded = false;
	
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
	public function getCountProducts() {
		if(!$this->countProductsLoaded){
         $sqlCount = $this->getDb()->select()->table($this->getModule()->getDbTable())
         ->colums(array("count"=>"COUNT(*)"))
         ->where(ProductDetailModel::COLUMN_PRODUCT_ID_ITEM, $this->getModule()->getId());
		
			$count = $this->getDb()->fetchObject($sqlCount);
         if(empty ($count)){
            throw new UnexpectedValueException(
               _m('Neočekávaná hodnota počtu produktů, Zřejmně neexistuje tabulka s produkty'));
         }

         $this->allProductsCount = $count->count;
			$this->countProductsLoaded = true;
		}
		return $this->allProductsCount;
	}
	
	
	/**
	 * Metoda vrací pole s vybranými produkty
	 * 
	 * @return array -- pole článků
	 */
   public function getSelectedListProducts($from, $count=5) {
      $sqlSelect = $this->getDb()->select()->table($this->getModule()->getDbTable(), 'article')
      ->colums(array(ProductDetailModel::COLUMN_PRODUCT_LABEL => "IFNULL(".ProductDetailModel::COLUMN_PRODUCT_LABEL
            .'_'.Locale::getLang().", ".ProductDetailModel::COLUMN_PRODUCT_LABEL.'_'.Locale::getDefaultLang().")",
            ProductDetailModel::COLUMN_PRODUCT_ID, ProductDetailModel::COLUMN_PRODUCT_FILE))
//      ->join(array('user' => $this->getUserTable()),
//         array('article'=>ProductDetailModel::COLUMN_PRODUCT_ID_USER, ProductDetailModel::COLUMN_USER_ID),
//         null, ProductDetailModel::COLUMN_USER_NAME)
//      ->join(array('user' => $this->getUserTable()), 'news.'.self::COLUMN_NEWS_ID_USER
//         .' = user.'.self::COLUMN_ISER_ID, null, self::COLUMN_USER_NAME)
      ->where("article.".ProductDetailModel::COLUMN_PRODUCT_ID_ITEM, $this->getModule()->getId())
      ->limit($from, $count)
      ->order("article.".ProductDetailModel::COLUMN_PRODUCT_TIME, Db::ORDER_DESC);
		$returArray = $this->getDb()->fetchAll($sqlSelect);
		return $returArray;
	}

/**
	 * Metoda vrací pole se všemi články
	 *
	 * @return array -- pole článků
	 */
	public function getListArticles() {
      $sqlSelect = $this->getDb()->select()->table($this->getModule()->getDbTable(), 'articletb')
      ->colums(array(ProductDetailModel::COLUMN_PRODUCT_LABEL => "IFNULL(".ProductDetailModel::COLUMN_PRODUCT_LABEL
            .'_'.Locale::getLang().", ".ProductDetailModel::COLUMN_PRODUCT_LABEL.'_'.Locale::getDefaultLang().")",
            ProductDetailModel::COLUMN_PRODUCT_TEXT => "IFNULL(".ProductDetailModel::COLUMN_PRODUCT_TEXT
            .'_'.Locale::getLang().", ".ProductDetailModel::COLUMN_PRODUCT_TEXT.'_'.Locale::getDefaultLang().")",
            ProductDetailModel::COLUMN_PRODUCT_ID_USER, ProductDetailModel::COLUMN_PRODUCT_ID,
            ProductDetailModel::COLUMN_PRODUCT_EDIT_TIME))
      ->where("articletb.".ProductDetailModel::COLUMN_PRODUCT_ID_ITEM, $this->getModule()->getId())
      ->order("articletb.".ProductDetailModel::COLUMN_PRODUCT_TIME, Db::ORDER_DESC);

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
      ->colums(ProductDetailModel::COLUMN_PRODUCT_TIME)
      ->limit(0, 1)
      ->order(ProductDetailModel::COLUMN_PRODUCT_TIME, Db::ORDER_DESC);

      $returArray = $this->getDb()->fetchObject($sqlSelect);

      if(!empty ($returArray)){
         $returArray = $returArray->{ProductDetailModel::COLUMN_PRODUCT_TIME};
      }
		return $returArray;
   }
	
}

?>