<?php
/*
 * Třída modelu
 */
class FAQ_Model extends Model_ORM_Ordered {
   
   const DB_TABLE = 'faq';

   /**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID               = 'id_faq';
	const COLUMN_ID_CATEGORY      = 'id_category';
	const COLUMN_TIME_ADD         = 'faq_date_add';
	const COLUMN_QUESTION         = 'faq_question';
	const COLUMN_ANSWER           = 'faq_answer';
	const COLUMN_ORDER           = 'faq_order';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_Faq');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      
      $this->addColumn(self::COLUMN_QUESTION, array('datatype' => 'TEXT', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER, 'lang' => true));
      $this->addColumn(self::COLUMN_ANSWER, array('datatype' => 'TEXT', 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER, 'lang' => true));

      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->setPk(self::COLUMN_ID);
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setLimitedColumns(array(self::COLUMN_ID_CATEGORY));
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category', Model_Category::COLUMN_ID);
   }
}
