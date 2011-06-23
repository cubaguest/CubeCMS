<?php
/*
 * Třída modelu detailem článku
*/
class Bands_Model extends Model_PDO {
   const DB_TABLE = 'bands';

   const CLIPS_SEPARATOR = ';;';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_NAME = 'name';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_IMAGE = 'image';
   const COLUMN_CLIPS = 'clips';
   const COLUMN_URLKEY = 'urlkey';
   const COLUMN_ADD_TIME = 'add_time';
   const COLUMN_EDIT_TIME = 'edit_time';
   const COLUMN_ID_USER_LAST_EDIT = 'id_user_last_edit';
   const COLUMN_ID = 'id_band';
   const COLUMN_SHOWED = 'viewed';
   const COLUMN_PUBLIC = 'public';
   
   private $usersTable = null;

   public function __construct()
   {
      $modelUsers = new Model_Users();
      $this->usersTable = $modelUsers->getTableName();
      parent::__construct();
   }

   /**
    * Metoda uloží novinku do db
    *
    * @param array -- pole s nadpisem článku
    * @param array -- pole s textem článku
    * @param boolean -- id uživatele
    */
   public function saveBand($name, $text, $urlKey, $image, $clips, $public = true, $id = null) {
      // generování unikátního klíče
      $urlKey = $this->generateUrlKeys($urlKey, self::DB_TABLE, $name,
              self::COLUMN_URLKEY, self::COLUMN_ID ,$id);

      if(!empty ($clips) AND is_array($clips)){
         $clips = implode(self::CLIPS_SEPARATOR, $clips);
      }
      if(empty ($clips) OR $clips == null){
         $clips = null;
      }

      $dbc = new Db_PDO();
      if($id !== null) {
         $sql = "UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".self::COLUMN_NAME."= :name, ".self::COLUMN_TEXT."= :text, "
                 .self::COLUMN_TEXT_CLEAR."= :textclear, ".self::COLUMN_URLKEY."= :urlkey, "
                 .self::COLUMN_EDIT_TIME."= :edittime, ".self::COLUMN_PUBLIC."= :public, "
                 .self::COLUMN_CLIPS."= :clips, ".self::COLUMN_ID_USER_LAST_EDIT."= :iduser";
         if($image != null) {
            $sql .= ", ".self::COLUMN_IMAGE."= :image";
         }
         $sql .=" WHERE ".self::COLUMN_ID." = :id";

         $dbst = $dbc->prepare($sql);

         $dbst->bindParam(':id', $id, PDO::PARAM_INT);
         $dbst->bindValue(':name', $name, PDO::PARAM_STR);
         $dbst->bindValue(':text', $text, PDO::PARAM_STR);
         $dbst->bindValue(':textclear', vve_strip_tags($text), PDO::PARAM_STR);
         $dbst->bindValue(':clips', $clips, PDO::PARAM_STR|PDO::PARAM_NULL);
         $dbst->bindValue(':urlkey', $urlKey, PDO::PARAM_STR);
         $dbst->bindValue(':edittime', date("Y-m-d H:i:s"), PDO::PARAM_STR);
         $dbst->bindValue(':public', $public, PDO::PARAM_BOOL);
         $dbst->bindValue(':iduser', Auth::getUserId(), PDO::PARAM_INT);
         if($image != null) {
            $dbst->bindValue(':image', $image, PDO::PARAM_STR);
         }
         return $dbst->execute();
      } else {
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)." "
                 ."(".self::COLUMN_NAME.",". self::COLUMN_TEXT.","
                 .self::COLUMN_TEXT_CLEAR.",". self::COLUMN_URLKEY.","
                 .self::COLUMN_ADD_TIME.",". self::COLUMN_PUBLIC.","
                 .self::COLUMN_IMAGE.",". self::COLUMN_CLIPS.","
                 .self::COLUMN_ID_USER_LAST_EDIT.")"
                 ." VALUES (:name, :text, :textclear, :urlkey, :addtime, :public, :image, :clips, :iduser)");

         $dbst->bindValue(':name', $name, PDO::PARAM_STR);
         $dbst->bindValue(':text', $text, PDO::PARAM_STR);
         $dbst->bindValue(':textclear', vve_strip_tags($text), PDO::PARAM_STR);
         $dbst->bindValue(':clips', $clips, PDO::PARAM_STR|PDO::PARAM_NULL);
         $dbst->bindValue(':urlkey', $urlKey, PDO::PARAM_STR);
         $dbst->bindValue(':addtime', date("Y-m-d H:i:s"), PDO::PARAM_STR);
         $dbst->bindValue(':public', $public, PDO::PARAM_BOOL);
         $dbst->bindValue(':image', $image, PDO::PARAM_STR);
         $dbst->bindValue(':iduser', (int)Auth::getUserId(), PDO::PARAM_INT);
         $dbst->execute();

