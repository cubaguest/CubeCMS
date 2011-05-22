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

class Model_Groups extends Model_ORM {
/**
 * Název tabulky s uživateli
 */
   const DB_TABLE = 'groups';

   const COLUMN_ID    = 'id_group';
   const COLUMN_NAME    = 'gname';
   const COLUMN_LABEL    = 'label';
   const COLUMN_IS_ADMIN    = 'admin';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE);

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'aliasFor' => 'name'));
      $this->addColumn(self::COLUMN_LABEL, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IS_ADMIN, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));


      $this->setPk(self::COLUMN_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Model_Users', Model_Users::COLUMN_GROUP_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Model_Rights', Model_Rights::COLUMN_ID_GROUP);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Model_SubSitesAdminGroups', Model_SubSitesAdminGroups::COLUMN_ID_GROUP);
   }

   /**
    * Metoda vrací název tabulky se skupinami (včetně prefixu)
    * @return string -- název tabulky
    */
   public static function getTable() {
      if(VVE_USE_GLOBAL_ACCOUNTS === true) {
         return VVE_GLOBAL_TABLES_PREFIX.self::DB_TABLE;
      } else {
         return Db_PDO::table(self::DB_TABLE);
      }
   }

   public function  getTableName() {
      return self::getTable();
   }
}
?>