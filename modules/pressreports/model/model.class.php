<?php
/*
 * Třída modelu s detailem textu
 * 
*/
class PressReports_Model extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'press_reports';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID = 'id_press_report';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_NAME = 'press_report_name';
   const COLUMN_AUTHOR = 'press_report_author';
   const COLUMN_TEXT = 'press_report_text';
   const COLUMN_FILE = 'press_report_file';
   const COLUMN_TIME_ADD = 'time_add';

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_press_reports');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'nn' => true, 
            'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'smallint', 'nn' => true, 
            'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'lang' => true, 
            'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_AUTHOR, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'varchar(500)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_FILE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 
            'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Categories', Model_Category::COLUMN_CAT_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
   }
   
}

?>