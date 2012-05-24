<?php
/*
 * Třída modelu lidí
*/
class Partners_Model extends Model_ORM {
   const DB_TABLE = 'partners';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_partner';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_NAME = 'partner_name';
   const COLUMN_TEXT = 'partner_text';
   const COLUMN_NOTE = 'partner_note';
   const COLUMN_URL = 'partner_url';
   const COLUMN_IMAGE = 'partner_image';
   const COLUMN_ORDER = 'partner_order';
   const COLUMN_DISABLED = 'partner_disabled';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_partners');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'smallint', 'nn' => true, 'index' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(20)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 
         'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'varchar(1000)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(1000)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_URL, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'default' => 0, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_DISABLED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category');
   }
}

?>