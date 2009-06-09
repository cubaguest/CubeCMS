<?php
/*
 * Třída modelu s listem Novinek
 */
class NewsDetailModel extends DbModel {
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

   private $newsLabel = null;

   private $newsText = null;
   
   private $newsId = null;

   private $newsIdUser = null;


   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem novinky
    * @param array -- pole s textem novinky
    * @param boolean -- id uživatele
    */
   public function saveNewNews($newsLabels, $newsTexts, $idUser = 0) {
      $newsArr = $this->createValuesArray(self::COLUMN_NEWS_LABEL, $newsLabels,
                                          self::COLUMN_NEWS_TEXT, $newsTexts,
                                          self::COLUMN_NEWS_ID_ITEM, $this->module()->getId(),
                                          self::COLUMN_NEWS_ID_USER, $idUser,
                                          self::COLUMN_NEWS_TIME, time());

      $sqlInsert = $this->getDb()->insert()->table($this->module()->getDbTable())
      ->colums(array_keys($newsArr))
      ->values(array_values($newsArr));
//      //		Vložení do db
      if($this->getDb()->query($sqlInsert)){
         return true;
      } else {
         return false;
      }
   }

   /**
    * Metoda vrací novinku podle zadaného ID a v aktuálním jazyku
    *
    * @param integer -- id novinky
    * @return array -- pole s novinkou
    */
   public function getNewsDetailSelLang($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->module()->getDbTable(), 'news')
      ->colums(array(self::COLUMN_NEWS_LABEL =>"IFNULL(".self::COLUMN_NEWS_LABEL_LANG_PREFIX
            .Locale::getLang().",".self::COLUMN_NEWS_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
            self::COLUMN_NEWS_TEXT =>"IFNULL(".self::COLUMN_NEWS_TEXT_LANG_PREFIX.Locale::getLang()
            .",".self::COLUMN_NEWS_TEXT_LANG_PREFIX.Locale::getDefaultLang().")",
            self::COLUMN_NEWS_TIME, self::COLUMN_NEWS_ID_NEW, self::COLUMN_NEWS_ID_USER))
      ->join(array('user' => $this->getUserTable()),
         array('news' => self::COLUMN_NEWS_ID_USER, self::COLUMN_ISER_ID),
         null, self::COLUMN_USER_NAME)
//      ->join(array('user' => $this->getUserTable()), 'news.'.self::COLUMN_NEWS_ID_USER.' = user.'.self::COLUMN_ISER_ID, null, self::COLUMN_USER_NAME)
      ->where('news.'.self::COLUMN_NEWS_ID_ITEM, $this->module()->getId())
      ->where('news.'.self::COLUMN_NEWS_ID_NEW, $id)
      ->where('news.'.self::COLUMN_NEWS_DELETED, (int)false);

      $news = $this->getDb()->fetchAssoc($sqlSelect, true);

      $this->newsId = $news[self::COLUMN_NEWS_ID_NEW];
      $this->newsIdUser = $news[self::COLUMN_NEWS_ID_USER];

      return $news;
   }

   public function getLabelsLangs() {
      return $this->newsLabel;
   }

   public function getTextsLangs() {
      return $this->newsText;
   }

   public function getId() {
      return $this->newsId;
   }

   public function getIdUser() {
      return $this->newsIdUser;
   }

   /**
    * Metoda vrací novinku podle zadaného ID ve všech jazycích
    *
    * @param integer -- id novinky
    * @return array -- pole s novinkou
    */
   public function getNewsDetailAllLangs($id) {
      //		načtení novinky z db
      $sqlSelect = $this->getDb()->select()
      ->table($this->module()->getDbTable())
      ->colums(Db::COLUMN_ALL)
      ->where(self::COLUMN_NEWS_ID_ITEM, $this->module()->getId())
      ->where(self::COLUMN_NEWS_ID_NEW, $id)
      ->where(self::COLUMN_NEWS_DELETED, (int)false);

      $news = $this->getDb()->fetchAssoc($sqlSelect);

      $news = $this->parseDbValuesToArray($news, array(self::COLUMN_NEWS_LABEL,
               self::COLUMN_NEWS_TEXT));

      $this->newsText = $news[self::COLUMN_NEWS_TEXT];
      $this->newsLabel = $news[self::COLUMN_NEWS_LABEL];
      $this->newsId = $news[self::COLUMN_NEWS_ID_NEW];

      return $news;
   }

   /**
    * Metoda uloží upravenou ovinku do db
    *
    * @param array -- pole s detaily novinky
    */
   public function saveEditNews($newsLabels, $newsTexts, $idNews) {
      $newsArr = $this->createValuesArray(self::COLUMN_NEWS_LABEL, $newsLabels,
                                          self::COLUMN_NEWS_TEXT, $newsTexts);

      $sqlInsert = $this->getDb()->update()->table($this->module()->getDbTable())
            ->set($newsArr)
            ->where(self::COLUMN_NEWS_ID_NEW, $idNews);

      // vložení do db
      if($this->getDb()->query($sqlInsert)){
         return true;
      } else {
         return false;
      };
   }

   public function deleteNews($idNews) {
      //			smazání novinky
      $sqlUpdate = $this->getDb()->update()->table($this->module()->getDbTable())
      ->set(array(self::COLUMN_NEWS_DELETED => (int)true))
      ->where(self::COLUMN_NEWS_ID_NEW." = ".$idNews);

      if($this->getDb()->query($sqlUpdate)){
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