<?php
/*
 * Třída modelu s detailem uživatele
 */
class CustomBlocks_Model_Gallery extends CustomBlocks_Model_Images {

   public static function getImages($idBlock, $index)
   {
      $m = new static();
      return $m->where(self::COLUMN_ID_BLOCK." = :idb AND ".self::COLUMN_INDEX." = :idi", array('idb' => $idBlock, 'idi' => (string)$index))
              ->order(self::COLUMN_FILE)
              ->records();
   }
   
   public static function getImagesPath(Module $module, $idBlock, $index)
   {
      return $module->getDataDir(false)
          .CustomBlocks_Model_Images::DIR_IMG.DIRECTORY_SEPARATOR.$idBlock.'-'.$index.DIRECTORY_SEPARATOR;
   }
}

class CustomBlocks_Model_Gallery_Record extends Model_ORM_Record {
   
   public function getPath(Module $module)
   {
      return $module->getDataDir(false)
          .CustomBlocks_Model_Images::DIR_IMG.DIRECTORY_SEPARATOR
              .$this->{CustomBlocks_Model_Gallery::COLUMN_ID_BLOCK}.'-'.$this->{CustomBlocks_Model_Gallery::COLUMN_INDEX}.DIRECTORY_SEPARATOR;
   }
   
   public function getUrl(Module $module)
   {
      return $module->getDataDir(true)
          .CustomBlocks_Model_Images::DIR_IMG.'/'.$this->{CustomBlocks_Model_Gallery::COLUMN_ID_BLOCK}.'-'.$this->{CustomBlocks_Model_Gallery::COLUMN_INDEX}
          .'/'.$this->{CustomBlocks_Model_Gallery::COLUMN_FILE};
   }
   
   /**
    * Vrací pole s o
    * @param Module $module
    * @return type
    */
   public function getImagesUrl(Module $module)
   {
      return $module->getDataDir(true)
          .CustomBlocks_Model_Images::DIR_IMG.'/'.$this->{CustomBlocks_Model_Gallery::COLUMN_ID_BLOCK}.'-'.$this->{CustomBlocks_Model_Gallery::COLUMN_INDEX}.'/';
   }
   
   /**
    * Vrací pole s obrázky
    * @return CustomBlocks_Model_Gallery_Record[]
    */
   public function getImages()
   {
      $m = new CustomBlocks_Model_Gallery();
      return $m->where(CustomBlocks_Model_Items::COLUMN_ID_BLOCK." = :idb AND ".CustomBlocks_Model_Items::COLUMN_INDEX." = :idi", 
              array('idb' => $this->{CustomBlocks_Model_Items::COLUMN_ID_BLOCK},
              'idi' => (string)$this->{CustomBlocks_Model_Items::COLUMN_INDEX}))
              ->order(CustomBlocks_Model_Gallery::COLUMN_FILE)
                      ->records();
   }
}   