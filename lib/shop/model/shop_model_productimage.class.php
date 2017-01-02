<?php

/*
 * Třída modelu detailem článku
 */

class Shop_Model_ProductImage extends Model_ORM_Ordered {

   const DB_TABLE = 'shop_products_images';
   const COLUMN_ID = 'id_product_image';
   const COLUMN_ID_PRODUCT = 'id_product';
   const COLUMN_NAME = 'image_name';
   const COLUMN_IS_TITLE = 'is_title';
   const COLUMN_TYPE = 'image_type';
   const COLUMN_ORDER = 'image_order';

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_pr_images');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_PRODUCT, array('datatype' => 'int', 'nn' => true, 'index' => array(self::COLUMN_ID_PRODUCT, self::COLUMN_ORDER)));

      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'lang' => true));
      $this->addColumn(self::COLUMN_TYPE, array('datatype' => 'varchar(5)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'default' => 'jpg'));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'dafeult' => 0));
      $this->addColumn(self::COLUMN_IS_TITLE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'dafeult' => false));

      $this->setPk(self::COLUMN_ID);
      $this->setOrderColumn(self::COLUMN_ORDER);
      $this->setLimitedColumns(array(self::COLUMN_ID_PRODUCT));

      $this->addForeignKey(self::COLUMN_ID_PRODUCT, 'Shop_Model_Product', Shop_Model_Product::COLUMN_ID);
   }

   protected function beforeDelete($pk)
   {
      // remove file
      $img = $this->record($pk);
      $img->deleteFile();
      parent::beforeDelete($pk);
   }
   
}

class Shop_Model_ProductImage_Record extends Model_ORM_Ordered_Record {

   public function getUrl()
   {
      return Shop_Tools::getProductImagesDir(true) . $this->{Shop_Model_ProductImage::COLUMN_ID_PRODUCT}
              . '/' . $this->{Shop_Model_ProductImage::COLUMN_ID} . '.' . $this->{Shop_Model_ProductImage::COLUMN_TYPE};
   }

   public function getPath()
   {
      return Shop_Tools::getProductImagesDir(false) . $this->{Shop_Model_ProductImage::COLUMN_ID_PRODUCT}
              . DIRECTORY_SEPARATOR . $this->{Shop_Model_ProductImage::COLUMN_ID} . '.' . $this->{Shop_Model_ProductImage::COLUMN_TYPE};
   }

   /**
    * 
    * @return File_Image
    */
   public function getFile()
   {
      $file = new File_Image($this->{Shop_Model_ProductImage::COLUMN_ID} . '.' . $this->{Shop_Model_ProductImage::COLUMN_TYPE}, 
              Shop_Tools::getProductImagesDir(false) . $this->{Shop_Model_ProductImage::COLUMN_ID_PRODUCT}
              . DIRECTORY_SEPARATOR);
      return $file;
   }
   
   /**
    * 
    */
   public function deleteFile()
   {
      $img = new File_Image($this->{Shop_Model_ProductImage::COLUMN_ID} . '.' . $this->{Shop_Model_ProductImage::COLUMN_TYPE}, 
              Shop_Tools::getProductImagesDir(false) . $this->{Shop_Model_ProductImage::COLUMN_ID_PRODUCT}
              . DIRECTORY_SEPARATOR);
      if($img->exist()){
         $img->delete();
      }
   }

   public function isTitle()
   {
      return $this->{Shop_Model_ProductImage::COLUMN_IS_TITLE} == true;
   }
   
   public function setAsTitle()
   {
      // odznačení všech na netitulní
      $m = new Shop_Model_ProductImage();
      
      $m->where(Shop_Model_ProductImage::COLUMN_ID_PRODUCT.' = :idp AND '.Shop_Model_ProductImage::COLUMN_IS_TITLE.' = 1', 
              array('idp' => $this->{Shop_Model_ProductImage::COLUMN_ID_PRODUCT}))
                      ->update(array(Shop_Model_ProductImage::COLUMN_IS_TITLE => false));
      
      // označení aktuálního na titulní
      $this->{Shop_Model_ProductImage::COLUMN_IS_TITLE} = true;
      return $this->save();        
   }

}
