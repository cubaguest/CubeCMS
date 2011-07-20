<?php
/*
 * Třída modelu detailem článku
 */
class MapLocations_Model extends Model_ORM {
   const DB_TABLE = 'map_locations';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_location';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_NAME = 'name';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_COORDINATE_X = 'coordinate_x'; // x
   const COLUMN_COORDINATE_Y = 'coordinate_y'; // y
   const COLUMN_IMAGE = 'image';
   const COLUMN_MARKER = 'marker';
   
   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_m_loc');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 
          'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'smallint', 
          'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 
          'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 
          'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 
          'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_COORDINATE_X, array('datatype' => 'varchar(20)',
          'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_COORDINATE_Y, array('datatype' => 'varchar(20)',
          'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(50)', 
          'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_MARKER, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));

      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Categories', Model_Category::COLUMN_CAT_ID);
   }
   
}

?>