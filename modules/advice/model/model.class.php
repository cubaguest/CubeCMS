<?php
/*
 * Třída modelu detailem článku
 */
class Advice_Model extends Model_ORM {
   const DB_TABLE = 'advice';

/**
 * Názvy sloupců v databázi
 */
   const COLUMN_ID = 'id_advice_question';
   const COLUMN_ID_CATEGORY = 'id_category';
   
   const COLUMN_NAME = 'advice_name';
   const COLUMN_QUESTION = 'advice_question';
   const COLUMN_ANSWER = 'advice_answer';
   const COLUMN_COLOR = 'advice_color';
   const COLUMN_IS_PUBLIC = 'advice_public';
   const COLUMN_IS_PUBLIC_ALLOW = 'advice_public_allow';
   const COLUMN_IS_COMMON = 'advice_is_common';
   
   const COLUMN_DATE_ADD = 'advice_date_add';
   const COLUMN_DATE_ANSWER = 'advice_date_answer';
   
   const COLUMN_QUESTIONER_NAME = 'advice_questioner_name';
   const COLUMN_QUESTIONER_GENDER = 'advice_questioner_gender';
   const COLUMN_QUESTIONER_AGE = 'advice_questioner_age';
   const COLUMN_QUESTIONER_CITY = 'advice_questioner_city';
   const COLUMN_QUESTIONER_EMAIL = 'advice_questioner_email';
   const COLUMN_QUESTIONER_REGULAR_USER = 'advice_questioner_regular_user';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_pradv');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'smallint', 'nn' => true, 'index' => true));
                                                 
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_QUESTION, array('datatype' => 'varchar(1000)', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'nn' => true ));
      $this->addColumn(self::COLUMN_ANSWER, array('datatype' => 'varchar(1000)', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true ));
      
      $this->addColumn(self::COLUMN_COLOR, array('datatype' => 'varchar(6)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      
      $this->addColumn(self::COLUMN_IS_PUBLIC, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_IS_PUBLIC_ALLOW, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_IS_COMMON, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_DATE_ANSWER, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_QUESTIONER_NAME, array('datatype' => 'varchar(45)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_QUESTIONER_GENDER, array('datatype' => 'varchar(1)', 'pdoparam' => PDO::PARAM_STR, 'default' => 'm'));
      $this->addColumn(self::COLUMN_QUESTIONER_AGE, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_QUESTIONER_CITY, array('datatype' => 'varchar(30)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_QUESTIONER_EMAIL, array('datatype' => 'varchar(30)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_QUESTIONER_REGULAR_USER, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Categories', Model_Category::COLUMN_CAT_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Advice_Model_Connections', Advice_Model_Connections::COLUMN_ID_QUESTION);
   }
   
   public function createQueryRandomCommon($num = 1)
   {
//      $stmt = $this->query('SELECT * FROM {THIS} AS pradv 
//      JOIN (SELECT FLOOR(MAX('.self::COLUMN_ID.')*RAND()) AS '.self::COLUMN_ID.' FROM {THIS}) AS pradvx '
//         .'ON pradv.'.self::COLUMN_ID.' >= pradvx.'.self::COLUMN_ID
//         .' WHERE pradv.'.self::COLUMN_IS_COMMON.' = 1 LIMIT :num');
//      
//      $stmt->bindValue(':num', $num, PDO::PARAM_INT);
//      $this->setQuery($stmt);
      
      
      return $this;
   }
}

?>
