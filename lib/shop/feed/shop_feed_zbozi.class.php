<?php
class Shop_Feed_Zbozi extends Shop_Feed_Heureka implements Shop_Feed_Interface {
    
   protected static $xmlFile = 'zbozi.xml';


   protected function writeVat($record)
   {
   }
   
   protected function addParams(Shop_Model_Product_Record $record)
   {
      $params = $record->getParams();
      if($params){
         foreach ($params as $param) {
            $this->xmlWriter->startElement('PARAM');
            $this->xmlWriter->writeElement('PARAM_NAME', $param->{Shop_Model_Product_Params::COLUMN_NAME});
            $this->xmlWriter->writeElement('VAL', $param->{Shop_Model_Product_ParamsValues::COLUMN_VALUE});
            $this->xmlWriter->endElement();
         }
      }
   }
   
   protected function addImages(Shop_Model_Product_Record $record)
   {
      $images = $record->getImages();
      
      if($images){
         foreach ($images as $img) {
            /* @var $img Shop_Model_Product_Images_Record */
            if($img->{Shop_Model_Product_Images::COLUMN_IS_TITLE}){
               $this->xmlWriter->writeElement('IMGURL', $img->getUrl());
            } 
         }
      }
      
   }
   
   protected function getCategoryName(Shop_Model_Product_Record $record, $catParams)
   {
      $catName = null;
      if(isset($this->categories[$record->cid])){
         return $this->categories[$record->cid];
      }
      
      if(isset($catParams['zbozi_cat']) && $catParams['zbozi_cat'] != null){
         $catName = $catParams['zbozi_cat'];
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
   
   protected function addCarriers(Shop_Model_Product_Record $record)
   {
      $this->xmlWriter->writeElement('DELIVERY_DATE', $this->maxCarrierSpeed);
   }
   
   public static function getCategories($q = null) {
      $cache = new Cache_File('zboti_cats', 60*60*24*3); // tři dny
      
      $items = $cache->get();
      
      if(!$items){
         // load json file
         $cnt = file_get_contents('https://www.zbozi.cz/static/categories.json');
         $data = json_decode($cnt);
         $items = self::catDataToArray($data);
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
         $name = $parentName ? ($parentName.' | '.$item->name) : $item->name;
         $ret[] = $name;
         
         if(isset($item->children) && !empty($item->children)){
            $ret = array_merge($ret, self::catDataToArray($item->children, $name, $level + 1));
         }
      }
      return $ret;
   }
}