<?php
/*
 * Třída modelu s detailem uživatele
 */
class CustomBlocks_Model_Texts extends CustomBlocks_Model_Items {
	const DB_TABLE 	   = 'custom_blocks_texts';
   
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_CONTENT    = 'block_text';

   protected function  _initTable() {
      parent::_initTable();
      $this->setTableName(self::DB_TABLE, 't_CustomBlocksTexts');
      $this->addColumn(self::COLUMN_CONTENT, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
   }
}
 