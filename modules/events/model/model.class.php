<?php
/*
 * Třída modelu lidí
*/
class Events_Model extends Model_ORM {
   const DB_TABLE = 'events';

   /**
    * Názvy sloupců v databázi
    */
   const COL_ID = 'id_event';
   const COL_ID_EVE_CATEGORY = 'id_events_cat';
   const COL_NAME = 'event_name';
   const COL_NOTE = 'event_note';
   const COL_TEXT = 'event_text';
   const COL_PLACE = 'event_place';
   const COL_PRICE = 'event_price';
   const COL_CONTACT = 'event_contact';
   const COL_DATE_FROM = 'event_date_from';
   const COL_DATE_TO = 'event_date_to';
   const COL_TIME_FROM = 'event_time_from';
   const COL_TIME_TO = 'event_time_to';
   const COL_PUBLIC = 'event_public';
   const COL_IP_ADD = 'event_ip_add';
   const COL_DATE_ADD = 'event_date_add';
   const COL_PUBLIC_ADD = 'event_public_add';
   const COL_IS_RECOMMENDED = 'event_is_recommended';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_events');

      $this->addColumn(self::COL_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COL_ID_EVE_CATEGORY, array('datatype' => 'smallint', 'nn' => true, 'index' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COL_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_NOTE, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_TEXT, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_CONTACT, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_PLACE, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_PRICE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COL_DATE_FROM, array('datatype' => 'date', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_DATE_TO, array('datatype' => 'date', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_TIME_FROM, array('datatype' => 'time', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COL_TIME_TO, array('datatype' => 'time', 'pdoparam' => PDO::PARAM_STR));
      
      
      $this->addColumn(self::COL_PUBLIC, array('datatype' => 'tinyint', 'default' => true, 'pdoparam' => PDO::PARAM_BOOL));
      $this->addColumn(self::COL_IP_ADD, array('datatype' => 'int', 'default' => null, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COL_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COL_PUBLIC_ADD, array('datatype' => 'tinyint', 'default' => true, 'pdoparam' => PDO::PARAM_BOOL));
      $this->addColumn(self::COL_IS_RECOMMENDED, array('datatype' => 'tinyint', 'default' => false, 'pdoparam' => PDO::PARAM_BOOL));
      
      $this->setPk(self::COL_ID);
      
      $this->addForeignKey(self::COL_ID_EVE_CATEGORY, 'Events_Model_Categories');
   }
}

?>