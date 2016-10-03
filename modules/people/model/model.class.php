<?php
/*
 * Třída modelu lidí
*/
class People_Model extends Model_ORM_Ordered {
   const DB_TABLE = 'people';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_person';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_NAME = 'person_name';
   const COLUMN_SURNAME = 'person_surname';
   const COLUMN_DEGREE = 'person_degree';
   const COLUMN_DEGREE_AFTER = 'person_degree_after';
   const COLUMN_TEXT = 'person_text';
   const COLUMN_TEXT_CLEAR = 'person_text_clear';
   const COLUMN_IMAGE = 'person_image';
   const COLUMN_ORDER = 'person_order';
   const COLUMN_AGE = 'person_age';
   const COLUMN_LABEL = 'person_label';
   const COLUMN_EMAIL = 'person_email';
   const COLUMN_PHONE = 'person_phone';
   const COLUMN_SOCIAL_URL = 'person_social_url';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_persons');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'int', 'nn' => true, 'index' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(40)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_SURNAME, array('datatype' => 'varchar(40)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_DEGREE, array('datatype' => 'varchar(10)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DEGREE_AFTER, array('datatype' => 'varchar(10)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'lang' => true));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'lang' => true));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(45)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'default' => 0, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_AGE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => null));
      $this->addColumn(self::COLUMN_LABEL, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'default' => null, 'lang' => true));
      $this->addColumn(self::COLUMN_EMAIL, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_PHONE, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_SOCIAL_URL, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      
      $this->setPk(self::COLUMN_ID);
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setLimitedColumns(array(self::COLUMN_ID_CATEGORY));
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category');
   }
}