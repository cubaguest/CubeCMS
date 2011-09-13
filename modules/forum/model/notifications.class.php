<?php
/*
 * Třída modelu odpovědí ve fóru
 * 
*/
class Notifications_Model_Posts extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'forum_notifications';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID = 'id_notify';
   const COLUMN_ID_TOPIC = 'id_topic';
   const COLUMN_EMAIL = 'post_email';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_f_t_notify');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_TOPIC, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      
      $this->addColumn(self::COLUMN_EMAIL, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_TOPIC, 'Forum_Model_Topics', Forum_Model_Topics::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users', Model_Users::COLUMN_ID);
   }

}

?>