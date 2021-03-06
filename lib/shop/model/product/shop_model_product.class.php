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
class Shop_Model_Product extends Model_ORM {

   const DB_TABLE = 'shop_products_general';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_product';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_ID_TAX = 'id_tax';
   const COLUMN_CODE = 'product_code';
   const COLUMN_NAME = 'product_name';
   const COLUMN_URLKEY = 'product_urlkey';
   const COLUMN_TEXT_SHORT = 'product_text_short';
   const COLUMN_TEXT = 'product_text';
   const COLUMN_TEXT_CLEAR = 'product_text_clear';
   const COLUMN_KEYWORDS = 'product_keywords';
   const COLUMN_PRICE = 'product_price';
   const COLUMN_UNIT = 'product_unit';
   const COLUMN_UNIT_SIZE = 'product_unit_size';
   const COLUMN_UNIT_DECIMALS = 'product_unit_decimals';
   const COLUMN_QUANTITY = 'product_quantity';
   const COLUMN_WARRANTY = 'product_warranty_years';
   const COLUMN_STOCK = 'product_stock';
   const COLUMN_WEIGHT = 'product_weight';
   const COLUMN_DISCOUNT = 'product_discount';
   const COLUMN_DELETED = 'product_deleted';
   const COLUMN_SHOWED = 'product_showed';
   const COLUMN_ACTIVE = 'product_active';
   const COLUMN_IMAGE = 'product_image';
   const COLUMN_DATE_ADD = 'product_date_add';
   const COLUMN_DATE_EDIT = 'product_date_edit';
   const COLUMN_IS_NEW_TO_DATE = 'product_is_new_to_date';
   const COLUMN_PERSONAL_PICKUP_ONLY = 'product_personal_pickup_only';
   const COLUMN_PICKUP_DATE = 'product_required_pickup_date';
   const COLUMN_ORDER = 'product_order';
   const COLUMN_MANUFACTURER = 'product_manufacturer';
   const COLUMN_HEUREKA_CAT = 'product_heureka_cat';
   const COLUMN_GOOGLE_CAT = 'product_google_cat';
   const COLUMN_ZBOZI_CAT = 'product_zbozi_cat';

