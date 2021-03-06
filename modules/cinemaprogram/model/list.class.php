<?php
/*
 * Třída modelu s listem Novinek
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
   public function getCountArticles() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT COUNT(*) FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE));
//          ." WHERE (".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = '".$idCat."')");
      $count = $dbst->fetch();
      return $count[0];
   }

   /**
    * Metoda vrací pole se všemi články
    * @return array -- pole článků
    */
   public function getList($fromRow = 0, $rowsCount = 100) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
          ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
          ." JOIN ".$this->usersTable." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
          ." = user.".Model_Users::COLUMN_ID
//          ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat)"
          ." ORDER BY ".Articles_Model_Detail::COLUMN_ADD_TIME." DESC"
          ." LIMIT :fromRow, :rowCount ");

      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
//      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':rowCount', (int)$rowsCount, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda vrací pole se články, seřazenými podle počtu zobrazení
    * @return array -- pole článků
    */
   public function getListTop($fromRow = 0, $rowsCount = 100) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
          ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
          ." JOIN ".$this->usersTable." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
          ." = user.".Model_Users::COLUMN_ID
//          ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat)"
          ." ORDER BY ".Articles_Model_Detail::COLUMN_SHOWED." DESC"
          ." LIMIT :fromRow, :rowCount ");
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
//      $dbst->bindParam(':idcat', $idCat, PDO::PARAM_INT);
      $dbst->bindParam(':fromRow', $fromRow, PDO::PARAM_INT);
      $dbst->bindParam(':rowCount', $rowsCount, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }
}

?>