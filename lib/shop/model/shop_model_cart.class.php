<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_Cart extends Model_ORM {
   const DB_TABLE = 'shop_cart_items';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_cart_item';
   const COLUMN_ID_PRODUCT = 'id_product';
   const COLUMN_ID_COMBINATION = 'id_product_combination';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_ID_SESSION = 'id_session';
   const COLUMN_QTY = 'cart_item_qty';
   const COLUMN_DATE_ADD = 'cart_item_date_add';
   const COLUMN_VARIANT_LABEL = 'cart_item_variant_label';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_cart');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_PRODUCT, array('datatype' => 'int', 'nn' => true));
      $this->addColumn(self::COLUMN_ID_COMBINATION, array('datatype' => 'int', 'nn' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'int', 'nn' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_ID_SESSION, array('datatype' => 'varchar', 'size' => 32 , 'nn' => false));
      $this->addColumn(self::COLUMN_QTY, array('datatype' => 'decimal(11,4)', 'nn' => true, 'default' => 1));
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_VARIANT_LABEL, array('datatype' => 'varchar(300)', 'default' => null));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_PRODUCT, 'Shop_Model_Product', Shop_Model_Product::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_COMBINATION, 'Shop_Model_Product_Combinations', Shop_Model_Product_Combinations::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
   }

   public static function getItems($idUser, $sessionId){

   }
}

?>