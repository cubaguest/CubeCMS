<?php
/*
 * Třída modelu témat ve fóru
 * 
*/
class Forum_Model_Topics extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'forum_topics';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUMN_ID = 'id_topic';
   const COLUMN_ID_CAT = 'id_category';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_EMAIL = 'email';
   const COLUMN_CREATED_BY = 'created_by';
   const COLUMN_WWW = 'www';
   const COLUMN_NAME = 'name';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_IP = 'ip_address';
   const COLUMN_DATE_ADD = 'date_add';
   const COLUMN_VIEWS = 'views';
   const COLUMN_SOLVED = 'solved';
   const COLUMN_CLOSED = 'closed';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_f_topics');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CAT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0, 'index' => true));
      
      $this->addColumn(self::COLUMN_CREATED_BY, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_WWW, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_EMAIL, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_IP, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_SOLVED, array('datatype' => 'tinyint', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));
      $this->addColumn(self::COLUMN_CLOSED, array('datatype' => 'tinyint', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));

      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_CAT, 'Model_Categories', Model_Category::COLUMN_CAT_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users', Model_Users::COLUMN_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Forum_Model_Posts', Forum_Model_Posts::COLUMN_ID_TOPIC);
   }

}

?>