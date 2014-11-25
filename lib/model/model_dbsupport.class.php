<?php
/**
 * Třída Modelu pro práci s celou databází, jsou zde metody pro výběr tabulek atd
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE6.0.0 $Revision: $
 * @author			$Author: $ $Date: $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro práci s celou databází
 */

class Model_DbSupport extends Model_PDO {
   const COL_TB_TABLE_NAMES = 'table_name';
   const COL_TB_TABLE_SCHEMA = 'table_schema';

   const DB_NAME_WITH_SCHEMAS = 'information_schema';
   const DB_TABLE_TABLES = 'tables';

   /**
    * Metoda načte všechny kategorie
    * @param bool $allCategories -- jestli mají být vráceny všechny kategorie
    * @param bool $withRights -- jestli mají být načtený pouze kategorie na které ma uživatel práva
    * @return PDOStatement -- objekt s daty
    */
   public function getTables($tableName) {
      $dbc = Db_PDO::getInstance();

      $dbst = $dbc->prepare('SELECT '.self::COL_TB_TABLE_NAMES.' FROM '.self::DB_NAME_WITH_SCHEMAS.'.'.self::DB_TABLE_TABLES
         .' WHERE '.self::COL_TB_TABLE_SCHEMA.' = :dbname AND '.self::COL_TB_TABLE_NAMES.' LIKE :tblname');// AND table_name != :thistabel');
      
      $dbst->bindValue(':dbname', VVE_DB_NAME, PDO::PARAM_STR);
      $dbst->bindValue(':tblname', '%_'.$tableName, PDO::PARAM_STR);
//      $dbst->bindValue(':thistabel', Db_PDO::table(Model_Config::DB_TABLE), PDO::PARAM_STR);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst;
   }
   
   public function getTablesByPrefix($prefix) {
      $dbc = Db_PDO::getInstance();

      $dbst = $dbc->prepare('SELECT '.self::COL_TB_TABLE_NAMES.' FROM '.self::DB_NAME_WITH_SCHEMAS.'.'.self::DB_TABLE_TABLES
         .' WHERE '.self::COL_TB_TABLE_SCHEMA.' = :dbname AND '.self::COL_TB_TABLE_NAMES.' LIKE :tblname');// AND table_name != :thistabel');
      
      $dbst->bindValue(':dbname', VVE_DB_NAME, PDO::PARAM_STR);
      $dbst->bindValue(':tblname', ''.$prefix.'%', PDO::PARAM_STR);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $tablesTMP = $dbst->fetchAll();
      $tables = array();
      if($tablesTMP){
         foreach ($tablesTMP as $t) {
            $tables[] = $t->table_name;
         }
      }
      
      return $tables;
   }

   /**
    * Metoda načte obsah tabulky v db
    * @param string $tableName -- název tabulky
    * @return PDOStatement
    */
   public function getTableContent($tableName) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare('SELECT * FROM '.$tableName);
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();
      return $dbst;
   }

   /**
    * Metoda spustí zadaný sql dotaz na db
    * @param string $sql -- SQL dotaz
    * @return PDOStatement
    */
   public function runSQL($sql) {
      $pdo = Db_PDO::getInstance();
      return $pdo->query($sql);
   }
   
   public static function dropTable($table)
   {
      $pdo = Db_PDO::getInstance();
      $stmt = $pdo->prepare('DROP TABLE IF EXISTS `'.$table.'`');
      Log::msg('Smazána tabluka: '.$table, null, Auth::getUserId());
      return $stmt->execute();
   }
}