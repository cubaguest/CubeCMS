<?php
/*
 * Třída modelu detailem článku
 */
class ProductDetailModel extends DbModel {
	/**
	 * Názvy sloupců v databázi
	 */
	const COLUMN_PRODUCT_LABEL = 'label';
	const COLUMN_PRODUCT_TEXT = 'text';
	const COLUMN_PRODUCT_FILE = 'main_image';
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

   private $productText = null;

   private $productId = null;

   private $lastEditIdProduct = null;

   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem článku
    * @param array -- pole s textem článku
    * @param boolean -- id uživatele
    */
   public function saveNewProduct($articleLabels, $articleTexts, $file, $idUser = 0) {
      $articleArr = $this->createValuesArray(self::COLUMN_PRODUCT_LABEL, $articleLabels,
                                          self::COLUMN_PRODUCT_TEXT, $articleTexts,
                                          self::COLUMN_PRODUCT_FILE, $file,
                                          self::COLUMN_PRODUCT_ID_ITEM, $this->getModule()->getId(),
                                          self::COLUMN_PRODUCT_ID_USER, $idUser,
                                          self::COLUMN_PRODUCT_TIME, time(),
                                          self::COLUMN_PRODUCT_EDIT_TIME, time());

      $sqlInsert = $this->getDb()->insert()->table($this->getModule()->getDbTable())
      ->colums(array_keys($articleArr))
      ->values(array_values($articleArr));
//    Vložení do db
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
    * Metoda vrací produkt podle zadaného ID a v aktuálním jazyku
    *
    * @param integer -- id produktu
    * @return array -- pole s produktem
    */
   public function getProductDetailSelLang($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->getModule()->getDbTable(), 'product')
      ->colums(array(self::COLUMN_PRODUCT_LABEL =>"IFNULL(".self::COLUMN_PRODUCT_LABEL.'_'.Locale::getLang()
            .",".self::COLUMN_PRODUCT_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_PRODUCT_TEXT =>"IFNULL(".self::COLUMN_PRODUCT_TEXT.'_'.Locale::getLang()
            .",".self::COLUMN_PRODUCT_TEXT.'_'.Locale::getDefaultLang().")",
            self::COLUMN_PRODUCT_ID, self::COLUMN_PRODUCT_FILE))
      ->where('product.'.self::COLUMN_PRODUCT_ID_ITEM, $this->getModule()->getId())
      ->where('product.'.self::COLUMN_PRODUCT_ID, $id);

      $product = $this->getDb()->fetchAssoc($sqlSelect);

      $this->productId = $product[self::COLUMN_PRODUCT_ID];

      return $product;
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
      return $this->productIdUser;
   }

   /**
    * Metoda vrací produkt podle zadaného ID ve všech jazycích
    *
    * @param integer -- id produktu
    * @return array -- pole s produktem
    */
   public function getProductDetailAllLangs($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->getModule()->getDbTable())
      ->colums(Db::COLUMN_ALL)
      ->where(self::COLUMN_PRODUCT_ID_ITEM, $this->getModule()->getId())
      ->where(self::COLUMN_PRODUCT_ID, $id);

      $product = $this->getDb()->fetchAssoc($sqlSelect);

      if(empty ($product)){
         throw new UnexpectedValueException(_('Zadaný produkt neexistuje'), 1);
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
   public function saveEditProduct($labels, $texts, $file, $id) {
      if($file == null){
      $articleArr = $this->createValuesArray(self::COLUMN_PRODUCT_LABEL, $labels,
                                          self::COLUMN_PRODUCT_TEXT, $texts,
                                          self::COLUMN_PRODUCT_EDIT_TIME, time());
      } else {
         $articleArr = $this->createValuesArray(self::COLUMN_PRODUCT_LABEL, $labels,
                                          self::COLUMN_PRODUCT_TEXT, $texts,
                                          self::COLUMN_PRODUCT_FILE, $file,
                                          self::COLUMN_PRODUCT_EDIT_TIME, time());
      }

      $sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
            ->set($articleArr)
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
    * @param integer $idArticle
    * @return bool
    */
   public function deleteArticle($idArticle) {
      $sqlDelete = $this->getDb()
      ->delete()->table($this->getModule()->getDbTable())
      ->where(self::COLUMN_PRODUCT_ID,$idArticle);

      if($this->getDb()->query($sqlDelete)){
         return true;
      } else {
         return false;
      };
   }

   private function getUserTable() {
      $tableUsers = AppCore::sysConfig()->getOptionValue(Auth::CONFIG_USERS_TABLE_NAME, Config::SECTION_DB_TABLES);
      return $tableUsers;
   }

}

?>