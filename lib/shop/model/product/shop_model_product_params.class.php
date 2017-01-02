<?php

/*
 * Třída modelu detailem článku
 */

class Shop_Model_Product_Params extends Model_ORM {

   const DB_TABLE = 'shop_products_params';
   const COLUMN_ID = 'id_product_param';
   const COLUMN_NAME = 'param_name';
   const COLUMN_DEFAULT = 'param_default_value';

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_pr_images');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));

      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'lang' => true));
      $this->addColumn(self::COLUMN_DEFAULT, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));

      $this->setPk(self::COLUMN_ID);
   }
}
