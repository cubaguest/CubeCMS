<?php
//require_once '.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_MODELS_DIR . DIRECTORY_SEPARATOR . 'model.class.php';

/**
 * Abstraktní třída pro Db Model.
 * Tříta pro vytvoření modelu, přistupujícího k databázi. Umožňuje základní práce
 * s databází.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Abstraktní třída pro vytvoření modelu pro práci s databází
 */

class Model_Db extends Model {
   /**
    * Metoda vrací objekt ke konektoru databáze
    *
    * @return DbInterface
    */
   final public function getDb() {
      return AppCore::getDbConnector();
   }

   /**
    * Metoda vytvoří pole s hodnotami a klíči vhodnými pro uložení do db
    * @param string $name -- název pro sloupec
    * @param mixed $val -- hodnota sloupce
    * @param string $separator -- oddělovač hodnot (option default: '_')
    */
   protected function createValuesArray($name, $val, $separator = '_') {
      ;
      $returnArray = array();
      if(func_num_args()%2 != 0) {
         throw new InvalidArgumentException(_('Nebyl předán potřebný počet argumentů'),1);
      }
      $argv = func_get_args();
      //         Skáčeme po dvou, protože první je sloupec a adruhý je hodnota
      for ($step = 0 ; $step < func_num_args() ; $step=$step+2) {
         if(is_array($argv[$step+1])) {
            $returnArray = array_merge($returnArray, $this->createOneArray(
                $argv[$step+1],$argv[$step]));
         } else {
            $returnArray[$argv[$step]] = $argv[$step+1];
         }
      }
      return $returnArray;
   }

   /**
    * Metoda vytvoří z pole jednorozměrné pole oddělené separátorem
    * @param array $arr -- pole hodnot
    * @param string $prefix -- prefix klíčů
    * @param string $separator -- oddělovač klíčů
    */
   private function createOneArray($arr, $prefix = null, $separator = '_') {
      $values = array();
      if($prefix != null) {
         if($prefix[strlen($prefix)-1] != $separator) {
            $prefixKey = $prefix.$separator;
         } else {
            $prefixKey = $prefix;
         }
      } else {
         $prefixKey = null;
      }
      foreach ($arr as $key => $val) {
         if(is_array($val)) {
            $values = array_merge($values, $this->createOneArray($val,$prefixKey.$key, $separator));
         } else {
            $values[$prefixKey.$key] = $val;
         }
      }
      return $values;
   }

   /**
    * Metoda rozparsuje pole databáze na strukturované pole
    * @param array &$values -- pole s hodnotami
    * @param array $values -- pole s prefixy, které se mají parsovat
    * @deprecated -- lepší je použití funkce setLangColumn, která umí vytvážet oba druhy sloupců s jazyky
    */
   protected function parseDbValuesToArray(&$values, $prefixArray, $separator = '_') {
      if(!is_array($prefixArray)) {
         $prefixArray = array($prefixArray);
      }
      foreach ($values as $key => $var) {
         if(!is_int($key)) {
            $matches = array();
            if(eregi('^([a-zA-Z0-9_]*)'.$separator.'([a-zA-Z0-9]*)$', $key, $matches)
                AND in_array($matches[1], $prefixArray)) {
               if(!isset ($values[$matches[1]])) {
                  $values[$matches[1]] = array();
               }
               $values[$matches[1]][$matches[2]] = $var;
               unset ($values[$key]);
            }
         } else {
            $this->parseDbValuesToArray($values[$key], $prefixArray, $separator);
         }
      }
      return $values;
   }

   /**
    * Metoda vytvoří sloupec s výchozí jazykem, pokud v daném jazyku existuje záznam,
    * jinak použije výchozí jazyk aplikace
    *
    * @param array $array -- pole se sloupci
    * @param array $columns -- pole s názvy sloupců, které jsou jazykové (index
    * označuje název nově vytvořeného sloupce)
    * @param boolean $createArray -- true pokud se má vytvořit jazykové pole se všemi jazyky
    * @param char $separator -- oddělovač položek jazyka a názvu sloupce
    */
   protected function setLangColumn(&$array, $columns, $createArray = false, $separator = '_') {
      if(!is_array($columns)) {
         $columns = array($columns);
      }
      reset($array);
      // pokud jsou přímo sloupce a hodnoty
      if(!is_int(key($array))) {
         if($createArray) {
            foreach ($array as $key => $var) {
               $matches = array();
               if(eregi('^([a-zA-Z0-9_]*)'.$separator.'([a-zA-Z0-9]*)$', $key, $matches)
                   AND in_array($matches[1], $columns)) {
                  if(!isset ($array[$matches[1]])) {
                     $array[$matches[1]] = array();
                  }
//                  if(!is_int($columnKey)) {
                     $array[$matches[1]][$matches[2]] = $var;
//                  }
                  unset ($array[$key]);
               }
            }
         } else {
            foreach ($columns as $columnKey => &$column) {
               if($array[$column.$separator.Locale::getLang()] != '') {
                  $content = $array[$column.$separator.Locale::getLang()];
               }
               // použití výchozího jazyka
               else {
                  $content = $array[$column.$separator.Locale::getDefaultLang()];
               }
               // určení indexu sloupce
               if(!is_int($columnKey)) {
                  $array[$columnKey] = $content;
               } else {
                  $array[$column] = $content;
               }
            }
         }
      }
      // pokud je vloženo pole záznamů (několik záznamů)
      else {
         foreach ($array as &$arr) {
            $this->setLangColumn($arr, $columns, $createArray, $separator);
         }
      }
      return $array;
   }
}
?>