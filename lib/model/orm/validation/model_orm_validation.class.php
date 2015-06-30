<?php

/**
 * Třída s metodami validace sloupce v db
 *
 * @author cuba
 */
class Model_ORM_Validation {

   static public function isEmail($string)
   {
      return preg_match('/^[a-z0-9!#$%&\'*+\/=?^`{}|~_-]+[.a-z0-9!#$%&\'*+\/=?^`{}|~_-]*@[a-z0-9]+[._a-z0-9-]*\.[a-z0-9]+$/ui', $string);
   }

   static public function isMd5($string)
   {
      return preg_match('/^[a-z0-9]{32}$/ui', $string);
   }

   static public function isSha1($string)
   {
      return preg_match('/^[a-z0-9]{40}$/ui', $string);
   }
   static public function isFloat($float)
   {
      return strval(floatval($float)) == strval($float);
   }

   static public function isUnsignedFloat($float)
   {
      return strval(floatval($float)) == strval($float) AND $float >= 0;
   }

   /**
    * Check for name validity
    *
    * @param string $name Name to validate
    * @return boolean Validity is ok or not
    */
   static public function isName($name)
   {
      return preg_match('/^[^0-9!<>,;?=+()@#"°{}_$%:]*$/ui', stripslashes($name));
   }

   /**
    * Check for a link (url-rewriting only) validity
    *
    * @param string $link Link to validate
    * @return boolean Validity is ok or not
    */
   static public function isLinkRewrite($link)
   {
      return empty($link) OR preg_match('/^[_a-z0-9-]+$/ui', $link);
   }
   
   /**
    * Check for standard name validity
    *
    * @param string $name Name to validate
    * @return boolean Validity is ok or not
    */
   static public function isGenericName($name)
   {
      return empty($name) OR preg_match('/^[^<>;=#{}]*$/ui', $name);
   }

   /**
    * Check for HTML field validity (no XSS please !)
    *
    * @param string $html HTML field to validate
    * @return boolean Validity is ok or not
    */
   static public function isCleanHtml($html)
   {
      $jsEvent = 'onmousedown|onmousemove|onmmouseup|onmouseover|onmouseout|onload|onunload|onfocus|onblur|onchange|onsubmit|ondblclick|onclick|onkeydown|onkeyup|onkeypress|onmouseenter|onmouseleave';
      return (!preg_match('/<[ \t\n]*script/ui', $html) && !preg_match('/<.*(' . $jsEvent . ')[ \t\n]*=/ui', $html) && !preg_match('/.*script\:/ui', $html));
   }

   /**
    * Check for date validity
    *
    * @param string $date Date to validate
    * @return boolean Validity is ok or not
    */
   static public function isDate($date)
   {
      if (!preg_match('/^([0-9]{4})-((0?[1-9])|(1[0-2]))-((0?[1-9])|([1-2][0-9])|(3[01]))( [0-9]{2}:[0-9]{2}:[0-9]{2})?$/ui', $date, $matches))
         return false;
      return checkdate(intval($matches[2]), intval($matches[5]), intval($matches[0]));
   }

   /**
    * Check for boolean validity
    *
    * @param boolean $bool Boolean to validate
    * @return boolean Validity is ok or not
    */
   static public function isBool($bool)
   {
      return is_null($bool) OR is_bool($bool) OR preg_match('/^[0|1]{1}$/ui', $bool);
   }

   /**
    * Check for an integer validity
    *
    * @param integer $id Integer to validate
    * @return boolean Validity is ok or not
    */
   static public function isInt($value)
   {
      return ((string) (int) $value === (string) $value OR $value === false);
   }

   /**
    * Check for an integer validity (unsigned)
    *
    * @param integer $id Integer to validate
    * @return boolean Validity is ok or not
    */
   static public function isUnsignedInt($value)
   {
      return (self::isInt($value) AND $value < 4294967296 AND $value >= 0);
   }

   /**
    * Check for an integer validity (unsigned)
    * Mostly used in database for auto-increment
    *
    * @param integer $id Integer to validate
    * @return boolean Validity is ok or not
    */
   static public function isUnsignedId($id)
   {
      return self::isUnsignedInt($id); /* Because an id could be equal to zero when there is no association */
   }

   static public function isNullOrUnsignedId($id)
   {
      return is_null($id) OR self::isUnsignedId($id);
   }

   /**
    * Check object validity
    *
    * @param integer $object Object to validate
    * @return boolean Validity is ok or not
    */
   static public function isLoadedObject($object)
   {
      return is_object($object) AND $object->id;
   }

   /**
    * Check object validity
    *
    * @param integer $object Object to validate
    * @return boolean Validity is ok or not
    */
   static public function isColor($color)
   {
      return preg_match('/^(#[0-9A-Fa-f]{6}|[[:alnum:]]*)$/ui', $color);
   }

   /**
    * Check object validity
    *
    * @param integer $object Object to validate
    * @return boolean Validity is ok or not
    */
   static public function isUrl($url)
   {
      return preg_match('/^([[:alnum:]]|[:#%&_=\(\)\.\? \+\-@\/])+$/ui', $url);
   }

   /**
    * Check object validity
    *
    * @param integer $object Object to validate
    * @return boolean Validity is ok or not
    */
   static public function isAbsoluteUrl($url)
   {
      if (!empty($url))
         return preg_match('/^https?:\/\/([[:alnum:]]|[:#%&_=\(\)\.\? \+\-@\/])+$/ui', $url);
      return true;
   }

   /**
    * Check for standard name file validity
    *
    * @param string $name Name to validate
    * @return boolean Validity is ok or not
    */
   static public function isFileName($name)
   {
      return preg_match('/^[a-z0-9_.-]*$/ui', $name);
   }

   static public function isProtocol($protocol)
   {
      return preg_match('/^http(s?):\/\/$/ui', $protocol);
   }
}
