<?php
/*
 * Třída modelu s detailem uživatele
 */
class CustomBlocks_Model_Images extends CustomBlocks_Model_Items {
	const DB_TABLE 	   = 'custom_blocks_images';
   
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_NAME       = 'block_image_name';
	const COLUMN_FILE       = 'block_image_file';

   const DIR_IMG = 'images';
   
   protected function  _initTable() {
      parent::_initTable();
      $this->setTableName(self::DB_TABLE, 't_CustomBlocksImages');
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_FILE, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
   }
   
   protected function beforeDelete($pk)
   {
      if(isset($this->params['dir'])){
         $image = self::getRecord($pk);
         $file = new File($image->{self::COLUMN_FILE}, $this->params['dir'].self::DIR_IMG);
         if($file->exist()){
            $file->delete();
         }
      }
      parent::beforeDelete($pk);
   }
}

class CustomBlocks_Model_Images_Record extends Model_ORM_Record {
   
   public function getPath($modulePath)
   {
//      $this->{CustomBlocks_Model_Blocks::COLUMN_DATA} = serialize($data);
      return null;
   }
   
   public function getUrl(Module $module)
   {
      return $module->getDataDir(true)
          .CustomBlocks_Model_Images::DIR_IMG.'/'
          .$this->{CustomBlocks_Model_Images::COLUMN_FILE};
   }
}   