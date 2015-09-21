<?php
/**
 * Třída modelu akcí
 * @deprecated since version 8.0.0 use Actions_Model
*/
class Actions_Model_Detail extends Actions_Model {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'actions';

   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_NAME = 'action_name';
   const COLUMN_AUTHOR = 'action_author';
   const COLUMN_SUBANME = 'action_subname';
   const COLUMN_TEXT = 'action_text';
   const COLUMN_NOTE = 'action_note';
   const COLUMN_TEXT_CLEAR = 'action_text_clear';
   const COLUMN_URLKEY = 'action_urlkey';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_ID_CAT = 'id_category';
   const COLUMN_ID = 'id_action';
   const COLUMN_PUBLIC = 'action_public';
   const COLUMN_DATE_START = 'action_start_date';
   const COLUMN_DATE_STOP = 'action_stop_date';
   const COLUMN_IMAGE = 'action_image';
   const COLUMN_CHANGED = 'action_changed';
   const COLUMN_ADDED = 'action_time_add';
   const COLUMN_TIME = 'action_time';
   const COLUMN_PLACE = 'action_place';
   const COLUMN_PRICE = 'action_price';
   const COLUMN_PREPRICE = 'action_preprice';

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
      $dbc = Db_PDO::getInstance();
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
      $dbc = Db_PDO::getInstance();
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
      $dbc = Db_PDO::getInstance();
//      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
//              ." WHERE (".self::COLUMN_URLKEY."_".Locales::getLang()." = :urlkey)"
//              ." LIMIT 0, 1");
      $modelUsers = new Model_Users();
      $tbUsers = $modelUsers->getTableName();

      $dbst = $dbc->prepare("SELECT action.*, user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(self::DB_TABLE)." AS action"
              ." JOIN ".$tbUsers." AS user ON action.".self::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE ((action.".self::COLUMN_URLKEY."_".Locales::getLang()." = :urlkey) OR (action.".self::COLUMN_URLKEY."_".Locales::getDefaultLang()." = :urlkey))"
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
//   public function getCurrentAction($idCat, $from = 0) {
//      $dbc = Db_PDO::getInstance();
//      $dbst = $dbc->prepare("SELECT *, ABS(DATEDIFF(".self::COLUMN_DATE_START.",CURDATE())) AS delta_days  FROM ".Db_PDO::table(self::DB_TABLE)
//              ." WHERE (".self::COLUMN_ID_CAT." = :idcat) AND (".self::COLUMN_PUBLIC." = 1)"
//              ." AND ((ISNULL(".self::COLUMN_DATE_STOP.") AND ".self::COLUMN_DATE_START." >= CURDATE())"
//              ." OR (ISNULL(".self::COLUMN_DATE_STOP.") = 0 AND ".self::COLUMN_DATE_START." <= CURDATE() AND ".self::COLUMN_DATE_STOP." > CURDATE()))"
//              ." ORDER BY delta_days, `".self::COLUMN_TIME."` LIMIT :from, 1");
//      $dbst->bindParam(':idcat', $idCat, PDO::PARAM_INT);
//      $dbst->bindParam(':from', $from, PDO::PARAM_INT);
//      $dbst->execute();
//      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
//      return $dbst->fetch();
//   }

   public function deleteAction($idAction) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COLUMN_ID ." = :id)");
      $dbst->bindParam(':id', $idAction, PDO::PARAM_INT);
      return $dbst->execute();
   }
}