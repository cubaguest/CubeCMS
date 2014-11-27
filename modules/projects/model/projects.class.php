<?php
/*
 * Třída modelu detailem článku
 */
class Projects_Model_Projects extends Model_ORM_Ordered {
   const DB_TABLE = 'projects';

   const COLUMN_ID = 'id_project';
   const COLUMN_ID_SECTION = 'id_project_section';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_ID_USER_LAST_EDIT = 'id_user_last_edit';
   const COLUMN_URLKEY = 'project_urlkey';
   const COLUMN_NAME = 'project_name';
   const COLUMN_NAME_SHORT = 'project_name_short';
   const COLUMN_TEXT = 'project_text';
   const COLUMN_TEXT_CLEAR = 'project_text_clear';
   const COLUMN_TIME_ADD = 'project_time_add';
   const COLUMN_TIME_EDIT = 'project_time_edit';
   const COLUMN_IMAGE = 'project_image';
   const COLUMN_THUMB = 'project_thumb';
   const COLUMN_RELATED = 'project_related';
   const COLUMN_WEIGHT = 'project_weight';
   const COLUMN_TPL_PARAMS = 'project_tpl_params';
   const COLUMN_KEYWORDS = 'project_keywords';
   const COLUMN_DESCRIPTION = 'project_desc';
   const COLUMN_ORDER = 'project_order';
   const COLUMN_PLACE = 'project_place';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_proj');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_USER_LAST_EDIT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_SECTION, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(300)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_NAME_SHORT, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_URLKEY, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_PLACE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_THUMB, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_RELATED, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_EDIT, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      
      $this->addColumn(self::COLUMN_WEIGHT, array('datatype' => 'smallint', 'default' => 0));
      $this->addColumn(self::COLUMN_TPL_PARAMS, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      
      $this->addColumn(self::COLUMN_KEYWORDS, array('datatype' => 'varchar(300)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DESCRIPTION, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      
      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_SECTION, 'Projects_Model_Sections', Projects_Model_Sections::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
      
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setLimitedColumns(array(self::COLUMN_ID_SECTION));
      
      // napojení na related projects   
//      $this->addRelatioOneToMany(self::COLUMN_ID, 'Articles_Model_PrivateUsers', Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_ARTICLE);
   }
}

class Projects_Model_Projects_Record extends Model_ORM_Ordered_Record {
   
   public function getImageSrc(Module $module)
   {
      return $module->getDataDir(true)
                . $this[Projects_Model_Projects::COLUMN_URLKEY].DIRECTORY_SEPARATOR
                . $this->{Projects_Model_Projects::COLUMN_IMAGE};
   }
}