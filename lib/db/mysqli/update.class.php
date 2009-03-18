<?php
require_once './lib/db/update.class.php';

/**
 * Třída pro aktualizaci záznamů v MySQL DB.
 * Třída obsahuje implementaci metody update z db.interface a implementuje metody
 * v rozhraní Db_Update
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.2 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro aktualizaci záznamů
 * @see           http://dev.mysql.com/doc/refman/5.1/en/update.html
 * @package       mysqli
 */

class Mysqli_Db_Update extends Mysqli_Db_Query implements Db_Update{
    /**
     * Konstanta určující obsah SQL dotazu
     * Musí mít správné pořadí, jak se má SQL dotaz řadit!!!
     *
     */
   protected static $_sqlPartsInit = array(
      self::INDEX_TABLE          => null,
      self::INDEX_COLUMS_ARRAY	=> array(),
      self::SQL_WHERE				=> array(),
      self::ORDER_ORDER_KEY		=> array(),
      self::SQL_LIMIT				=> array());

   /**
    * Pole s částmi SQL dotazu ze kterých se bude při výstupu generovat samotná SQL dotaz
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
    * @return Db_Update
    */
   public function table($table, $lockTable = false){
      return parent::setTable($table, null, $lockTable);
   }

   /**
    * Metoda nastavuje, které hodnoty se upraví
    * (název sloupce) => (hodnota)
    *
    * @param array $values -- pole s hodnotami array((název sloupce) => (hodnota))
    *
    * @return Db_Update -- objekt Db_Update
    */
   public function set($values)
   {
      $this->_sqlQueryParts[self::INDEX_COLUMS_ARRAY] = $values;
      return $this;
   }

   /**
    * Metoda přiřadí řazení sloupcu v SQL dotazu
    *
    * @param string -- sloupec, podle kterého se má řadit
    * @param integer -- (option) jak se má sloupec řadit konstanta Db::ORDER_XXX (default: ASC)
    *
    * @return Db_Update -- objekt Db_Update
    */
   public function order($colum, $order = self::SQL_ASC) {
      return parent::order($colum, $order);
   }

   /**
    * Metoda přidá do SQL dotazu klauzuli LIMIT
    * @param integer -- počet záznamů
    * @param integer -- záčátek
    *
    * @return Db_Update -- objekt Db_Update
    */
   public function limit($rowCount, $offset) {
      return parent::limit($rowCount, $offset);
   }

   /**
    * Metody vytvoří část SQL dotazu se sloupcy, které se mají vybírat
    * @return string -- část SQL dotazu se sloupci
    */
   private function _createSet() {
      $columsString = null;
      if(!empty($this->_sqlQueryParts[self::INDEX_COLUMS_ARRAY])){
         $columsString .= self::SQL_SEPARATOR.self::SQL_SET;
         foreach ($this->_sqlQueryParts[self::INDEX_COLUMS_ARRAY] as $colum => $value) {
            if($value != null){
               //	Pokud je zadán SQL příkaz
               if($this->isMySQLFunction($value) ){
                  $columsString.=self::SQL_SEPARATOR."`".$colum."`= ".$value.self::SQL_VALUE_SEPARATOR;
               }
               // Je výpočetní výraz
               else if(ereg('^([a-zA-Z0-9]*)([+-\*/]*)([0-9]*)$', $value)){
                  $columsString.=self::SQL_SEPARATOR."`".$colum."`= ".$value.self::SQL_VALUE_SEPARATOR;
               }
               // Je normální hodnota
               else {
                  if(!is_int($value)){
                     $columsString.=parent::SQL_SEPARATOR."`".$colum."`= '".$this->getDbConnector()
                        ->escapeString($value)."'".parent::SQL_VALUE_SEPARATOR;
                  } else {
                     $columsString.=parent::SQL_SEPARATOR."`".$colum."`= ".$value
                        .parent::SQL_VALUE_SEPARATOR;
                  }
               }
            } else {
               $columsString.=self::SQL_SEPARATOR."`".$colum."`=".parent::SQL_NULL
                  .self::SQL_VALUE_SEPARATOR;
            }
         }
         //			odstranění poslední čárky
         $columsString = substr($columsString, 0, strlen($columsString)-1);
      }
      return $columsString;
   }

   /**
     * Metoda převede objekt na řetězec
     *
     * @return string -- objekt jako řetězec
     */
   public function __toString()
   {
      $sql = self::SQL_UPDATE;
      // tabulka
      $sql .= $this->_createTable(false);
      // sloupce
      $sql .= $this->_createSet();
      // where
      $sql .= $this->_createWhere();
      // order
      $sql .= $this->_createOrder();
      // limit
      $sql .= $this->_createLimit();

      return $sql;
   }
}
?>