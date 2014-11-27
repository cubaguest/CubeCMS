<?php
/*
 * Třída modelu detailem článku
 */
class Projects_Model_Sections extends Model_ORM_Ordered {
   const DB_TABLE = 'projects_sections';

   const COLUMN_ID = 'id_project_section';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_NAME = 'section_name';
   const COLUMN_URLKEY = 'section_urlkey';
   const COLUMN_TEXT = 'section_text';
   const COLUMN_TEXT_CLEAR = 'section_text_clear';
   const COLUMN_TIME_ADD = 'section_time_add';
   const COLUMN_WEIGHT = 'section_weight';
   const COLUMN_ORDER = 'section_order';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_pr_sec');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_URLKEY, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_WEIGHT, array('datatype' => 'smallint', 'default' => 0));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'default' => 0));

      $this->setPk(self::COLUMN_ID);
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setLimitedColumns(array(self::COLUMN_ID_CATEGORY));
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Categories', Model_Category::COLUMN_CAT_ID);
      
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Projects_Model_Projects', Projects_Model_Projects::COLUMN_ID_SECTION);
   }
}

class Projects_Model_Sections_Record extends Model_ORM_Ordered_Record {
   public function getProjects()
   {
      $m = new Projects_Model_Projects();
      return $m->where(Projects_Model_Projects::COLUMN_ID_SECTION." = :ids", array('ids' => $this->getPK()))->records();
   }
}