<?php
/*
 * Třída modelu detailem článku
 */
class Courses_Model_Places extends Model_PDO {
   const DB_TABLE = 'courses_places';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_place';
   const COLUMN_NAME = 'place';

   public function getPlaces($searched = null){
      $dbc = new Db_PDO();

      if($searched == null){
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." ORDER BY ".self::COLUMN_NAME." ASC");
      } else {
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_NAME." LIKE :searched"
              ." ORDER BY ".self::COLUMN_NAME." ASC"
                 );
         $dbst->bindValue(':searched', $searched.'%', PDO::PARAM_STR);
      }
      $dbst->execute();
      return $dbst->fetchAll(PDO::FETCH_OBJ);
   }

   public function savePlace($place) {
      $dbc = new Db_PDO();
      // kontrola jestli místo již neexistuje
      $dbst = $dbc->prepare("SELECT COUNT(*) AS count FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_NAME." = :place");
      $dbst->execute(array(':place' => $place));
      $counter = $dbst->fetchObject();

      // pokud neexistuje uložíme nové místo
      if($counter->count == 0){
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)." "
                 ."(".self::COLUMN_NAME.")"
                 ." VALUES (:name)");
         $dbst->execute(array(':name' => $place));
      }
   }

}

?>