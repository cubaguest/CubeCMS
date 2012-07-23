<?php

/*
 * Třída modelu s bannery
 */

class Banners_Model extends Model_ORM {
   const DB_TABLE = "banners";
   
   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_ID = 'id_banner';
   const COLUMN_NAME = 'banner_name';
   const COLUMN_FILE = 'banner_file';
   const COLUMN_ACTIVE = 'banner_active';
   const COLUMN_BOX = 'banner_box';
   const COLUMN_ORDER = 'banner_order';
   const COLUMN_URL = 'banner_url';
   const COLUMN_TEXT = 'banner_text';
   const COLUMN_TIME_ADD = 'banner_time_add';
   const COLUMN_NEW_WINDOW = 'banner_new_window';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_banner');
   
      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_FILE, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_BOX, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_URL, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'varchar(300)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_NEW_WINDOW, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      
      $this->setPk(self::COLUMN_ID);
//       $this->addRelatioOneToMany(self::COLUMN_ID, 'Articles_Model_TagsConnection', Articles_Model_TagsConnection::COLUMN_ID_ARTICLE);
   }

}
?>