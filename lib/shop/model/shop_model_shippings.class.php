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
   const COLUMN_PERSONAL_PICKUP = 'shipping_is_personal_pickup';
   const COLUMN_MIN_DAYS = 'shipping_min_days';
   const COLUMN_MAX_DAYS = 'shipping_max_days';
   const COLUMN_HEUREKA_CODE = 'shipping_heureka_code';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_ssh');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_ZONE, array('datatype' => 'smallint', 'nn' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'lang' => true, 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_VALUE, array('datatype' => 'varchar(10)', 'default' => 0, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DISALLOWED_PAYMENTS, array('datatype' => 'varchar(50)', 'default' => null, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PERSONAL_PICKUP, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_HEUREKA_CODE, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_MIN_DAYS, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 2));
      $this->addColumn(self::COLUMN_MAX_DAYS, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 5));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_ZONE, 'Shop_Model_Zones', Shop_Model_Zones::COLUMN_ID);
   }
}