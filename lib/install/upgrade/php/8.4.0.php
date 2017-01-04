<?php
// pokud je instalován eshop, přeuspořádat obrázky
if(defined('CUBE_CMS_SHOP') && CUBE_CMS_SHOP == true){
   // načíst všechny produkty
   $products = Shop_Model_Product::getAllRecords();
   $modelImages = new Shop_Model_ProductImage();
   
   // projití a uložení obrázků do nové tabulky s označením titulní
   $basePath = Shop_Tools::getProductImagesDir(false);
   
   foreach ($products as $product) {
      $file = new File($product->{Shop_Model_Product::COLUMN_IMAGE}, $basePath);
      if($file->exist()){
         /*
          * Nový formát souboru:
          * 
          * /shop/products/{idproduct}/{id obrázku}.{typ}
          * 
          */
         $productDir = $basePath.$product->getPK().DIRECTORY_SEPARATOR;
         FS_Dir::checkStatic($productDir);
         
         $imageRecord = $modelImages->newRecord();
         $imageRecord->{Shop_Model_ProductImage::COLUMN_ID_PRODUCT} = $product->getPK();
         $imageRecord->{Shop_Model_ProductImage::COLUMN_NAME} = $product->{Shop_Model_Product::COLUMN_NAME};
         $imageRecord->{Shop_Model_ProductImage::COLUMN_ORDER} = 1;
         $imageRecord->{Shop_Model_ProductImage::COLUMN_IS_TITLE} = true;
         $imageRecord->{Shop_Model_ProductImage::COLUMN_TYPE} = $file->getExtension();
         $imageRecord->save();
         $file->move($productDir);
         $file->rename($imageRecord->getPK().'.'.$file->getExtension());
         
      }
   }
}