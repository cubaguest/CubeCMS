<?php

/*
 * Třída modelu s bannery
 */

class Banners_Model_Clicks extends Model_ORM {
   const DB_TABLE = "banners_clicks";
   
   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_ID = 'id_banner_click';
   const COLUMN_ID_BANNER = 'id_banner';
   const COLUMN_TIME = 'banner_click_time';
   const COLUMN_IP = 'banner_click_ip';
   const COLUMN_BROWSER = 'banner_click_browser';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_banner_c');
   
      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_BANNER, array('datatype' => 'smallint', 'nn' => true, 'index' => true));
      $this->addColumn(self::COLUMN_IP, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => null));
      $this->addColumn(self::COLUMN_TIME, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_BROWSER, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_BANNER, 'Banners_Model', Banners_Model::COLUMN_ID);
   }

}
?>