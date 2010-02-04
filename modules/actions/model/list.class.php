<?php
/*
 * Třída modelu s listem akcí
*/
class Actions_Model_List extends Model_PDO {
   /**
    * Metoda vrací počet novinek
    *
    * @return integer -- počet novinek
    */
   public function getCountActions() {
      if(!$this->countActionsLoaded) {
         $sqlCount = $this->getDb()->select()->table(Db::table(Actions_Model_Detail::DB_TABLE))
                 ->colums(array("count"=>"COUNT(*)"))
                 ->where(Actions_Model_Detail::COLUMN_ACTION_ID_ITEM, $this->module()->getId())
                 ->where(Actions_Model_Detail::COLUMN_ACTION_DISABLED, (int)false)
                 ->where(Actions_Model_Detail::COLUMN_ACTION_DATE_START, time(), "<=")
                 ->where(Actions_Model_Detail::COLUMN_ACTION_DATE_STOP, time(), ">=");
         ;

         $count = $this->getDb()->fetchObject($sqlCount);
         if(!empty ($count)) {
            $this->allActionsCount = $count->count;
            $this->countActionsLoaded = true;
         }
      }
      return $this->allActionsCount;
   }


   /**
    * Metoda vrací vybrané akce podle času
    * @return PDOStatement -- pole akcí
    */
   public function getActions($idCat, $fromTime, $toTime, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $whereP = " AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)";
      } else {
         $whereP = null;
      }
      $dbst = $dbc->prepare("SELECT actions.*, user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." JOIN ".Model_Users::getUsersTable()." AS user ON actions.".Actions_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
              . $whereP // public
              ." AND (".Actions_Model_Detail::COLUMN_DATE_START." < :dateE)"
              ." AND (".Actions_Model_Detail::COLUMN_DATE_STOP." > :dateS)"
              ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." ASC");

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':dateS', (int)$fromTime, PDO::PARAM_INT);
      $dbst->bindValue(':dateE', (int)$toTime, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda vrací vybrané akce podle času
    * @return PDOStatement -- pole akcí
    */
   public function getFeaturedActions($idCat, $toTime = null, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($toTime == null) $toTime = time();

      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
              ." AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)"
              ." AND ((".Actions_Model_Detail::COLUMN_DATE_START." < :dateNow"
              ." AND  ".Actions_Model_Detail::COLUMN_DATE_STOP." > :dateNow)"
              ." OR (".Actions_Model_Detail::COLUMN_DATE_START." > :dateNow))"
              ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." ASC");

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':dateNow', time(), PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda vrací vybrané akce podle času
    * @return PDOStatement -- pole akcí
    */
   public function getActionsByAdded($idCat, $num = 100, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $whereP = " AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)";
      } else {
         $whereP = null;
      }
      $dbst = $dbc->prepare("SELECT actions.*, user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." JOIN ".Model_Users::getUsersTable()." AS user ON actions.".Actions_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
              . $whereP // public
              ." ORDER BY ".Actions_Model_Detail::COLUMN_ADDED." ASC"
              ." LIMIT 0, :numAc");

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':numAc', (int)$num, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda vrací aktuální akci
    * @return PDOStatement -- pole akcí
    */
   public function getCurrentActions($idCat, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)
                 ." WHERE (".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
                 ." AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)"
                 ." AND (".Actions_Model_Detail::COLUMN_DATE_START." < :date)"
                 ." AND (".Actions_Model_Detail::COLUMN_DATE_STOP." > :date)"
                 ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." ASC");
      } else {
         $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)
                 ." WHERE (".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
                 ." AND (".Actions_Model_Detail::COLUMN_DATE_START." < :date)"
                 ." AND (".Actions_Model_Detail::COLUMN_DATE_STOP." > :date)"
                 ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." ASC");
      }
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':date', time(), PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda vrací všechny akce
    * @param int $idCat -- id kategorie
    * @param bool $onlyPublic -- (option) jestli jenom veřejné akce
    * @return array -- pole akcí
    */
   public function getAllActions($idCat, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $wherePub = " AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)";
      } else {
         $wherePub = null;
      }

      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)
              ." WHERE (".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
              . $wherePub
              ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." DESC");
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   public function getLastChange() {
   }

   /**
    * Metoda vrací vybrané akce podle času z navolených kategorií
    * @return PDOStatement -- pole akcí
    */
   public function getActionsByCatIds($fromTime, $toTime, $idCats, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $whereP = " AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)";
      } else {
         $whereP = null;
      }

      // vytvoření podřetězce IN
      $in = null;
      foreach ($idCats as $idC){
         $in .= $dbc->quote($idC, PDO::PARAM_INT).',';
      }
      $in = substr($in, 0, strlen($in)-1);

      $dbst = $dbc->prepare("SELECT actions.*, cat.".Model_Category::COLUMN_URLKEY.'_'.Locale::getLang()
              ." AS curlkey ,user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." JOIN ".Model_Users::getUsersTable()." AS user ON actions.".Actions_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON actions.".Actions_Model_Detail::COLUMN_ID_CAT
              ." = cat.".Model_Category::COLUMN_CAT_ID
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." IN (".$in."))"
              . $whereP // public
              ." AND (".Actions_Model_Detail::COLUMN_DATE_START." < :dateE)"
              ." AND (".Actions_Model_Detail::COLUMN_DATE_STOP." > :dateS)"
              ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." ASC");

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':dateS', (int)$fromTime, PDO::PARAM_INT);
      $dbst->bindValue(':dateE', (int)$toTime, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda vrací vybrané akce podle času z navolených kategorií
    * @return PDOStatement -- pole akcí
    */
   public function getActionsByAddedByCatIds($idCats, $num = 10) {
      $dbc = new Db_PDO();

      // vytvoření podřetězce IN
      $in = null;
      foreach ($idCats as $idC){
         $in .= $dbc->quote($idC, PDO::PARAM_INT).',';
      }
      $in = substr($in, 0, strlen($in)-1);

      $dbst = $dbc->prepare("SELECT actions.*, cat.".Model_Category::COLUMN_URLKEY.'_'.Locale::getLang()
              ." AS curlkey ,user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." JOIN ".Model_Users::getUsersTable()." AS user ON actions.".Actions_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON actions.".Actions_Model_Detail::COLUMN_ID_CAT
              ." = cat.".Model_Category::COLUMN_CAT_ID
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." IN (".$in."))"
              ." AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)"
              ." ORDER BY ".Actions_Model_Detail::COLUMN_ADDED." ASC"
              ." LIMIT 0, :numAc");

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':numAc', (int)$num, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

}

?>