<?php
/*
 * Třída modelu s detailem uživatele
 */
class UserQuestions_Model extends Model_ORM {
   
   const DB_TABLE = 'userquestions';


   /**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID 	= 'id_userquestion';
	const COLUMN_ID_USER_APPROVED	= 'is_user_approved';
	const COLUMN_ID_CATEGORY      = 'id_category';
	const COLUMN_TIME_ADD         = 'userquestion_date_add';
	const COLUMN_TIME_ANSWER      = 'userquestion_date_answer';
	const COLUMN_NAME             = 'userquestion_name';
	const COLUMN_EMAIL            = 'userquestion_email';
	const COLUMN_QUESTION         = 'userquestion_question';
	const COLUMN_ANSWER           = 'userquestion_answer';
	const COLUMN_APPROVED         = 'userquestion_approved';
	const COLUMN_APPROVED_SEND    = 'userquestion_approved_send';
	const COLUMN_SECURE_KEY       = 'userquestion_secure_key';
	const COLUMN_IP               = 'userquestion_ip';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_UserQuestions');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER_APPROVED, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_TIME_ANSWER, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'VARCHAR(50)', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_EMAIL, array('datatype' => 'VARCHAR(50)', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_SECURE_KEY, array('datatype' => 'VARCHAR(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IP, array('datatype' => 'VARCHAR(15)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_QUESTION, array('datatype' => 'TEXT', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_ANSWER, array('datatype' => 'TEXT', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));

      $this->addColumn(self::COLUMN_APPROVED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));
      $this->addColumn(self::COLUMN_APPROVED_SEND, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => 0));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_USER_APPROVED, 'Model_Users', Model_Users::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category', Model_Category::COLUMN_ID);
   }
   
   protected function beforeSave(Model_ORM_Record $record, $type = 'U')
   {
      // generování unkátního url klíče
      if($record->{self::COLUMN_SECURE_KEY} == null){
         $record->{self::COLUMN_SECURE_KEY} = md5(microtime().$_SERVER['HTTP_HOST'].Utils_Net::getClientIP());
      }
      if($record->{self::COLUMN_IP} == null){
         $record->{self::COLUMN_IP} = Utils_Net::getClientIP();
      }
   }
}
