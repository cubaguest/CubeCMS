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
class Shop_Cart implements Iterator, ArrayAccess {
   private static $items = false;

   private static $personalPickUpOnly = false;

   private static $needPickUpDate = false;

   public function __construct()
   {
      $this->loadItems();
   }

   public function loadItems()
   {
      if(self::$items !== false){
         return;
      }
      
      $modelCart = new Shop_Model_Cart();
      $modelCart->setSelectAllLangs(false);

      $modelCart->columns(array(
         Shop_Model_Cart::COLUMN_ID_PRODUCT, Shop_Model_Cart::COLUMN_QTY,
         Shop_Model_Cart::COLUMN_ID_USER, Shop_Model_Cart::COLUMN_ID_COMBINATION,
         /*
          * COALESCE(SUM(t_var.variant_weight_add), 0) + t_pr.weight AS weight_full,
            COALESCE(SUM(t_var.variant_price_add), 0) + t_pr.price AS price_full,
            GROUP_CONCAT(t_attr_g.atgroup_name_cs, ': ', t_attr.attribute_name_cs SEPARATOR ', ') AS combination_label,
            COALESCE(product_combination_quantity, quantity) AS product_quantity
          * */
//         Shop_Model_Product::COLUMN_WEIGHT => 'COALESCE(SUM(t_var.'.Shop_Model_Product_Variants::COLUMN_WEIGHT_ADD.'), 0) + t_pr.'.Shop_Model_Product::COLUMN_WEIGHT,
//         Shop_Model_Product::COLUMN_PRICE => 'COALESCE(SUM(t_comb.'.Shop_Model_Product_Combinations::COLUMN_PRICE.'), 0) + t_pr.'.Shop_Model_Product::COLUMN_PRICE,
         Shop_Model_Product::COLUMN_WEIGHT => 'COALESCE(t_var.'.Shop_Model_Product_Variants::COLUMN_WEIGHT_ADD.', 0) + t_pr.'.Shop_Model_Product::COLUMN_WEIGHT,
         Shop_Model_Product::COLUMN_PRICE => 'COALESCE(t_comb.'.Shop_Model_Product_Combinations::COLUMN_PRICE.', 0) + t_pr.'.Shop_Model_Product::COLUMN_PRICE,
         Shop_Model_Product::COLUMN_QUANTITY => 'COALESCE(t_comb.'.Shop_Model_Product_Combinations::COLUMN_QTY.', t_pr.'.Shop_Model_Product::COLUMN_QUANTITY.')',
         'combination_label' => 'GROUP_CONCAT(t_attr_g.'.Model_ORM::getLangColumn(Shop_Model_AttributesGroups::COLUMN_NAME)
            .', \': \', t_attr.'.Model_ORM::getLangColumn(Shop_Model_Attributes::COLUMN_NAME).' SEPARATOR \', \')',
         'combination_codes_json' => 'CONCAT(\'{\', GROUP_CONCAT(\'"\', t_attr.'.Shop_Model_Attributes::COLUMN_ID_GROUP.', \'"\', \' : \',
                                 \'"\', t_var.'.Shop_Model_Product_Variants::COLUMN_CODE_ADD.', \'"\' SEPARATOR \', \'), \'}\')',

      ))
      ->join(
         Shop_Model_Cart::COLUMN_ID_PRODUCT,
         array('t_pr' => 'Shop_Model_Product'), Shop_Model_Product::COLUMN_ID,
         array(Shop_Model_Product::COLUMN_NAME,
//            Shop_Model_Product::COLUMN_PRICE, // je použit ve vzorci s kombinacemi
//            Shop_Model_Product::COLUMN_WEIGHT, // je použit ve vzorci s kombinacemi
            Shop_Model_Product::COLUMN_CODE,
            Shop_Model_Product::COLUMN_IMAGE, Shop_Model_Product::COLUMN_URLKEY,
            Shop_Model_Product::COLUMN_ID_TAX, Shop_Model_Product::COLUMN_ID_CATEGORY, Shop_Model_Product::COLUMN_UNIT, 
            Shop_Model_Product::COLUMN_UNIT_SIZE, Shop_Model_Product::COLUMN_PERSONAL_PICKUP_ONLY, Shop_Model_Product::COLUMN_PICKUP_DATE),
         Model_ORM::JOIN
      )
      // join daní
      ->join(
         array('t_pr' => Shop_Model_Product::COLUMN_ID_TAX),
         'Shop_Model_Tax', Shop_Model_Tax::COLUMN_ID,
         array(Shop_Model_Tax::COLUMN_VALUE), false)
      // join kategorií
      ->join(
         array('t_pr' => Shop_Model_Product::COLUMN_ID_CATEGORY),
         'Model_Category', Model_Category::COLUMN_ID,
         array('curlkey' => Model_Category::COLUMN_URLKEY), false)
      // join kombinací
      ->join(
         array($modelCart->getTableShortName() => Shop_Model_Cart::COLUMN_ID_COMBINATION),
         array('t_comb' => 'Shop_Model_Product_Combinations'),
         Shop_Model_Product_Combinations::COLUMN_ID , false
      )
      // propojení kombinace varinaty
      ->join(
         array('t_comb' => Shop_Model_Product_Combinations::COLUMN_ID),
         array('t_comb_var' => 'Shop_Model_Product_CombinationHasVariant'),
         Shop_Model_Product_CombinationHasVariant::COLUMN_ID_COMBINATION  , false
      )
      // napojneí na varinaty
      ->join(
         array('t_comb_var' => Shop_Model_Product_CombinationHasVariant::COLUMN_ID_VARIANT),
         array('t_var' => 'Shop_Model_Product_Variants'),
         Shop_Model_Product_Variants::COLUMN_ID , false
      )
      // napojneí na atributy
      ->join(
         array('t_var' => Shop_Model_Product_Variants::COLUMN_ID_ATTR),
         array('t_attr' => 'Shop_Model_Attributes'),
         Shop_Model_Attributes::COLUMN_ID , false
      )
      // napojneí na skupiny atributů
      ->join(
         array('t_attr' => Shop_Model_Attributes::COLUMN_ID_GROUP),
         array('t_attr_g' => 'Shop_Model_AttributesGroups'),
         Shop_Model_AttributesGroups::COLUMN_ID  , false
      )
      ->groupBy(array(Shop_Model_Cart::COLUMN_ID))
      ;

      if(Auth::isLogin()){
         $modelCart->where(Shop_Model_Cart::COLUMN_ID_USER.' = :idu OR '.Shop_Model_Cart::COLUMN_ID_SESSION.' = :ids',
            array('idu' => Auth::getUserId(), 'ids' => session_id()));
      } else {
         $modelCart->where(Shop_Model_Cart::COLUMN_ID_SESSION, session_id());
      }

      $items = $modelCart->records();

      self::$items = array();
      if($items != false){
         foreach ($items as $item) {
            self::$items[$item->{Shop_Model_Cart::COLUMN_ID}] = new Shop_Cart_Item($item);

            if($item->{Shop_Model_Product::COLUMN_PERSONAL_PICKUP_ONLY} == true){
               self::$personalPickUpOnly = true;
            }
            
            if($item->{Shop_Model_Product::COLUMN_PICKUP_DATE} == true){
               self::$needPickUpDate = true;
            }
            // update pokud je login a položky byly vloženy bez přihlášení
            if(Auth::isLogin() && $item->{Shop_Model_Cart::COLUMN_ID_USER} == 0){
               $item->{Shop_Model_Cart::COLUMN_ID_USER} = Auth::getUserId();
               $modelCart->save($item);
            }
         }
      }
   }

