<?php
/**
 * Třída s modelem pro práci se sessions
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.4.0 $Revision: 625 $
 * @author			$Author: jakub $ $Date: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 * @abstract 		Třída s modelem pro práci s uživateli
 */

class Model_Session extends Model_ORM {
   /**
    * Název tabulky
    */
   protected static $tableName = 'sessions';

   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
   const COLUMN_KEY         = 'session_key';
   const COLUMN_VALUE      = 'value';
   const COLUMN_CREATED    = 'created';
   const COLUMN_UPDATED    = 'updated';
   const COLUMN_IP         = 'ip';
   const COLUMN_ID_USER    = 'id_user';

   protected function  _initTable() {
      $this->setTableName(self::$tableName, 't_ses');

      $this->addColumn(self::COLUMN_KEY, array('datatype' => 'varchar', 'nn' => true, 'pk' => true, 'uq' => true));
      $this->addColumn(self::COLUMN_VALUE, array('datatype' => 'blob', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IP, array('datatype' => 'varchar', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_CREATED, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR, 'nn' => true, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_UPDATED, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR, 'nn' => true, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'default' => 0));

      $this->setPk(self::COLUMN_KEY);
   }
}
?>