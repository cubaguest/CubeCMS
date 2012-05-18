<?php
/*
 * Třída modelu detailem článku
*/
class Forms_Model extends Model_ORM {
   const DB_TABLE = 'forms';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_form';
   const COLUMN_NAME = 'form_name';
   const COLUMN_MSG = 'form_message';
   const COLUMN_SEND_TO_MAILS = 'form_send_to_mails';
   const COLUMN_SEND_TO_USERS = 'form_send_to_users';
   const COLUMN_SENDED = 'form_sended';
   const COLUMN_ACTIVE = 'form_active';
   const COLUMN_TIME_ADD = 'form_time_add';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_forms');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_MSG, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_SEND_TO_MAILS, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_SEND_TO_USERS, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));

      $this->addColumn(self::COLUMN_SENDED, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      
      $this->setPk(self::COLUMN_ID);
      
   }
   
   public function getForms()
   {
      return $this->where(self::COLUMN_ACTIVE." = 1", array())->records();
   }
}

?>