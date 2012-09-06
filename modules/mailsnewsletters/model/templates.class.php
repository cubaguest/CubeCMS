<?php

class MailsNewsletters_Model_Templates extends Model_ORM {
   const DB_TABLE = 'mails_newsletters_templates';
   
   const COLUMN_ID = 'id_newsletter_template';
   const COLUMN_NAME = 'newsletter_template_name';
   const COLUMN_DELETED = 'newsletter_template_deleted';
//    const COLUMN_CNT = 'newsletter_template_text';
//    const COLUMN_CNT_HTML = 'newsletter_template_html';
   
   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_m_news_tpls');
   
      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
   
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DELETED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));
//       $this->addColumn(self::COLUMN_CNT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
//       $this->addColumn(self::COLUMN_CNT_HTML, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
   
      $this->setPk(self::COLUMN_ID);
   }
    
}
?>
