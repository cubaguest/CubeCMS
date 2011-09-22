<?php
/*
 * Třída modelu detailem článku
 */
class MessagesBoard_Model extends Model_ORM {
   const DB_TABLE = 'messagesboard';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_message';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_TIME_ADD = 'time_add';
   const COLUMN_IP_ADDRESS = 'ip_address';
   const COLUMN_COLOR = 'color';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_msgbrd');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'varchar(1000)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'varchar(1000)', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));

      $this->addColumn(self::COLUMN_IP_ADDRESS, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_COLOR, array('datatype' => 'varchar(6)', 'pdoparam' => PDO::PARAM_STR));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Categories', Model_Category::COLUMN_CAT_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
   }
}

?>