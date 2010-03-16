<?php
/*
 * Třída modelu s detailem textu
 * 
*/
class Polls_Model_Detail extends Model_PDO {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'polls';
   const DB_TABLE_VOTAR = 'polls_votar_users';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COL_ID         = 'id_poll';
   const COL_ID_CAT     = 'id_category';
   const COL_QUESTION   = 'question';
   const COL_IS_MULTI   = 'is_multi';
   const COL_ACTIVE     = 'active';
   const COL_DATA       = 'data';
   const COL_VOTES      = 'votes';
   const COL_DATE       = 'date';

   const COL_V_ID_POLL     = 'id_poll';
   const COL_V_USER_IDEN   = 'user_identification';
   const COL_V_ID_USER     = 'id_user';

   public function getPoll($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE `".self::COL_ID."` = :idpoll");

      $dbst->bindValue(':idpoll', (int)$id, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   public function getTimesWithMovies($idCat, $from, $num) {
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

   public function getPolls($idCat, $from, $num = 100) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COL_ID_CAT." = :idcat"
              ." ORDER BY ".self::COL_DATE." DESC"
              ." LIMIT :fromRow, :rowCount ");

      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':fromRow', (int)$from, PDO::PARAM_INT);
      $dbst->bindValue(':rowCount', (int)$num, PDO::PARAM_INT);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst;
   }

   public function savePoll($idCat, $question, $multianswer, $serData, $votes = 0, $id = null) {
      $dbc = new Db_PDO();

      if($id !== null) {
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET `".self::COL_QUESTION."` = :question, `".self::COL_DATA."` = :data,"
          ." `".self::COL_IS_MULTI."` = :ismulti, `".self::COL_VOTES."` = :votes"
          ." WHERE ".self::COL_ID." = :idpoll");

         $dbst->bindValue(':idpoll', $id, PDO::PARAM_INT);
         $dbst->bindValue(':question', $question, PDO::PARAM_STR);
         $dbst->bindValue(':ismulti', $multianswer, PDO::PARAM_BOOL);
         $dbst->bindValue(':data', $serData, PDO::PARAM_STR);
         $dbst->bindValue(':votes', $votes, PDO::PARAM_INT);
         return $dbst->execute();
      } else {
         if($idCat == 0) {
            throw new InvalidArgumentException($this->_('Při ukládání nové ankety musí být zadáno id kategorie'), 1);
         }
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
                 ."  (`".self::COL_ID_CAT."`,`".self::COL_QUESTION."`, `".self::COL_IS_MULTI."`,"
                 ." `".self::COL_DATA."`, `".self::COL_VOTES."`)"
                 ." VALUES (:idcat, :question, :ismulti, :data, :votes)");

         $dbst->bindValue(':idcat', $idCat, PDO::PARAM_INT);
         $dbst->bindValue(':question', $question, PDO::PARAM_STR);
         $dbst->bindValue(':ismulti', $multianswer, PDO::PARAM_BOOL);
         $dbst->bindValue(':data', $serData, PDO::PARAM_STR);
         $dbst->bindValue(':votes', $votes, PDO::PARAM_INT);
         $dbst->execute();
         return $dbc->lastInsertId();
      }
   }

   public function savePollData($idPoll, $data) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
              ." SET `".self::COL_DATA."` = :data, `".self::COL_VOTES."` = `".self::COL_VOTES."`+1"
              ." WHERE ".self::COL_ID." = :idpoll");
      $dbst->bindValue(':data', $data, PDO::PARAM_STR);
      $dbst->bindValue(':idpoll', $idPoll, PDO::PARAM_INT);
      return $dbst->execute();
   }


   public function deletePoll($id) {
      // tady bude ještě přidání mazání uložených klientů

      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("DELETE FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COL_ID ." = :idpoll)");
      $dbst->bindParam(':idpoll', $id, PDO::PARAM_INT);
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

   /**
    * Metoda vyhledává články -- je tu kvůli zbytečnému nenačítání modelu List
    * @param integer $idCat
    * @param string $string
    * @param bool $publicOnly
    * @return PDOStatement
    */
   public function search($idCat, $string) {
      $dbc = new Db_PDO();

      $dbst = $dbc->prepare('SELECT *, ('.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER).' * MATCH('.self::COL_NAME.') AGAINST (:sstring)'
              .' +'.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER).' * MATCH('.self::COL_NAME_ORIG.') AGAINST (:sstring)'
              .' + MATCH('.self::COL_LABEL_CLEAR.') AGAINST (:sstring)) as '.Search::COLUMN_RELEVATION
              .' FROM '.Db_PDO::table(self::DB_TABLE)
              .' WHERE MATCH('.self::COL_NAME.', '.self::COL_NAME_ORIG.', '.self::COL_LABEL_CLEAR.') AGAINST (:sstring IN BOOLEAN MODE)'
              .' AND `'.self::COL_ID_CAT.'` = :idCat'
              .' ORDER BY '.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER).' * MATCH('.self::COL_NAME.') AGAINST (:sstring)'
              .' + '.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER).' * MATCH('.self::COL_NAME_ORIG.') AGAINST (:sstring)'
              .' + MATCH('.self::COL_LABEL_CLEAR.') AGAINST (:sstring) DESC');

      $dbst->bindValue(':idCat', $idCat, PDO::PARAM_INT);
      $dbst->bindValue(':sstring', $string, PDO::PARAM_STR);
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->execute();
      return $dbst;
   }

    /**
    * Metoda vrací počet anket
    *
    * @return integer -- počet anket v kategorii
    */
   public function getCountPolls($idCat) {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT COUNT(*) FROM ".Db_PDO::table(self::DB_TABLE)
                 ." WHERE (".self::COL_ID_CAT." = ".(int)$idCat.")");
      $count = $dbst->fetch();
      return $count[0];
   }
}

?>