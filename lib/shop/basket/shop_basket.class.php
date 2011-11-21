<?php

/**
 * Třída shop_cart
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id:  $ VVE 6.4 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
 */
class Shop_Basket implements Iterator, ArrayAccess {
   private static $items = false;

   private static $personalPickUpOnly = false;

   private static $needPickUpDate = false;

   public function __construct()
   {
   }

   public function loadItems()
   {
      if(self::$items !== false){
         return;
      }
      
      $modelBasket = new Shop_Model_Basket();
      $modelBasket->setSelectAllLangs(false);
      $modelBasket->columns(array(Shop_Model_Basket::COLUMN_ID_PRODUCT, Shop_Model_Basket::COLUMN_QTY, Shop_Model_Basket::COLUMN_ID_USER));
      $modelBasket->join(Shop_Model_Basket::COLUMN_ID_PRODUCT,array('t_pr' => 'Shop_Model_Product'), Shop_Model_Product::COLUMN_ID,
         array(Shop_Model_Product::COLUMN_NAME, Shop_Model_Product::COLUMN_PRICE, Shop_Model_Product::COLUMN_IMAGE, Shop_Model_Product::COLUMN_URLKEY,
            Shop_Model_Product::COLUMN_ID_TAX, Shop_Model_Product::COLUMN_ID_CATEGORY, Shop_Model_Product::COLUMN_UNIT, 
            Shop_Model_Product::COLUMN_UNIT_SIZE, Shop_Model_Product::COLUMN_PERSONAL_PICKUP_ONLY, Shop_Model_Product::COLUMN_PICKUP_DATE));
      // join daní
      $modelBasket->join(array('t_pr' => Shop_Model_Product::COLUMN_ID_TAX), 'Shop_Model_Tax', Shop_Model_Tax::COLUMN_ID, array(Shop_Model_Tax::COLUMN_VALUE));
      // join kategorií
      $modelBasket->join(array('t_pr' => Shop_Model_Product::COLUMN_ID_CATEGORY), 'Model_Category', Model_Category::COLUMN_ID, array('curlkey' => Model_Category::COLUMN_URLKEY));
      if(Auth::isLogin()){
         $modelBasket->where(Shop_Model_Basket::COLUMN_ID_USER.' = :idu OR '.Shop_Model_Basket::COLUMN_ID_SESSION.' = :ids', 
            array('idu' => Auth::getUserId(), 'ids' => session_id()));
      } else {
         $modelBasket->where(Shop_Model_Basket::COLUMN_ID_SESSION, session_id());
      }
      
      $items = $modelBasket->records();
      self::$items = array();
      if($items != false){
         foreach ($items as $item) {
            $url = new Url_Link();
            $itemO = new Shop_Basket_Item($item->{Shop_Model_Product::COLUMN_ID});
            
            $itemO->setImage($item->{Shop_Model_Product::COLUMN_IMAGE})
               ->setName($item->{Shop_Model_Product::COLUMN_NAME})
               ->setQty($item->{Shop_Model_Basket::COLUMN_QTY})
               ->setTax($item->{Shop_Model_Tax::COLUMN_VALUE})
               ->setUnit($item->{Shop_Model_Product::COLUMN_UNIT})
               ->setUnitSize($item->{Shop_Model_Product::COLUMN_UNIT_SIZE})
               ->setPrice($item->{Shop_Model_Product::COLUMN_PRICE})
               ->setImage($item->{Shop_Model_Product::COLUMN_IMAGE});
            $itemO->setUrl($url->clear()->category($item->curlkey.'/'.$item->{Shop_Model_Product::COLUMN_URLKEY}) );   
            self::$items[$item->{Shop_Model_Basket::COLUMN_ID_PRODUCT}] = $itemO;
            
            if($item->{Shop_Model_Product::COLUMN_PERSONAL_PICKUP_ONLY} == true){
               self::$personalPickUpOnly = true;
            }
            
            if($item->{Shop_Model_Product::COLUMN_PICKUP_DATE} == true){
               self::$needPickUpDate = true;
            }
            
            // update pokud je login a položky byly vloženy bez přihlášení
            if(Auth::isLogin() && $item->{Shop_Model_Basket::COLUMN_ID_USER} == 0){
               $item->{Shop_Model_Basket::COLUMN_ID_USER} = Auth::getUserId();
               $modelBasket->save($item);
            }
         }
      }
   }

   /**
    * Metoda vrací pole s položkami v košíku
    * @return array of Shop_Basket_Item
    */
   public function getItems()
   {
      $this->loadItems();
      return self::$items;
   }

