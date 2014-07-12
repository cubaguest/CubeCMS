<?php

/**
 * Třída pro usnadnění práce s poli
 *
 * @copyright  	Copyright (c) 2008-2014 Jakub Matas
 * @version    	$Id: $ CubeCMS 8.0.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 */
class Utils_Array {

   /**
    * Funkce vloží požadovanou hodnotu do pole na danou pozici
    * @param array $array -- pole kde se má prvek vložit
    * @param int $pos -- pozice na kterou se má prvek vložit (začíná se od 0)
    * @param mixed $val -- hodnota pro vložení
    * @param string $valkey -- (option) pokud není zadáno použije se první volný index
    *
    * @return array -- upravené pole, v případě chyby false
    */
   public static function insert($array, $pos, $val, $valkey = null)
   {
      $array2 = array_splice($array, $pos);
      if ($valkey == null) {
         $array[] = $val;
      } else {
         $array[$valkey] = $val;
      }
      $array = array_merge($array, $array2);

      return $array;
   }

   /**
    * Funkce vloží požadovanou hodnotu do pole za dany klíč
    * @param array $array -- pole kde se má prvek vložit
    * @param string $pos -- pozice na kterou se má prvek vložit (klíč)
    * @param mixed $val -- hodnota pro vložení
    * @param string $valkey -- (option) pokud není zadáno použije se první volný index
    * @param string $pos -- (option) (after|before) jestli se má prvek vložit před daný klíč nebo zaněj
    *
    * @return array -- upravené pole, v případě chyby false
    */
   public static function insertByKey($array, $key, $val, $valkey = null, $sort = 'after')
   {
      $pos = 0;
      foreach ($array as $lkey => $lval) {
         if ($lkey == $key)
            break;
         $pos++;
      }
      if ($sort == 'after') {
         $array = vve_array_insert($array, $pos + 1, $val, $valkey);
      } else {
         $array = vve_array_insert($array, $pos, $val, $valkey);
      }
      return $array;
   }

   /**
    * Meotda převede položky pole na obbjekt javascriptu včet funkcí
    * @param array $array
    * @return string
    */
   public static function arrayToJsObject($array)
   {
      $value_arr = array();
      $replace_keys = array();
      foreach ($array as $key => &$value) {
         // Look for values starting with 'function('
         if (strpos($value, 'function(') === 0) {
            // Store function string.
            $value_arr[] = $value;
            // Replace function string in $foo with a 'unique' special key.
            $value = '%' . $key . '%';
            // Later on, we'll look for the value, and replace it.
            $replace_keys[] = '"' . $value . '"';
         }
      }
      $json = json_encode($params);
      return str_replace($replace_keys, $value_arr, $json);
   }

}
