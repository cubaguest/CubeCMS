<?php

/*
 * Třída modelu s detailem uživatele
 */

class AdvEventsBase_Model_UserAdds extends Model_ORM {

   const DB_TABLE = 'adv_events_useradds';

   /**
    * Pole s názvy sloupců v tabulce
    * @var array
    */
   const COLUMN_ID = 'id_events_useradd';
   const COLUMN_ID_EVENT = 'id_event';
   const COLUMN_NAME = 'event_useradd_name';
   const COLUMN_EMAIL = 'event_useradd_mail';
   const COLUMN_PHONE = 'event_useradd_phone';
   const COLUMN_KEY = 'event_useradd_key';
   const COLUMN_ADMIN_KEY = 'event_useradd_admin_key';
   const COLUMN_DATE = 'event_useradd_date';
   const COLUMN_NOTE = 'event_useradd_note';
   const COLUMN_IP = 'event_useradd_ip';

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_event');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_EVENT, array('datatype' => 'int', 'index' => true, 'nn' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_EMAIL, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PHONE, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NOTE, array('datatype' => 'varchar(400)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IP, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_KEY, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ADMIN_KEY, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_DATE, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));

      $this->setPk(self::COLUMN_ID);
   }

   protected function beforeSave(\Model_ORM_Record $record, $type = 'U')
   {
      parent::beforeSave($record, $type);

      if ($record->{self::COLUMN_KEY} == null) {
         $record->{self::COLUMN_KEY} = sha1(time() . $_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR']);
      }
      if ($record->{self::COLUMN_ADMIN_KEY} == null) {
         $record->{self::COLUMN_ADMIN_KEY} = sha1('admin' . time() . $_SERVER['SERVER_ADDR'] . $_SERVER['REMOTE_ADDR']);
      }
      if ($record->{self::COLUMN_IP} == null) {
         $record->{self::COLUMN_IP} = $_SERVER['REMOTE_ADDR'];
      }
   }

   public static function getEventByKey($key)
   {
      
   }
   
   public static function getUserByEvent($idEvent)
   {
      $m = new self();
      return $m->where(self::COLUMN_ID_EVENT." = :ide", array('ide' => $idEvent))->record();
      
   }

}
