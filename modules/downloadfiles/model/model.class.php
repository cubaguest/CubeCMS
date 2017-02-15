<?php
/*
 * Třída modelu s detailem textu
 * 
*/
class DownloadFiles_Model extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'dwfiles';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID = 'id_dwfile';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_ID_SECTION = 'id_dwfile_section';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_NAME = 'dwfile_name';
   const COLUMN_TEXT = 'dwfile_text';
   const COLUMN_FILE = 'dwfile';
   const COLUMN_TIME_ADD = 'time_add';
   const COLUMN_COLUMN = 'dwfile_column';
   const COLUMN_ACTIVE = 'dwfile_active';

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_dw_files');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_SECTION, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'varchar(500)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_FILE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_COLUMN, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 1));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_SECTION, 'DownloadFiles_Model_Sections', DownloadFiles_Model_Sections::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
   }
}
