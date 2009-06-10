<?php
/*
 * Třída modelu detailem článku
 */
class ArticleDetailModel extends DbModel {
	/**
	 * Názvy sloupců v databázi
	 */
	const COLUMN_ARTICLE_LABEL = 'label';
	const COLUMN_ARTICLE_TEXT = 'text';
	const COLUMN_ARTICLE_TIME = 'add_time';
	const COLUMN_ARTICLE_EDIT_TIME = 'edit_time';
	const COLUMN_ARTICLE_ID_USER = 'id_user';
	const COLUMN_ARTICLE_ID_ITEM = 'id_item';
	const COLUMN_ARTICLE_ID = 'id_article';

	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
	const COLUMN_USER_NAME = 'username';
	const COLUMN_USER_ID =	 'id_user';

   private $articleLabel = null;
//
   private $articleText = null;
//
   private $articleId = null;
//
   private $articleIdUser = null;
   private $lastEditIdArticle = null;

   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem článku
    * @param array -- pole s textem článku
    * @param boolean -- id uživatele
    */
   public function saveNewArticle($articleLabels, $articleTexts, $idUser = 0) {
      $articleArr = $this->createValuesArray(self::COLUMN_ARTICLE_LABEL, $articleLabels,
                                          self::COLUMN_ARTICLE_TEXT, $articleTexts,
                                          self::COLUMN_ARTICLE_ID_ITEM, $this->getModule()->getId(),
                                          self::COLUMN_ARTICLE_ID_USER, $idUser,
                                          self::COLUMN_ARTICLE_TIME, time(),
                                          self::COLUMN_ARTICLE_EDIT_TIME, time());

      $sqlInsert = $this->getDb()->insert()->table($this->getModule()->getDbTable())
      ->colums(array_keys($articleArr))
      ->values(array_values($articleArr));
//      //		Vložení do db
      if($this->getDb()->query($sqlInsert)){
         $this->lastEditIdArticle = $this->getDb()->getLastInsertedId();
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
      return $this->lastEditIdArticle;
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
      ->table($this->module()->getDbTable(), 'article')
      ->colums(array(self::COLUMN_ARTICLE_LABEL =>"IFNULL(".self::COLUMN_ARTICLE_LABEL.'_'.Locale::getLang()
            .",".self::COLUMN_ARTICLE_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_ARTICLE_TEXT =>"IFNULL(".self::COLUMN_ARTICLE_TEXT.'_'.Locale::getLang()
            .",".self::COLUMN_ARTICLE_TEXT.'_'.Locale::getDefaultLang().")",
            self::COLUMN_ARTICLE_TIME, self::COLUMN_ARTICLE_ID, self::COLUMN_ARTICLE_ID_USER))
      ->join(array('user' => $this->getUserTable()),
         array('article' => self::COLUMN_ARTICLE_ID_USER, self::COLUMN_USER_ID),
         null, self::COLUMN_USER_NAME)
//      ->join(array('user' => $this->getUserTable()), 'news.'.self::COLUMN_NEWS_ID_USER.' = user.'.self::COLUMN_ISER_ID, null, self::COLUMN_USER_NAME)
      ->where('article.'.self::COLUMN_ARTICLE_ID_ITEM, $this->module()->getId())
      ->where('article.'.self::COLUMN_ARTICLE_ID, $id);

      $article = $this->getDb()->fetchAssoc($sqlSelect);

      $this->articleId = $article[self::COLUMN_ARTICLE_ID];
      $this->articleIdUser = $article[self::COLUMN_ARTICLE_ID_USER];

      return $article;
   }

   public function getLabelsLangs() {
      return $this->articleLabel;
   }

   public function getTextsLangs() {
      return $this->articleText;
   }

   public function getId() {
      return $this->articleId;
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
   public function getArticleDetailAllLangs($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->module()->getDbTable())
      ->colums(Db::COLUMN_ALL)
      ->where(self::COLUMN_ARTICLE_ID_ITEM, $this->module()->getId())
      ->where(self::COLUMN_ARTICLE_ID, $id);

      $article = $this->getDb()->fetchAssoc($sqlSelect);

      if(empty ($article)){
         throw new UnexpectedValueException(_('Zadaný článek neexistuje'), 1);
      }
      $article = $this->parseDbValuesToArray($article, array(self::COLUMN_ARTICLE_LABEL,
               self::COLUMN_ARTICLE_TEXT));

      $this->articleText = $article[self::COLUMN_ARTICLE_TEXT];
      $this->articleLabel = $article[self::COLUMN_ARTICLE_LABEL];
      $this->articleId = $article[self::COLUMN_ARTICLE_ID];

      return $article;
   }

   /**
    * Metoda uloží upravený článek do db
    *
    * @param array -- pole s detaily článku
    */
   public function saveEditArticle($labels, $texts, $id) {
      $articleArr = $this->createValuesArray(self::COLUMN_ARTICLE_LABEL, $labels,
                                          self::COLUMN_ARTICLE_TEXT, $texts,
                                          self::COLUMN_ARTICLE_EDIT_TIME, time());

      $sqlInsert = $this->getDb()->update()->table($this->module()->getDbTable())
            ->set($articleArr)
            ->where(self::COLUMN_ARTICLE_ID, $id);

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
      ->delete()->table($this->module()->getDbTable())
      ->where(self::COLUMN_ARTICLE_ID,$idArticle);

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