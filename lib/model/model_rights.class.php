<?php
/**
 * Třída s modelem pro práci s uživateli
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: model_module.class.php 625 2009-06-13 16:01:09Z jakub $ VVE 5.1.0 $Revision: 625 $
 * @author			$Author: jakub $ $Date: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 * @abstract 		Třída s modelem pro práci s uživateli
 */

class Model_Rights extends Model_PDO {
/**
 * Název tabulky s uživateli
 */
   const DB_TABLE = 'rights';

   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
   const COLUMN_ID         = 'id_right';
   const COLUMN_ID_CATEGORY   = 'id_category';
   const COLUMN_ID_GROUP   = 'id_group';
   const COLUMN_RIGHT   = 'right';

   /**
    * Metoda načte kategori, pokud je zadán klíč je načtena určitá, pokud ne je
    * načtena kategorie s nejvyšší prioritou
    * @param string $catKey -- (option) klíč kategorie
    * @return PDOStatement
    */
   public function getRights($idCat) {
   //      $idCat = 2;
      $dbc = new Db_PDO();
      //      var_dump("SELECT * FROM ".self::getRightsTable()." AS rights"
      //             ." JOIN ".Model_Users::getGroupsTable()." AS grps ON rights.".self::COLUMN_ID_GROUP." = grps.".Model_Users::COLUMN_ID_GROUP
      //             ." WHERE (rights.".self::COLUMN_ID_CATEGORY." = ".$idCat.") GROUP BY grps.".Model_Users::COLUMN_ID_GROUP);

      $dbst = $dbc->prepare("SELECT *, grps.name AS gname FROM ".self::getRightsTable()." AS rights"
          ." JOIN ".Model_Users::getGroupsTable()." AS grps ON rights.".self::COLUMN_ID_GROUP." = grps.".Model_Users::COLUMN_ID_GROUP
          ." WHERE (rights.".self::COLUMN_ID_CATEGORY." = :idcat ) GROUP BY grps.".Model_Users::COLUMN_ID_GROUP);
      $dbst->bindValue(':idcat', $idCat, PDO::PARAM_INT);
      $dbst->execute();
      return $dbst;
   }


   /**
    * Metoda uloží práva skupiny ke kategorii
    * @param string $right -- řetězec s právy
    * @param int $idGroup -- id skupiny
    * @param int $idCat -- id kategorie
    */
   public function saveRight($right, $idGroup, $idCat, $tablePrefix = null) {
      if($tablePrefix === null) $table = self::getRightsTable();
      else $table = $tablePrefix.self::DB_TABLE;

      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT COUNT(*) FROM ".$table." AS rights"
          ." WHERE (rights.".self::COLUMN_ID_CATEGORY." = ".$dbc->quote($idCat)." AND rights.".self::COLUMN_ID_GROUP." = ".$dbc->quote($idGroup).")");
      if ($dbst) {
         if ($dbst->fetchColumn() > 0) {
         // update
            $dbst = $dbc->prepare("UPDATE ".$table. " SET"
                ." `".self::COLUMN_RIGHT."` = :rights"
                ." WHERE (".self::COLUMN_ID_CATEGORY." = :idcat AND ".self::COLUMN_ID_GROUP." = :idgrp)");

         } else {
         // insert
            $dbst = $dbc->prepare("INSERT INTO ".$table
                ." (`".self::COLUMN_ID_CATEGORY."`, `".self::COLUMN_ID_GROUP."`, `".self::COLUMN_RIGHT."`)"
                ." VALUES (:idcat, :idgrp, :rights)");
         }
         $dbst->bindValue(':idcat', (int)$idCat, PDO::PARAM_INT);
         $dbst->bindValue(':idgrp', (int)$idGroup, PDO::PARAM_INT);
         $dbst->bindValue(':rights', $right, PDO::PARAM_STR);
         $dbst->execute();
         return $dbst;
      }
      return false;
   }

   /**
    * Metoda vymaže práva
    * @param int $idCat -- id kategorie
    * @deprecated -- použít funkci deleteRightsByCatID
    */
   public function deleteCatRights($idCat) {
      return $this->deleteRightsByCatID($idCat);
   }

   /**
    * Metoda vymaže práva
    * @param int $idCat -- id kategorie
    */
   public function deleteRightsByCatID($idCat) {
      $dbc = new Db_PDO();
      $st = $dbc->prepare("DELETE FROM ".self::getRightsTable()
          . " WHERE ".self::COLUMN_ID_CATEGORY." = :idcat ");
      return $st->execute(array(':idcat' => $idCat));
   }

   /**
    * Metoda vymaže práva podle id skupiny
    * @param int $idGroup -- id skupiny
    */
   public function deleteRightsByGrID($idGrp, $tablePrefix = null) {
      if($tablePrefix === null) $table = self::getRightsTable();
      else $table = $tablePrefix.self::DB_TABLE;

      $dbc = new Db_PDO();
      $st = $dbc->prepare("DELETE FROM ".$table
          . " WHERE ".self::COLUMN_ID_GROUP." = :idgrp ");
      return $st->execute(array(':idgrp' => $idGrp));
   }

   /**
    * Metoda vrací název tabulky s právy (včetně prefixu)
    * @return string -- název tabulky
    */
   public static function getRightsTable() {
      return Db_PDO::table(self::DB_TABLE);
   }
}
?>