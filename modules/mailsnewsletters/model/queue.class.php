<?php

class MailsNewsletters_Model_Queue extends Model_ORM {
   const DB_TABLE = 'mails_newsletters_queue';

   const COLUMN_ID               = 'id_newsletter_queue';
   const COLUMN_ID_NEWSLETTER    = 'id_newsletter';
   const COLUMN_MAIL             = 'newsletter_queue_mail';
   const COLUMN_NAME             = 'newsletter_queue_name';
   const COLUMN_SURNAME          = 'newsletter_queue_surname';
   const COLUMN_DATE_SEND        = 'newsletter_queue_date_send';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_newsletters_queue');
   
      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_NEWSLETTER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
   
      $this->addColumn(self::COLUMN_MAIL, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'nn' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_SURNAME, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_DATE_SEND, array('datatype' => 'date', 'pdoparam' => PDO::PARAM_STR, 'nn' => true));
   
      $this->setPk(self::COLUMN_ID);
   
      $this->addForeignKey(self::COLUMN_ID_NEWSLETTER, 'MailsNewsletters_Model_Newsletter', MailsNewsletters_Model_Newsletter::COLUMN_ID);
   }
}
?>
