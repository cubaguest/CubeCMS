<?php

/**
 * Třída modelu produktů
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id:  $ VVE 7.3 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
 */
class Shop_Model_Product extends Model_ORM
{
   const DB_TABLE = 'shop_products_general';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_product';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_ID_TAX = 'id_tax';
   
   const COLUMN_CODE = 'code';
   const COLUMN_NAME = 'name';
   const COLUMN_URLKEY = 'urlkey';
   const COLUMN_TEXT_SHORT = 'text_short';
   const COLUMN_TEXT = 'text';
   const COLUMN_TEXT_CLEAR = 'text_clear';
   const COLUMN_KEYWORDS = 'keywords';
   
   const COLUMN_PRICE = 'price';
   const COLUMN_UNIT = 'unit';
   const COLUMN_UNIT_SIZE = 'unit_size';
   const COLUMN_QUANTITY = 'quantity';
   const COLUMN_STOCK = 'product_stock';
   const COLUMN_WEIGHT = 'weight';
   const COLUMN_DISCOUNT = 'discount';
   const COLUMN_DELETED = 'deleted';
   const COLUMN_SHOWED = 'showed';
   const COLUMN_ACTIVE = 'active';
   const COLUMN_IMAGE = 'image';
   const COLUMN_DATE_ADD = 'date_add';
   const COLUMN_IS_NEW_TO_DATE = 'is_new_to_date';
   const COLUMN_PERSONAL_PICKUP_ONLY = 'personal_pickup_only';
   const COLUMN_PICKUP_DATE = 'required_pickup_date';
   
   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_sh_pr_gen');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true, 'index' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true ));
      $this->addColumn(self::COLUMN_ID_TAX, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addColumn(self::COLUMN_CODE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(200)', 'nn' => true, 'lang' => true, 
         'pdoparam' => PDO::PARAM_STR, 'fulltext' => true, 'fulltextRel' => VVE_SEARCH_ARTICLE_REL_MULTIPLIER));
      $this->addColumn(self::COLUMN_URLKEY, array('datatype' => 'varchar(200)', 'nn' => true, 'lang' => true, 
         'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_SHORT, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT_CLEAR, array('datatype' => 'text', 'lang' => true, 'pdoparam' => PDO::PARAM_STR, 'fulltext' => true));
      $this->addColumn(self::COLUMN_KEYWORDS, array('datatype' => 'varchar(200)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      
      $this->addColumn(self::COLUMN_PRICE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_STR, 'default' => 0));
      $this->addColumn(self::COLUMN_UNIT, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_UNIT_SIZE, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_QUANTITY, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_STOCK, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_WEIGHT, array('datatype' => 'float', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DISCOUNT, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_DELETED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_SHOWED, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_IS_NEW_TO_DATE, array('datatype' => 'date', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PERSONAL_PICKUP_ONLY, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_PICKUP_DATE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category', Model_Category::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_TAX, "Shop_Model_Tax", Shop_Model_Tax::COLUMN_ID);
      
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Shop_Model_ProductVariants', Shop_Model_ProductVariants::COLUMN_ID_PRODUCT);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Shop_Model_ProductCombinations', Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT);
   }

   protected function beforeSave(Model_ORM_Record $record, $type = 'U')
   {
//      if($record->{self::COLUMN_QUANTITY} < 0){
//         $record->{self::COLUMN_QUANTITY} = -1;
//      }
      // generování unkátního url klíče
      $urlkeys = $record->{self::COLUMN_URLKEY};
      foreach (Locales::getAppLangs() as $lang) {
         // pokud není url klíč
         if($urlkeys[$lang] == null){
            $urlkeys[$lang] = vve_cr_url_key($record[Shop_Model_Product::COLUMN_NAME][$lang]);
         }

         $counter = 1;
         $baseKey = $urlkeys[$lang];
         $urlKeyParts = array();
         if(preg_match('/(.*)-([0-9]+)$/', $baseKey, $urlKeyParts)){
            $baseKey = $urlKeyParts[1];
            $counter = (int)$urlKeyParts[2];
         }
         if($record->isNew()){
            while($this->where(self::COLUMN_URLKEY." = :ukey", array('ukey' => $urlkeys[$lang]))->count() != 0 ){
               $urlkeys[$lang] = $baseKey."-".$counter;
               $counter++;
            };
         } else {
            // exist record ignore yourself
            while($this->where(self::COLUMN_URLKEY." = :ukey AND ".self::COLUMN_ID ." != :id",
               array('ukey' => $urlkeys[$lang], 'id' => $record->getPK() ))->count() != 0 ){
               $urlkeys[$lang] = $baseKey."-".$counter;
               $counter++;
            };
         }
      }
      $record->{self::COLUMN_URLKEY} = $urlkeys;
   }

   public static function getProductWithCombination()
   {

   }

   public static function getProductsWithDefaultCombinations($idCategory = 0)
   {

   }
}
?>
