<?php
require_once './lib/db/insert.class.php';

/**
 * Třída pro vkládání záznamů do MySQL DB.
 * Třída obsahuje implementaci metody insert z db.interfacu.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: insert.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro vkládaání záznamů do MySQL DB
 */

class Mysqli_Db_Insert extends Mysqli_Db_Query implements Db_Insert {
    /**
     * Konstanty pro ukládání do pole SQL dotazu
     * @var string
     */
   const COLUMS_ARRAY 					= 'COLUMS';
   const SQL_TABLE						= 'TABLE';
   const VALUES_ARRAY					= 'VALUES';

    /**
     * Konstanta určující obsah SQL dotazu
     * Musí mít správné pořadí, jak se má SQL dotaz řadit!!!
     *
     */
   protected static $_sqlPartsInit = array(self::INDEX_TABLE   => null,
      self::COLUMS_ARRAY	=> array(),
      self::VALUES_ARRAY	=> array());

   /**
    * Pole s zadávanými částmi SQL dotazu
    * @var array
    */
   protected $_sqlQueryParts = array();

   /**
    * Proměná s počtem zadaných sloupců/hodnot
    * @var integer
    */
   protected $_numberOfColums = null;

   /**
    * Inicializace
    */
   protected function init() {
      $this->_sqlQueryParts = self::$_sqlPartsInit;
   }

   /**
    * Metoda nastavuje která tabulka se bude používat
    *
    * @param string -- tabulka pro použití
    * @param boolean -- (option) jestli se májí tabulky zamknout
    * @return Db_Insert
    */
   public function table($table, $lockTable = false){
      return parent::setTable($table, null, $lockTable);
   }

   /**
    * Metody vatváří sloupce, které se budou zapisovat
    *
    * @param mixed -- sloupce (neomezený počet parametrů) (string nebo array)
    * @return Db_Insert -- objekt Db_Insert
    */
   public function colums($colums) {
      $countColums = func_num_args();
      if(!is_array($colums)){
         if($this->_numberOfColums != null AND $countColums != $this->_numberOfColums){
            throw new InvalidArgumentException(_('Nesprávný počet argumentů'),1);
         }
         $this->_numberOfColums = $countColums;
         //Doplnění pole se sloupcy do pole sloupců
         $this->_sqlQueryParts[self::COLUMS_ARRAY] = func_get_args();
      } else if(is_array($colums) AND $countColums == 1) {
         if($this->_numberOfColums != null AND count($colums) != $this->_numberOfColums){
            throw new InvalidArgumentException(_('Nesprávný počet argumentů'),1);
         }
         $this->_numberOfColums = count($colums);
         $this->_sqlQueryParts[self::COLUMS_ARRAY] = $colums;
      } else {
         throw new InvalidArgumentException(_('Špatně zadané argumenty'),2);
      }
      return $this;
   }

   /**
    * Metoda přiřadí hodnoty sloupcům
    *
    * @param string -- hodnota sloupce (proměnný počet parametrů)
    * @return Db_Insert -- objekt Db_Insert
    */
   public function values($value) {
      $countColums = func_num_args();
      if(!is_array($value)){
         if($this->_numberOfColums != null AND $countColums != $this->_numberOfColums){
            throw new InvalidArgumentException(_('Nesprávný počet argumentů'),3);
         }
         $this->_numberOfColums = $countColums;
         //Doplnění pole se sloupcy do pole sloupců
         $this->_sqlQueryParts[self::VALUES_ARRAY][] = func_get_args();
      } else if(is_array($value) AND $countColums == 1){
         if($this->_numberOfColums != null AND count($value) != $this->_numberOfColums){
            throw new InvalidArgumentException(_('Nesprávný počet argumentů'),3);
         }
         $this->_numberOfColums = count($value);
         $this->_sqlQueryParts[self::VALUES_ARRAY][] = $value;
      } else {
         throw new InvalidArgumentException(_('Špatně zadané argumenty'),4);
      }
      return $this;
   }

   /**
    * Metody vytvoří část SQL dotazu se sloupcy, do kterých se má zapisovat
    * @return string -- část SQL dotazu se sloupci
    */
   private function _createColums() {
      $columsString = self::SQL_SEPARATOR.self::SQL_PARENTHESIS_L;
      $colum = null;
      foreach ($this->_sqlQueryParts[self::COLUMS_ARRAY] as $colum) {
         $columsString.=self::SQL_SEPARATOR."`".$colum."`".self::SQL_SEPARATOR.self::SQL_VALUE_SEPARATOR;
      }
      //		odstranění poslední čárky
      $columsString = substr($columsString, 0, strlen($columsString)-1);
      $columsString.=self::SQL_PARENTHESIS_R;
      return $columsString;
   }

//   /**
//    * Metoda vytváří část dotazu sek FROM
//    * @return string -- část SQL dotazu s částí FROM
//    */
//   private function _createTable() {
//      $intoString = null;
//      $intoString = self::SQL_SEPARATOR.self::SQL_INTO.self::SQL_SEPARATOR."`".MySQLiDb::$_tablePrefix.$this->_sqlQueryParts[self::SQL_TABLE]."`";
//
//      return $intoString;
//   }

   /**
    * Metoda vygeneruje část SQL příkazu s hodnotami sloupců
    * @return string -- řetězec s hodnotami
    */
   private function _createValues() {
      $valuesString = null;
      $valuesString = self::SQL_SEPARATOR.self::SQL_VALUES;

      foreach ($this->_sqlQueryParts[self::VALUES_ARRAY] as $values){
         $valuesString.=self::SQL_SEPARATOR.self::SQL_PARENTHESIS_L;
         foreach ($values as $value){
            if($this->isMySQLFunction($value) OR is_int($value)){
               $valuesString.=self::SQL_SEPARATOR.$value.self::SQL_VALUE_SEPARATOR;
            } else {
               if($value != null){
                  $valuesString.=self::SQL_SEPARATOR."'".$this->getDbConnector()->escapeString($value)."'".self::SQL_VALUE_SEPARATOR;
               } else {
                  $valuesString.=self::SQL_SEPARATOR.parent::SQL_NULL.self::SQL_VALUE_SEPARATOR;
               }
            }
         }
         //		odstranění poslední čárky
         $valuesString = substr($valuesString, 0, strlen($valuesString)-1);
         $valuesString.=self::SQL_PARENTHESIS_R.self::SQL_VALUE_SEPARATOR;
      }
      //		odstranění poslední čárky
      $valuesString = substr($valuesString, 0, strlen($valuesString)-1);
      return $valuesString;
   }

   /**
     * Metoda převede objekt na řetězec
     *
     * @return string -- objekt jako řetězec
     */
   public function __toString(){
      $sql = parent::SQL_INSERT.parent::SQL_SEPARATOR.parent::SQL_INTO.parent::SQL_SEPARATOR;

      // tabulka
      $sql .= $this->_createTable(false);

      //sloupce
      $sql .= $this->_createColums();

      //hodnoty
      $sql .= $this->_createValues();
      return $sql;
   }
}
?>