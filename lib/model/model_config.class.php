<?php
/**
 * Třída Modelu pro práci s konfiguračními volbami systému
 * Třída, která umožňuje pracovet s modelem konfiguračních voleb
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: model_module.class.php 648 2009-09-16 16:20:04Z jakub $ VVE3.9.2 $Revision: 648 $
 * @author			$Author: jakub $ $Date: 2009-09-16 18:20:04 +0200 (St, 16 zář 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-09-16 18:20:04 +0200 (St, 16 zář 2009) $
 * @abstract 		Třída pro vytvoření modelu pro práci s moduly
 */

class Model_Config extends Model_PDO {
/**
 * Tabulka s detaily
 */
   const DB_TABLE = 'config';

   /**
    * slouce v db
    */
   const COLUMN_ID = 'id_config';
   const COLUMN_KEY = 'key';
   const COLUMN_VALUE = 'value';
   const COLUMN_VALUES = 'values';
   const COLUMN_PROTECTED = 'protected';
   const COLUMN_TYPE = 'type';
   const COLUMN_LABEL = 'label';


   /**
    * Metoda načte konfigurační volby
    * @return PDOStatement -- konfigurační volby
    */
   public function getConfigStat() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE));
      return $dbst;
   }

   public function saveNewCfg($key, $value) {
      ;
   }

   public function saveCfg($key,$value) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("UPDATE ".Db_PDO::table(self::DB_TABLE)." SET `value` = :val WHERE `key` = :key");
      $dbst->bindValue(':key', $key, PDO::PARAM_STR);
      $dbst->bindValue(':val', $value, PDO::PARAM_STR);
      //      $dbst->debugDumpParams();
      //      exit();
      return $dbst->execute();
   }

   public function getList() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COLUMN_PROTECTED." = 0)");

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();

      return $dbst;
   }

   public function getOption($id) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
          ." WHERE (".self::COLUMN_ID." = :id)");
      $dbst->bindValue(":id", $id, PDO::PARAM_INT);

      $dbst->execute();
      return $dbst->fetchObject();
   }
}
?>