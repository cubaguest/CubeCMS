<?php

class Shop_Feed_Heureka extends Shop_Feed implements Shop_Feed_Interface {
   
   protected static $xmlFile = 'heureka.xml';

   /**
    * @var XMLWriter
    */
   protected $xmlWriter = null;
   
   protected $opened = false;
   
   
   protected $carriers = false;
   protected $maxCarrierSpeed = 0;
   protected $categories = array();
   
   protected $taxes = array();
   
   public function __construct() {
   
      // pokud je feed starý do 4 hodin, neregenerovat
      
      $this->xmlWriter = new XMLWriter();
      $this->xmlWriter->openMemory();
      $this->xmlWriter->startDocument('1.0', 'UTF-8');
      $this->xmlWriter->setIndent(true);
   }
   
   protected function addItem(Shop_Model_Product_Record $record, $catParams = array()){
      if($record->hasCombinations()){
         $this->addCombinedItem($record, $catParams);
      } else {
         $this->addSimpleItem($record, $catParams);
      }
   }
   
   protected function addSimpleItem(Shop_Model_Product_Record $record, $catParams = array()){
      $this->xmlWriter->startElement('SHOPITEM');
      $this->xmlWriter->writeElement('ITEM_ID', $record->getPK());
      $name = null;
      if($record->{Shop_Model_Product::COLUMN_MANUFACTURER}){
         $name .= $record->{Shop_Model_Product::COLUMN_MANUFACTURER}.' ';
      }
      $this->xmlWriter->writeElement('PRODUCTNAME', $name.$record->{Shop_Model_Product::COLUMN_NAME});
      $this->xmlWriter->writeElement('PRODUCT', $name.$record->{Shop_Model_Product::COLUMN_NAME});
      $this->xmlWriter->writeElement('DESCRIPTION', $record->{Shop_Model_Product::COLUMN_TEXT_CLEAR});
      $this->xmlWriter->writeElement('PRICE_VAT', round($record->getPriceWithVat(), CUBE_CMS_SHOP_PRICE_ROUND_DECIMAL));
      $this->writeVat($record);
      if($record->{Shop_Model_Product::COLUMN_MANUFACTURER}){
         $this->xmlWriter->writeElement('MANUFACTURER', $record->{Shop_Model_Product::COLUMN_MANUFACTURER});
      }
      
      $this->xmlWriter->writeElement('CATEGORYTEXT', $this->getCategoryName($record, $catParams));
      $this->xmlWriter->writeElement('PRODUCTNO', $record->{Shop_Model_Product::COLUMN_CODE});
      
      $link = new Url_Link_Module(true);
      $link->setModuleRoutes(new ShopProductGeneral_Routes());
      $link->category($record->curlkey)->route('detail', array('urlkey'=>$record->{Shop_Model_Product::COLUMN_URLKEY}));
      $this->xmlWriter->writeElement('URL', (string)$link);
      $this->addImages($record);
      $this->addParams($record);
      $this->addCarriers($record);
      
      $this->xmlWriter->endElement(); // SHOPITEM
      
   }
   
   protected function writeVat($record)
   {
      $this->xmlWriter->writeElement('VAT', $this->taxes[$record->{Shop_Model_Product::COLUMN_ID_TAX}].'%');
   }
   
   protected function getCategoryName(Shop_Model_Product_Record $record, $catParams)
   {
      $catName = null;
      if(isset($this->categories[$record->cid])){
         return $this->categories[$record->cid];
      }
      
      if(isset($catParams['heureka_cat']) && $catParams['heureka_cat'] != null){
         $catName = $catParams['heureka_cat'];
      }
      if($record->{Shop_Model_Product::COLUMN_HEUREKA_CAT} != null){
         $catName = $record->{Shop_Model_Product::COLUMN_HEUREKA_CAT};
      }
      
      if($catName == null){
         $path = Category_Structure::getStructure(Category_Structure::ALL)->getPath($record->cid);
         if(!empty($path)){
            $names = array();
            foreach ($path as $p) {
               $names[] = $p->getCatObj()->getName();
            }
            $catName = implode(' | ', $names);
         } else {
            $catName = $record['cname'][Locales::getDefaultLang()];
         }
      }
      
      $this->categories[$record->cid] = $catName;
      
      return $catName;
   }
   
