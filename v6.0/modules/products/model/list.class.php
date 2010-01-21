<?php
/*
 * Třída modelu s listem Novinek
 */
class Products_Model_List extends Model_Db {
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
         $sqlCount = $this->getDb()->select()->table(Db::table(Products_Model_Detail::DB_TABLE))
         ->colums(array("count"=>"COUNT(*)"))
         ->where(Products_Model_Detail::COLUMN_PRODUCT_ID_ITEM, $this->module()->getId());
		
			$count = $this->getDb()->fetchObject($sqlCount);
			$this->allProductsCount = $count->count;
			$this->countProductsLoaded = true;
		}
		return $this->allProductsCount;
	}
	
	
	/**
	 * Metoda vrací pole s vybranými články
	 * 
	 * @return array -- pole článků
	 */
   public function getSelectedListProducts($from, $count=5) {
      $sqlSelect = $this->getDb()->select()->table(Db::table(Products_Model_Detail::DB_TABLE), 'product')
      ->colums(array(Products_Model_Detail::COLUMN_PRODUCT_LABEL => "IFNULL(".Products_Model_Detail::COLUMN_PRODUCT_LABEL
            .'_'.Locale::getLang().", ".Products_Model_Detail::COLUMN_PRODUCT_LABEL.'_'.Locale::getDefaultLang().")",
            Products_Model_Detail::COLUMN_PRODUCT_TEXT => "IFNULL(".Products_Model_Detail::COLUMN_PRODUCT_TEXT.'_'.Locale::getLang()
            .", ".Products_Model_Detail::COLUMN_PRODUCT_TEXT.'_'.Locale::getDefaultLang().")",
            Products_Model_Detail::COLUMN_PRODUCT_ID_USER, Products_Model_Detail::COLUMN_PRODUCT_ID,
            Products_Model_Detail::COLUMN_PRODUCT_TIME))
      ->where("product.".Products_Model_Detail::COLUMN_PRODUCT_ID_ITEM, $this->module()->getId())
      ->limit($from, $count)
      ->order(Products_Model_Detail::COLUMN_PRODUCT_LABEL, Db::ORDER_ASC);
		$returArray = $this->getDb()->fetchAll($sqlSelect);
		return $returArray;
	}

/**
	 * Metoda vrací pole se všemi články
	 *
	 * @return array -- pole článků
	 */
	public function getListProducts() {
      $sqlSelect = $this->getDb()->select()->table(Db::table(Products_Model_Detail::DB_TABLE), 'products')
      ->colums(array(Products_Model_Detail::COLUMN_PRODUCT_LABEL => "IFNULL(".Products_Model_Detail::COLUMN_PRODUCT_LABEL
            .'_'.Locale::getLang().", ".Products_Model_Detail::COLUMN_PRODUCT_LABEL.'_'.Locale::getDefaultLang().")",
            Products_Model_Detail::COLUMN_PRODUCT_TEXT => "IFNULL(".Products_Model_Detail::COLUMN_PRODUCT_TEXT
            .'_'.Locale::getLang().", ".Products_Model_Detail::COLUMN_PRODUCT_TEXT.'_'.Locale::getDefaultLang().")",
            Products_Model_Detail::COLUMN_PRODUCT_ID_USER, Products_Model_Detail::COLUMN_PRODUCT_ID,
            Products_Model_Detail::COLUMN_PRODUCT_EDIT_TIME))
      ->where("products.".Products_Model_Detail::COLUMN_PRODUCT_ID_ITEM, $this->module()->getId())
      ->order(Products_Model_Detail::COLUMN_PRODUCT_LABEL, Db::ORDER_ASC);

		$returArray = $this->getDb()->fetchAll($sqlSelect);
		return $returArray;
	}

   public function getLastChange() {
      $sqlSelect = $this->getDb()->select()
      ->table(Db::table(Products_Model_Detail::DB_TABLE))
      ->colums(Products_Model_Detail::COLUMN_PRODUCT_TIME)
      ->limit(0, 1)
      ->order(Products_Model_Detail::COLUMN_PRODUCT_TIME, Db::ORDER_DESC);

      $returArray = $this->getDb()->fetchObject($sqlSelect);

      if(!empty ($returArray)){
         $returArray = $returArray->{Products_Model_Detail::COLUMN_PRODUCT_TIME};
      }
		return $returArray;
   }
}
?>