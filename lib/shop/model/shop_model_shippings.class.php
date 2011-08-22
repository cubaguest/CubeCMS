<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_Shippings extends Model_ORM {
   const DB_TABLE = 'shop_shippings';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_shipping';
   const COLUMN_ID_ZONE = 'id_zone';
   const COLUMN_NAME = 'shipping_name';
   const COLUMN_TEXT = 'shipping_text';
   const COLUMN_VALUE = 'shipping_price';
   const COLUMN_DISALLOWED_PAYMENTS = 'payments_disallowed';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_ssh');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_ZONE, array('datatype' => 'smallint', 'nn' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'lang' => true, 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'varchar(500)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_VALUE, array('datatype' => 'varchar(10)', 'default' => 0, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DISALLOWED_PAYMENTS, array('datatype' => 'varbinary(200)', 'default' => null, 'pdoparam' => PDO::PARAM_STR));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_ZONE, 'Shop_Model_Zones', Shop_Model_Zones::COLUMN_ID);
   }
}

?>