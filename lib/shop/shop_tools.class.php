<?php
/**
 * Podpůrné metody pro eshop
 */
class Shop_Tools
{
   const DIR_IMAGES_PRODUCTS = 'products';

   public static function getPriceOfProduct(Model_ORM_Record $product, $priceCombination = 0, $formated = false)
   {
      $price = $product->{Shop_Model_Product::COLUMN_PRICE} + $priceCombination;
      $tax = 0;
      // produkt je objekt z db
      if(isset($product->{Shop_Model_Tax::COLUMN_VALUE})){
          // compute tax
          $tax = $product->{Shop_Model_Tax::COLUMN_VALUE};
       } else {
          // load tax
       }
      return $formated ? self::getFormatedPrice($price, $tax) : self::getPrice($price, $tax);
   }

   public static function getPrice($price, $tax = 0, $withCurrency = true)
   {
      // compute tax
      $price = round($price*( 1 + $tax/100 ), VVE_SHOP_PRICE_ROUND_DECIMAL);
      return $withCurrency ? $price.self::getCurrency() : $price;
   }

   public static function getFormatedPrice($price, $tax = 0, $withCurrency = true)
   {
      // compute tax
      return number_format(self::getPrice($price, $tax, false), VVE_SHOP_PRICE_DECIMALS , ",", " ") . ($withCurrency ? self::getCurrency() : null );
   }

   public static function getCurrency($id = 0)
   {
      return ' '.VVE_SHOP_CURRENCY_NAME;
   }

   public static function getProductCode($code, $groupIdsNames = false)
   {
      if(is_string($groupIdsNames)){
         $groupIdsNames = json_decode($groupIdsNames);
      }
      if(is_object($groupIdsNames)){
         $groupIdsNames = get_object_vars($groupIdsNames);
      }
      if(is_array($groupIdsNames)){
         $replace = array_keys($groupIdsNames);
         foreach($replace as &$r) {
            $r = '{'.$r.'}';
         }
         $code = str_replace($replace, $groupIdsNames, $code);
      }
      return $code;
   }

   public static function getProductImagesDir($url = false)
   {
      if($url){
         return Url_Request::getBaseWebDir().VVE_DATA_DIR.'/shop/'.self::DIR_IMAGES_PRODUCTS.'/';
      } else {
         return AppCore::getAppDataDir().'shop'.DIRECTORY_SEPARATOR
            .self::DIR_IMAGES_PRODUCTS.DIRECTORY_SEPARATOR;
      }
   }

   public static function getPaymentOrShippingPrice($price, $cartPrice = 0)
   {
      if(strpos($price, '%') !== false && (int)$price != 0){
         $price = $cartPrice*((int)$price/100);
      }
      return (int)$price;
   }

}
