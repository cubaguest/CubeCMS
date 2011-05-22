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

class Model_SubSites extends Model_ORM {
/**
 * Název tabulky s uživateli
 */
   const DB_TABLE = 'sub_sites';

   const COLUMN_ID         = 'id_site';
   const COLUMN_DOMAIN     = 'domain';
   const COLUMN_DIR        = 'dir';
   const COLUMN_TB_PREFIX  = 'table_prefix';

   protected function  _initTable() {
      if(VVE_GLOBAL_TABLES_PREFIX != null) {
         $this->setTableName(VVE_GLOBAL_TABLES_PREFIX.self::DB_TABLE, 't_ss', false);
      } else {
         $this->setTableName(self::DB_TABLE, 't_ss');
      }

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_DOMAIN, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DIR, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TB_PREFIX, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));


      $this->setPk(self::COLUMN_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Model_SubSitesAdminGroups', Model_SubSitesAdminGroups::COLUMN_ID_SITE);
   }
}
?>