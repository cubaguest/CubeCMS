<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_OrderItems extends Model_ORM {
   const DB_TABLE = 'shop_order_items';

   const COLUMN_ID = 'id_order_item';
   const COLUMN_ID_ORDER = 'id_order';
   const COLUMN_ID_PRODUCT = 'id_product';
   
   const COLUMN_NAME = 'order_product_name';
   const COLUMN_QTY = 'order_product_quantity';
   const COLUMN_PRICE = 'order_product_price';
   const COLUMN_TAX = 'order_product_tax';
   const COLUMN_CODE = 'order_product_code';
   const COLUMN_UNIT = 'order_product_unit';
   const COLUMN_NOTE = 'order_product_note';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_ord_items');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_PRODUCT, array('datatype' => 'smallint', 'nn' => true,'default' => 0));
      $this->addColumn(self::COLUMN_ID_ORDER, array('datatype' => 'smallint', 'nn' => true));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(400)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_QTY, array('datatype' => 'smallint', 'nn' => true, 'default' => 1));
      $this->addColumn(self::COLUMN_PRICE, array('datatype' => 'smallint', 'default' => 0));
      $this->addColumn(self::COLUMN_TAX, array('datatype' => 'smallint', 'default' => 0));
      $this->addColumn(self::COLUMN_CODE, array('datatype' => 'varchar(100)', 'default' => null, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_UNIT, array('datatype' => 'varchar(5)', 'default' => 'ks', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(500)', 'default' => null, 'pdoparam' => PDO::PARAM_STR));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_PRODUCT, 'Shop_Model_Product', Shop_Model_Product::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_ORDER, 'Shop_Model_Orders', Shop_Model_Orders::COLUMN_ID);
   }
}

?>