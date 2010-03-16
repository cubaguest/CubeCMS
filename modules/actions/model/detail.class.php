<?php
/*
 * Třída modelu s listem Novinek
*/
class Actions_Model_Detail extends Model_PDO {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'actions';

   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_NAME = 'name';
   const COLUMN_AUTHOR = 'author';
   const COLUMN_SUBANME = 'subname';
   const COLUMN_TEXT = 'text';
   const COLUMN_NOTE = 'note';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_URLKEY = 'urlkey';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_ID_CAT = 'id_category';
   const COLUMN_ID = 'id_action';
   const COLUMN_PUBLIC = 'public';
   const COLUMN_DATE_START = 'start_date';
   const COLUMN_DATE_STOP = 'stop_date';
   const COLUMN_IMAGE = 'image';
   const COLUMN_CHANGED = 'changed';
   const COLUMN_ADDED = 'time_add';
   const COLUMN_TIME = 'time';
   const COLUMN_PLACE = 'place';
   const COLUMN_PRICE = 'price';
   const COLUMN_PREPRICE = 'preprice';

   /**
    * Metoda uloží akci do db
    *
    * @param array -- pole s nadpisem novinky
    * @param array -- pole s textem novinky
    * @param boolean -- id uživatele
    */
   public function saveAction($name, $subname, $author, $text, $note, $urlkey, DateTime $dateFrom, $dateTo,
           $time = null, $place = null, $price = null, $preprice = null, $image = null, $idCat = 0,
           $idUser = 1, $public = true, $id = null) {
      $dbc = new Db_PDO();
      $dbc->beginTransaction();

      $urlkey = $this->generateUrlKeys($urlkey, self::DB_TABLE, $name,
              self::COLUMN_URLKEY, self::COLUMN_ID,$id);

      if($dateTo != null){
         $dt = $dateTo->format("Y-m-d");
      } else {
         $dt = null;
      }

      // globalní prvky
      $this->setIUValues(array(self::COLUMN_NAME => $name,self::COLUMN_TEXT => $text,
              self::COLUMN_SUBANME => $subname, self::COLUMN_AUTHOR => $author,
              self::COLUMN_TEXT_CLEAR => vve_strip_tags($text), self::COLUMN_NOTE => $note,
              self::COLUMN_URLKEY => $urlkey, self::COLUMN_PRICE => $price,self::COLUMN_PREPRICE => $preprice,
              self::COLUMN_PUBLIC => $public, self::COLUMN_IMAGE => $image,
              self::COLUMN_TIME => $time, self::COLUMN_PLACE => $place,
              self::COLUMN_DATE_START => $dateFrom->format("Y-m-d"),
              self::COLUMN_DATE_STOP => $dt));

      if($id !== null) {
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".$this->getUpdateValues()
                 ." WHERE ".self::COLUMN_ID." = :id");
         $dbst->bindParam(':id', $id, PDO::PARAM_INT);
         $result = $dbst->execute();
      } else {
         if($idCat == 0) {
            throw new InvalidArgumentException($this->_('Při ukládání nové akce musí být zadáno id'), 1);
         }

         $this->setIUValues(array(self::COLUMN_ID_CAT => $idCat,
                 self::COLUMN_ID_USER => $idUser,
                 self::COLUMN_ADDED => date('c')));
         $dbc->exec("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
                 ." ".$this->getInsertLabels()." VALUES ".$this->getInsertValues());

         $result = $dbc->lastInsertId();
      }
      $dbc->commit();
      return $result;
   }

   /**
    * Metoda vrací akci podle zadaného ID
    *
    * @param integer -- id akce
    * @return Model_langContainer -- objekt s akcí
    */
   public function getActionById($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS action"
              ." WHERE (".self::COLUMN_ID." = :id)".
              " LIMIT 0, 1");
      $dbst->bindParam(':id', $id, PDO::PARAM_INT);
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      return $dbst->fetch();
   }

   /**
    * Metoda vrací akci podle zadaného klíče
    *
    * @param string -- url klíč článku
    * @return PDOStatement -- pole s článkem
    */
   public function getAction($urlKey, $idCat) {
      $dbc = new Db_PDO();
//      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
//              ." WHERE (".self::COLUMN_URLKEY."_".Locale::getLang()." = :urlkey)"
//              ." LIMIT 0, 1");

      $dbst = $dbc->prepare("SELECT action.*, user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(self::DB_TABLE)." AS action"
              ." JOIN ".Model_Users::getUsersTable()." AS user ON action.".self::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE ((action.".self::COLUMN_URLKEY."_".Locale::getLang()." = :urlkey) OR (action.".self::COLUMN_URLKEY."_".Locale::getDefaultLang()." = :urlkey))"
              ." AND (action.".self::COLUMN_ID_CAT." = :idcat)"
              ." LIMIT 0, 1");
      $dbst->bindParam(':urlkey', $urlKey, PDO::PARAM_STR);
      $dbst->bindParam(':idcat', $idCat, PDO::PARAM_INT);
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      return $dbst->fetch();
   }

   /**
    * Metoda vrací akci podle zadaného klíče
    *
    * @param string -- url klíč článku
    * @return PDOStatement -- pole s článkem
    */
   public function getCurrentAction($idCat) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COLUMN_ID_CAT." = :idcat)"
              ." AND ((".self::COLUMN_DATE_START." >= CURDATE() AND ".self::COLUMN_TIME." >= CURTIME())"
              ." OR (".self::COLUMN_DATE_START." < CURDATE() AND ".self::COLUMN_DATE_STOP." > CURDATE()))"
              ." LIMIT 0, 1");
      $dbst->bindParam(':idcat', $idCat, PDO::PARAM_INT);
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      return $dbst->fetch();
   }
   
   public function deleteAction($idAction) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COLUMN_ID ." = :id)");
      $dbst->bindParam(':id', $idAction, PDO::PARAM_INT);
      return $dbst->execute();
   }

   /**
    * Metoda vyhledává články -- je tu kvůli zbytečnému nenačítání modelu List
    * @param integer $idCat
    * @param string $string
    * @param bool $publicOnly
    * @return PDOStatement
    */
   public function search($idCat, $string, $publicOnly = true) {
      $dbc = new Db_PDO();
      $clabel = self::COLUMN_NAME.'_'.Locale::getLang();
      $sublabel = self::COLUMN_SUBANME.'_'.Locale::getLang();
      $author = self::COLUMN_AUTHOR;
      $ctext = self::COLUMN_TEXT_CLEAR.'_'.Locale::getLang();
      $cplace = self::COLUMN_PLACE;

      $wherePub = null;
      if($publicOnly) {
         $wherePub = ' AND '.self::COLUMN_PUBLIC.' = 1';
      }

      $dbst = $dbc->prepare('SELECT *, ('.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER)
              .' * (MATCH('.$clabel.') AGAINST (:sstring) + MATCH('.$sublabel.') AGAINST (:sstring))'
              .' + MATCH('.$ctext.') AGAINST (:sstring) + MATCH('.$cplace.') AGAINST (:sstring))'
              .' + MATCH('.$author.') AGAINST (:sstring) AS '.Search::COLUMN_RELEVATION
              .' FROM '.Db_PDO::table(self::DB_TABLE)
              .' WHERE MATCH('.$clabel.', '.$sublabel.', '.$author.', '.$ctext.', '.$cplace.') AGAINST (:sstring IN BOOLEAN MODE)'
              .' AND `'.self::COLUMN_ID_CAT.'` = :idCat'
              .$wherePub // Public articles
              .' ORDER BY '.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER)
              .' * (MATCH('.$clabel.') AGAINST (:sstring) + MATCH('.$sublabel.') AGAINST (:sstring))'
              .' + MATCH('.$author.') AGAINST (:sstring)'
              .' + MATCH('.$ctext.') AGAINST (:sstring)'
              .' + MATCH('.$cplace.') AGAINST (:sstring) DESC');

      $dbst->bindValue(':idCat', $idCat, PDO::PARAM_INT);
      $dbst->bindValue(':sstring', $string, PDO::PARAM_STR);
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->execute();
      return $dbst;
   }
}

?>