         return $dbc->lastInsertId();
      }
   }

   /**
    * Metoda vrací počet skupin
    *
    * @return integer -- počet
    */
   public function getCount($onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $dbst = $dbc->query("SELECT COUNT(".self::COLUMN_ID.")"
                 ." FROM ".Db_PDO::table(self::DB_TABLE)
                 ." WHERE (".self::COLUMN_PUBLIC." = 1)");
      } else {
         $dbst = $dbc->query("SELECT COUNT(".self::COLUMN_ID.") FROM ".Db_PDO::table(self::DB_TABLE));
      }
      $count = $dbst->fetch();
      return $count[0];
   }

   /**
    * Metoda vrací pole se všemi skupinami
    * @return array -- pole skupin
    */
   public function getList($fromRow = 0, $rowsCount = 100, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $wherePub = " WHERE (".Articles_Model_Detail::COLUMN_PUBLIC." = 1)";
      } else {
         $wherePub = null;
      }
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              . $wherePub // public
              ." ORDER BY ".self::COLUMN_NAME." ASC"
              ." LIMIT :fromRow, :rowCount ");
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':rowCount', (int)$rowsCount, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst->fetchAll();
   }

   /**
    * Metoda přičte přečtení skupiny
    * @return int $id -- id skupiny
    */
   public function addShowCount($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
              ." SET ".self::COLUMN_SHOWED." = ".self::COLUMN_SHOWED."+1"
              ." WHERE (".self::COLUMN_ID." = :idb)");
      $dbst->bindParam(':idb', $id, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda vrací skupinu podle zadaného klíče
    *
    * @param string -- url klíč skupiny
    * @return PDOStatement -- pole s skupinou
    */
   public function getBand($urlKey) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT tband.*, tuser.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(self::DB_TABLE)." AS tband"
              ." JOIN ".$this->usersTable." AS tuser ON tband.".self::COLUMN_ID_USER_LAST_EDIT." = tuser.".Model_Users::COLUMN_ID
              ." WHERE (tband.".self::COLUMN_URLKEY." = :urlkey)"
              ." LIMIT 0, 1");

//         $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
//         ." FROM ".Db_PDO::table(self::DB_TABLE)." AS article"
//         ." JOIN ".Model_Users::getUsersTable()." AS user ON article.".self::COLUMN_ID_USER
//         ." = user.".Model_Users::COLUMN_ID
      $dbst->bindParam(':urlkey', $urlKey, PDO::PARAM_STR);
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   /**
    * Metoda vrací článek podle zadaného ID
    *
    * @param int -- id článku
    * @return PDOStatement -- pole s článkem
    */
   public function getBandById($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS article"
              ." WHERE (".self::COLUMN_ID." = :id)".
              " LIMIT 0, 1");
      $dbst->bindParam(':id', $id, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   /**
    * Metoda smaže zadanou kapelu
    * @param integer $id
    * @return bool
    */
   public function deleteBand($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COLUMN_ID ." = :id)");
      $dbst->bindParam(':id', $id, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda provede náhodný výběr klipu
    * @return <type>
    */
   public function getRandomCLip() {
      $retClip = null;

      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (ISNULL(".self::COLUMN_CLIPS.") = 0)"
              ." ORDER BY RAND() LIMIT 0, 1");
      $dbst->execute();
      $fetch = $dbst->fetchObject();
      if($fetch != false){
         $clipsArr = explode(self::CLIPS_SEPARATOR, (string)$fetch->{self::COLUMN_CLIPS});
         $fetch->{self::COLUMN_CLIPS} = $clipsArr[rand(0, count($clipsArr)-1)];
      }

      return $fetch;
   }

   /**
    * Metoda provede náhodný výběr skupiny
    * @return Object
    */
   public function getRandomBand() {
      $retBand = null;
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." ORDER BY RAND() LIMIT 0, 1");
      $dbst->execute();
      return $dbst->fetchObject();
   }

   /**
    * Metoda vymaže články podle zadaného id kategorie
    * @param int $id -- id kategorie
    */
//   public function deleteArticleByCat($id) {
//      $dbc = new Db_PDO();
//      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)
//              ." WHERE (".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat )");
//      $dbst->bindValue(':idcat', (int)$id, PDO::PARAM_INT);
//      $dbst->execute();
//      return $dbst;
//   }

   /**
    * Metoda nastaví změnu článku
    * @param int $id -- id článku
    */
   public function setLastChange($idArt) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
              ." SET `".self::COLUMN_EDIT_TIME."` = NOW()"
              ." WHERE (".self::COLUMN_ID." = :idart)");
      $dbst->bindParam(':idart', $idArt, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda vyhledává články -- je tu kvůli zbytečnému nenačítání modelu List
    * @param integer $idCat
    * @param string $string
    * @param bool $publicOnly
    * @return PDOStatement
    */
   public function search($string, $publicOnly = true) {
      $dbc = new Db_PDO();
      $clabel = self::COLUMN_NAME;
      $ctext = self::COLUMN_TEXT_CLEAR;

      $wherePub = null;
      if($publicOnly) {
         $wherePub = ' AND '.self::COLUMN_PUBLIC.' = 1';
      }

      $dbst = $dbc->prepare('SELECT *, ('.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER).' * MATCH('.$clabel.') AGAINST (:sstring)'
              .' + MATCH('.$ctext.') AGAINST (:sstring)) as '.Search::COLUMN_RELEVATION
              .' FROM '.Db_PDO::table(self::DB_TABLE)
              .' WHERE MATCH('.$clabel.', '.$ctext.') AGAINST (:sstring IN BOOLEAN MODE)'
              .$wherePub // Public articles
              .' ORDER BY '.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER)
              .' * MATCH('.$clabel.') AGAINST (:sstring) + MATCH('.$ctext.') AGAINST (:sstring) DESC');

      $dbst->bindValue(':sstring', $string, PDO::PARAM_STR);
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->execute();
      return $dbst;
   }
}

?>