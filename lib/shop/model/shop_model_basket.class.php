<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_Basket extends Model_ORM {
   const DB_TABLE = 'shop_basket';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_basket_item';
   const COLUMN_ID_PRODUCT = 'id_product';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_ID_SESSION = 'id_session';
   const COLUMN_QTY = 'basket_qty';
   const COLUMN_DATE_ADD = 'basket_date_add';
   const COLUMN_ATTRIBUTES = 'basket_attributes';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_sbasket');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_PRODUCT, array('datatype' => 'smallint', 'nn' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'nn' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_ID_SESSION, array('datatype' => 'varchar', 'size' => 32 , 'nn' => false));
      $this->addColumn(self::COLUMN_QTY, array('datatype' => 'smallint', 'nn' => true, 'default' => 1));
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'default' => 'CURRENT_TIMESTAMP'));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_PRODUCT, 'Shop_Model_Product', Shop_Model_Product::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
   }
}

?>