   /**
    * Metoda vrací pole s položkami v košíku
    * @return array of Shop_Cart_Item
    */
   public function getItems()
   {
      $this->loadItems();
      return self::$items;
   }

   /**
    * Metoda vrací celkový počet položk v kočíku
    * @return int
    */
   public function getCartQty()
   {
      $qty = 0;
      $this->loadItems();
      foreach ($this as $item) {
         $qty += $item->getQty();
      }
      return $qty;
   }

   /**
    * Metoda vrací položku košíku
    * @return Shop_Cart_Item
    */
   public function getItem($id)
   {
      $this->loadItems();
      return isset(self::$items[$id]) ? self::$items[$id] : false;
   }

   /**
    * Metoda přidá produkt do košíku
    * @param int $idp
    * @param int $qty
    * @param int $idCombination - id kombinace
    */
   public function addItem($idp, $qty, $idCombination = 0, $variantLabel = null)
   {
      
      $modelCart = new Shop_Model_Cart();
      if(Auth::isLogin()){
         $modelCart->where(
            Shop_Model_Cart::COLUMN_ID_PRODUCT.' = :idp AND '
            .Shop_Model_Cart::COLUMN_ID_COMBINATION.' = :idc AND '
            .Shop_Model_Cart::COLUMN_ID_USER.' = :idu' ,
            array('idp' => $idp, 'idu' => Auth::getUserId(), 'idc' => $idCombination));
      } else {
         $modelCart->where(
            Shop_Model_Cart::COLUMN_ID_PRODUCT.' = :idp AND '
            .Shop_Model_Cart::COLUMN_ID_COMBINATION.' = :idc AND '
            .Shop_Model_Cart::COLUMN_ID_SESSION.' = :ids',
            array('idp' => $idp, 'ids' => session_id(), 'idc' => $idCombination));
      }
         
      $item = $modelCart->record();
      if($item == false){
         $item = $modelCart->newRecord();
         $item->{Shop_Model_Cart::COLUMN_ID_PRODUCT} = $idp;
         if(Auth::isLogin()){
            $item->{Shop_Model_Cart::COLUMN_ID_USER} = Auth::getUserId();
         } else {
            $item->{Shop_Model_Cart::COLUMN_ID_SESSION} = session_id();
         }
         $item->{Shop_Model_Cart::COLUMN_QTY} = $qty;
         $item->{Shop_Model_Cart::COLUMN_ID_COMBINATION} = $idCombination;
         $item->{Shop_Model_Cart::COLUMN_VARIANT_LABEL} = $variantLabel;
      } else {
         $item->{Shop_Model_Cart::COLUMN_QTY} = $item->{Shop_Model_Cart::COLUMN_QTY}+$qty;
      }
      $modelCart->save($item);
   }
   
