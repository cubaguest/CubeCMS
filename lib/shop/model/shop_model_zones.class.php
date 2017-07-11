<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_Zones extends Model_ORM {
   const DB_TABLE = 'shop_zones';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_zone';
   const COLUMN_NAME = 'zone_name';
   const COLUMN_CODES = 'zone_codes';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_szones');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CODES, array('datatype' => 'varchar(20)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'default' => 'CZ'));

      $this->setPk(self::COLUMN_ID);
   }
}