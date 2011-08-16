<?php

/**
 * Třída modelu produktů
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id:  $ VVE 7.3 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
 */
class Shop_Model_Payments extends Model_ORM
{
   const DB_TABLE = 'shop_payments';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_payment';
   const COLUMN_NAME = 'payment_name';
   const COLUMN_TEXT = 'payment_text';
   const COLUMN_PRICE_ADD = 'price_add';
   const COLUMN_CLASS = 'payment_class';
   const COLUMN_SETINGS = 'payment_settings';
   
   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_s_pay');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_CLASS, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_PRICE_ADD, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR, 'default' => 0));
      $this->addColumn(self::COLUMN_SETINGS, array('datatype' => 'blob', 'pdoparam' => PDO::PARAM_STR, 'default' => null));

      $this->setPk(self::COLUMN_ID);
   }
}
?>
