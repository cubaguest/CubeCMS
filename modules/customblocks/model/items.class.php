<?php
/*
 * Třída modelu s detailem uživatele
 */
abstract class CustomBlocks_Model_Items extends Model_ORM {
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID         = 'id_block_item';
	const COLUMN_ID_BLOCK   = 'id_custom_block';
	const COLUMN_INDEX      = 'block_item_index';
//	const COLUMN_ORDER         = 'block_item_order';

   
   const TYPE_IMAGE = 'img';
   const TYPE_TEXT = 'txt';
   const TYPE_VIDEO = 'vid';
   const TYPE_EMBED = 'emb';


   protected function  _initTable() {
      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_BLOCK, array('datatype' => 'int', 'nn' => true, 'index' => true));
      $this->addColumn(self::COLUMN_INDEX, array('datatype' => 'varchar(10)', 'nn' => true, 'index' => true, 'pdoparam' => PDO::PARAM_STR, 'default' => 0));
//      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'nn' => true, 'default' => 0));
      
//      $this->setLimitedColumns(array(self::COLUMN_ID_BLOCK));
//      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_BLOCK, "CustomBlocks_Model_Blocks", CustomBlocks_Model_Blocks::COLUMN_ID);
   }
   
   public static function getItem($idBlock, $index)
   {
      $m = new static();
      $rec = $m->where(self::COLUMN_ID_BLOCK." = :idb AND ".self::COLUMN_INDEX." = :idi", array('idb' => $idBlock, 'idi' => (string)$index))->record();
      if(!$rec || $rec->isNew()){
         return false;
      }
      return $rec;
   }

   public static function getItems($idBlock, $index)
   {
      $m = new static();
      return $m->where(self::COLUMN_ID_BLOCK." = :idb AND ".self::COLUMN_INDEX." = :idi", array('idb' => $idBlock, 'idi' => (string)$index))->records();
   }
}
 