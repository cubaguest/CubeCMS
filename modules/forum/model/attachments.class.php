<?php
/*
 * Třída modelu odpovědí ve fóru
 * 
*/
class Forum_Model_Attachments extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'forum_attachments';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID = 'id_forum_attachment';
   const COLUMN_ID_TOPIC = 'id_topic';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_ID_MESSAGE = 'id_message';
   const COLUMN_FILENAME = 'forum_attachment_filename';
   const COLUMN_DATE_ADD = 'forum_attachment_date_add';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_f_attachments');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_TOPIC, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'default' => 0, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_ID_MESSAGE, array('datatype' => 'int', 'default' => 0, 'pdoparam' => PDO::PARAM_INT,'index' => array(self::COLUMN_ID_MESSAGE, self::COLUMN_ID_TOPIC)));
      
      $this->addColumn(self::COLUMN_FILENAME, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));

      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_TOPIC, 'Forum_Model_Topics', Forum_Model_Topics::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_MESSAGE, 'Forum_Model_Messages', Forum_Model_Messages::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users', Model_Users::COLUMN_ID);
//      $this->addRelatioOneToOne(self::COLUMN_ID_PARENT_MESSAGE, __CLASS__, self::COLUMN_ID);
   }

}

?>