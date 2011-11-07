<?php
/*
 * Třída modelu detailem článku
 */
class Projects_Model_Projects extends Model_ORM {
   const DB_TABLE = 'projects';

   const COLUMN_ID = 'id_project';
   const COLUMN_ID_SECTION = 'id_project_section';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_URLKEY = 'project_urlkey';
   const COLUMN_NAME = 'project_name';
   const COLUMN_NAME_SHORT = 'project_name_short';
   const COLUMN_TEXT = 'project_text';
   const COLUMN_TEXT_CLEAR = 'project_text_clear';
   const COLUMN_TIME_ADD = 'project_time_add';
   const COLUMN_IMAGE = 'project_image';
   const COLUMN_RELATED = 'project_related';
   const COLUMN_WEIGHT = 'project_weight';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_art');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_SECTION, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(300)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_NAME_SHORT, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_URLKEY, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'bool', 'pdoparam' => PDO::PARAM_BOOL));
      $this->addColumn(self::COLUMN_RELATED, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_WEIGHT, array('datatype' => 'smallint', 'default' => 0));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_SECTION, 'Projects_Model_Sections', Projects_Model_Sections::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
      // napojení na related projects   
//      $this->addRelatioOneToMany(self::COLUMN_ID, 'Articles_Model_PrivateUsers', Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_ARTICLE);
   }
}

?>