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

class Model_AutoRun extends Model_ORM {
   /**
    * Název tabulky s registrovanými moduly
    */
   const DB_TABLE = 'autorun';

   const PERIOD_HOURLY = 'hourly';
   const PERIOD_DAILY = 'daily';
   const PERIOD_WEEKLY = 'weekly';
   const PERIOD_MONTHLY = 'monthly';
   const PERIOD_YEARLY = 'yearly';
   
   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
   const COLUMN_ID             = 'id_autorun';
   const COLUMN_MODULE_NAME   = 'autorun_module_name';
   const COLUMN_PERIOD        = 'autorun_period';
   const COLUMN_URL            = 'autorun_url';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_autorun');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_MODULE_NAME, array('datatype' => 'varchar(20)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PERIOD, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 
            'default' => self::PERIOD_DAILY));
      $this->addColumn(self::COLUMN_URL, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));

      $this->setPk(self::COLUMN_ID);
   }
}
?>