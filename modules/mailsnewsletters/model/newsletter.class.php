<?php

class MailsNewsletters_Model_Newsletter extends Model_ORM {
   const DB_TABLE = 'mails_newsletters';

   const COLUMN_ID = 'id_newsletter';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_SUBJECT = 'newsletter_subject';
   const COLUMN_CONTENT = 'newsletter_content';
   const COLUMN_DELETED = 'newsletter_deleted'; // unused
   const COLUMN_ACTIVE = 'newsletter_active';
   const COLUMN_DATE_SEND = 'newsletter_date_send';
   const COLUMN_GROUPS_IDS = 'newsletter_groups_ids';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_newsletters');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      
      $this->addColumn(self::COLUMN_SUBJECT, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CONTENT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_DATE_SEND, array('datatype' => 'date', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_GROUPS_IDS, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
   }
}
?>
