<?php

/*
 * Třída modelu detailem článku
 */

class Shop_Model_Product_ParamsValues extends Model_ORM_Ordered {

   const DB_TABLE = 'shop_products_params_values';
   const COLUMN_ID = 'id_product_paramvalue';
   const COLUMN_ID_PARAM = 'id_product_param';
   const COLUMN_ID_PRODUCT = 'id_product';
   const COLUMN_VALUE = 'product_param_value';
   const COLUMN_ORDER = '	product_param_order';

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_pr_params_vals');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_PRODUCT, array('datatype' => 'int', 'nn' => true, 'index' => array(self::COLUMN_ID_PRODUCT, self::COLUMN_ID_PARAM, self::COLUMN_ORDER)));
      $this->addColumn(self::COLUMN_ID_PARAM, array('datatype' => 'int', 'nn' => true));

      $this->addColumn(self::COLUMN_VALUE, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'dafeult' => 0));

      $this->setPk(self::COLUMN_ID);
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setLimitedColumns(array(self::COLUMN_ID_PRODUCT));

      $this->addForeignKey(self::COLUMN_ID_PRODUCT, 'Shop_Model_Product', Shop_Model_Product::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_PARAM, 'Shop_Model_Product_Params', Shop_Model_Product_Params::COLUMN_ID);
   }
   
   public static function getExistValues($idParam = 0)
   {
      
   }
   
   public static function getProductValues($idProduct)
   {
      
   }
}
