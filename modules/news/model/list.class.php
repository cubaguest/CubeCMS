<?php
/*
 * Třída modelu s listem Novinek
 */
class News_Model_List extends Model_PDO {
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

   /**
    * Celkový počet novinek
    * @var integer
    */
   private $allNewsCount = 0;
   private $countNewsLoaded = false;

   /**
    * Metoda vrací počet novinek
    *
    * @return integer -- počet novinek
    */
   public function getCountNews() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT count(*) as 'cnt' FROM ".Db_PDO::table(News_Model_Detail::DB_TABLE)
         ." WHERE ".News_Model_Detail::COLUMN_NEWS_ID_ITEM." = ".$this->idItem()
         ." AND ".News_Model_Detail::COLUMN_NEWS_DELETED." = 0");
      $count = $dbst->fetch(PDO::FETCH_OBJ);
      return $count->cnt;

//"select count (*) as 'pocet' FROM tabulka WHERE id='26' AND et='1'";
   //		if(!$this->countNewsLoaded){
   //         $sqlCount = $this->getDb()->select()->table(Db::table(News_Model_Detail::DB_TABLE))
   //         ->colums(array("count"=>"COUNT(*)"))
   //         ->where(self::COLUMN_NEWS_ID_ITEM, $this->module()->getId())
   //			->where(self::COLUMN_NEWS_DELETED, (int)false);
   //
   //			$count = $this->getDb()->fetchObject($sqlCount);
   //			$this->allNewsCount = $count->count;
   //			$this->countNewsLoaded = true;
   //		}
   //		return $this->allNewsCount;
   }

   /**
    * Metoda vrací pole s vybranými novinkami
    *
    * @return PDOStatement -- objekt PDOStatement s prvky
    */
   public function getSelectedListNews($from, $count=5) {
   //      SELECT IFNULL(label_cs, label_cs) AS label, IFNULL(text_cs, text_cs) AS text,
   //      news.`id_user`, news.`id_new`, news.`time`, user.`username`
   //      FROM `moravaokno_news` AS news
   //      JOIN `moravaokno_users` AS user ON news.id_user = user.id_user
   //      WHERE (news.id_item = '8') AND (news.deleted = 0)
   //      ORDER BY news.time DESC
   //      LIMIT 0, 10

      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT news.*, user.`".Model_Users::COLUMN_USERNAME."`
      FROM ".Db_PDO::table(News_Model_Detail::DB_TABLE)." AS news
      JOIN ".Db_PDO::table(Model_Users::DB_TABLE)." AS user ON news.id_user = user.id_user
      WHERE (news.id_item = :id_item) AND (news.deleted = 0)
      ORDER BY news.time DESC LIMIT :from, :count");

      $dbst->bindValue('id_item', $this->idItem(), PDO::PARAM_INT);
      $dbst->bindValue('from', $from, PDO::PARAM_INT);
      $dbst->bindValue('count', $count, PDO::PARAM_INT);
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst;
   }

   /**
    * Metoda vrací pole s vybranými novinkami
    *
    * @return array -- pole novinek
    */
   public function getListNews() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(News_Model_Detail::DB_TABLE)."
      WHERE (id_item = ".$this->module()->getId().") AND (deleted = 0)
      ORDER BY time DESC");

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst;
   }

   public function getLastChange() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT ".News_Model_Detail::COLUMN_NEWS_TIME
         ." FROM ".Db_PDO::table(News_Model_Detail::DB_TABLE)
         ." WHERE (id_item = ".$this->module()->getId().") AND (deleted = 0)"
         ." ORDER BY time DESC LIMIT 0,1");
      
      $arr = $dbst->fetch();

      return $arr[News_Model_Detail::COLUMN_NEWS_TIME];
   }

}

?>