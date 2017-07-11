<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_ProductParams extends Model_ORM_Ordered {
   const DB_TABLE = 'shop_products_params';

   const COLUMN_ID = 'id_product_param';
   const COLUMN_ID_PRODUCT = 'id_product';
   const COLUMN_NAME = 'product_param_name';
   const COLUMN_VALUE = 'product_param_value';
   
   const COLUMN_ORDER = 'product_param_order';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_product_var');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_PRODUCT, array('datatype' => 'int', 'nn' => true, 'index' => true));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'lang' => true, 'default' => null));
      $this->addColumn(self::COLUMN_VALUE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'default' => null, 'lang' => true));
      
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));

      $this->setPk(self::COLUMN_ID);
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setLimitedColumns(array(self::COLUMN_ID_PRODUCT));
      $this->addForeignKey(self::COLUMN_ID_PRODUCT, 'Shop_Model_Product', Shop_Model_Product::COLUMN_ID);
   }

   public static function getParams($idProduct)
   {
      $m = new self();
      return $m->where(self::COLUMN_ID_PRODUCT, $idProduct)->records();
   }
}
