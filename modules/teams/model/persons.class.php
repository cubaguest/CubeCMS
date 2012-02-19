<?php
/*
 * Třída modelu lidí
*/
class Teams_Model_Persons extends Model_ORM {
   const DB_TABLE = 'teams_persons';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_person';
   const COLUMN_ID_TEAM = 'id_team';
   const COLUMN_NAME = 'person_name';
   const COLUMN_SURNAME = 'person_surname';
   const COLUMN_DEGREE = 'person_degree';
   const COLUMN_DEGREE_AFTER = 'person_degree_after';
   const COLUMN_TEXT = 'person_text';
   const COLUMN_TEXT_CLEAR = 'person_text_clear';
   const COLUMN_IMAGE = 'person_image';
   const COLUMN_LINK = 'person_link';
   const COLUMN_ORDER = 'person_order';
   const COLUMN_DELETED = 'person_deleted';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_people');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_TEAM, array('datatype' => 'smallint', 'nn' => true, 'index' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(20)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_SURNAME, array('datatype' => 'varchar(20)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_DEGREE, array('datatype' => 'varchar(10)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DEGREE_AFTER, array('datatype' => 'varchar(10)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(45)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_LINK, array('datatype' => 'varchar(300)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'default' => 0, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_DELETED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_TEAM, 'Teams_Model');
   }
}

?>