   protected static $productCatCounter = false;

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_sh_pr_gen');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true, 'index' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_ID_TAX, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));

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
      $this->addColumn(self::COLUMN_UNIT_SIZE, array('datatype' => 'decimal(11,4)', 'pdoparam' => PDO::PARAM_STR, 'default' => 0));
      $this->addColumn(self::COLUMN_QUANTITY, array('datatype' => 'decimal(11,4)', 'pdoparam' => PDO::PARAM_STR, 'default' => 0));
      $this->addColumn(self::COLUMN_WARRANTY, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_STR, 'default' => 2));
      $this->addColumn(self::COLUMN_STOCK, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_WEIGHT, array('datatype' => 'float', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DISCOUNT, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_DELETED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_SHOWED, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_MANUFACTURER, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DATE_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_DATE_EDIT, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_IS_NEW_TO_DATE, array('datatype' => 'date', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_PERSONAL_PICKUP_ONLY, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_PICKUP_DATE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_HEUREKA_CAT, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_GOOGLE_CAT, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ZBOZI_CAT, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));

      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category', Model_Category::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_TAX, "Shop_Model_Tax", Shop_Model_Tax::COLUMN_ID);

      $this->addRelatioOneToMany(self::COLUMN_ID, 'Shop_Model_Product_Variants', Shop_Model_Product_Variants::COLUMN_ID_PRODUCT);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Shop_Model_Product_Combinations', Shop_Model_Product_Combinations::COLUMN_ID_PRODUCT);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Shop_Model_Product_Images', Shop_Model_Product_Images::COLUMN_ID_PRODUCT);
   }

   protected function beforeSave(Model_ORM_Record $record, $type = 'U')
   {
      $record->{self::COLUMN_DATE_EDIT} = new DateTime();
      // generování unkátního url klíče
      $urlkeys = $record->{self::COLUMN_URLKEY};
      foreach (Locales::getAppLangs() as $lang) {
         // pokud není url klíč
         if ($urlkeys[$lang] == null) {
            $urlkeys[$lang] = vve_cr_url_key($record[Shop_Model_Product::COLUMN_NAME][$lang]);
         }

         $counter = 1;
         $baseKey = $urlkeys[$lang];
         $urlKeyParts = array();
         if (preg_match('/(.*)-([0-9]+)$/', $baseKey, $urlKeyParts)) {
            $baseKey = $urlKeyParts[1];
            $counter = (int) $urlKeyParts[2];
         }
         if ($record->isNew()) {
            while ($this->where(self::COLUMN_URLKEY . " = :ukey", array('ukey' => $urlkeys[$lang]))->count() != 0) {
               $urlkeys[$lang] = $baseKey . "-" . $counter;
               $counter++;
            };
         } else {
            // exist record ignore yourself
            while ($this->where(self::COLUMN_URLKEY . " = :ukey AND " . self::COLUMN_ID . " != :id", array('ukey' => $urlkeys[$lang], 'id' => $record->getPK()))->count() != 0) {
               $urlkeys[$lang] = $baseKey . "-" . $counter;
               $counter++;
            };
         }
      }
      $record->{self::COLUMN_URLKEY} = $urlkeys;

      // kontrola jestli je zadána pozice
      if ($record->{self::COLUMN_ORDER} == 0) {
         $counter = $this->where(self::COLUMN_ID_CATEGORY . " = :id", array('id' => $record->{self::COLUMN_ID_CATEGORY}))->count();
         $record->{self::COLUMN_ORDER} = $counter + 1;
      }
   }

   protected function beforeDelete($pk)
   {
      $m = new self();
      $record = $m->record($pk);

      // reorganizovat pořadí
      $m->where(self::COLUMN_ID_CATEGORY . " = :id AND " . self::COLUMN_ID_CATEGORY . " > :ord", array('id' => $record->{self::COLUMN_ID_CATEGORY}, 'ord' => $record->{self::COLUMN_ORDER},))
              ->update(array(self::COLUMN_ORDER => array('stmt' => self::COLUMN_ORDER . " - 1")));
   }

   public static function changeOrder($id, $newPos)
   {
      $m = new self();
      $rec = $m->record($id);

      if ($newPos > $rec->{self::COLUMN_ORDER}) {
         // move down
         $m->where(self::COLUMN_ORDER . " > :oldOrder AND " . self::COLUMN_ORDER . " <= :newOrder AND " . self::COLUMN_ID_CATEGORY . " = :id", array('oldOrder' => $rec->{self::COLUMN_ORDER}, 'newOrder' => $newPos, 'id' => $rec->{self::COLUMN_ID_CATEGORY}))
                 ->update(array(self::COLUMN_ORDER => array('stmt' => self::COLUMN_ORDER . ' - 1')));
      } else {
         // move up
         $m->where(self::COLUMN_ORDER . " < :oldOrder AND " . self::COLUMN_ORDER . " >= :newOrder AND " . self::COLUMN_ID_CATEGORY . " = :id", array('oldOrder' => $rec->{self::COLUMN_ORDER}, 'newOrder' => $newPos, 'id' => $rec->{self::COLUMN_ID_CATEGORY}))
                 ->update(array(self::COLUMN_ORDER => array('stmt' => self::COLUMN_ORDER . ' + 1')));
      }
      // update row
      $rec->{self::COLUMN_ORDER} = $newPos;
      $rec->save();
   }

   public static function getProductWithCombination()
   {
      
   }

   public static function getProductsWithDefaultCombinations($idCategory = 0)
   {
      
   }

   public static function getCountFromCategory($idCategory = 0)
   {
      if (self::$productCatCounter === false) {
         $m = new self();
         $counters = $m
                 ->columns(array('counter' => 'COUNT( `'.$m->getTableShortName().'`.`' . self::COLUMN_ID . '`)', self::COLUMN_ID_CATEGORY))
                 ->joinFK(self::COLUMN_ID_CATEGORY)
                 ->where(self::COLUMN_ACTIVE . ' = 1', array())
                 ->groupBy(array(self::COLUMN_ID_CATEGORY))
                 ->records(PDO::FETCH_OBJ);
         if($counters){
            foreach ($counters as $c) {
               self::$productCatCounter[$c->{self::COLUMN_ID_CATEGORY}] = $c->counter;
            }
         }
      }
      return isset(self::$productCatCounter[$idCategory]) ? self::$productCatCounter[$idCategory] : 0;
   }

   public function records($fetchParams = self::FETCH_LANG_CLASS)
   {
      // asi načíst i s titulními obrázky nějak rozumně, tak aby se to dalo kešovat
      // je bez id produktu v obrázku,protože se kryje s id produktu 
      $this->join(self::COLUMN_ID, 'Shop_Model_Product_Images', Shop_Model_Product_Images::COLUMN_ID_PRODUCT, 
//              array(Shop_Model_ProductImage::COLUMN_ID, Shop_Model_ProductImage::COLUMN_NAME, 
//                  Shop_Model_ProductImage::COLUMN_IS_TITLE, Shop_Model_ProductImage::COLUMN_ORDER, 
//                  Shop_Model_ProductImage::COLUMN_TYPE), 
//              array('*'), 
              null, 
              self::JOIN_LEFT, ' AND '.Shop_Model_Product_Images::COLUMN_IS_TITLE.' = 1');
//      Debug::log($this->getSQLQuery());
      $records = parent::records($fetchParams);
      return $records;
   }
   
   public static function getLastChange($idc)
   {
      $m = new self();
      $ret = $m->columns(array(self::COLUMN_DATE_EDIT))
              ->where(self::COLUMN_ID_CATEGORY.' = :idc', array('idc' => $idc))
              ->order(array(self::COLUMN_DATE_EDIT => Model_ORM::ORDER_DESC))
              ->record();
      if($ret){
         return new DateTime($ret->{self::COLUMN_DATE_EDIT});
      } 
      return false;
   }

}

