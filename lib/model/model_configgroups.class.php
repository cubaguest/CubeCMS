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

class Model_ConfigGroups extends Model_ORM {
/**
 * Tabulka s detaily
 */
   const DB_TABLE = 'cubecms_global_config_groups';

   /**
    * slouce v db
    */
   const COLUMN_ID = 'id_group';
   const COLUMN_NAME = 'name';
   const COLUMN_DESC = 'desc';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_cfg_grps', false);

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(45)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DESC, array('datatype' => 'varchar(100)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));

      $this->setPk(self::COLUMN_ID);

      $this->addRelatioOneToMany(self::COLUMN_ID, 'Model_Config', Model_Config::COLUMN_ID_GROUP);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Model_ConfigGlobal', Model_ConfigGlobal::COLUMN_ID_GROUP);
   }
}
?>