   protected function addImages(Shop_Model_Product_Record $record)
   {
      $images = $record->getImages();
      
      if($images){
         foreach ($images as $img) {
            /* @var $img Shop_Model_Product_Images_Record */
            if($img->{Shop_Model_Product_Images::COLUMN_IS_TITLE}){
               $this->xmlWriter->writeElement('IMGURL', $img->getUrl());
            } else {
               $this->xmlWriter->writeElement('IMGURL_ALTERNATIVE', $img->getUrl());
            }
               
         }
      }
      
   }
   
   protected function addParams(Shop_Model_Product_Record $record)
   {
      $params = $record->getParams();
      if($params){
         foreach ($params as $param) {
            $this->xmlWriter->startElement('PARAM');
            $this->xmlWriter->writeElement('PARAM_NAME', $param->{Shop_Model_Product_Params::COLUMN_NAME});
            $this->xmlWriter->writeElement('VALUE', $param->{Shop_Model_Product_ParamsValues::COLUMN_VALUE});
            $this->xmlWriter->writeElement('VAL', $param->{Shop_Model_Product_ParamsValues::COLUMN_VALUE});
            $this->xmlWriter->endElement();
         }
      }
   }
   
   protected function addCarriers(Shop_Model_Product_Record $record)
   {
      foreach ($this->carriers as $c) {
         $this->xmlWriter->startElement('DELIVERY');
         $this->xmlWriter->writeElement('DELIVERY_ID', $c->{Shop_Model_Shippings::COLUMN_HEUREKA_CODE});
         
         // pokud je 
         $isCod = false;
         $isNormal = false;
         foreach ($c->payments as $p) {
            if($p->{Shop_Model_Payments::COLUMN_IS_COD} && $isCod == false){
               $this->xmlWriter->writeElement('DELIVERY_PRICE_COD', $c->{Shop_Model_Shippings::COLUMN_VALUE} + $p->{Shop_Model_Payments::COLUMN_PRICE_ADD});
               $isCod = true;
            } else if($isNormal == false) {
               $this->xmlWriter->writeElement('DELIVERY_PRICE', $c->{Shop_Model_Shippings::COLUMN_VALUE} + $p->{Shop_Model_Payments::COLUMN_PRICE_ADD});
               $isNormal = true;
            }
            
            if($isCod && $isNormal){
               break;
            }
         }
         $this->xmlWriter->endElement(); // DELIVERY
      }
      $this->xmlWriter->writeElement('DELIVERY_DATE', $this->maxCarrierSpeed);
   }


