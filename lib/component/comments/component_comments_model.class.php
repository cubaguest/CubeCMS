<?php
/**
 * Třída Modelu pro načítání a ukládání komentářů
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.1.0 $Revision: $
 * @author      $Author: $ $Date:  $
 *              $LastChangedBy:$ $LastChangedDate:  $
 * @abstract    Třída pro vytvoření modelu pro práci s komentáři
 */

class Component_Comments_Model extends Model_PDO {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'comments';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COL_ID			= 'id_comment';
   const COL_ID_ART		= 'id_article';
   const COL_ID_CAT		= 'id_category';
   const COL_ID_PARENT	= 'id_parent';
   const COL_NICK       = 'nick';
   const COL_COMMENT		= 'comment';
   const COL_PUBLIC		= 'public';
   const COL_CENSORED	= 'censored';
   const COL_ORDER		= 'corder';
   const COL_LEVEL		= 'level';
   const COL_TIME_ADD	= 'time_add';
   const COL_IP_ADDRESS	= 'ip_address';

   public function saveComment($nick, $comment, $idCat, $idArt, $idParent = 0, $public = true) {
      $dbc = new Db_PDO();

      $tbl = $dbc->table(self::DB_TABLE);
      $order = 1;
      $level = 0;

      $dbc->query("LOCK TABLES $tbl WRITE");
      $whereCatArt = self::COL_ID_CAT." = :idcat AND ".self::COL_ID_ART." = :idart";

      // rodič
      $dbstParent = $dbc->prepare("SELECT ".self::COL_ORDER.",".self::COL_LEVEL." FROM $tbl WHERE ".self::COL_ID." = :idp");
      $dbstParent->execute(array(':idp' => (int)$idParent));
      if($idParent != 0 AND $row = $dbstParent->fetch()) {
         $dbstParent = $dbc->prepare("SELECT MIN(".self::COL_ORDER.")-1, ".$row[1]."+1 "
                 ."FROM $tbl WHERE ".self::COL_ORDER." > :rorder AND ".self::COL_LEVEL." <= :rlevel AND $whereCatArt");

         $dbstParent->execute(array(':idcat' => $idCat,':idart' => $idArt, ':rorder' => $row[0], ':rlevel'=> $row[1]));
         $row = $dbstParent->fetch();
         if((int)$row[0] > 0) {// bude se vkládat doprostřed tabulky, posunout následující záznamy
            print ('update');
            $dbstParent = $dbc->prepare("UPDATE $tbl SET ".self::COL_ORDER." = ".self::COL_ORDER."+1 "
                    ."WHERE ".self::COL_ORDER." > :curord AND $whereCatArt");
            $dbstParent->execute(array(':idcat' => $idCat, ':idart' => $idArt, ':curord' => $row[0]));

         } else {
            $dbstParent = $dbc->query("SELECT MAX(".self::COL_ORDER."),".$row[1]." FROM $tbl");
            $row = $dbstParent->fetch();
         }
         if($row != false) {
            $order = (int)$row[0];
            $level = (int)$row[1];
         }

      } else { // neni reakce
         $dbstParent = $dbc->prepare("SELECT MAX(`".self::COL_ORDER."`) FROM $tbl WHERE $whereCatArt");
         $dbstParent->execute(array(':idcat' => $idCat, ':idart' => $idArt));
         $row = $dbstParent->fetch();
         if($row != false) {
            $order = (int)$row[0];
         }
      }
      $dbstIns = $dbc->prepare("INSERT INTO $tbl (".self::COL_ID_CAT.",".self::COL_ID_ART.",".self::COL_ID_PARENT.","
              .self::COL_ORDER.",".self::COL_LEVEL.","
              .self::COL_NICK.",".self::COL_COMMENT.",".self::COL_TIME_ADD.",".self::COL_IP_ADDRESS.",".self::COL_PUBLIC.") "
              ."VALUES (:idcat, :idart, :idparent, :corder, :clevel, :nick, :comment, NOW(), :ipaddr, :public)");
      $dbstIns->bindValue(':idcat', $idCat, PDO::PARAM_INT);
      $dbstIns->bindValue(':idart', $idArt, PDO::PARAM_INT);
      $dbstIns->bindValue(':idparent', $idParent, PDO::PARAM_INT);
      $dbstIns->bindValue(':corder', $order+1, PDO::PARAM_INT);
      $dbstIns->bindValue(':clevel', $level, PDO::PARAM_INT);
      $dbstIns->bindValue(':nick', $nick, PDO::PARAM_STR);
      $dbstIns->bindValue(':comment', $comment, PDO::PARAM_STR);
      $dbstIns->bindValue(':ipaddr', $_SERVER['REMOTE_ADDR'], PDO::PARAM_STR);
      $dbstIns->bindValue(':public', $public, PDO::PARAM_BOOL);

      $dbstIns->execute();

      $dbc->query("UNLOCK TABLES");
   }

   /**
    * Metoda načte všechny sdílení
    * @return PDOStatement
    */
   public function getComments($idCat, $idArt, $onlyPublic = true) {
      $dbc = new Db_PDO();
      if($onlyPublic == true) {
         $dbst = $dbc->prepare("SELECT * FROM ".$dbc->table(self::DB_TABLE)
                 ." WHERE ".self::COL_ID_CAT." = :idcat AND ".self::COL_ID_ART." = :idart"
                 ." AND ".self::COL_PUBLIC." = 1"
                 ." ORDER BY ".self::COL_ORDER);
         $dbst->execute(array(':idcat' => $idCat, ':idart' => $idArt));
      } else {
         $dbst = $dbc->prepare("SELECT * FROM ".$dbc->table(self::DB_TABLE)
                 ." WHERE ".self::COL_ID_CAT." = :idcat AND ".self::COL_ID_ART." = :idart"
                 ." ORDER BY ".self::COL_ORDER);
         $dbst->execute(array(':idcat' => $idCat, ':idart' => $idArt));
      }

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetchAll();
   }

   public function changePublic($idComment) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".$dbc->table(self::DB_TABLE)." SET ".self::COL_PUBLIC." = IF(".self::COL_PUBLIC."=1,0,1) WHERE ".self::COL_ID." = :idc");
      $dbst->execute(array(':idc' => $idComment));

   }

   public function changeCensored($idComment) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".$dbc->table(self::DB_TABLE)." SET ".self::COL_CENSORED." = IF(".self::COL_CENSORED."=1,0,1) WHERE ".self::COL_ID." = :idc");
      $dbst->execute(array(':idc' => $idComment));

   }

   public function getCountComments($idCat, $idArt){
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT COUNT(".self::COL_ID.") AS cnt FROM ".$dbc->table(self::DB_TABLE)
                 ." WHERE ".self::COL_ID_CAT." = :idcat AND ".self::COL_ID_ART." = :idart"
              ." AND ".self::COL_PUBLIC." = 1");
      $dbst->execute(array(':idcat' => $idCat, ':idart' => $idArt));
      $cnt = $dbst->fetchObject();
      return $cnt->cnt;
   }

}
?>