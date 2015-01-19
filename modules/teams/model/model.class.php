<?php
/*
 * Třída modelu lidí
*/
class Teams_Model extends Model_ORM_Ordered {
   const DB_TABLE = 'teams';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_team';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_NAME = 'team_name';
   const COLUMN_ORDER = 'team_order';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_teams');
      $this->setPk(self::COLUMN_ID);
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setLimitedColumns(array(self::COLUMN_ID_CATEGORY));

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'smallint', 'nn' => true, 'index' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(20)', 'nn' => true, 'lang' => true,  'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'default' => 0, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category');
   }
}