<?php
/*
 * Třída modelu s detailem uživatele
 */
class CustomBlocks_Model_Blocks extends Model_ORM_Ordered {
	const DB_TABLE 	   = 'custom_blocks';
   
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID 	   = 'id_custom_block';
	const COLUMN_ID_CAT 	   = 'id_category';
	const COLUMN_NAME		= 'block_name';
	const COLUMN_DATA		= 'block_data';
	const COLUMN_TYPE		= 'block_type';
	const COLUMN_ORDER		= 'block_order';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_CustomBlocks');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CAT, array('datatype' => 'int', 'nn' => true, 'index' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_DATA, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TYPE, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'nn' => true, 'default' => 0));

      $this->setLimitedColumns(array(self::COLUMN_ID_CAT));
      $this->setOrderColumn(self::COLUMN_ORDER);
      
      $this->setPk(self::COLUMN_ID);
   }
   
   
   public static function getBlocks($idCategory)
   {
      $m = new static();
      return $m->where(self::COLUMN_ID_CAT." = :idc", array('idc' => $idCategory))->records();
   }
}

class CustomBlocks_Model_Blocks_Rrcord extends Model_ORM_Ordered_Record {
   
   public function getData()
   {
      if($this->{CustomBlocks_Model_Blocks::COLUMN_DATA} != null){
         return unserialize($this->{CustomBlocks_Model_Blocks::COLUMN_DATA});
      }
      return null;
   }
   
   public function setData($data)
   {
      $this->{CustomBlocks_Model_Blocks::COLUMN_DATA} = serialize($data);
   }
}   