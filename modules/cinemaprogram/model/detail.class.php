<?php
/*
 * Třída modelu s detailem textu
 * 
*/
class CinemaProgram_Model_Detail extends Model_PDO {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'cinemaprogram_movies';
   const DB_TABLE_TIME = 'cinemaprogram_time';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COL_ID = 'id_movie';
   const COL_ID_CAT = 'id_category';
   const COL_NAME = 'name';
   const COL_NAME_ORIG = 'name_orig';
   const COL_LABEL = 'label';
   const COL_LENGTH = 'length';
   const COL_VERSION = 'version';
   const COL_IMAGE = 'image';
   const COL_IMDBID = 'imdbid';
   const COL_CSFDID = 'csfdid';
   const COL_PRICE = 'price';
   const COL_ACCESS = 'accessibility';
   const COL_FC = 'film_club';
   const COL_CHANGE = 'changed';

   const COL_T_ID = 'id_time';
   const COL_T_ID_M = 'id_movie';
   const COL_T_DATE = 'date';
   const COL_T_TIME = 'time';

   /**
    * Metoda provede načtení textu z db
    *
    * @return string -- načtený text
    */
   public function getMovies($idCat, DateTime $fromDate, DateTime $toDate) {
      /*      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS movies"
              ." JOIN ".Db_PDO::table(self::DB_TABLE_TIME)." AS times ON movies.".self::COL_ID
              ." = times.".self::COL_T_ID_M
              ." WHERE times.".self::COL_T_DATETO." >= :datefrom"
              ." AND times.".self::COL_T_DATETO." <= :dateto"
              ." AND movies.".self::COL_ID_CAT." = :idcat"
              ." GROUP BY times.".self::COL_T_ID_M
              ." ORDER BY times.".self::COL_T_DATEFROM." ASC");

      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':datefrom', $fromDate->format('Y-m-d'), PDO::PARAM_STR);
      $dbst->bindValue(':dateto', $toDate->format('Y-m-d'), PDO::PARAM_STR);*/
//      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS movies"
//          ." JOIN ".Db_PDO::table(self::DB_TABLE_TIME)." AS times ON movies.".self::COL_ID
//          ." = times.".self::COL_T_ID_M
//          ." WHERE (MONTH(times.".self::COL_T_DATEFROM.") = :mm OR MONTH(times.".self::COL_T_DATETO.") = :mm )"
//       ." AND DAY(times.".self::COL_T_DATEFROM.") >= :fromdate"
//       ." AND DAY(times.".self::COL_T_DATETO.") <= :today"
//      ." AND movies.".self::COL_ID_CAT." = :idcat"
//      ." GROUP BY times.".self::COL_T_ID_M);
//
////WHERE (MONTH(times.date_from) = 12 OR MONTH(times.date_to) = 12)
////AND (DAY(times.date_from) >= 1) AND (DAY(times.date_to) <= 14)
//
//      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
//      $dbst->bindValue(':mm', (int)$month, PDO::PARAM_INT);
//      $dbst->bindValue(':fromday', (int)$fromDay, PDO::PARAM_INT);
//      $dbst->bindValue(':today', (int)$days+$fromDay-1, PDO::PARAM_INT);

      /*      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst;*/
   }

