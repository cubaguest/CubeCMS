<?php
/*
 * Třída modelu s detailem textu
 * 
*/
class DownloadFiles_Model_Sections extends Model_ORM_Ordered {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'dwfiles_sections';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID = 'id_dwfile_section';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_NAME = 'dwsection_name';
   const COLUMN_PASSWORD = 'dwsection_password';
   const COLUMN_ORDER = 'dwsection_order';

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_dw_files_secs');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_PASSWORD, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 1));

      $this->setPk(self::COLUMN_ID);
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setLimitedColumns(array(self::COLUMN_ID_CATEGORY));
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category', Model_Category::COLUMN_CAT_ID);
   }
   
   public static function getSectionsWithCats()
   {
      $m = new DownloadFiles_Model_Sections();
      
      return $m
         ->joinFK(self::COLUMN_ID_CATEGORY)
         ->records();
      
   }
}
