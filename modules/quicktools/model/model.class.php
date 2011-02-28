<?php
/*
 * Třída modelu s detailem textu
 * 
*/
class QuickTools_Model extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'quicktools';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID = 'id_tool';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_NAME = 'name';
   const COLUMN_URL = 'url';
   const COLUMN_ICON = 'icon';
   const COLUMN_ORDER = 'order';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_tools');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_URL, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ICON, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      

      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
   }
   
}

?>