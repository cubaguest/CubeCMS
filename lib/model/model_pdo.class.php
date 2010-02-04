<?php
/**
 * Abstraktní třída pro Db Model typu PDO.
 * Tříta pro vytvoření modelu, přistupujícího k databázi. Umožňuje základní práce
 * s databází.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: dbmodel.class.php 615 2009-06-09 13:05:12Z jakub $ VVE3.9.2 $Revision: 615 $
 * @author			$Author: jakub $ $Date: 2009-06-09 15:05:12 +0200 (Út, 09 čen 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-06-09 15:05:12 +0200 (Út, 09 čen 2009) $
 * @abstract 		Abstraktní třída pro vytvoření modelu pro práci s databází
 */

class Model_PDO extends Model {
   /**
    * Pole s hodnotami pro převod z jazykového pole na řetězec
    * @var array
    */
   private $insUpdtValues = array();

   /**
    * Metoda nastaví pole modelu pro vytvoření řetězců pro insert update
    * @param array $columns -- pole s názvy sloupců a hodotami
    * @param char $separator -- oddělovač mezi názvy sloupců pokud jsou v poli (option default: '_')
    */
   protected function setIUValues($columns, $separator = '_', $clPrefix = null) {
      foreach ($columns as $clKey => $clVal) {
         if(!is_null($clPrefix)) {
            $prefix = $clPrefix.$separator;
         } else {
            $prefix = null;
         }
         if(is_array($clVal)) {
            $this->setIUValues($clVal, $separator, $prefix.$clKey);
         } else {
            $this->insUpdtValues[$prefix.$clKey] = $clVal;
         }
      }
   }

   /**
    * Metoda vrací řetězec s názvy sloupců pro vložení do insertu
    * @return string
    */
   public function getInsertLabels($separator = '_') {
      $returnStr = "(";
      foreach (array_keys($this->insUpdtValues) as $variable) {
         //         $returnStr .= '´'.$variable.'´, ';
         $returnStr .= $variable.', ';
      };
      return substr($returnStr, 0, strlen($returnStr)-2).")";
   }

   /**
    * Metoda vrací řetězec s názvy sloupců pro vložení do insertu
    * @return string
    */
   public function getInsertValues() {
      $pdo = new Db_PDO();
      $returnStr = "(";
      foreach (array_values($this->insUpdtValues) as $variable) {
         if(is_bool($variable) AND $variable) {
            $returnStr .= '1, ';
         } else if(is_bool($variable) AND !$variable) {
            $returnStr .= '0, ';
         } else if($variable == null OR $variable == '') {
            $returnStr .= "NULL, ";
         } else {
            $returnStr .= $pdo->quote($variable).", ";
         }
      };
      return substr($returnStr, 0, strlen($returnStr)-2).")";
   }

   /**
    * Metoda vrací řetězec pro příkaz update
    * @return string
    */
   public function getUpdateValues() {
      $pdo = new Db_PDO();//`label_cs`= 'Saul Griffith's lofty',
      $returnStr = null;
      //      var_dump($this->insUpdtValues);
      foreach ($this->insUpdtValues as $key => $variable) {
         //         $returnStr .= '`'.$key.'` = '.$pdo->quote($variable).", ";
         if($variable == null) {
            $var = 'NULL';
         } else {
            $var = $pdo->quote($variable);
         }
         $returnStr .= $key.' = '.$var.", ";
      };
      return substr($returnStr, 0, strlen($returnStr)-2);
   }

   protected function generateUrlKeys($urlKeys, $table, $alternative, $columnName = 'urlkey',
           $columnId = 'id', $id = null) {
      // načteme všechny klíče z tabulky
      $dbc = new Db_PDO();
      if(is_array($urlKeys)) {
         foreach ($urlKeys as $lang => $key) {
            if($key == null AND $alternative[$lang] == null) continue;
            if($key == null) $key = $alternative[$lang];
            $urlKeys[$lang] = $this->generateUrlKeys($key, $table, $alternative[$lang],
                    $columnName.'_'.$lang, $columnId, $id);
         }
      } else {
         $newKey = $urlKeys;
         if($urlKeys == null AND $alternative == null) return null;
         if($urlKeys == null) $newKey = $alternative;
         $whereId = null;
         if($id !== null){
            $whereId = ' AND `'.$columnId.'` != '.$dbc->quote($id, PDO::PARAM_INT);
         }

         $dbst = $dbc->prepare('SELECT * FROM '.Db_PDO::table($table).
                 ' WHERE `'.$columnName.'` = :newurlkey'
                 .$whereId);
         $dbst->bindParam(':newurlkey', $newKey, PDO::PARAM_STR);
         $dbst->execute();
         $fetch = $dbst->fetch();

         $step = 1;
         while ($fetch != false) {
            $newKey = $this->createNewKey($newKey);
            $dbst->execute();
            $fetch = $dbst->fetch();
            if($step == 1000) break; // jistota ukončení
            $step++;
         }
         $urlKeys = $newKey;
         if($step ==  1000) trigger_error(_('Chyba při vytváření URL klíče'));
      }
      $urlKeys = vve_cr_url_key($urlKeys);
      return $urlKeys;
   }

   private function createNewKey($key) {
      if(preg_match('/.*_([0-9]+)$/', $key)) {
         // již je číslo na konci
         $key = preg_replace('/(.*_)([0-9]+)$/e', "'\\1'.('\\2'+1)", $key);
      } else {
         // přidáme číslo
         $key .= '_1';
      }
      return $key;
   }

   /**
    * Metoda vytvoří pole s hodnotami a klíči vhodnými pro uložení do db
    * @param string $name -- název pro sloupec
    * @param mixed $val -- hodnota sloupce
    * @param string $separator -- oddělovač hodnot (option default: '_')
    * @todo odstranit
    */
   protected function createValuesArray($name, $val, $separator = '_') {
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
    * Metoda vytvoří sloupec s výchozí jazykem, pokud v daném jazyku existuje záznam,
    * jinak použije výchozí jazyk aplikace
    *
    * @param array $array -- pole se sloupci
    * @param array $columns -- pole s názvy sloupců, které jsou jazykové (index
    * označuje název nově vytvořeného sloupce)
    * @param boolean $createArray -- true pokud se má vytvořit jazykové pole se všemi jazyky
    * @param char $separator -- oddělovač položek jazyka a názvu sloupce
    */
   protected function prepareLangs(&$array, $columns, $createArray = false, $separator = '_') {
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
            $this->prepareLangs($arr, $columns, $createArray, $separator);
         }
      }
      return $array;
   }
}
?>