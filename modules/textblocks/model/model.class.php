<?php
/*
 * Třída modelu lidí
*/
class TextBlocks_Model extends Model_ORM_Ordered {
   const DB_TABLE = 'texts_blocks';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_text_block';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_NAME = 'block_text_name';
   const COLUMN_TEXT = 'block_text';
   const COLUMN_TEXT_CLEAR = 'block_text_clear';
   const COLUMN_IMAGE = 'block_image';
   const COLUMN_FILE = 'block_file';
   const COLUMN_ORDER = 'block_order';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_tblocks');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'int', 'nn' => true, 'index' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'lang' => true, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'lang' => true));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'lang' => true));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_FILE, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'default' => 0, 'pdoparam' => PDO::PARAM_INT));
      
      $this->setPk(self::COLUMN_ID);
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setLimitedColumns(array(self::COLUMN_ID_CATEGORY));
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category');
   }
   
   protected function beforeSave(Model_ORM_Record $record, $type = 'U')
   {
      $record->{self::COLUMN_TEXT_CLEAR} = strip_tags($record->{self::COLUMN_TEXT});
      parent::beforeSave($record, $type);
   }
}