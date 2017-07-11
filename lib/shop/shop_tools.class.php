<?php

/**
 * Podpůrné metody pro eshop
 */
class Shop_Tools extends TrObject {

   const DIR_IMAGES_PRODUCTS = 'products';

   public static function getPriceOfProduct(Model_ORM_Record $product, $priceCombination = 0, $formated = false)
   {
      $price = $product->{Shop_Model_Product::COLUMN_PRICE} + $priceCombination;
      $tax = 0;
      // produkt je objekt z db
      if (isset($product->{Shop_Model_Tax::COLUMN_VALUE})) {
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
      $price = round($price * ( 1 + $tax / 100 ), VVE_SHOP_PRICE_ROUND_DECIMAL);
      return $withCurrency ? $price . self::getCurrency() : $price;
   }

   public static function getFormatedPrice($price, $tax = 0, $withCurrency = true)
   {
      // compute tax
      return number_format(self::getPrice($price, $tax, false), VVE_SHOP_PRICE_DECIMALS, ",", " ") . ($withCurrency ? ' '.self::getCurrency() : null );
   }

   public static function getCurrency($id = 0)
   {
      return CUBE_CMS_SHOP_CURRENCY_NAME;
   }
   
   public static function getCurrencyCode($id = 0)
   {
      return CUBE_CMS_SHOP_CURRENCY_CODE;
   }

   public static function getProductCode($code, $groupIdsNames = false, $attrCodes = false)
   {
      if(is_object($groupIdsNames) && property_exists($groupIdsNames, 'comb_attr_codes_json') && property_exists( $groupIdsNames, 'comb_codes_json')){
         $attrCodes = $groupIdsNames->comb_attr_codes_json;
         $groupIdsNames = $groupIdsNames->comb_codes_json;
      }
      if(is_object($groupIdsNames) && $groupIdsNames instanceof Model_ORM_Record){
         $attrCodes = $groupIdsNames->comb_attr_codes_json;
         $groupIdsNames = $groupIdsNames->comb_codes_json;
      }
      
      if (is_string($groupIdsNames)) {
         $groupIdsNames = json_decode($groupIdsNames);
      }
      if (is_object($groupIdsNames)) {
         $groupIdsNames = get_object_vars($groupIdsNames);
      }
      if (is_array($groupIdsNames)) {
         $replace = array_keys($groupIdsNames);
         foreach ($replace as &$r) {
            $r = '{' . $r . '}';
         }
         $code = str_replace($replace, $groupIdsNames, $code);
      }

      if (is_string($attrCodes)) {
         $attrCodes = json_decode($attrCodes);
      }
      if (is_object($attrCodes)) {
         $attrCodes = get_object_vars($attrCodes);
      }
      if (is_array($attrCodes)) {
         $replace = array_keys($attrCodes);
         foreach ($replace as &$r) {
            $r = '{' . $r . '}';
         }
         $code = str_replace($replace, $attrCodes, $code);
      }

//      if($attrCodes && is_string($attrCodes)){
//         $attrGroups = explode(';', $attrCodes);
//         foreach ($attrGroups as $attrCodeStr) {
//            $attrCode = explode(':', $attrCodeStr);
//            $code = str_replace('{'.$attrCode[0].'}', $attrCode[1], $code);
//         }
//      }

      return $code;
   }

   public static function getProductImagesDir($url = false)
   {
      if ($url) {
         return Url_Request::getBaseWebDir() . VVE_DATA_DIR . '/shop/' . self::DIR_IMAGES_PRODUCTS . '/';
      } else {
         return AppCore::getAppDataDir() . 'shop' . DIRECTORY_SEPARATOR
                 . self::DIR_IMAGES_PRODUCTS . DIRECTORY_SEPARATOR;
      }
   }

   public static function getPaymentOrShippingPrice($price, $cartPrice = 0)
   {
      if (strpos($price, '%') !== false && (int) $price != 0) {
         $price = $cartPrice * ((int) $price / 100);
      }
      return (int) $price;
   }

   /**
    * Formátuje číslo objednávky
    * @param Model_ORM_Record $order
    * @return type
    */
   public static function getFormatOrderNumber($order)
   {
      $num = $order;
      if ($order instanceof Model_ORM_Record) {
         $num = $order->getPK();
      }
      return str_pad($num, 8, "0", STR_PAD_LEFT);
   }

   /**
    * Formátuje číslo objednávky
    * @param Model_ORM_Record $order
    * @return type
    */
   public static function getMailTplContent($cnt, $order = null, $customer = null, $stateHistory = null, $lang = null, $aditional = array())
   {
      // základní nahrazení
      $cnt = str_replace(array(
          "{STRANKY}",
          "{WEBSITE_NAME}",
          "{ADRESA_OBCHOD}",
          "{STORE_ADDRESS}",
          "{CURRENT_DATE}",
              ), array(
          CUBE_CMS_WEB_NAME,
          CUBE_CMS_WEB_NAME,
          CUBE_CMS_SHOP_STORE_ADDRESS,
          CUBE_CMS_SHOP_STORE_ADDRESS,
          Utils_DateTime::fdate('%x'),
              ), $cnt);

      /* Objednávka */
      if ($order instanceof Model_ORM_Record) {
         $cnt = str_replace(array(
             "{ORDER_NUMBER}",
             "{ORDER_NOTE}",
             "{CISLO}",
             "{ORDER_TOTAL}",
             "{ORDER_DATE}",
             
             "{ADDRESS_DELIVERY}",
             "{ADDRESS_BILLING}",
             
             "{PAYMENT_NAME}",
             "{PAYMENT_PRICE}",
             "{SHIPPING_NAME}",
             "{SHIPPING_PRICE}",
             
                 ), array(
             self::getFormatOrderNumber($order->{Shop_Model_Orders::COLUMN_ID}),
             $order->{Shop_Model_Orders::COLUMN_NOTE},
             self::getFormatOrderNumber($order->{Shop_Model_Orders::COLUMN_ID}),
             self::getFormatedPrice($order->{Shop_Model_Orders::COLUMN_TOTAL}),
             Utils_DateTime::fdate("%x",new DateTime($order->{Shop_Model_Orders::COLUMN_TIME_ADD})),
                     
             self::createAddressDeliveryHtmlBlock($order, $lang),
             self::createAddressBillingHtmlBlock($order, $lang),
                     
             $order->{Shop_Model_Orders::COLUMN_PAYMENT_METHOD},
             Shop_Tools::getFormatedPrice($order->{Shop_Model_Orders::COLUMN_PAYMENT_PRICE}),
             $order->{Shop_Model_Orders::COLUMN_SHIPPING_METHOD},
             Shop_Tools::getFormatedPrice($order->{Shop_Model_Orders::COLUMN_SHIPPING_PRICE}),
                     
                 ), $cnt);
         // zboží má mít tabulku    
         $cnt = preg_replace_callback("/\{ZBOZI\}/", function ($matches) use($order,$lang) {
            return Shop_Tools::createProductsHtmlTable($order,$lang);
         }, $cnt);
         $cnt = preg_replace_callback("/\{PRODUCTS\}/", function ($matches) use($order,$lang) {
            return Shop_Tools::createProductsHtmlTable($order, $lang);
         }, $cnt);
      }

      /* stav objednávky */
      if ($stateHistory instanceof Model_ORM_Record) {
         if(!isset($stateHistory->{Shop_Model_OrdersStates::COLUMN_NAME})){
            $m = new Shop_Model_OrdersHistory();
            $stateHistory = $m
                    ->joinFK(Shop_Model_OrdersHistory::COLUMN_ID_STATE)
                    ->where(Shop_Model_OrdersHistory::COLUMN_ID, $stateHistory->getPK())
                    ->record();
         }
         
         $cnt = str_replace(array(
             "{STATE}",
             "{STAV}",
             "{POZN}",
             "{STATE_NOTE}",
                 ), array(
             (string) $stateHistory->{Shop_Model_OrdersStates::COLUMN_NAME},
             (string) $stateHistory->{Shop_Model_OrdersStates::COLUMN_NAME},
             (string) $stateHistory->{Shop_Model_OrdersHistory::COLUMN_NOTE},
             (string) $stateHistory->{Shop_Model_OrdersHistory::COLUMN_NOTE},
                 ), $cnt);
      }
      
      /* stav objednávky */
      if ($customer instanceof Model_ORM_Record) {
         $cnt = str_replace(array(
             "{STAV}",
                 ), array(
             (string) $customer->{Shop_Model_OrdersHistory::COLUMN_NOTE},
                 ), $cnt);
      }
      $cnt = str_replace( array_keys($aditional), array_values($aditional), $cnt);
      
      return $cnt;
   }

   public static function createAddressBillingHtmlBlock(Model_ORM_Record $order, $lang = null)
   {
      $addressPayment = '';
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY} != null ? $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY}."<br />" : null;

      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_NAME}."<br />";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_STREET}."<br />";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_CITY}.' '.$order->{Shop_Model_Orders::COLUMN_CUSTOMER_POST_CODE}."<br />";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COUNTRY}."<br />";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_EMAIL}."<br />";
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_PHONE}."<br />";

      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_DIC} != null ?
         'DIČ: '.$order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_DIC}."<br />" : null;
      $addressPayment .= $order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_IC} != null ?
         'IČ: '.$order->{Shop_Model_Orders::COLUMN_CUSTOMER_COMPANY_IC}."<br /><br />" : null;

      return $addressPayment;
   }
   
   public static function createAddressDeliveryHtmlBlock(Model_ORM_Record $order)
   {
      $addressShip = "<div>";
      $addressShip .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_NAME}."<br />";
      $addressShip .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_STREET}."<br />";
      $addressShip .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_CITY}.' '.$order->{Shop_Model_Orders::COLUMN_DELIVERY_POST_CODE}."<br />";
      $addressShip .= $order->{Shop_Model_Orders::COLUMN_DELIVERY_COUNTRY}."<br />";
      $addressShip .= '</div>';
      
      return $addressShip;
   }
   
   public static function createProductsHtmlTable(Model_ORM_Record $order, $lang = null)
   {
      $items = $order->getItems();
      $translator = new Translator();
      $str = '<table class="table table-data" style="width:100%;padding: 5px 0px;">';
      $str .= '<tr>'
         .'<th style="text-align: left;">'.$translator->tr('Zboží').'</th>'
         .'<th style="text-align: left;">'.$translator->tr('Množství').'</th>'
         .'<th style="text-align: center;">'.$translator->tr('Cena').'</th>'
      ;
      $str .= '</tr>';
      $prodPrice = 0;
      foreach ($items as $item) {
         // 1 Ks Náhrdelník (zlatý) = 500 Kč
         $itemStr = $item->{Shop_Model_OrderItems::COLUMN_NAME};
         if($item->{Shop_Model_OrderItems::COLUMN_NOTE} != null){
            $itemStr .= '<br />('.$item->{Shop_Model_OrderItems::COLUMN_NOTE}.')';
         }
         $str .= '<tr><th>'.$itemStr.'</th>'
            .'<td>'.$item->{Shop_Model_OrderItems::COLUMN_QTY}.' '.$item->{Shop_Model_OrderItems::COLUMN_UNIT}.'</td>'
            .'<td style="text-align: right;">'.Shop_Tools::getFormatedPrice($item->{Shop_Model_OrderItems::COLUMN_PRICE}).'</td></tr>';
         $prodPrice = $prodPrice + $item->{Shop_Model_OrderItems::COLUMN_PRICE};
      }
      // mezisoučet
      $str .= '<tr><td colspan="3"></td></tr>';
      $str .= '<tr><th colspan="2"><strong>Mezisoučet:</strong></th>'
         .'<td style="text-align: right;"><strong>'.Shop_Tools::getFormatedPrice($prodPrice)."</strong></td></tr>";
      $str .= '<tr><td colspan="3"></td></tr>';
      
      // info k dopravě
      $str .= '<tr>'
              . '<th colspan="2">Doprva: '.$order->{Shop_Model_Orders::COLUMN_SHIPPING_METHOD}.'</th>'
            .'<td style="text-align: right;"><strong>'.Shop_Tools::getFormatedPrice($order->{Shop_Model_Orders::COLUMN_SHIPPING_PRICE})."</strong></td>"
                    . "</tr>";
      $str .= '<tr><td colspan="3"></td></tr>';
      // info k platbě
      $str .= '<tr>'
              . '<th colspan="2">Platba: '.$order->{Shop_Model_Orders::COLUMN_PAYMENT_METHOD}.'</th>'
            .'<td style="text-align: right;"><strong>'.Shop_Tools::getFormatedPrice($order->{Shop_Model_Orders::COLUMN_PAYMENT_PRICE})."</strong></td>"
                    . "</tr>";
      $str .= '<tr><td colspan="3"></td></tr>';
      $str .= '<tr><td colspan="3"></td></tr>';
      
      // kompletní cena
      $str .= '<tr><th colspan="2"><strong>Cena celkem:</strong></th>'.
         '<td style="text-align: right; font-size: 110%;"><strong>'.Shop_Tools::getFormatedPrice(
            $prodPrice + $order->{Shop_Model_Orders::COLUMN_PAYMENT_PRICE}+$order->{Shop_Model_Orders::COLUMN_SHIPPING_PRICE})
            ."</strong></td></tr>";
      $str .= '</table>';
      
      return $str;
   }

}
