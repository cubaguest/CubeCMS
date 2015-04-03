<?php
/*
 * Třída modelu s detailem uživatele
 */
class CustomBlocks_Model_Embeds extends CustomBlocks_Model_Items {
	const DB_TABLE 	   = 'custom_blocks_embeds';
   
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_CONTENT    = 'block_embed';

   protected function  _initTable() {
      parent::_initTable();
      $this->setTableName(self::DB_TABLE, 't_CustomBlocksEmbeds');
      $this->addColumn(self::COLUMN_CONTENT, array('datatype' => 'varchar(1024)', 'nn' => true));
   }
}
 