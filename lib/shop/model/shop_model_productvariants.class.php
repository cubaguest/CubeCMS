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
   const COLUMN_CODE_ADD = 'variant_code_add';
   const COLUMN_IS_DEFAULT = 'variant_is_default';
   const COLUMN_WEIGHT_ADD = 'variant_weight_add';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_product_var');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_ATTR, array('datatype' => 'smallint', 'nn' => true));
      $this->addColumn(self::COLUMN_ID_PRODUCT, array('datatype' => 'smallint', 'nn' => true, 'index' => array(self::COLUMN_ID_PRODUCT, self::COLUMN_ID_ATTR)));
      
      $this->addColumn(self::COLUMN_PRICE_ADD, array('datatype' => 'float', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'default' => 0));
      $this->addColumn(self::COLUMN_CODE_ADD, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_WEIGHT_ADD, array('datatype' => 'float', 'pdoparam' => PDO::PARAM_STR, 'default' => 0));
      $this->addColumn(self::COLUMN_IS_DEFAULT, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_PRODUCT, 'Shop_Model_Product', Shop_Model_Product::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_ATTR, 'Shop_Model_Attributes', Shop_Model_Attributes::COLUMN_ID);

      $this->addRelatioOneToMany(self::COLUMN_ID, 'Shop_Model_ProductCombinationHasVariant', Shop_Model_ProductCombinationHasVariant::COLUMN_ID_VARIANT);
   }

   public static function getVariants($idProduct)
   {
      $model = new self();
      return $model
         ->joinFK(array('attributes' => self::COLUMN_ID_ATTR) )
         ->join(array('attributes' => Shop_Model_Attributes::COLUMN_ID_GROUP), 'Shop_Model_AttributesGroups', Shop_Model_AttributesGroups::COLUMN_ID)
         ->where(self::COLUMN_ID_PRODUCT." = :idp", array('idp' => $idProduct))
         ->order(array(
            Shop_Model_AttributesGroups::COLUMN_NAME => Model_ORM::ORDER_ASC,
            Shop_Model_Attributes::COLUMN_NAME => Model_ORM::ORDER_ASC
         ))
         ->records();
   }

   /**
    * Metoda vrací celkovou cenu variant
    * @param $variandsId -- pole s id variant
    * @return int
    */
   public static function getVariantsPrice($variandsId)
   {
      $m = new self();

      $binds = array();
      foreach ($variandsId as $id) {
         $binds[':bind_'.$id] = (int)$id;
      }

      $rec = $m->columns(array('price' => 'SUM('.self::COLUMN_PRICE_ADD.')'))
         ->where(self::COLUMN_ID." IN (".implode(",", array_keys($binds)).")", $binds)
         ->record();
      return $rec ? $rec->price : 0;
   }
}

?>