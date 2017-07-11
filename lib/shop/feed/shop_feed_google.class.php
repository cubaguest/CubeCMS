<?php

class Shop_Feed_Google extends Shop_Feed implements Shop_Feed_Interface {

   const XML_FILE = 'google.xml';

   /**
    * @var XMLWriter
    */
   protected $xmlWriter = null;
   protected $opened = false;
   protected $carriers = false;
   protected $maxCarrierSpeed = 0;
   protected $categories = array();
   protected $taxes = array();

   public function __construct()
   {

      // pokud je feed starý do 4 hodin, neregenerovat

      $this->xmlWriter = new XMLWriter();
      $this->xmlWriter->openMemory();
      $this->xmlWriter->startDocument('1.0', 'UTF-8');
      $this->xmlWriter->setIndent(true);
   }

   protected function addItem(Shop_Model_Product_Record $record, $catParams = array())
   {
      if ($record->hasCombinations()) {
         $this->addCombinedItem($record, $catParams);
      } else {
         $this->addSimpleItem($record, $catParams);
      }
   }

   protected function addSimpleItem(Shop_Model_Product_Record $record, $catParams = array())
   {
      $this->xmlWriter->startElement('entry');
      $this->xmlWriter->writeElementNs('g', 'id', null, $record->getPK());

      // základní rss
      $name = null;
      if ($record->{Shop_Model_Product::COLUMN_MANUFACTURER}) {
         $name .= $record->{Shop_Model_Product::COLUMN_MANUFACTURER} . ' ';
      }
      $this->xmlWriter->writeElementNs('g', 'title', null, $name . $record->{Shop_Model_Product::COLUMN_NAME});
//      $this->xmlWriter->writeElement('description', $record->{Shop_Model_Product::COLUMN_TEXT_CLEAR});
      $this->xmlWriter->writeElementNs('g','description', null, $record->{Shop_Model_Product::COLUMN_TEXT_CLEAR});
//      $this->xmlWriter->startElementNs('g', 'description', null);
//      $this->xmlWriter->writeCdata($record->{Shop_Model_Product::COLUMN_TEXT_CLEAR});
//      $this->xmlWriter->endElement();


      $link = new Url_Link_Module(true);
      $link->setModuleRoutes(new ShopProductGeneral_Routes());
      $link->category($record->curlkey)->route('detail', array('urlkey' => $record->{Shop_Model_Product::COLUMN_URLKEY}));
      $this->xmlWriter->writeElementNs('g', 'link', null, (string) $link);


      $this->addImages($record);

      $this->xmlWriter->writeElementNs('g', 'condition', null, 'new');
      $this->xmlWriter->writeElementNs('g', 'availability', null, ($record->{Shop_Model_Product::COLUMN_QUANTITY} > 0 ? 'in stock' : 'out of stock'));
      $this->xmlWriter->writeElementNs('g', 'price', null, round($record->getPriceWithVat(), CUBE_CMS_SHOP_PRICE_ROUND_DECIMAL) . ' CZK');

      $this->addCarriers($record);

      if ($record->{Shop_Model_Product::COLUMN_MANUFACTURER} != null) {
         $this->xmlWriter->writeElementNs('g', 'brand', null, $record->{Shop_Model_Product::COLUMN_MANUFACTURER});
      }
      if ($record->{Shop_Model_Product::COLUMN_CODE} != null) {
         $this->xmlWriter->writeElementNs('g', 'mpn', null, $record->{Shop_Model_Product::COLUMN_CODE});
      } else {
         $this->xmlWriter->writeElementNs('g', 'identifier_exists', null, 'no');
      }

      $this->xmlWriter->writeElementNs('g', 'google_product_category', null, $this->getCategoryName($record, $catParams));

//      
      $this->xmlWriter->endElement(); // item
//      die;
   }

