<?php
/**
 * Třída modelu s detailem textu
 * @deprecated use Guestbook_Model
*/
class GuestBook_Model_Detail extends GuestBook_Model {
   /**
    * Metoda provede načtení textu z db
    *
    * @return string -- načtený text
    */
   public function getText($idCat) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)." AS text
             WHERE (text.".self::COLUMN_ID_CATEGORY." = :idCat)");
      $dbst->bindParam(':idCat', $idCat, PDO::PARAM_INT);
      $dbst->execute();

      $cl = new Model_LangContainer();
      $dbst->setFetchMode(PDO::FETCH_INTO, $cl);
      return $dbst->fetch();
   }

   /**
    * Metoda provede načtení seznamu příspěvků
    *
    * @return string -- načtený text
    */
   public function getList($idCat, $fromRow, $rowsCount = 100, $deleted = false) {
      $dbc = Db_PDO::getInstance();
      if($deleted === false) {
         $whereDel = " AND (".self::COL_DELETED." = 0)";
      } else {
         $whereDel = null;
      }
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE (".self::COL_ID_CAT ." = :idcat)"
              . $whereDel // deleted
              ." ORDER BY ".self::COL_DATE_ADD." DESC"
              ." LIMIT :fromRow, :rowCount ");
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':rowCount', (int)$rowsCount, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda provede načtení seznamu příspěvků
    *
    * @return string -- načtený text
    */
   public function getListAll($fromRow, $rowsCount = 100, $deleted = false) {
      $dbc = Db_PDO::getInstance();
      if($deleted === false) {
         $whereDel = " WHERE (".self::COL_DELETED." = 0)";
      } else {
         $whereDel = null;
      }
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              . $whereDel // deleted
              ." ORDER BY ".self::COL_DATE_ADD." DESC"
              ." LIMIT :fromRow, :rowCount ");
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->bindValue(':fromRow', (int)$fromRow, PDO::PARAM_INT);
      $dbst->bindValue(':rowCount', (int)$rowsCount, PDO::PARAM_INT);
      $dbst->execute();

      return $dbst;
   }

   public function saveBook($idCat, $email, $text, $nick, $www = null, $idBook = null) {
      $dbc = Db_PDO::getInstance();
      
      $client = NULL;
      if(isset ($_SERVER['HTTP_USER_AGENT'])){
         $client = $_SERVER['HTTP_USER_AGENT'];
      }
      $ip = NULL;
      if(isset ($_SERVER['HTTP_USER_AGENT'])){
         $client = $_SERVER['HTTP_USER_AGENT'];
      }

      if($id !== null) {
         $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET `".self::COL_ID_CAT."` = :idcat,"
          ." `".self::COL_EMAIL."` = :email, `".self::COL_TEXT."` = :text,"
          ." `".self::COL_WWW."` = :web, `".self::COL_NICK."` = :nick,"
          ." `".self::COL_IP."` = :ip, `".self::COL_CLIENT."` = :client"
          ." WHERE ".self::COL_ID." = :idbook");

         $dbst->bindValue(':idcat', $idCat, PDO::PARAM_INT);
         $dbst->bindValue(':email', $email, PDO::PARAM_STR);
         $dbst->bindValue(':text', $text, PDO::PARAM_STR);
         $dbst->bindValue(':nick', $nick, PDO::PARAM_STR);
         $dbst->bindValue(':web', $www, PDO::PARAM_STR);
         $dbst->bindValue(':ip', $ip, PDO::PARAM_STR);
         $dbst->bindValue(':client', $client, PDO::PARAM_STR);
         $dbst->bindValue(':idbook', $idBook, PDO::PARAM_INT);

         return $dbst->execute();
      } else {
         $dbst = $dbc->prepare("INSERT INTO ".Db_PDO::table(self::DB_TABLE)
                 ."  (`".self::COL_ID_CAT."`, `"
                 .self::COL_EMAIL."`, `".self::COL_TEXT."`, `".self::COL_WWW."`, `".self::COL_NICK."`,`"
                 .self::COL_IP."`,`".self::COL_CLIENT."`)"
                 ." VALUES (:idcat, :email, :text, :web, :nick, :ip, :client)");

         $dbst->bindValue(':idcat', $idCat, PDO::PARAM_INT);
         $dbst->bindValue(':email', $email, PDO::PARAM_STR);
         $dbst->bindValue(':text', $text, PDO::PARAM_STR);
         $dbst->bindValue(':web', $www, PDO::PARAM_STR);
         $dbst->bindValue(':nick', $nick, PDO::PARAM_STR);
         $dbst->bindValue(':ip', $ip, PDO::PARAM_STR);
         $dbst->bindValue(':client', $client, PDO::PARAM_STR);

         $dbst->execute();
         return $dbc->lastInsertId();
      }
   }

   public function getLastChange($idCat) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT ".self::COLUMN_CHANGED_TIME." AS tm FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_ID_CATEGORY." = :idcategory");
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
    * Metoda vymaže prvek s guestbooku
    * @param int $idItem -- id prvku
    * @return PDOStatement
    */
   public function deleteItem($idBook) {
      $dbc = Db_PDO::getInstance();
      
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)
          ." SET `".self::COL_DELETED."` = 1"
          ." WHERE ".self::COL_ID." = :idbook");
         $dbst->bindValue(':idbook', $idBook, PDO::PARAM_INT);
         return $dbst->execute();
   }

   /**
    * Vrací počet příspěvků
    * @param int $idCat -- id kategorie
    * @return int
    */
   public function getCount($idCat, $deleted = false){
      $dbc = Db_PDO::getInstance();
      if($deleted === false) {
         $dbst = $dbc->query("SELECT COUNT(".self::COL_ID.") FROM ".Db_PDO::table(self::DB_TABLE)
                 ." WHERE (".self::COL_ID_CAT ." = '".$idCat."') AND (".self::COL_DELETED." = 0)");
      } else {
         $dbst = $dbc->query("SELECT COUNT(".self::COL_ID.") FROM ".Db_PDO::table(self::DB_TABLE)
                 ." WHERE (".self::COL_ID_CAT ." = '".$idCat."')");
      }
      $count = $dbst->fetch();
      return $count[0];
   }

   /**
    * Metoda provede hledání textu
    * @param integer $idCat -- id kategorie
    * @param string $string -- hledaný řetězec
    * @return PDOStatement
    */
