<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_ProductCombinationHasVariant extends Model_ORM {
   const DB_TABLE = 'shop_products_combinations_variants';

//   const COLUMN_ID = 'id_combination_attribute';
   const COLUMN_ID_COMBINATION = 'id_product_combination';
   const COLUMN_ID_VARIANT = 'id_variant';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_product_comb');

      $this->addColumn(self::COLUMN_ID_COMBINATION, array('datatype' => 'int', 'nn' => true, 'index' => self::COLUMN_ID_COMBINATION ));
      $this->addColumn(self::COLUMN_ID_VARIANT, array('datatype' => 'int', 'nn' => true, 'index' => array(self::COLUMN_ID_VARIANT, self::COLUMN_ID_COMBINATION)));

      $this->addForeignKey(self::COLUMN_ID_COMBINATION, 'Shop_Model_ProductCombinations', Shop_Model_ProductCombinations::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_VARIANT, 'Shop_Model_Attributes', Shop_Model_Attributes::COLUMN_ID);
   }
}

?>