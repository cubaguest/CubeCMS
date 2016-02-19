<?php
/*
 * Třída modelu s detailem uživatele
 */
class CustomBlocks_Model_Files extends CustomBlocks_Model_Items {
	const DB_TABLE 	   = 'custom_blocks_files';
   
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_FILE       = 'block_file_filename';

   const DIR_FILES = 'files';
   
   protected function  _initTable() {
      parent::_initTable();
      $this->setTableName(self::DB_TABLE, 't_CustomBlocksFiles');
      $this->addColumn(self::COLUMN_FILE, array('datatype' => 'varchar(200)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
   }
   
   protected function beforeDelete($pk)
   {
      if(isset($this->params['dir'])){
         $image = self::getRecord($pk);
         $file = new File($image->{self::COLUMN_FILE}, $this->params['dir'].self::DIR_FILES);
         $file->delete();
      }
      parent::beforeDelete($pk);
   }
}

class CustomBlocks_Model_Files_Record extends Model_ORM_Record {
   
   public function getPath($modulePath)
   {
//      $this->{CustomBlocks_Model_Blocks::COLUMN_DATA} = serialize($data);
      return null;
   }
   
   public function getUrl(Module $module)
   {
      return $module->getDataDir(true)
          .  CustomBlocks_Model_Files::DIR_FILES.'/'
          .$this->{CustomBlocks_Model_Files::COLUMN_FILE};
   }
}   