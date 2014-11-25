<?php
/*
 * Třída modelu s detailem uživatele
 */
class AdminSites_Model extends Model_ORM {
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID 	   = 'id_item';
	const COLUMN_NAME		= 'name';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_AdminSites');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));

      $this->setPk(self::COLUMN_ID);
   }
}