   public function addCombinedItem(Shop_Model_Product_Record $record, $catParams = array()){
      $combinations = $record->getCombinations();
      foreach ($combinations as $comb) {
         $this->xmlWriter->startElement('SHOPITEM');
         $this->xmlWriter->writeElement('ITEM_ID', $record->getPK().'-'.$comb->getPK());
         $this->xmlWriter->writeElement('ITEMGROUP_ID', $record->getPK());
         
         $name = null;
         if($record->{Shop_Model_Product::COLUMN_MANUFACTURER}){
            $name .= $record->{Shop_Model_Product::COLUMN_MANUFACTURER}.' ';
         }
         $this->xmlWriter->writeElement('PRODUCTNAME', $name.$record->{Shop_Model_Product::COLUMN_NAME}.', '.$comb->comb_name);
         $this->xmlWriter->writeElement('PRODUCT', $name.$record->{Shop_Model_Product::COLUMN_NAME});
         $this->xmlWriter->writeElement('DESCRIPTION', $record->{Shop_Model_Product::COLUMN_TEXT_CLEAR});
         $this->xmlWriter->writeElement('PRICE_VAT', round($record->getPriceWithVat($comb->{Shop_Model_ProductCombinations::COLUMN_PRICE}), CUBE_CMS_SHOP_PRICE_ROUND_DECIMAL));
         $this->writeVat($record);
         if($record->{Shop_Model_Product::COLUMN_MANUFACTURER}){
            $this->xmlWriter->writeElement('MANUFACTURER', $record->{Shop_Model_Product::COLUMN_MANUFACTURER});
         }
         
         $this->xmlWriter->writeElement('CATEGORYTEXT', $this->getCategoryName($record, $catParams));
         $this->xmlWriter->writeElement('PRODUCTNO', 
                 Shop_Tools::getProductCode($record->{Shop_Model_Product::COLUMN_CODE}, $comb)
                 );

         $link = new Url_Link_Module(true);
         $link->setModuleRoutes(new ShopProductGeneral_Routes());
         $link->category($record->curlkey)->route('detail', array('urlkey'=>$record->{Shop_Model_Product::COLUMN_URLKEY}));
         $this->xmlWriter->writeElement('URL', (string)$link);
         $this->addImages($record);
         $this->addParams($record);
         $this->addCarriers($record);

         $this->xmlWriter->endElement(); // SHOPITEM
      }
   }
   
   
   public function generate() {
      // kontrola souboru
      $file = AppCore::getAppCacheDir().static::$xmlFile;
      
      $now = time();
      $filetime = filemtime($file);
      if( (is_file($file) && ($now - $filetime) >= (3600 * 4)) || CUBE_CMS_DEBUG_LEVEL > 1){
         @unlink($file);
      }
      
      if(!is_file($file)){
         if(!$this->carriers){
            $payments = array();
            $cariersModel = new Shop_Model_Shippings();
            $cariers = $cariersModel->where(Shop_Model_Shippings::COLUMN_PERSONAL_PICKUP.' = 0 AND '.Shop_Model_Shippings::COLUMN_HEUREKA_CODE.' != \'\'', array())->records();
            $paymentsTMP = Shop_Model_Payments::getAllRecords();


            foreach ($paymentsTMP as $p) {
               $payments[$p->getPK()] = $p;
            }
            foreach ($cariers as $c) {
               $disalowP = explode(';', $c->{Shop_Model_Shippings::COLUMN_DISALLOWED_PAYMENTS});
               $c->payments = $payments;
               foreach ($disalowP as $idp) {
                  unset($c->payments[$idp]);
               }
               if($c->{Shop_Model_Shippings::COLUMN_MAX_DAYS} >= $this->maxCarrierSpeed){
                  $this->maxCarrierSpeed = $c->{Shop_Model_Shippings::COLUMN_MAX_DAYS};
               }
            }
            $this->carriers = $cariers;
         }
         
         $taxes = Shop_Model_Tax::getAllRecords();
         foreach ($taxes as $tax) {
            $this->taxes[$tax->getPK()] = $tax->{Shop_Model_Tax::COLUMN_VALUE};
         }

         $this->xmlWriter->startElement('SHOP');
         $this->xmlWriter->writeAttribute('xmlns', 'http://www.zbozi.cz/ns/offer/1.0');
         $productsModel = new Shop_Model_Product();
         $products = $productsModel
                 ->joinFK(Shop_Model_Product::COLUMN_ID_CATEGORY, array(
                     'cid' => Model_Category::COLUMN_ID, 
                     'curlkey' => Model_Category::COLUMN_URLKEY, 
                     'cparams' => Model_Category::COLUMN_PARAMS,
                     'cname' => Model_Category::COLUMN_NAME
                         ))
                 ->where(Shop_Model_Product::COLUMN_ACTIVE, 1)
                 ->records();

         foreach ($products as $pr) {
            $params = array();
            if($pr->cparams != null){
               $params = unserialize($pr->cparams);
            }

            $this->addItem($pr, $params);
            file_put_contents($file, $this->xmlWriter->flush(true), FILE_APPEND);
         }
         $this->xmlWriter->endElement(); // SHOP
         file_put_contents($file, $this->xmlWriter->flush(true), FILE_APPEND);
      }
   }
   
   /**
    * @retun string
    */
   public function getFilePath() {
      return AppCore::getAppCacheDir().static::$xmlFile;
   }
   
   public static function getCategories($q = null) {
      $cache = new Cache_File('heureka_cats', 60*60*24*3); // tři dny
      
      $items = $cache->get();
      
      if(!$items){
         // load xml file
         $xml = new SimpleXMLElement(file_get_contents('https://www.heureka.cz/direct/xml-export/shops/heureka-sekce.xml'));
         $items = self::catDataToArray($xml);
         $cache->set($items);
      }
      
      if($q != null){
         $itemsAll = $items;
         $items = array();
         foreach ($itemsAll as $item) {
            if(strpos($item, $q) !== false){
               $items[] = $item;
            }
         }
      }
      
      return $items;
   }
   
   protected static function catDataToArray($data, $parentName = null, $level = 1)
   {
      $ret = array();
      
      foreach ($data as $item) {
         if(isset($item->CATEGORY_FULLNAME)){
            $name = (string)$item->CATEGORY_FULLNAME;
            $ret[] = $name;
         } else {
            if(isset($item->CATEGORY) && !empty($item->CATEGORY)){
               $ret = array_merge($ret, self::catDataToArray($item->CATEGORY, null, $level + 1));
            }
         }
      }
      return $ret;
   }
}