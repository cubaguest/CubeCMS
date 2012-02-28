<?php
/*
 * Třída modelu detailem článku
 */
class Advice_Model_Categories extends Model_ORM {
   const DB_TABLE = 'advice_cats';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_advice_cat';
   const COLUMN_NAME = 'advice_cat_name';
   const COLUMN_ORDER = 'advice_cat_order';
   const COLUMN_IS_DRUG = 'advice_cat_is_drug';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_adv_cats');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IS_DRUG, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      
      $this->setPk(self::COLUMN_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Advice_Model_Connections', Advice_Model_Connections::COLUMN_ID_CAT);
   }
}

?>
