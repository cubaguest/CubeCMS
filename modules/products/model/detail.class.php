<?php
/*
 * Třída modelu detailem článku
 */
class Products_Model_Detail extends Model_Db {
	/**
	 * Názvy sloupců v databázi
	 */
	const COLUMN_PRODUCT_LABEL = 'label';
	const COLUMN_PRODUCT_TEXT = 'text';
	const COLUMN_PRODUCT_TIME = 'add_time';
	const COLUMN_PRODUCT_EDIT_TIME = 'edit_time';
	const COLUMN_PRODUCT_ID_USER = 'id_user';
	const COLUMN_PRODUCT_ID_ITEM = 'id_item';
	const COLUMN_PRODUCT_ID = 'id_product';

	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
	const COLUMN_USER_NAME = 'username';
	const COLUMN_USER_ID =	 'id_user';

   private $productLabel = null;
//
   private $productText = null;
//
   private $productId = null;
//
   private $articleIdUser = null;
   private $lastEditIdProduct = null;

   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem článku
    * @param array -- pole s textem článku
    * @param boolean -- id uživatele
    */
   public function saveNewProduct($productsLabels, $productsTexts, $idUser = 0) {
      $productArr = $this->createValuesArray(self::COLUMN_PRODUCT_LABEL, $productsLabels,
                                          self::COLUMN_PRODUCT_TEXT, $productsTexts,
                                          self::COLUMN_PRODUCT_ID_ITEM, $this->module()->getId(),
                                          self::COLUMN_PRODUCT_ID_USER, $idUser,
                                          self::COLUMN_PRODUCT_TIME, time(),
                                          self::COLUMN_PRODUCT_EDIT_TIME, time());

      $sqlInsert = $this->getDb()->insert()->table($this->module()->getDbTable())
      ->colums(array_keys($productArr))
      ->values(array_values($productArr));
//      //		Vložení do db
      if($this->getDb()->query($sqlInsert)){
         $this->lastEditIdProduct = $this->getDb()->getLastInsertedId();
         return true;
      } else {
         return false;
      }
   }

   /**
    * Metoda vrací id posledního vloženého článku
    * @return integer -- id článku
    */
   public function getLastInsertedId() {
      return $this->lastEditIdProduct;
   }

   /**
    * Metoda vrací článek podle zadaného ID a v aktuálním jazyku
    *
    * @param integer -- id článku
    * @return array -- pole s článkem
    */
   public function getProductDetailSelLang($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->module()->getDbTable(), 'article')
      ->colums(array(self::COLUMN_PRODUCT_LABEL =>"IFNULL(".self::COLUMN_PRODUCT_LABEL.'_'.Locale::getLang()
            .",".self::COLUMN_PRODUCT_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_PRODUCT_TEXT =>"IFNULL(".self::COLUMN_PRODUCT_TEXT.'_'.Locale::getLang()
            .",".self::COLUMN_PRODUCT_TEXT.'_'.Locale::getDefaultLang().")",
            self::COLUMN_PRODUCT_TIME, self::COLUMN_PRODUCT_ID, self::COLUMN_PRODUCT_ID_USER))
      ->where('article.'.self::COLUMN_PRODUCT_ID_ITEM, $this->module()->getId())
      ->where('article.'.self::COLUMN_PRODUCT_ID, $id);

      $article = $this->getDb()->fetchAssoc($sqlSelect);

      $this->productId = $article[self::COLUMN_PRODUCT_ID];
      $this->articleIdUser = $article[self::COLUMN_PRODUCT_ID_USER];

      return $article;
   }

   public function getLabelsLangs() {
      return $this->productLabel;
   }

   public function getTextsLangs() {
      return $this->productText;
   }

   public function getId() {
      return $this->productId;
   }

   public function getIdUser() {
      return $this->articleIdUser;
   }

   /**
    * Metoda vrací novinku podle zadaného ID ve všech jazycích
    *
    * @param integer -- id novinky
    * @return array -- pole s novinkou
    */
   public function getProductDetailAllLangs($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->module()->getDbTable())
      ->colums(Db::COLUMN_ALL)
      ->where(self::COLUMN_PRODUCT_ID_ITEM, $this->module()->getId())
      ->where(self::COLUMN_PRODUCT_ID, $id);

      $product = $this->getDb()->fetchAssoc($sqlSelect);

      if(empty ($product)){
         throw new UnexpectedValueException(_('Zadaný článek neexistuje'), 1);
      }
      $product = $this->parseDbValuesToArray($product, array(self::COLUMN_PRODUCT_LABEL,
               self::COLUMN_PRODUCT_TEXT));

      $this->productText = $product[self::COLUMN_PRODUCT_TEXT];
      $this->productLabel = $product[self::COLUMN_PRODUCT_LABEL];
      $this->productId = $product[self::COLUMN_PRODUCT_ID];

      return $product;
   }

   /**
    * Metoda uloží upravený článek do db
    *
    * @param array -- pole s detaily článku
    */
   public function saveEditProduct($labels, $texts, $id) {
      $productArr = $this->createValuesArray(self::COLUMN_PRODUCT_LABEL, $labels,
                                          self::COLUMN_PRODUCT_TEXT, $texts,
                                          self::COLUMN_PRODUCT_EDIT_TIME, time());

      $sqlInsert = $this->getDb()->update()->table($this->module()->getDbTable())
            ->set($productArr)
            ->where(self::COLUMN_PRODUCT_ID, $id);

      // vložení do db
      if($this->getDb()->query($sqlInsert)){
         return true;
      } else {
         return false;
      };
   }

   /**
    * Metoda smaže zadaný článek
    * @param integer $idProduct
    * @return bool
    */
   public function deleteProduct($idProduct) {
      $sqlDelete = $this->getDb()
      ->delete()->table($this->module()->getDbTable())
      ->where(self::COLUMN_PRODUCT_ID,$idProduct);

      if($this->getDb()->query($sqlDelete)){
         return true;
      } else {
         return false;
      };
   }
}

?>