   /**
    * Metoda přidá produkt do košíku
    * @param int $idp
    * @param int $qty
    * @param array $attributes -- (option) atributy
    */
   public function addItem($idp, $qty, $attributes = array())
   {
      
      $modelBasket = new Shop_Model_Basket();
      if(Auth::isLogin()){
         $modelBasket->where(Shop_Model_Basket::COLUMN_ID_PRODUCT.' = :idp AND '
            .Shop_Model_Basket::COLUMN_ID_USER.' = :idu' , array('idp' => $idp, 'idu' => Auth::getUserId()));
      } else {
         $modelBasket->where(Shop_Model_Basket::COLUMN_ID_PRODUCT.' = :idp AND '
            .Shop_Model_Basket::COLUMN_ID_SESSION.' = :ids', array('idp' => $idp, 'ids' => session_id()));
      }
         
      $item = $modelBasket->record();
      if($item == false){
         $item = $modelBasket->newRecord();
         $item->{Shop_Model_Basket::COLUMN_ID_PRODUCT} = $idp;
         if(Auth::isLogin()){
            $item->{Shop_Model_Basket::COLUMN_ID_USER} = Auth::getUserId();
         } else {
            $item->{Shop_Model_Basket::COLUMN_ID_SESSION} = session_id();
         }
         $item->{Shop_Model_Basket::COLUMN_QTY} = $qty;
      } else {
         $item->{Shop_Model_Basket::COLUMN_QTY} = $item->{Shop_Model_Basket::COLUMN_QTY}+$qty;
      }
      $modelBasket->save($item);
   }
   
   public function editQty($idProduct, $qty)
   {
      $modelBasket = new Shop_Model_Basket();
      if(Auth::isLogin()){
         $modelBasket->where(Shop_Model_Basket::COLUMN_ID_PRODUCT.' = :idp AND '
            .Shop_Model_Basket::COLUMN_ID_USER.' = :idu' , array('idp' => $idProduct, 'idu' => Auth::getUserId()));
      } else {
         $modelBasket->where(Shop_Model_Basket::COLUMN_ID_PRODUCT.' = :idp AND '
            .Shop_Model_Basket::COLUMN_ID_SESSION.' = :ids', array('idp' => $idProduct, 'ids' => session_id()));
      }
      $item = $modelBasket->record();
      $item->{Shop_Model_Basket::COLUMN_QTY} = $qty;
      $modelBasket->save($item);
   }
   
   public function deleteItem($idProduct)
   {
      $modelBasket = new Shop_Model_Basket();
      if(is_array($idProduct)){
         
      } else {
         if(Auth::isLogin()){
            $modelBasket->where(Shop_Model_Basket::COLUMN_ID_PRODUCT.' = :idp AND '
               .Shop_Model_Basket::COLUMN_ID_USER.' = :idu' , array('idp' => $idProduct, 'idu' => Auth::getUserId()));
         } else {
            $modelBasket->where(Shop_Model_Basket::COLUMN_ID_PRODUCT.' = :idp AND '
               .Shop_Model_Basket::COLUMN_ID_SESSION.' = :ids', array('idp' => $idProduct, 'ids' => session_id()));
         }
         $modelBasket->delete();
      }
   }

   public function getPrice()
   {
      $price = 0;
      foreach ($this as $item) {
         $price += $item->getPrice();
      }
      return $price;
   }
   
   public function clear()
   {
      $modelBasket = new Shop_Model_Basket();
      if(Auth::isLogin()){
         $modelBasket->where(Shop_Model_Basket::COLUMN_ID_USER, Auth::getUserId())->delete();
      } else {
         $modelBasket->where(Shop_Model_Basket::COLUMN_ID_SESSION, session_id())->delete();
      }
   }
   /**
    * Metoda vrací true pokud je košík prázdný
    * @return bool -- true pro prázdný košík
    */
   public function isEmpty()
   {
      if(self::$items === false){//pokud nebyl košík vůbec načten
         $model = new Shop_Model_Basket();
         if(Auth::isLogin()){
            $model->where(Shop_Model_Basket::COLUMN_ID_USER.' = :idu OR '.Shop_Model_Basket::COLUMN_ID_SESSION.' = :ids', 
               array('idu' => Auth::getUserId(), 'ids' => session_id()));
         } else {
            $model->where(Shop_Model_Basket::COLUMN_ID_SESSION, session_id());
         }
         return !(bool)$model->columns(array())->record();
         
      }
      return empty (self::$items);
   }
   
   /**
    * Metoda vrací jestli je zboží nuntné vyzvednout pouze osobně
    * @return bool
    */
   public function personalPickUpOnly()
   {
      return self::$personalPickUpOnly;
   }
   
   /**
    * metoda vrcí jestli zboží potřebuje datum vyzvednutí
    * @return bool
    */
   public function needPickUpDate()
   {
      return self::$needPickUpDate;
   }

   /* Implements ITERATOR */
   function rewind()
   {
      $this->loadItems();
      reset(self::$items);
   }

   function current()
   {
      $this->loadItems();
      return current(self::$items);
   }

   function key()
   {
      $this->loadItems();
      return key(self::$items);
   }

   function next()
   {
      $this->loadItems();
      next(self::$items);
   }

   function valid()
   {
      $this->loadItems();
      return key(self::$items) !== null;
   }
   
   /**
    *  Implements ArrayAccess  REALY NEED THIS?
    * @todo Tohle asi nepoužívat
    */
   public function offsetSet($offset, $value)
   {
      if (is_null($offset)) {
         self::$items[] = $value;
      } else {
         self::$items[$offset] = $value;
      }
   }

   public function offsetExists($offset)
   {
      return isset(self::$items[$offset]);
   }

   public function offsetUnset($offset)
   {
      unset(self::$items[$offset]);
   }

   public function offsetGet($offset)
   {
      return isset(self::$items[$offset]) ? self::$items[$offset] : null;
   }
}
?>
