<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_ProductVariants extends Model_ORM {
   const DB_TABLE = 'shop_products_variants';

   const COLUMN_ID = 'id_variant';
   const COLUMN_ID_ATTR = 'id_attribute';
   const COLUMN_ID_PRODUCT = 'id_product';
   const COLUMN_PRICE_ADD = 'variant_price_add';
   const COLUMN_PRODUCT_CODE = 'variant_product_code';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_product_var');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_ATTR, array('datatype' => 'smallint', 'nn' => true));
      $this->addColumn(self::COLUMN_ID_PRODUCT, array('datatype' => 'smallint', 'nn' => true));
      
      $this->addColumn(self::COLUMN_PRICE_ADD, array('datatype' => 'float', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'default' => 0));
      $this->addColumn(self::COLUMN_PRODUCT_CODE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_GROUP, 'Shop_Model_AttrGroups', Shop_Model_AttrGroups::COLUMN_ID);
   }
}

?>