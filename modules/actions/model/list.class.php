<?php
/*
 * Třída modelu s listem akcí
*/
class Actions_Model_List extends Model_PDO {
   private $usersTable = null;

   public function __construct()
   {
      $modelUsers = new Model_Users();
      $this->usersTable = $modelUsers->getTableName();
      parent::__construct();
   }
   /**
    * Metoda vrací vybrané akce podle času
    * @return PDOStatement -- pole akcí
    */
   public function getActions($idCat,DateTime $fromTime, DateTime $toTime, $onlyPublic = true) {
      $dbc = Db_PDO::getInstance();
      if($onlyPublic) {
         $whereP = " AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)";
      } else {
         $whereP = null;
      }
      $dbst = $dbc->prepare("SELECT actions.*, user.".Model_Users::COLUMN_USERNAME.","
              ." ABS(DATEDIFF(".Actions_Model_Detail::COLUMN_DATE_START.",:dateStart)) AS delta_days,"
              ." IF(actions.`".Actions_Model_Detail::COLUMN_TIME."`,actions.`".Actions_Model_Detail::COLUMN_TIME."`, '23:59:59') AS timeord"
              ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." JOIN ".Db_PDO::table(Model_Users::DB_TABLE)." AS user ON actions.".Actions_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
              . $whereP // public
//              ." AND ( (ISNULL(".Actions_Model_Detail::COLUMN_TIME.") AND ".Actions_Model_Detail::COLUMN_DATE_START." >= :dateStart AND ".Actions_Model_Detail::COLUMN_DATE_START." <= :dateStop)"
//              ." OR (".Actions_Model_Detail::COLUMN_DATE_START." = :dateStart AND ".Actions_Model_Detail::COLUMN_TIME." >= CURTIME())"
//              ." OR (".Actions_Model_Detail::COLUMN_DATE_START." > :dateStart AND ".Actions_Model_Detail::COLUMN_DATE_START." <= :dateStop)"
//              ." OR (".Actions_Model_Detail::COLUMN_DATE_STOP." >= :dateStart AND ".Actions_Model_Detail::COLUMN_DATE_STOP." <= :dateStop) )"
              ." AND ( (ISNULL(`".Actions_Model_Detail::COLUMN_DATE_STOP."`) = 0 AND ".Actions_Model_Detail::COLUMN_DATE_START." <= :dateStart AND ".Actions_Model_Detail::COLUMN_DATE_STOP." >= :dateStart)"
              ." OR (ISNULL(`".Actions_Model_Detail::COLUMN_DATE_STOP."`) = 0 AND ".Actions_Model_Detail::COLUMN_DATE_START." >= :dateStart AND ".Actions_Model_Detail::COLUMN_DATE_START." <= :dateStop)"
              ." OR (ISNULL(`".Actions_Model_Detail::COLUMN_DATE_STOP."`) = 1 AND ".Actions_Model_Detail::COLUMN_DATE_START." = :dateStart AND ".Actions_Model_Detail::COLUMN_TIME." >= CURTIME())"
              ." OR (ISNULL(`".Actions_Model_Detail::COLUMN_DATE_STOP."`) = 1 AND ".Actions_Model_Detail::COLUMN_DATE_START." > :dateStart AND ".Actions_Model_Detail::COLUMN_DATE_START." < :dateStop) )"
//              ." ORDER BY delta_days ASC, ".Actions_Model_Detail::COLUMN_DATE_START." ASC, ".Actions_Model_Detail::COLUMN_DATE_STOP." ASC , timeord ASC"
              ." ORDER BY delta_days ASC, timeord ASC, ".Actions_Model_Detail::COLUMN_DATE_STOP." ASC");

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':dateStart', $fromTime->format("Y-m-d"), PDO::PARAM_STR);
      $dbst->bindValue(':dateStop', $toTime->format("Y-m-d"), PDO::PARAM_STR);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda vrací vybrané akce podle času
    * @return PDOStatement -- pole akcí
    */
   public function getFeaturedActions($idCat, $onlyPublic = true) {
      $dbc = Db_PDO::getInstance();

      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
              ." AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)"
//               ." AND (".Actions_Model_Detail::COLUMN_URLKEY.'_'.Locales::getLang()." IS NOT NULL)"
              ." AND ((".Actions_Model_Detail::COLUMN_DATE_START." >= CURDATE() AND ".Actions_Model_Detail::COLUMN_TIME." >= CURTIME())"
              ." OR (".Actions_Model_Detail::COLUMN_DATE_START." > CURDATE())"
              ." OR (".Actions_Model_Detail::COLUMN_DATE_START." >= DATE_ADD(CURDATE(), INTERVAL 1 DAY))"
              ." OR (".Actions_Model_Detail::COLUMN_DATE_START." < CURDATE() AND ".Actions_Model_Detail::COLUMN_DATE_STOP." >= CURDATE() ))"
              ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." ASC");
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }

   /**
    * Metoda vrací vybrané akce podle času
    * @return PDOStatement -- pole akcí
    */
   public function getActionsByAdded($idCat, $num = 100, $onlyPublic = true) {
      $dbc = Db_PDO::getInstance();
      if($onlyPublic) {
         $whereP = " AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)";
      } else {
         $whereP = null;
      }
      $dbst = $dbc->prepare("SELECT actions.*, user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." JOIN ".$this->usersTable." AS user ON actions.".Actions_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." = :idcat)"
              . $whereP // public
              ." ORDER BY ".Actions_Model_Detail::COLUMN_ADDED." DESC"
              ." LIMIT 0, :numAc");

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':numAc', (int)$num, PDO::PARAM_INT);
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
      $dbc = Db_PDO::getInstance();
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
   public function getActionsByCatIds(DateTime $fromTime, DateTime $toTime, $idCats, $onlyPublic = true) {
      $dbc = Db_PDO::getInstance();
      if($onlyPublic) {
         $whereP = " AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)";
      } else {
         $whereP = null;
      }

      $dbst = $dbc->prepare("SELECT actions.*, cat.".Model_Category::COLUMN_URLKEY.'_'.Locales::getLang()
              ." AS curlkey, cat.".Model_Category::COLUMN_DATADIR.", cat.".Model_Category::COLUMN_MODULE.","
              ." user.".Model_Users::COLUMN_USERNAME.","
              ." ABS(DATEDIFF(".Actions_Model_Detail::COLUMN_DATE_START.",:dateStart)) AS delta_days,"
              ." IF(actions.`time`,actions.`time`, '23:59:59') AS timeord"
              ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." JOIN ".$this->usersTable." AS user ON actions.".Actions_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON actions.".Actions_Model_Detail::COLUMN_ID_CAT
              ." = cat.".Model_Category::COLUMN_CAT_ID
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." IN (".$this->generateSQLIN($idCats)."))"
              . $whereP // public
//              ((ISNULL(`".Actions_Model_Detail::COLUMN_DATE_STOP."`) = 0 AND start_date <= '2010-03-10' AND stop_date >= '2010-03-10')
//      OR (ISNULL(`stop_date`) = 0 AND start_date >= '2010-03-10' AND start_date <= '2010-09-05')
//      OR (ISNULL(`stop_date`) = 1 AND start_date = '2010-03-10' AND `time` >= CURTIME())
//      OR (ISNULL(`stop_date`) = 1 AND start_date > '2010-03-10' AND start_date < '2010-09-10'))
              ." AND ( (ISNULL(`".Actions_Model_Detail::COLUMN_DATE_STOP."`) = 0 AND ".Actions_Model_Detail::COLUMN_DATE_START." <= :dateStart AND ".Actions_Model_Detail::COLUMN_DATE_STOP." >= :dateStart)"
              ." OR (ISNULL(`".Actions_Model_Detail::COLUMN_DATE_STOP."`) = 0 AND ".Actions_Model_Detail::COLUMN_DATE_START." >= :dateStart AND ".Actions_Model_Detail::COLUMN_DATE_START." <= :dateStop)"
              ." OR (ISNULL(`".Actions_Model_Detail::COLUMN_DATE_STOP."`) = 1 AND ".Actions_Model_Detail::COLUMN_DATE_START." = :dateStart AND `time` >= CURTIME())"
              ." OR (ISNULL(`".Actions_Model_Detail::COLUMN_DATE_STOP."`) = 1 AND ".Actions_Model_Detail::COLUMN_DATE_START." > :dateStart AND ".Actions_Model_Detail::COLUMN_DATE_START." < :dateStop) )"
//              ." AND ( (ISNULL(`".Actions_Model_Detail::COLUMN_TIME."`) AND ".Actions_Model_Detail::COLUMN_DATE_START." >= :dateStart AND ".Actions_Model_Detail::COLUMN_DATE_START." <= :dateStop)"
//              ." OR (".Actions_Model_Detail::COLUMN_DATE_START." = :dateStart AND `".Actions_Model_Detail::COLUMN_TIME."` >= CURTIME())"
//              ." OR (".Actions_Model_Detail::COLUMN_DATE_START." > :dateStart AND ".Actions_Model_Detail::COLUMN_DATE_START." <= :dateStop)"
//              ." OR (".Actions_Model_Detail::COLUMN_DATE_STOP." >= :dateStart AND ".Actions_Model_Detail::COLUMN_DATE_STOP." <= :dateStop) )"
//              ." ORDER BY delta_days ASC, ".Actions_Model_Detail::COLUMN_DATE_START." ASC, `".Actions_Model_Detail::COLUMN_TIME."` ASC, timeord ASC");
              ." ORDER BY delta_days ASC, timeord ASC, ".Actions_Model_Detail::COLUMN_DATE_STOP." ASC");
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':dateStart', $fromTime->format('Y-m-d'), PDO::PARAM_STR);
      $dbst->bindValue(':dateStop', $toTime->format('Y-m-d'), PDO::PARAM_STR);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda vrací vybrané akce podle času z navolených kategorií
    * @return PDOStatement -- pole akcí
    */
   public function getActionsListByCatIds($fromDate, $idCats, $fromRow = 0, $numRows = 100, $past = true) {
      $dbc = Db_PDO::getInstance();
      $sql = "SELECT actions.*, cat.".Model_Category::COLUMN_URLKEY.'_'.Locales::getLang()
              ." AS curlkey, cat.".Model_Category::COLUMN_DATADIR.", cat.".Model_Category::COLUMN_MODULE
              ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON actions.".Actions_Model_Detail::COLUMN_ID_CAT
              ." = cat.".Model_Category::COLUMN_CAT_ID
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." IN (".$this->generateSQLIN($idCats)."))";

      if($past == true) {
         // minulost
         $sql .= " AND (actions.".Actions_Model_Detail::COLUMN_DATE_START." <= :dateFrom)"
               ." AND (actions.".Actions_Model_Detail::COLUMN_DATE_STOP." <= :dateFrom OR ISNULL(actions.".Actions_Model_Detail::COLUMN_DATE_STOP."))"
              ." ORDER BY actions.".Actions_Model_Detail::COLUMN_DATE_START." DESC, ISNULL(actions.".Actions_Model_Detail::COLUMN_TIME.") DESC, actions.".Actions_Model_Detail::COLUMN_TIME." DESC";
      } else {
         // budoucnost
         $sql .= " AND (actions.".Actions_Model_Detail::COLUMN_DATE_START." >= :dateFrom)"
              ." ORDER BY actions.".Actions_Model_Detail::COLUMN_DATE_START." ASC, ISNULL(actions.".Actions_Model_Detail::COLUMN_TIME.") DESC, actions.".Actions_Model_Detail::COLUMN_TIME." DESC";
      }

      $sql .= " LIMIT :fromRow, :numRows";

      $dbst = $dbc->prepare($sql);

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':dateFrom', $fromDate->format('Y-m-d'), PDO::PARAM_INT);
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':numRows', (int)$numRows, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }

   /**
    * Metoda vrací vybrané akce podle času z navolených kategorií
    * @return PDOStatement -- pole akcí
    */
   public function getCountActionsListByCatIds($fromDate, $idCats, $past = true) {
      $dbc = Db_PDO::getInstance();

      $sql = "SELECT COUNT(".Actions_Model_Detail::COLUMN_ID.") FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)
              ." WHERE (".Actions_Model_Detail::COLUMN_ID_CAT ." IN (".$this->generateSQLIN($idCats)."))";

      if($past) {
         // minulost
         $sql .= " AND (".Actions_Model_Detail::COLUMN_DATE_START." <= :dateFrom)";
      } else {
         // budoucnost
         $sql .= " AND (".Actions_Model_Detail::COLUMN_DATE_START." >= :dateFrom)";
      }

      $dbst = $dbc->prepare($sql);

      $dbst->bindValue(':dateFrom', $fromDate->format('Y-m-d'), PDO::PARAM_INT);
      $dbst->execute();
      $count = $dbst->fetch();
      return $count[0];
   }

   /**
    * Metoda vrací vybrané akce podle času z navolených kategorií
    * @return PDOStatement -- pole akcí
    */
   public function getActionsByAddedByCatIds($idCats, $num = 10) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT actions.*, cat.".Model_Category::COLUMN_URLKEY.'_'.Locales::getLang()
              ." AS curlkey ,user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." JOIN ".$this->usersTable." AS user ON actions.".Actions_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON actions.".Actions_Model_Detail::COLUMN_ID_CAT
              ." = cat.".Model_Category::COLUMN_CAT_ID
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." IN (".$this->generateSQLIN($idCats)."))"
              ." AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)"
              ." ORDER BY ".Actions_Model_Detail::COLUMN_ADDED." DESC"
              ." LIMIT 0, :numAc");

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':numAc', (int)$num, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda vrací vybrané akce podle času
    * @return PDOStatement -- pole akcí
    */
   public function getFeaturedActionsByCatIds($idCats) {
      $dbc = Db_PDO::getInstance();
      $in = null;
      foreach ($idCats as $idC) {
         $in .= $dbc->quote($idC, PDO::PARAM_INT).',';
      }
      $in = substr($in, 0, strlen($in)-1);
      $dbst = $dbc->prepare("SELECT actions.*, cat.".Model_Category::COLUMN_URLKEY.'_'.Locales::getLang()
              ." AS curlkey, cat.".Model_Category::COLUMN_DATADIR.", cat.".Model_Category::COLUMN_MODULE
              ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE)." AS actions"
              ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON actions.".Actions_Model_Detail::COLUMN_ID_CAT
              ." = cat.".Model_Category::COLUMN_CAT_ID
              ." WHERE (actions.".Actions_Model_Detail::COLUMN_ID_CAT ." IN (".$in."))"
              ." AND (".Actions_Model_Detail::COLUMN_PUBLIC." = 1)"
              ." AND ((".Actions_Model_Detail::COLUMN_DATE_START." >= CURDATE())"
//              ." AND ((".Actions_Model_Detail::COLUMN_DATE_START." >= CURDATE() AND ".Actions_Model_Detail::COLUMN_TIME." >= CURTIME())"
              ." OR (".Actions_Model_Detail::COLUMN_DATE_START." >= DATE_ADD(CURDATE(), INTERVAL 1 DAY)))"
              ." ORDER BY ".Actions_Model_Detail::COLUMN_DATE_START." ASC, ".Actions_Model_Detail::COLUMN_TIME." ASC");
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->execute();
      return $dbst;
   }

   /**
    * Metoda pro vybrání všech akcí včetně kategorie - INTERNÍ PRO UPDATE
    * @return Array of objects (prop.: actkey, catkey, img)
    */
   public function getAllActionsWithCats()
   {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT "
         ." act.".Actions_Model_Detail::COLUMN_URLKEY.'_'.Locales::getDefaultLang().' AS actkey,'
         ." act.".Actions_Model_Detail::COLUMN_IMAGE.' AS img,'
         ." cat.".Model_Category::COLUMN_URLKEY.'_'.Locales::getDefaultLang().' AS catkey'
         
         ." FROM ".Db_PDO::table(Actions_Model_Detail::DB_TABLE).' AS act'
         ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cat ON act.".Actions_Model_Detail::COLUMN_ID_CAT." = cat.".Model_Category::COLUMN_CAT_ID);
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();
      return $dbst;
   }
}

?>