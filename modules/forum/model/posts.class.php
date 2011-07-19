<?php
/*
 * Třída modelu odpovědí ve fóru
 * 
*/
class Forum_Model_Posts extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'forum_posts';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID = 'id_post';
   const COLUMN_ID_TOPIC = 'id_topic';
   const COLUMN_ID_USER = 'post_id_user';
   const COLUMN_EMAIL = 'post_email';
   const COLUMN_CREATED_BY = 'post_created_by';
   const COLUMN_CREATED_BY_MODERATOR = 'post_created_by_moderator';
   const COLUMN_WWW = 'post_www';
   const COLUMN_NAME = 'post_name';
   const COLUMN_TEXT = 'post_text';
   const COLUMN_TEXT_CLEAR = 'post_text_clear';
   const COLUMN_IP = 'post_ip_address';
   const COLUMN_CENSORED = 'post_censored';
   const COLUMN_DATE_ADD = 'post_date_add';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_f_topics');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_TOPIC, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'default' => 0, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      
      $this->addColumn(self::COLUMN_CREATED_BY, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CREATED_BY_MODERATOR, array('datatype' => 'tinyint', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_WWW, array('datatype' => 'varchar(400)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_EMAIL, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_IP, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_CENSORED, array('datatype' => 'tinyint', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));

      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_TOPIC, 'Forum_Model_Topics', Forum_Model_Topics::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users', Model_Users::COLUMN_ID);
   }

}

?>