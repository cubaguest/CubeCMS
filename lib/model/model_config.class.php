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

class Model_Config extends Model_ORM {
/**
 * Tabulka s detaily
 */
   const DB_TABLE = 'config';

   /**
    * slouce v db
    */
   const COLUMN_ID = 'id_config';
   const COLUMN_ID_GROUP = 'id_group';
   const COLUMN_KEY = 'key';
   const COLUMN_VALUE = 'value';
   const COLUMN_VALUES = 'values';
   const COLUMN_PROTECTED = 'protected';
   const COLUMN_TYPE = 'type';
   const COLUMN_LABEL = 'label';
   const COLUMN_HIDDEN = 'hidden_value';
   const COLUMN_CALLBACK = 'callback_func';

   const TYPE_STRING = 'string';
   const TYPE_NUMBER = 'number';
   const TYPE_BOOL = 'bool';
   const TYPE_LIST = 'list';
   const TYPE_LIST_MULTI = 'listmulti';
   const TYPE_SER_DATA = 'ser_object';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_cfg');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_GROUP, array('datatype' => 'smallint', 'nn' => true, 'default' => 1));
      $this->addColumn(self::COLUMN_KEY, array('datatype' => 'varchar(50)', 'nn' => true, 'uq' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_LABEL, array('datatype' => 'varchar(1000)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_VALUE, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_VALUES, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_PROTECTED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_TYPE, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR, 'default' => 'string')); // ENUM
      $this->addColumn(self::COLUMN_HIDDEN, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_CALLBACK, array('datatype' => 'varchar(40)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));

      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_GROUP, 'Model_ConfigGroups', Model_ConfigGroups::COLUMN_ID);
   }

   /**
    * Metoda nastaví proměnnou konfigurace
    * @param $name
    * @param $value
    * @return bool
    */
   public static function setValue($name, $value)
   {
      $model = new self();
      return $model
         ->where(self::COLUMN_KEY." = :name", array('name' => $name))
         ->update(array(self::COLUMN_VALUE => $value));
   }
}
