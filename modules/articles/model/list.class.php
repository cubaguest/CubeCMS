<?php
/*
 * Třída modelu s listem Novinek
*/
class Articles_Model_List extends Model_PDO {
   /**
    * Metoda vrací počet článků
    *
    * @return integer -- počet článků
    */
   public function getCountArticles($idCat, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $dbst = $dbc->query("SELECT COUNT(*) FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)
                 ." WHERE (".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = '".$idCat."')"
                 ." AND (".Articles_Model_Detail::COLUMN_PUBLIC." = 1)");
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
   public function getList($idCat, $onlyPublic = true, $fromRow = 0, $rowsCount = 100) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $wherePub = " AND (article.".Articles_Model_Detail::COLUMN_PUBLIC." = 1)";
      } else {
         $wherePub = null;
      }
         $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
                 ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
                 ." JOIN ".Model_Users::getUsersTable()." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
                 ." = user.".Model_Users::COLUMN_ID
                 ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat)"
                 . $wherePub // public
                 ." ORDER BY ".Articles_Model_Detail::COLUMN_ADD_TIME." DESC"
                 ." LIMIT :fromRow, :rowCount ");
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
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
         $wherePub = " AND (article.".Articles_Model_Detail::COLUMN_PUBLIC." = 1)";
      } else {
         $wherePub = null;
      }
         $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
                 ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
                 ." JOIN ".Model_Users::getUsersTable()." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
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
   public function getListTop($idCat, $onlyPublic = true, $fromRow = 0, $rowsCount = 100) {
      $dbc = new Db_PDO();
      if($onlyPublic) {
         $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
                 ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
                 ." JOIN ".Model_Users::getUsersTable()." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
                 ." = user.".Model_Users::COLUMN_ID
                 ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat)"
                 ." AND (article.".Articles_Model_Detail::COLUMN_PUBLIC." = 1)"
                 ." ORDER BY ".Articles_Model_Detail::COLUMN_SHOWED." DESC"
                 ." LIMIT :fromRow, :rowCount ");
      } else {
         $dbst = $dbc->prepare("SELECT article.*, user.".Model_Users::COLUMN_USERNAME
                 ." FROM ".Db_PDO::table(Articles_Model_Detail::DB_TABLE)." AS article"
                 ." JOIN ".Model_Users::getUsersTable()." AS user ON article.".Articles_Model_Detail::COLUMN_ID_USER
                 ." = user.".Model_Users::COLUMN_ID
                 ." WHERE (article.".Articles_Model_Detail::COLUMN_ID_CATEGORY ." = :idcat)"
                 ." ORDER BY ".Articles_Model_Detail::COLUMN_SHOWED." DESC"
                 ." LIMIT :fromRow, :rowCount ");
      }
      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
      $dbst->bindParam(':idcat', $idCat, PDO::PARAM_INT);
      $dbst->bindParam(':fromRow', $fromRow, PDO::PARAM_INT);
      $dbst->bindParam(':rowCount', $rowsCount, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }
}

?>