<?php
/*
 * Třída modelu s detailem textu
 * 
 */
class CinemaProgram_Model_TimeDetail extends Model_PDO {
/**
 * Tabulka s detaily
 */
   const DB_TABLE = 'cinemaprogram_time';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID = 'id_time';
   const COLUMN_ID_MOVIE = 'id_movie';
   const COLUMN_DATEFROM = 'date_from';
   const COLUMN_DATETO = 'date_to';
   const COLUMN_TIME = 'time';

   public function saveTime(DateTime $dateF,DateTime $dateT, DateTime $time, $idMovie, $idTime = null) {
      $dbc = new Db_PDO();
      if($idTime !== null) {
      // je už uloženo
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
             ." SET ".$this->getUpdateValues()." WHERE ".self::COLUMN_ID_CATEGORY." = ".(int)$idCat);
         //             ." WHERE ".self::COLUMN_ID_CATEGORY." = :category");
        //$dbst->bindParam(1, $idCat, PDO::PARAM_INT);
         $dbst->execute();
      } else {
      // není uloženo
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
             ." (`id_movie`, `date_from`, `date_to`, `time`) VALUES (:idm, :datef, :datet, :mtime)");

         $dbst->bindValue('idm', $idMovie, PDO::PARAM_INT);
         $dbst->bindValue('datef', $dateF->format('Y-m-d'));
//         if($dateT == null){
//            $dbst->bindValue('datet', null);
//         } else {
            $dbst->bindValue('datet', $dateT->format('Y-m-d'));
//         }
         $dbst->bindValue('mtime', $time->format('H:i'));
         return $dbst->execute();
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