<?php
/*
 * Třída modelu guestbooku
 * 
*/
class GuestBook_Model extends Model_ORM {
   /**
    * Tabulka s detaily
    */
   const DB_TABLE = 'guestbook';

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COL_ID = 'id_book';
   const COLUMN_ID = 'id_book';
   const COL_ID_CAT = 'id_category';
   const COLUMN_ID_CAT = 'id_category';
   const COLUMN_ID_USER = 'id_user';
//   const COL_SUBJECT = 'subject';
//   const COLUMN_SUBJECT = 'subject';
   const COL_EMAIL = 'email';
   const COLUMN_EMAIL = 'email';
   const COL_NICK = 'nick';
   const COLUMN_NICK = 'nick';
   const COL_WWW = 'www';
   const COLUMN_WWW = 'www';
   const COL_TEXT = 'text';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COL_IP = 'ip_address';
   const COLUMN_IP = 'ip_address';
   const COL_CLIENT = 'client';
   const COLUMN_CLIENT = 'client';
   const COL_DATE_ADD = 'date_add';
   const COLUMN_DATE_ADD = 'date_add';
   const COL_DELETED = 'deleted';
   const COLUMN_DELETED = 'deleted';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_guesbook');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CAT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
//      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
//      $this->addColumn(self::COLUMN_SUBJECT, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_NICK, array('datatype' => 'varchar(400)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_WWW, array('datatype' => 'varchar(400)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_EMAIL, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
//      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_IP, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CLIENT, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_DELETED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));

      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_CAT, 'Model_Categories', Model_Category::COLUMN_CAT_ID);
//      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users', Model_Users::COLUMN_ID);
   }

}

?>