   protected function getCategoryName(Shop_Model_Product_Record $record, $catParams)
   {
      $catName = null;
      if (isset($this->categories[$record->cid])) {
         return $this->categories[$record->cid];
      }

      if (isset($catParams['google_cat']) && $catParams['google_cat'] != null) {
         $catName = $catParams['google_cat'];
      }
      if ($record->{Shop_Model_Product::COLUMN_GOOGLE_CAT} != null) {
         $catName = $record->{Shop_Model_Product::COLUMN_GOOGLE_CAT};
      }

      if ($catName == null) {
         $path = Category_Structure::getStructure(Category_Structure::ALL)->getPath($record->cid);
         if (!empty($path)) {
            $names = array();
            foreach ($path as $p) {
               $names[] = $p->getCatObj()->getName();
            }
            $catName = implode(' > ', $names);
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

      if ($images) {
         foreach ($images as $img) {
            /* @var $img Shop_Model_Product_Images_Record */
            if ($img->{Shop_Model_Product_Images::COLUMN_IS_TITLE}) {
               $this->xmlWriter->writeElementNs('g', 'image_link', null, $img->getUrl());
            } else {
               $this->xmlWriter->writeElementNs('g', 'additional_image_link', null, $img->getUrl());
            }
         }
      }
   }

   protected function addParams(Shop_Model_Product_Record $record)
   {
//      $params = $record->getParams();
//      if($params){
//         foreach ($params as $param) {
//            $this->xmlWriter->startElement('PARAM');
//            $this->xmlWriter->writeElement('PARAM_NAME', $param->{Shop_Model_Product_Params::COLUMN_NAME});
//            $this->xmlWriter->writeElement('VALUE', $param->{Shop_Model_Product_ParamsValues::COLUMN_VALUE});
//            $this->xmlWriter->endElement();
//         }
//      }
   }

   protected function addCarriers(Shop_Model_Product_Record $record)
   {
      foreach ($this->carriers as $c) {

         $countries = explode(';', $c->{Shop_Model_Zones::COLUMN_CODES});

         foreach ($countries as $country) {
            // pokud je 
            $this->xmlWriter->startElementNs('g', 'shipping', null);
            $maxPaymentPrice = 0;
            foreach ($c->payments as $p) {
               if(!$p->{Shop_Model_Shippings::COLUMN_PERSONAL_PICKUP} && $p->{Shop_Model_Payments::COLUMN_PRICE_ADD} > $maxPaymentPrice){
                  $maxPaymentPrice = $p->{Shop_Model_Payments::COLUMN_PRICE_ADD};
               }
            }
            $this->xmlWriter->writeElementNs('g', 'country', null, $country);
            $this->xmlWriter->writeElementNs('g', 'service', null, $c->{Shop_Model_Shippings::COLUMN_NAME});
            $this->xmlWriter->writeElementNs('g', 'price', null, ($c->{Shop_Model_Shippings::COLUMN_VALUE} + $maxPaymentPrice) . ' CZK');
            $this->xmlWriter->endElement(); // shipping
         }
      }
   }

   public function addCombinedItem(Shop_Model_Product_Record $record, $catParams = array())
   {
//      $combinations = $record->getCombinations();
//      foreach ($combinations as $comb) {
//         $this->xmlWriter->startElement('SHOPITEM');
//         $this->xmlWriter->writeElement('ITEM_ID', $record->getPK().'-'.$comb->getPK());
//         $this->xmlWriter->writeElement('ITEMGROUP_ID', $record->getPK());
//         
//         $name = null;
//         if($record->{Shop_Model_Product::COLUMN_MANUFACTURER}){
//            $name .= $record->{Shop_Model_Product::COLUMN_MANUFACTURER}.' ';
//         }
//         $this->xmlWriter->writeElement('PRODUCTNAME', $name.$record->{Shop_Model_Product::COLUMN_NAME}.', '.$comb->comb_name);
//         $this->xmlWriter->writeElement('PRODUCT', $name.$record->{Shop_Model_Product::COLUMN_NAME});
//         $this->xmlWriter->writeElement('DESCRIPTION', $record->{Shop_Model_Product::COLUMN_TEXT_CLEAR});
//         $this->xmlWriter->writeElement('PRICE_VAT', round($record->getPriceWithVat($comb->{Shop_Model_ProductCombinations::COLUMN_PRICE}), CUBE_CMS_SHOP_PRICE_ROUND_DECIMAL));
//         $this->xmlWriter->writeElement('VAT', $this->taxes[$record->{Shop_Model_Product::COLUMN_ID_TAX}].'%');
//         if($record->{Shop_Model_Product::COLUMN_MANUFACTURER}){
//            $this->xmlWriter->writeElement('MANUFACTURER', $record->{Shop_Model_Product::COLUMN_MANUFACTURER});
//         }
//         
//         $this->xmlWriter->writeElement('CATEGORYTEXT', $this->getCategoryName($record, $catParams));
//         $this->xmlWriter->writeElement('PRODUCTNO', 
//                 Shop_Tools::getProductCode($record->{Shop_Model_Product::COLUMN_CODE}, $comb)
//                 );
//
//         $link = new Url_Link_Module(true);
//         $link->setModuleRoutes(new ShopProductGeneral_Routes());
//         $link->category($record->curlkey)->route('detail', array('urlkey'=>$record->{Shop_Model_Product::COLUMN_URLKEY}));
//         $this->xmlWriter->writeElement('URL', (string)$link);
//         $this->addImages($record);
//         $this->addParams($record);
//         $this->addCarriers($record);
//
//         $this->xmlWriter->endElement(); // SHOPITEM
//      }
   }

   public function generate()
   {
      // kontrola souboru
      $file = AppCore::getAppCacheDir() . self::XML_FILE;

      $now = time();
      $filetime = filemtime($file);
      if ((is_file($file) && ($now - $filetime) >= (3600 * 4)) || CUBE_CMS_DEBUG_LEVEL > 1 || true) {
         @unlink($file);
      }

      if (!is_file($file)) {
         if (!$this->carriers) {
            $payments = array();
            $cariersModel = new Shop_Model_Shippings();
            $cariers = $cariersModel
                    ->joinFK(Shop_Model_Shippings::COLUMN_ID_ZONE)
                    ->where(Shop_Model_Shippings::COLUMN_PERSONAL_PICKUP . ' = 0', array())
                    ->records();
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
            }
            $this->carriers = $cariers;
         }

         $taxes = Shop_Model_Tax::getAllRecords();
         foreach ($taxes as $tax) {
            $this->taxes[$tax->getPK()] = $tax->{Shop_Model_Tax::COLUMN_VALUE};
         }

         $productsModel = new Shop_Model_Product();
         $products = $productsModel
                 ->joinFK(Shop_Model_Product::COLUMN_ID_CATEGORY, array(
                     'cid' => Model_Category::COLUMN_ID,
                     'curlkey' => Model_Category::COLUMN_URLKEY,
                     'cparams' => Model_Category::COLUMN_PARAMS,
                     'cname' => Model_Category::COLUMN_NAME
                 ))
                 ->where(Shop_Model_Product::COLUMN_ACTIVE.' = 1', array())
                 ->records();

         $this->xmlWriter->startElement('feed');
         $this->xmlWriter->writeAttribute('xmlns', 'http://www.w3.org/2005/Atom');
         $this->xmlWriter->writeAttributeNs('xmlns', 'g', null, 'http://base.google.com/ns/1.0');
         $this->xmlWriter->writeElement('title', CUBE_CMS_WEB_NAME . ' - Google merchant feed');
         $this->xmlWriter->startElement('link');
         $this->xmlWriter->writeAttribute('rel', 'slef');
         $this->xmlWriter->writeAttribute('href', Url_Link::getMainWebDir());
         $this->xmlWriter->endElement();
         $this->xmlWriter->writeElement('updated', date(DATE_ATOM));

         if (CUBE_CMS_WEB_DESCRIPTION != null) {
            $this->xmlWriter->writeElement('description', CUBE_CMS_WEB_DESCRIPTION);
         }
         foreach ($products as $pr) {
            $params = array();
            if ($pr->cparams != null) {
               $params = unserialize($pr->cparams);
            }

            $this->addItem($pr, $params);
            file_put_contents($file, $this->xmlWriter->flush(true), FILE_APPEND);
         }
//         $this->xmlWriter->endElement(); // chanel
         $this->xmlWriter->endElement(); // rss
         file_put_contents($file, $this->xmlWriter->flush(true), FILE_APPEND);
      }
   }

   /**
    * @retun string
    */
   public function getFilePath()
   {
      return AppCore::getAppCacheDir() . self::XML_FILE;
   }

   public static function getCategories($q = null) {
      $cache = new Cache_File('google_cats', 60*60*24*3); // tři dny
      
      $items = $cache->get();
      
      if(!$items){
         // load json file
         $items = file('http://www.google.com/basepages/producttype/taxonomy-with-ids.'. str_replace('_', '-', Locales::getLangLocale()).'.txt');
         array_shift($items);
         $items = array_map(function($data){
            return str_replace("\n", '', preg_replace('/[0-9]+ - /', '', $data));
         }, $items);
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
      
      return (array)$items;
   }
   
   protected static function catDataToArray($data, $parentName = null, $level = 1)
   {
      $ret = array();
      
//      foreach ($data as $item) {
//         if(isset($item->CATEGORY_FULLNAME)){
//            $name = (string)$item->CATEGORY_FULLNAME;
//            $ret[] = $name;
//         } else {
//            if(isset($item->CATEGORY) && !empty($item->CATEGORY)){
//               $ret = array_merge($ret, self::catDataToArray($item->CATEGORY, null, $level + 1));
//            }
//         }
//      }
      return $ret;
   }
   
}
