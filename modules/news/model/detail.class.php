<?php
/*
 * Třída modelu s listem Novinek
 */
class News_Model_Detail extends Model_PDO {
/**
 * Tabulka s detaily
 */
   const DB_TABLE = 'news';

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
   // INSERT INTO `moravaokno_news` ( `label_cs` , `label_en` , `text_cs` , `text_en` , `id_item` , `id_user` , `time` )
   //VALUES ( 'pokuis', NULL, 'texttt', NULL, '8', '1', 1248730583)

      $this->setIUValues(array(self::COLUMN_NEWS_LABEL => $newsLabels,
          self::COLUMN_NEWS_TEXT => $newsTexts,
          self::COLUMN_NEWS_ID_ITEM => $this->module()->getId(),
          self::COLUMN_NEWS_ID_USER => $idUser,
          self::COLUMN_NEWS_TIME => time()));
//echo ("INSERT INTO ".Db_PDO::table(News_Model_Detail::DB_TABLE)
//          ." {$this->getInsertLabels()}"
//          ." VALUES {$this->getInsertValues()}");
      $dbc = new Db_PDO();
      return $dbc->exec("INSERT INTO ".Db_PDO::table(News_Model_Detail::DB_TABLE)
          ." {$this->getInsertLabels()}"
          ." VALUES {$this->getInsertValues()}");
   }

   /**
    * Metoda vrací novinku podle zadaného ID a v aktuálním jazyku
    *
    * @param integer -- id novinky
    * @return array -- pole s novinkou
    */
   //   public function getNewsDetailSelLang($id) {
   public function getNewsDetail($id) {
   //      SELECT IFNULL(label_en,label_cs) AS label, IFNULL(text_en,text_cs) AS text,
   //      news.`time`, news.`id_new`, news.`id_user`, user.`username`
   //      FROM `moravaokno_news` AS news JOIN `moravaokno_users` AS user
   //      ON news.id_user = user.id_user
   //      WHERE (news.id_item = '8') AND (news.id_new = 6) AND (news.deleted = 0)
   //		načtení novinky z db
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT news.*, user.`".Model_Users::COLUMN_USERNAME."`
      FROM ".Db_PDO::table(News_Model_Detail::DB_TABLE)." AS news
      JOIN ".Db_PDO::table(Model_Users::DB_TABLE)." AS user ON news.".self::COLUMN_NEWS_ID_USER
          ." = user.".Model_Users::COLUMN_ID." WHERE (news.".self::COLUMN_NEWS_ID_NEW." = :id)");

      $dbst->bindValue('id', $id, PDO::PARAM_INT);
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);

      return $dbst->fetch();
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
    * Metoda uloží upravenou ovinku do db
    *
    * @param array -- pole s detaily novinky
    */
   public function saveEditNews($newsLabels, $newsTexts, $idNews) {
   //UPDATE `moravaokno_news` SET `label_cs`= 'Saul Griffith's lofty', `label_en`=NULL,
   //`text_cs`= ' text', `text_en`=NULL WHERE (id_new = '6')
      $this->setIUValues(array(self::COLUMN_NEWS_LABEL => $newsLabels,
          self::COLUMN_NEWS_TEXT => $newsTexts));

      $dbc = new Db_PDO();
      return $dbc->exec("UPDATE ".Db_PDO::table(News_Model_Detail::DB_TABLE)
             ." SET {$this->getUpdateValues()} "
             ."WHERE (".self::COLUMN_NEWS_ID_NEW." = ".$dbc->quote($idNews, PDO::PARAM_INT).")");
   }

   public function deleteNews($idNews) {
   //			smazání novinky
      $dbc = new Db_PDO();
      return $dbc->exec("DELETE FROM ".Db_PDO::table(News_Model_Detail::DB_TABLE)
             ." WHERE (".self::COLUMN_NEWS_ID_NEW." = ".$dbc->quote($idNews, PDO::PARAM_INT).")");
   }
}
?>