   public function editQty($idItem, $qty)
   {
      $modelCart = new Shop_Model_Cart();
      if($qty == 0){
         $modelCart->delete($idItem);
         unset(self::$items[$idItem]);
      } else {
         $modelCart
            ->where( Shop_Model_Cart::COLUMN_ID.' = :id', array('id' => $idItem ))
            ->update(array(Shop_Model_Cart::COLUMN_QTY => $qty));
         if(isset(self::$items[$idItem])){
            self::$items[$idItem]->setQty($qty);
         }
      }
   }
   
   public function deleteItem($idItem)
   {
      $modelCart = new Shop_Model_Cart();
      if(is_array($idItem)){
         
      } else {
         $modelCart->delete($idItem);
         unset(self::$items[$idItem]);
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
      $modelCart = new Shop_Model_Cart();
      if(Auth::isLogin()){
         $modelCart->where(Shop_Model_Cart::COLUMN_ID_USER, Auth::getUserId())->delete();
      } else {
         $modelCart->where(Shop_Model_Cart::COLUMN_ID_SESSION, session_id())->delete();
      }
   }
   /**
    * Metoda vrací true pokud je košík prázdný
    * @return bool -- true pro prázdný košík
    */
   public function isEmpty()
   {
      if(self::$items === false){//pokud nebyl košík vůbec načten
         $model = new Shop_Model_Cart();
         if(Auth::isLogin()){
            $model->where(Shop_Model_Cart::COLUMN_ID_USER.' = :idu OR '.Shop_Model_Cart::COLUMN_ID_SESSION.' = :ids', 
               array('idu' => Auth::getUserId(), 'ids' => session_id()));
         } else {
            $model->where(Shop_Model_Cart::COLUMN_ID_SESSION, session_id());
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
