<?php
/**
 * Třída s modelem relace SubSite <--> Groups
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: model_module.class.php 625 2009-06-13 16:01:09Z jakub $ VVE 5.1.0 $Revision: 625 $
 * @author			$Author: jakub $ $Date: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 * @abstract 		Třída s modelem pro práci s uživateli
 */

class Model_SitesGroups extends Model_ORM {
/**
 * Název tabulky s uživateli
 */
   const DB_TABLE = 'sites_groups';

   const COLUMN_ID_SITE      = 'id_site';
   const COLUMN_ID_GROUP     = 'id_group';

   protected function  _initTable() {
      if(VVE_GLOBAL_TABLES_PREFIX != null) {
         $this->setTableName(VVE_GLOBAL_TABLES_PREFIX.self::DB_TABLE, 't_sites_a_g', false);
      } else {
         $this->setTableName(self::DB_TABLE, 't_sites_a_g');
      }

      $this->addColumn(self::COLUMN_ID_SITE, array('datatype' => 'smallint', 'nn' => true));
      $this->addColumn(self::COLUMN_ID_GROUP, array('datatype' => 'smallint', 'nn' => true));

      $this->addForeignKey(self::COLUMN_ID_SITE, 'Model_Sites', Model_Sites::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_GROUP, 'Model_Groups', Model_Groups::COLUMN_ID);
   }
}
?>