<?php
/*
 * Třída modelu lidí
*/
class Partners_Model extends Model_ORM_Ordered {
   const DB_TABLE = 'partners';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_partner';
   const COLUMN_ID_GROUP = 'id_group';
   const COLUMN_NAME = 'partner_name';
   const COLUMN_TEXT = 'partner_text';
   const COLUMN_NOTE = 'partner_note';
   const COLUMN_URL = 'partner_url';
   const COLUMN_IMAGE = 'partner_image';
   const COLUMN_ORDER = 'partner_order';
   const COLUMN_DISABLED = 'partner_disabled';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_partners');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_GROUP, array('datatype' => 'int', 'nn' => true, 'index' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(20)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 
         'fulltext' => true, 'fulltextRel' => CUBE_CMS_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'varchar(1000)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(1000)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_URL, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'default' => 0, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_DISABLED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_GROUP, 'Partners_Model_Groups', Partners_Model_Groups::COLUMN_ID);
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setLimitedColumns(array(self::COLUMN_ID_GROUP));
   }
}