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
//
   private $productText = null;
//
   private $productId = null;
//
   private $productIdUser = null;
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
    * Metoda vrací článek podle zadaného ID a v aktuálním jazyku
    *
    * @param integer -- id článku
    * @return array -- pole s článkem
    */
   public function getArticleDetailSelLang($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->getModule()->getDbTable(), 'article')
      ->colums(array(self::COLUMN_PRODUCT_LABEL =>"IFNULL(".self::COLUMN_PRODUCT_LABEL.'_'.Locale::getLang()
            .",".self::COLUMN_PRODUCT_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_PRODUCT_TEXT =>"IFNULL(".self::COLUMN_PRODUCT_TEXT.'_'.Locale::getLang()
            .",".self::COLUMN_PRODUCT_TEXT.'_'.Locale::getDefaultLang().")",
            self::COLUMN_PRODUCT_TIME, self::COLUMN_PRODUCT_ID, self::COLUMN_PRODUCT_ID_USER))
      ->join(array('user' => $this->getUserTable()),
         array('article' => self::COLUMN_PRODUCT_ID_USER, self::COLUMN_USER_ID),
         null, self::COLUMN_USER_NAME)
//      ->join(array('user' => $this->getUserTable()), 'news.'.self::COLUMN_NEWS_ID_USER.' = user.'.self::COLUMN_ISER_ID, null, self::COLUMN_USER_NAME)
      ->where('article.'.self::COLUMN_PRODUCT_ID_ITEM, $this->getModule()->getId())
      ->where('article.'.self::COLUMN_PRODUCT_ID, $id);

      $article = $this->getDb()->fetchAssoc($sqlSelect);

      $this->productId = $article[self::COLUMN_PRODUCT_ID];
      $this->productIdUser = $article[self::COLUMN_PRODUCT_ID_USER];

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
      return $this->productIdUser;
   }

   /**
    * Metoda vrací novinku podle zadaného ID ve všech jazycích
    *
    * @param integer -- id novinky
    * @return array -- pole s novinkou
    */
   public function getArticleDetailAllLangs($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->getModule()->getDbTable())
      ->colums(Db::COLUMN_ALL)
      ->where(self::COLUMN_PRODUCT_ID_ITEM, $this->getModule()->getId())
      ->where(self::COLUMN_PRODUCT_ID, $id);

      $article = $this->getDb()->fetchAssoc($sqlSelect);

      if(empty ($article)){
         throw new UnexpectedValueException(_('Zadaný článek neexistuje'), 1);
      }
      $article = $this->parseDbValuesToArray($article, array(self::COLUMN_PRODUCT_LABEL,
               self::COLUMN_PRODUCT_TEXT));

      $this->productText = $article[self::COLUMN_PRODUCT_TEXT];
      $this->productLabel = $article[self::COLUMN_PRODUCT_LABEL];
      $this->productId = $article[self::COLUMN_PRODUCT_ID];

      return $article;
   }

   /**
    * Metoda uloží upravený článek do db
    *
    * @param array -- pole s detaily článku
    */
   public function saveEditArticle($labels, $texts, $id) {
      $articleArr = $this->createValuesArray(self::COLUMN_PRODUCT_LABEL, $labels,
                                          self::COLUMN_PRODUCT_TEXT, $texts,
                                          self::COLUMN_PRODUCT_EDIT_TIME, time());

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