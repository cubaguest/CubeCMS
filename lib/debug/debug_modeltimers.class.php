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

class Debug_ModelTimers extends Model_ORM {
/**
 * Název tabulky s uživateli
 */
   const DB_TABLE = 'timers';

   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
   const COLUMN_ID         = 'id_right';
   const COLUMN_TIME   = 'time';
   const COLUMN_MESSAGE   = 'msg';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_timers');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_TIME, array('datatype' => 'varchar(10)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_MESSAGE, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR, 'default' => null)); // měl by být enum

      $this->setPk(self::COLUMN_ID);
   }

}
?>