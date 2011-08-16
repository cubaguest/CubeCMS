<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_Tax extends Model_ORM {
   const DB_TABLE = 'shop_tax';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_tax';
   const COLUMN_NAME = 'tax_name';
   const COLUMN_VALUE = 'tax_value';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_stax');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_VALUE, array('datatype' => 'float', 'default' => 0, 'pdoparam' => PDO::PARAM_STR));

      $this->setPk(self::COLUMN_ID);
   }
}

?>