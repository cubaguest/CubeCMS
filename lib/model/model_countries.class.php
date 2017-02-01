<?php
/*
 * Třída modelu s detailem uživatele
 */
class Model_Countries extends Model_ORM {
   const DB_TABLE = 'countries';


   /**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID         = 'id_country';
	const COLUMN_NUMCODE		= 'country_numcode';
	const COLUMN_ISO        = 'country_iso';
	const COLUMN_ISO3       = 'country_iso3';
	const COLUMN_NAME       = 'country_name';
	const COLUMN_NAME_FULL	= 'country_namefull';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_countries');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'lang' => true));
      $this->addColumn(self::COLUMN_NAME_FULL, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'lang' => true));
      $this->addColumn(self::COLUMN_ISO, array('datatype' => 'char(2)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'index' => true));
      $this->addColumn(self::COLUMN_ISO3, array('datatype' => 'char(3)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'index' => true));
      $this->addColumn(self::COLUMN_NUMCODE, array('datatype' => 'int(3)', 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->setPk(self::COLUMN_ID);
   }
}
