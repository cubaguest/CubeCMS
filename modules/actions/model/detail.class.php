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
   const COLUMN_TEXT = 'text';
   const COLUMN_URLKEY = 'urlkey';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_ID_CAT = 'id_category';
   const COLUMN_ID = 'id_action';
   const COLUMN_PUBLIC = 'public';
   const COLUMN_DATE_START = 'start_date';
   const COLUMN_DATE_STOP = 'stop_date';
   const COLUMN_IMAGE = 'image';
   const COLUMN_CHANGED = 'changed';

   /**
    * Metoda uloží akci do db
    *
    * @param array -- pole s nadpisem novinky
    * @param array -- pole s textem novinky
    * @param boolean -- id uživatele
    */
   public function saveAction($name, $text, $urlKey, DateTime $dateFrom, DateTime $dateTo, $image = null, $idCat = 0,
           $idUser = 0, $public = true, $id = null) {
      // globalní prvky
      $this->setIUValues(array(self::COLUMN_NAME => $name,self::COLUMN_TEXT => $text,
              self::COLUMN_URLKEY => $urlKey,
              self::COLUMN_PUBLIC => $public, self::COLUMN_IMAGE => $image,
              self::COLUMN_DATE_START => $dateFrom->format('U'),
              self::COLUMN_DATE_STOP => $dateTo->format('U')));

      $dbc = new Db_PDO();

      if($id !== null) {
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
                 ." SET ".$this->getUpdateValues()
                 ." WHERE ".self::COLUMN_ID." = :id");
         $dbst->bindParam(':id', $id, PDO::PARAM_INT);
         return $dbst->execute();
      } else {
         if($idCat == 0) {
            throw new InvalidArgumentException($this->_('Při ukládání nové akce musí být zadáno id'), 1);
         }
         // unikátní klíč
//         $dbc = new Db_PDO();
//         // načtu všechny existující url klíče
//         $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)."
//             WHERE (".self::COLUMN_ID_CATEGORY." = '".$idCat."')");
//
//         while($row = $dbst->fetch()){
//            $cats[$row->{Model_Category::COLUMN_CAT_ID}] = $row;
//         }

         $this->setIUValues(array(self::COLUMN_ID_CAT => $idCat,
                 self::COLUMN_ID_USER => $idUser));
         $dbc->exec("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
                 ." ".$this->getInsertLabels()." VALUES ".$this->getInsertValues());

         return $dbc->lastInsertId();
      }
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
   public function getAction($urlKey) {
      $dbc = new Db_PDO();
//      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
//              ." WHERE (".self::COLUMN_URLKEY."_".Locale::getLang()." = :urlkey)"
//              ." LIMIT 0, 1");

      $dbst = $dbc->prepare("SELECT action.*, user.".Model_Users::COLUMN_USERNAME
         ." FROM ".Db_PDO::table(self::DB_TABLE)." AS action"
         ." JOIN ".Model_Users::getUsersTable()." AS user ON action.".self::COLUMN_ID_USER
         ." = user.".Model_Users::COLUMN_ID
         ." WHERE (action.".self::COLUMN_URLKEY."_".Locale::getLang()." = :urlkey)"
         ." LIMIT 0, 1");
      $dbst->bindParam(':urlkey', $urlKey, PDO::PARAM_STR);
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      return $dbst->fetch();
   }

   public function deleteAction($idAction) {
      //			smazání novinky
      $sqlUpdate = $this->getDb()->delete()->table(Db::table(self::DB_TABLE))
              ->where(self::COLUMN_ACTION_ID, $idAction);

      if($this->getDb()->query($sqlUpdate)) {
         return true;
      } else {
         return false;
      };
   }
}

?>