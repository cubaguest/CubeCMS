<?php
/*
 * Třída modelu s detailem uživatele
 */
class CustomBlocks_Model_Videos extends CustomBlocks_Model_Items {
	const DB_TABLE 	   = 'custom_blocks_videos';
   
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_URL    = 'block_video_url';

   protected function  _initTable() {
      parent::_initTable();
      $this->setTableName(self::DB_TABLE, 't_CustomBlocksVideos');
      $this->addColumn(self::COLUMN_URL, array('datatype' => 'varchar(300)', 'nn' => true));
   }
   
}
 