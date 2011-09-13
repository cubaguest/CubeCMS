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
   const COL_LABEL_CLEAR = 'label_clear';
   const COL_LENGTH = 'length';
   const COL_VERSION = 'version';
   const COL_IMAGE = 'image';
   const COL_IMDBID = 'imdbid';
   const COL_CSFDID = 'csfdid';
   const COL_PRICE = 'price';
   const COL_ACCESS = 'accessibility';
   const COL_FC = 'film_club';
   const COL_CRITIQUE = 'critique';
   const COL_ORDER_LINK = 'orderlink';
   const COL_CHANGE = 'changed';
   const COL_TYPE = 'type';

   const COL_T_ID = 'id_time';
   const COL_T_ID_M = 'id_movie';
   const COL_T_DATE = 'date';
   const COL_T_TIME = 'time';

   public function getMovie($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE `".self::COL_ID."` = :idmovie");

      $dbst->bindValue(':idmovie', (int)$id, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   public function getTimesWithMovies(DateTime $dateFrom, DateTime $dateTo, $fromCurTime = false) {
      $dbc = new Db_PDO();
      if($fromCurTime === false){
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE_TIME)." AS times"
              ." JOIN ".Db_PDO::table(self::DB_TABLE)." AS movies ON movies.".self::COL_ID
              ." = times.".self::COL_T_ID_M
              ." WHERE times.".self::COL_T_DATE." >= :datefrom"
              ." AND times.".self::COL_T_DATE." <= :dateto"
              ." ORDER BY times.".self::COL_T_DATE." ASC, times.".self::COL_T_TIME." ASC");
      } else {
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE_TIME)." AS times"
              ." JOIN ".Db_PDO::table(self::DB_TABLE)." AS movies ON movies.".self::COL_ID
              ." = times.".self::COL_T_ID_M
              ." WHERE (times.".self::COL_T_DATE." > :datefrom AND times.".self::COL_T_DATE." <= :dateto)"
              ." OR (times.".self::COL_T_DATE." = :datefrom AND times.".self::COL_T_TIME." >= CURTIME())"
              ." ORDER BY times.".self::COL_T_DATE." ASC, times.".self::COL_T_TIME." ASC");
      }


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
              ." ORDER BY ".self::COL_T_DATE." ASC");
      $dbst->bindValue(':idmov', (int)$idMovie, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst;
   }

   public function saveMovie($name, $label, $price, $length, $version,
           $origname = null, $type = null, $filmClub = false,
           $access = 0, $imdbid = null, $csfdid = null, $image = null, $critique = null, $orderlink=null, $id = null) {
      $dbc = new Db_PDO();

      if($id !== null) {
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET `".self::COL_NAME."` = :name, `".self::COL_LABEL."` = :label, `".self::COL_LABEL_CLEAR."` = :label_c,"
          ." `".self::COL_PRICE."` = :price, `".self::COL_LENGTH."` = :length, `".self::COL_TYPE."` = :type,"
          ." `".self::COL_VERSION."` = :version, `".self::COL_NAME_ORIG."` = :origname,"
          ." `".self::COL_FC."` = :filmclub, `".self::COL_ACCESS."` = :access,"
          ." `".self::COL_IMDBID."` = :imdbid, `".self::COL_CSFDID."` = :csfdid,"
          ." `".self::COL_IMAGE."` = :image, `".self::COL_CRITIQUE."` = :critique, `".self::COL_ORDER_LINK."` = :orderlink"
          ." WHERE ".self::COL_ID." = :idmovie");
         $dbst->bindValue(':name', $name, PDO::PARAM_STR);
         $dbst->bindValue(':label', $label, PDO::PARAM_STR);
         $dbst->bindValue(':type', $type, PDO::PARAM_STR|PDO::PARAM_NULL);
         $dbst->bindValue(':label_c', strip_tags($label), PDO::PARAM_STR);
         $dbst->bindValue(':price', $price, PDO::PARAM_INT);
         $dbst->bindValue(':length', $length, PDO::PARAM_INT);
         $dbst->bindValue(':version', $version, PDO::PARAM_STR);
         $dbst->bindValue(':origname', $origname, PDO::PARAM_STR);
         $dbst->bindValue(':filmclub', $filmClub, PDO::PARAM_INT);
         $dbst->bindValue(':access', $access, PDO::PARAM_INT);
         $dbst->bindValue(':imdbid', $imdbid, PDO::PARAM_INT);
         $dbst->bindValue(':csfdid', $csfdid, PDO::PARAM_INT);
         $dbst->bindValue(':image', $image, PDO::PARAM_STR);
         $dbst->bindValue(':critique', $critique, PDO::PARAM_STR);
         $dbst->bindValue(':orderlink', $orderlink, PDO::PARAM_STR);
         $dbst->bindValue(':idmovie', $id, PDO::PARAM_INT);
         return $dbst->execute();
      } else {
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
                 ."  (`".self::COL_NAME."`, `".self::COL_NAME_ORIG."`, `"
                 .self::COL_FC."`, `".self::COL_LABEL."`, `".self::COL_LABEL_CLEAR."`,`".self::COL_PRICE."`,`".self::COL_LENGTH
                 ."`,`".self::COL_ACCESS."`,`".self::COL_VERSION."`,`".self::COL_IMDBID."`,`".self::COL_CSFDID
                 ."`,`".self::COL_IMAGE."`,`".self::COL_CRITIQUE."`,`".self::COL_ORDER_LINK."`,`".self::COL_TYPE."`)"
                 ." VALUES (:name, :nameorig, :fc, :lab, :lab_c, :price, :leng, :access,".
                 " :vers, :imdbid, :csfdid, :img, :critique, :orderlink, :type)");

         $dbst->bindValue(':price', $price, PDO::PARAM_INT);
         $dbst->bindValue(':leng', $length, PDO::PARAM_INT);
         $dbst->bindValue(':imdbid', $imdbid, PDO::PARAM_INT);
         $dbst->bindValue(':csfdid', $csfdid, PDO::PARAM_INT);
         $dbst->bindValue(':name', $name, PDO::PARAM_STR);
         $dbst->bindValue(':type', $type, PDO::PARAM_STR|PDO::PARAM_NULL);
         $dbst->bindValue(':img', $image, PDO::PARAM_STR);
         $dbst->bindValue(':access', $access, PDO::PARAM_STR);
         $dbst->bindValue(':nameorig', $origname, PDO::PARAM_STR);
         $dbst->bindValue(':lab', $label, PDO::PARAM_STR);
         $dbst->bindValue(':lab_c', strip_tags($label), PDO::PARAM_STR);
         $dbst->bindValue(':vers', $version, PDO::PARAM_STR);
         $dbst->bindValue(':critique', $critique, PDO::PARAM_STR);
         $dbst->bindValue(':orderlink', $orderlink, PDO::PARAM_STR);
         $dbst->bindValue(':fc', $filmClub, PDO::PARAM_BOOL);

         $dbst->execute();
         return $dbc->lastInsertId();
      }
   }

   public function saveTime(DateTime $date, DateTime $time, $idMovie, $idTime = null) {
      $dbc = new Db_PDO();
      if($idTime !== null) {
         // je už uloženo
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE_TIME)
          ." SET `".self::COL_T_DATE."` = :mdate, `".self::COL_T_TIME."` = :mtime"
          ." WHERE ".self::COL_T_ID." = :idtime");
         $dbst->bindValue(':mdate', (string)$date->format('Y-m-d'), PDO::PARAM_STR);
         $dbst->bindValue(':mtime', (string)$time->format('H:i'), PDO::PARAM_STR);
         $dbst->bindValue(':idtime', $idTime, PDO::PARAM_INT);
         return $dbst->execute();
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


   public function deleteMovie($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COL_ID ." = :idmovie)");
      $dbst->bindParam(':idmovie', $id, PDO::PARAM_INT);
      return $dbst->execute();
   }

   public function deleteTime($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE_TIME)
          ." WHERE (".self::COL_T_ID ." = :idtime)");
      $dbst->bindParam(':idtime', $id, PDO::PARAM_INT);
      return $dbst->execute();
   }

   public function deleteTimes($idMovie) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE_TIME)
          ." WHERE (".self::COL_T_ID_M ." = :idmovie)");
      $dbst->bindParam(':idmovie', $idMovie, PDO::PARAM_INT);
      return $dbst->execute();
   }

   
   public function getLastChange($idCat) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT ".self::COLUMN_CHANGED_TIME." AS tm FROM ".Db_PDO::table(self::DB_TABLE));
      $dbst->bindParam(':idcategory', $idCat);
      $dbst->execute();
      $fetch = $dbst->fetchObject();
      if($fetch == false) {
         return false;
      } else {
         return $fetch->tm;
      }
   }

   /**
    * Metoda vyhledává články -- je tu kvůli zbytečnému nenačítání modelu List
    * @param integer $idCat
    * @param string $string
    * @param bool $publicOnly
    * @return PDOStatement
    */
   public function search($string){
      $dbc = new Db_PDO();

      $dbst = $dbc->prepare('SELECT *, ('.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER).' * MATCH('.self::COL_NAME.') AGAINST (:sstring)'
              .' +'.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER).' * MATCH('.self::COL_NAME_ORIG.') AGAINST (:sstring)'
              .' + MATCH('.self::COL_LABEL_CLEAR.') AGAINST (:sstring)) as '.Search::COLUMN_RELEVATION
              .' FROM '.Db_PDO::table(self::DB_TABLE)
              .' WHERE MATCH('.self::COL_NAME.', '.self::COL_NAME_ORIG.', '.self::COL_LABEL_CLEAR.') AGAINST (:sstring IN BOOLEAN MODE)'
              .' ORDER BY '.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER).' * MATCH('.self::COL_NAME.') AGAINST (:sstring)'
              .' + '.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER).' * MATCH('.self::COL_NAME_ORIG.') AGAINST (:sstring)'
              .' + MATCH('.self::COL_LABEL_CLEAR.') AGAINST (:sstring) DESC');

      $dbst->bindValue(':sstring', $string, PDO::PARAM_STR);
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->execute();
      return $dbst;
   }

   public function getFeaturedMovies(){
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS movies"
              ." JOIN ".Db_PDO::table(self::DB_TABLE_TIME)." AS times ON movies.".self::COL_ID
              ." = times.".self::COL_T_ID_M
              ." WHERE times.".self::COL_T_DATE." >= :datefrom"
              ." GROUP BY movies.".self::COL_ID
              ." ORDER BY times.".self::COL_T_DATE." ASC, times.".self::COL_T_TIME." ASC");

      $dateFrom = new DateTime();
      $dbst->bindValue(':datefrom', $dateFrom->format('Y-m-d'), PDO::PARAM_STR);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst;
   }

   public function getCurrentMovie($from = 0){
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE_TIME)." AS times"
              ." JOIN ".Db_PDO::table(self::DB_TABLE)." AS movies ON movies.".self::COL_ID
              ." = times.".self::COL_T_ID_M
              ." WHERE (times.".self::COL_T_DATE." = :datefrom AND times.".self::COL_T_TIME." >= :timefrom)"
              ." OR times.".self::COL_T_DATE." > :datefrom"
              ." ORDER BY times.".self::COL_T_DATE." ASC, times.".self::COL_T_TIME." ASC"
              ." LIMIT :limitfrom,1");
      $dateFrom = new DateTime();
      $dbst->bindValue(':datefrom', $dateFrom->format('Y-m-d'), PDO::PARAM_STR);
      $dbst->bindValue(':timefrom', $dateFrom->format('H:m:s'), PDO::PARAM_STR);
      $dbst->bindValue(':limitfrom', $from, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   public function getMoviesFk($year){
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS movies"
              ." JOIN ".Db_PDO::table(self::DB_TABLE_TIME)." AS times ON movies.".self::COL_ID
              ." = times.".self::COL_T_ID_M
              ." WHERE (DATE_FORMAT(times.".self::COL_T_DATE.",'%Y') = :year )"
//              ." WHERE (((times.".self::COL_T_DATE." = CURDATE() AND times.".self::COL_T_TIME." >= CURTIME())"
//              ." OR times.".self::COL_T_DATE." > CURDATE()))"
//              ." AND times.".self::COL_T_DATE." <= CURDATE() + INTERVAL :numMonth MONTH"
              ." AND movies.".self::COL_FC." = 1"
              ." ORDER BY times.".self::COL_T_DATE." ASC, times.".self::COL_T_TIME." ASC");
      $dbst->setFetchMode(PDO::FETCH_OBJ);
//      $dbst->bindValue(':numMonth', (int)$numMounths, PDO::PARAM_INT);
      $dbst->bindValue(':year', (int)$year, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }
}

?>