//   public function search($idCat, $string) {
//      $dbc = Db_PDO::getInstance();
//      $clabel = self::COLUMN_LABEL.'_'.Locales::getLang();
//      $ctext = self::COLUMN_TEXT_CLEAR.'_'.Locales::getLang();
//
//      $dbst = $dbc->prepare('SELECT *, ('.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER/2).' * MATCH(`'.$clabel.'`) AGAINST (:sstring)'
//              .' + MATCH(`'.$ctext.'`) AGAINST (:sstring)) as '.Search::COLUMN_RELEVATION
//              .' FROM '.Db_PDO::table(self::DB_TABLE)
//              .' WHERE MATCH(`'.$clabel.'`, `'.$ctext.'`) AGAINST (:sstring IN BOOLEAN MODE)'
//              .' AND `'.self::COLUMN_ID_CATEGORY.'` = :idCat'
//              .' ORDER BY '.round(VVE_SEARCH_ARTICLE_REL_MULTIPLIER/2)
//              .' * MATCH(`'.$clabel.'`) AGAINST (:sstring) + MATCH(`'.$ctext.'`) AGAINST (:sstring) DESC');
//
//      $dbst->bindValue(':idCat', $idCat, PDO::PARAM_INT);
//      $dbst->bindValue(':sstring', $string, PDO::PARAM_STR);
//      $dbst->setFetchMode(PDO::FETCH_CLASS, 'Model_LangContainer');
//      $dbst->execute();
//
//      return $dbst->fetch();
//   }
}

?>