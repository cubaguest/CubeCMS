<?php
/**
 * Model pro detail galerie
 *
 */
class Photogalery_Model_GaleryDetail extends Model_PDO {
   const DB_TABLE = 'galeries';
   /**
    * Názvy sloupců v databázi pro tabulku s galeriemi
    * @var string
    */
   const COLUMN_NAME       = 'name';
   const COLUMN_TEXT       = 'text';
   const COLUMN_TIME_ADD 	= 'time_add';
   const COLUMN_TIME_EDIT	= 'time_edit';
   const COLUMN_ID         = 'id_galery';
   const COLUMN_ID_CATEGORY= 'id_category';

   /**
    * Metoda provede načtení textu z db
    *
    * @return string -- načtený text
    */
   public function getTextByCatId($idCat) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)."
             WHERE (".self::COLUMN_ID_CATEGORY." = :idCat)");
      $dbst->bindParam(':idCat', $idCat, PDO::PARAM_INT);
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst->fetch();
   }

   public function saveText($texts, $idCat, $name = null) {
   // zjištění jestli existuje záznam
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COLUMN_ID_CATEGORY." = :idCat)");
      $dbst->bindValue(':idCat', $idCat, PDO::PARAM_INT);
      $dbst->execute();
      $count = $dbst->rowCount();
      // globalní prvky
      $dbc = new Db_PDO();
      $this->setIUValues(array(self::COLUMN_TEXT => $texts,
          self::COLUMN_CHANGED_TIME => time()));

      if($name !== null){
         $this->setIUValues(array(self::COLUMN_NAME => $name));
      }

      if($count != 0) {
      // je už uloženo
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
             ." SET ".$this->getUpdateValues()." WHERE ".self::COLUMN_ID_CATEGORY." = ".(int)$idCat);
         //             ." WHERE ".self::COLUMN_ID_CATEGORY." = :category");
        //$dbst->bindParam(1, $idCat, PDO::PARAM_INT);
         $dbst->execute();
      } else {
      // není uloženo
         $this->setIUValues(array(self::COLUMN_ID_CATEGORY => $idCat));
         $dbc->query("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
             ." ".$this->getInsertLabels()." VALUES ".$this->getInsertValues());
         return $dbc->lastInsertId();
      }
   }

   public function getLastChange($idCat) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT ".self::COLUMN_CHANGED_TIME." AS tm FROM ".Db_PDO::table(self::DB_TABLE)
             ." WHERE ".self::COLUMN_ID_CATEGORY." = :idcategory");
      $dbst->bindParam(':idcategory', $idCat);
      $dbst->execute();
//      var_dump($dbst);
      $fetch = $dbst->fetchObject();
      if($fetch == false){
         return false;
      } else {
         return $fetch->tm;
      }
   }
}

?>