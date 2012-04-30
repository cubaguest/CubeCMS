<?php
/*
 * Třída modelu lidí
*/
class Events_Model_Categories extends Model_ORM {
   const DB_TABLE = 'events_cats';

   /**
    * Názvy sloupců v databázi
    */
   const COL_ID            = 'id_events_cat';
   const COL_ID_CATEGORY   = 'id_category';
   const COL_NAME          = 'event_cat_name';
   const COL_NOTE          = 'event_cat_note';
   const COL_IMAGE         = 'event_cat_image';
   const COL_WWW           = 'event_cat_www';
   const COL_CONTACT       = 'event_cat_contact';
   const COL_IS_PUBLIC     = 'event_cat_is_public';
   const COL_ACCESS_TOKEN  = 'event_access_token';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_ev_cats');

      $this->addColumn(self::COL_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COL_ID_CATEGORY, array('datatype' => 'smallint', 'nn' => true, 'index' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COL_NAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_NOTE, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_IMAGE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_WWW, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_CONTACT, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_IS_PUBLIC, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COL_ACCESS_TOKEN, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->setPk(self::COL_ID);
      
      $this->addForeignKey(self::COL_ID_CATEGORY, 'Model_Category');
   }
}

?>