   public function getTimesWithMovies($idCat, DateTime $dateFrom, DateTime $dateTo) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE_TIME)." AS times"
              ." JOIN ".Db_PDO::table(self::DB_TABLE)." AS movies ON movies.".self::COL_ID
              ." = times.".self::COL_T_ID_M
              ." WHERE times.".self::COL_T_DATE." >= :datefrom"
              ." AND times.".self::COL_T_DATE." <= :dateto"
              ." AND movies.".self::COL_ID_CAT." = :idcat"
              ." ORDER BY times.".self::COL_T_DATE." ASC, times.".self::COL_T_TIME." ASC");

      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':datefrom', $dateFrom->format('Y-m-d'), PDO::PARAM_STR);
      $dbst->bindValue(':dateto', $dateTo->format('Y-m-d'), PDO::PARAM_STR);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst;
   }

   public function getTimes($idMovie) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE_TIME)
              ." WHERE ".self::COL_T_ID_M." = :idmov"
              ." ORDER BY ".self::COL_T_DATEFROM." ASC");
      $dbst->bindValue(':idmov', (int)$idMovie, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst;
   }

   public function saveMovie($name, $label, $price, $length, $version, $idCat, $origname = null, $filmClub = false,
           $access = 0, $imdbid = null, $csfdid = null, $image = null, $id = null) {
      $dbc = new Db_PDO();

      if($id !== null) {
//         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
//          ." SET ".$this->getUpdateValues()
//          ." WHERE ".self::COLUMN_ID." = :id");
//         $dbst->bindParam(':id', $id, PDO::PARAM_INT);
//         return $dbst->execute();
      } else {
         if($idCat == 0) {
            throw new InvalidArgumentException($this->_('Při ukládání nového filmu musí být zadáno id kategorie'), 1);
         }
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
                 ."  (`".self::COL_ID_CAT."`,`".self::COL_NAME."`, `".self::COL_NAME_ORIG."`, `"
                 .self::COL_FC."`, `".self::COL_LABEL."`,`".self::COL_PRICE."`,`".self::COL_LENGTH
                 ."`,`".self::COL_ACCESS."`,`".self::COL_VERSION."`,`".self::COL_IMDBID."`,`".self::COL_CSFDID
                 ."`,`".self::COL_IMAGE."`)"
                 ." VALUES (:idcat, :name, :nameorig, :fc, :lab, :price, :leng, :access,".
                 " :vers, :imdbid, :csfdid, :img)");

         $dbst->bindValue(':idcat', $idCat, PDO::PARAM_INT);
         $dbst->bindValue(':price', $price, PDO::PARAM_INT);
         $dbst->bindValue(':leng', $length, PDO::PARAM_INT);
         $dbst->bindValue(':imdbid', $imdbid, PDO::PARAM_INT);
         $dbst->bindValue(':csfdid', $csfdid, PDO::PARAM_INT);
         $dbst->bindValue(':name', $name, PDO::PARAM_STR);
         $dbst->bindValue(':img', $image, PDO::PARAM_STR);
         $dbst->bindValue(':access', $access, PDO::PARAM_STR);
         $dbst->bindValue(':nameorig', $origname, PDO::PARAM_STR);
         $dbst->bindValue(':lab', $label, PDO::PARAM_STR);
         $dbst->bindValue(':vers', $version, PDO::PARAM_STR);
         $dbst->bindValue(':fc', $filmClub, PDO::PARAM_BOOL);
//         $dbst->bindValue(':change', time(), PDO::PARAM_INT);

         $dbst->execute();
         return $dbc->lastInsertId();
      }
   }

   public function saveTime(DateTime $date, DateTime $time, $idMovie, $idTime = null) {
      $dbc = new Db_PDO();
      if($idTime !== null) {
         // je už uloženo
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE_TIME)
                 ." SET ".$this->getUpdateValues()." WHERE ".self::COLUMN_ID_CATEGORY." = ".(int)$idCat);
         //             ." WHERE ".self::COLUMN_ID_CATEGORY." = :category");
         //$dbst->bindParam(1, $idCat, PDO::PARAM_INT);
         $dbst->execute();
      } else {
         // není uloženo
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE_TIME)
                 ." (`".self::COL_T_ID_M."`, `".self::COL_T_DATE."`, `".self::COL_T_TIME."`)"
                 ." VALUES (:idm, :date, :mtime)");

         $dbst->bindValue('idm', $idMovie, PDO::PARAM_INT);
         $dbst->bindValue('date', $date->format('Y-m-d'));
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
      if($fetch == false) {
         return false;
      } else {
         return $fetch->tm;
      }
   }
}

?>