<?php
/*
 * Třída modelu s detailem uživatele
 */
class LinksList_Model_Links extends Model_ORM_Ordered {
	/**
	 * Pole s názvy sloupců v tabulce
	 */
	const COLUMN_ID            = 'id_link';
	const COLUMN_ID_CATEGORY 	= 'id_category';
	const COLUMN_TITLE         = 'link_title';
	const COLUMN_TARGET        = 'link_target';
	const COLUMN_CATEGORY      = 'link_category';
	const COLUMN_ORDER         = 'link_order';
	const COLUMN_EXTERNAL      = 'link_external';

   protected function  _initTable() {
      $this->setTableName('links_list', 't_links');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'int', 'nn' => true, 'index' => true));
      $this->addColumn(self::COLUMN_TITLE, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_TARGET, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CATEGORY, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'index' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_EXTERNAL, array('datatype' => 'tinyint', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));

      $this->setLimitedColumns(array(self::COLUMN_ID_CATEGORY));
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, "Model_Category", Model_Category::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_CATEGORY, "Model_Category", Model_Category::COLUMN_ID);
   }
}
