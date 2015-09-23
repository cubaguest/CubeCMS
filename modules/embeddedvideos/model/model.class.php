<?php
/*
 * Třída modelu s detailem uživatele
 */
class EmbeddedVideos_Model extends Model_ORM_Ordered {
   const DB_TABLE = 'embedded_videos';

   /**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID 	   = 'id_video';
	const COLUMN_ID_CATEGORY = 'id_category';
	const COLUMN_NAME		= 'video_name';
	const COLUMN_URL		= 'video_url';
	const COLUMN_CODE		= 'video_code';
	const COLUMN_ORDER	= 'video_order';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_EmbeddedVideos');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'int', 'nn' => true, 'index' => true));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_URL, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CODE, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'default' => 1));

      $this->setPk(self::COLUMN_ID);
      $this->setLimitedColumns(array(self::COLUMN_ID_CATEGORY));
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category', Model_Category::COLUMN_ID);
   }
}
