<?php
/**
 * Třída modelu s listem Novinek
 * @deprecated -- není nutná stačí přistupovat pře orm record
*/
class Articles_Model_List extends Model_PDO {
   
   private $usersTable = null;

   public function __construct()
   {
      $modelUsers = new Model_Users();
      $this->usersTable = $modelUsers->getTableName();
      parent::__construct();
   }
   
   /**
    * Metoda vrací počet článků
    *
    * @return integer -- počet článků
    */
   public function getCountArticles($idCat, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $dbst = $dbc->query("SELECT COUNT(".Articles_Model_Detail::COLUMN_ID.")"
                 ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)
                 ." WHERE (".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = '".$idCat."')"
                 ." AND (".Articles_Model_Detail::COLUMN_CONCEPT." = 0)");
      } else {
         $dbst = $dbc->query("SELECT COUNT(*) FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)
                 ." WHERE (".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = '".$idCat."')");
      }
      $count = $dbst->fetch();
      return $count[0];
   }

   /**
    * Metoda vrací pole se všemi články
    * @return array -- pole článků
    */
   public function getList($idCat, $fromRow = 0, $rowsCount = 100, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $wherePub = " AND (article.".Articles_Model_Detail::COLUMN_CONCEPT." = 0)";
      } else {
         $wherePub = null;
      }
      $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
              ." JOIN ".$this->usersTable." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat)"
              . $wherePub // public
              ." ORDER BY ".Articles_Model_Detail::COLUMN_ADD_TIME." DESC"
              ." LIMIT :fromRow, :rowCount ");
      $dbst->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, 'Model_LangContainer', array(Articles_Model_Detail::COLUMN_NAME));
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':rowCount', (int)$rowsCount, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }
   /**
    * Metoda vrací pole se všemi články
    * @return array -- pole článků
    */
   public function getListAll($idCat, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $wherePub = " AND (article.".Articles_Model_Detail::COLUMN_CONCEPT." = 0)";
      } else {
         $wherePub = null;
      }
      $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
              ." JOIN ".$this->usersTable." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat)"
              . $wherePub // public
              ." ORDER BY ".Articles_Model_Detail::COLUMN_ADD_TIME." DESC");
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda vrací pole se články, seřazenými podle počtu zobrazení
    * @return array -- pole článků
    */
   public function getListTop($idCat, $fromRow = 0, $rowsCount = 100, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $whereP = " AND (article.".Articles_Model_Detail::COLUMN_CONCEPT." = 0)";
      } else {
         $whereP = null;
      }
      
      $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
              ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
              ." JOIN ".$this->usersTable." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat)"
              . $whereP
              ." ORDER BY ".Articles_Model_Detail::COLUMN_SHOWED." DESC"
              ." LIMIT :fromRow, :rowCount ");
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':rowCount', (int)$rowsCount, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }


   /**
    * Metoda vrací poslední změnu článků v dané kategorii
    * @param int $id -- id kategorie
    * @return int -- timestamp
    */
   public function getLastChange($id, $onlyPublic = true) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT ".Articles_Model_Detail::COLUMN_EDIT_TIME." AS et FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
              ." WHERE (".Articles_Model_Detail::COLUMN_ID_CATEGORY." = :id) AND (".Articles_Model_Detail::COLUMN_CONCEPT." = :onlyPublic)"
              ." ORDER BY ".Articles_Model_Detail::COLUMN_EDIT_TIME." DESC"
              ." LIMIT 0, 1");
      $dbst->bindParam(':id', $id, PDO::PARAM_INT);
      $dbst->bindValue(':onlyPublic', (int)!$onlyPublic, PDO::PARAM_INT);
      $dbst->execute();

      $fetch = $dbst->fetchObject();
      if($fetch != false) {
         return $fetch->et;
      }
      return false;
   }

   /**
    * Metoda vrací pole se všemi články
    * @return array -- pole článků
    */
   public function getListByCats($idCats, $num = 10, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $wherePub = " AND (article.".Articles_Model_Detail::COLUMN_CONCEPT." = 0)";
      } else {
         $wherePub = null;
      }
      $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME.", cats.".Model_Category::COLUMN_URLKEY.'_'.Locales::getLang()." AS curlkey"
              ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
              ." JOIN ".$this->usersTable." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
              ." = user.".Model_Users::COLUMN_ID
              ." JOIN ".Db_PDO::table(Model_Category::DB_TABLE)." AS cats ON article.".Articles_Model_Detail::COLUMN_ID_CATEGORY
              ." = cats.".Model_Category::COLUMN_CAT_ID
              ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." IN (".$this->generateSQLIN($idCats)."))"
              . $wherePub // public
              ." ORDER BY article.".Articles_Model_Detail::COLUMN_ADD_TIME." DESC"
              ." LIMIT 0, :rowCount ");
      
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindValue(':rowCount', (int)$num, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst->fetchAll();
   }
}

?>