class Shop_Model_Product_Record extends Model_ORM_Record {

   protected static $_titleImages = array();

   protected $vat = null;

   protected function load()
   {
      $this->vat = Shop_Model_Tax::getRecord($this->getPK());
   }

   public function getTitleImage()
   {
      // pokud je již nasatven, jen ho vrať
      if(!isset(self::$_titleImages[$this->getPK()])){
         if(isset($this->{Shop_Model_Product_Images::COLUMN_ID})){
            self::$_titleImages[$this->getPK()] = $this->createModelRecordObject('Shop_Model_Product_Images');
         } else {
            // pokud není tak jej vypiš
            $m = new Shop_Model_Product_Images();
            self::$_titleImages[$this->getPK()] = $m
                    ->where(Shop_Model_Product_Images::COLUMN_ID_PRODUCT.' = :idp AND '.Shop_Model_Product_Images::COLUMN_IS_TITLE.' = 1',
                            array('idp' => $this->getPK()))
                    ->record();
         }
      }
      return self::$_titleImages[$this->getPK()];
   }

   public function getImages()
   {
      $m = new Shop_Model_Product_Images();
      return $m->where(Shop_Model_Product_Images::COLUMN_ID_PRODUCT, $this->getPK())->records();
   }
   
   public function hasCombinations()
   {
      return Shop_Model_Product_Combinations::productHasCombination($this->getPK());
   }
   
   /**
    * 
    * @return Model_ORM_Record[]
    */
   public function getCombinations()
   {
      return Shop_Model_ProductCombinations::getCombinations($this->getPK());
   }
   
   public function getUrlKey()
   {
      return $this->{Shop_Model_Product::COLUMN_URLKEY};
   }
   
   public function getParams()
   {
      $m = new Shop_Model_Product_ParamsValues();
      
      return $m->joinFK(Shop_Model_Product_ParamsValues::COLUMN_ID_PARAM)
              ->where(Shop_Model_Product_ParamsValues::COLUMN_ID_PRODUCT, $this->getPK())
              ->records();
   }
   
   public function getPriceWithVat($combinationAddProce = 0)
   {
      if($this->vat === null){
         $this->vat = Shop_Model_Tax::getRecord($this->{Shop_Model_Product::COLUMN_ID_TAX});
      }
      return ($this->{Shop_Model_Product::COLUMN_PRICE} + $combinationAddProce) * ($this->vat->{Shop_Model_Tax::COLUMN_VALUE}/100 + 1);
   }
   
   public function getPriceWithoutVat($combinationAddProce = 0)
   {
      return ($this->{Shop_Model_Product::COLUMN_PRICE} + $combinationAddProce);
   }
}
