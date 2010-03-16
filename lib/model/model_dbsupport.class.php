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
      $dbc = new Db_PDO();

      $dbst = $dbc->prepare('SELECT '.self::COL_TB_TABLE_NAMES.' FROM '.self::DB_NAME_WITH_SCHEMAS.'.'.self::DB_TABLE_TABLES
         .' WHERE '.self::COL_TB_TABLE_SCHEMA.' = :dbname AND '.self::COL_TB_TABLE_NAMES.' LIKE :tblname');// AND table_name != :thistabel');
      
      $dbst->bindValue(':dbname', VVE_DB_NAME, PDO::PARAM_STR);
      $dbst->bindValue(':tblname', '%_'.$tableName, PDO::PARAM_STR);
//      $dbst->bindValue(':thistabel', Db_PDO::table(Model_Config::DB_TABLE), PDO::PARAM_STR);
      $dbst->execute();
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst;
   }

   /**
    * Metoda načte obsah tabulky v db
    * @param string $tableName -- název tabulky
    * @return PDOStatement
    */
   public function getTableContent($tableName) {
      $dbc = new Db_PDO();
      $dbst = $dbc->prepare('SELECT * FROM '.$tableName);// AND table_name != :thistabel');
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();
      return $dbst;
   }
}

?>