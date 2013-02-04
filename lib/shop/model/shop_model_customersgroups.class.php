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
class Shop_Model_CustomersGroups extends Model_ORM
{
   const DB_TABLE = 'shop_customers_groups';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_customer_group';
   const COLUMN_REDUCTION = 'customer_group_reduction';
   const COLUMN_NAME = 'customer_group_name';
   const COLUMN_DELETED = 'customer_group_deleted';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_s_cust');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));

      $this->addColumn(self::COLUMN_REDUCTION, array('datatype' => 'int', 'nn' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DELETED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));

      $this->setPk(self::COLUMN_ID);
   }
}
?>
