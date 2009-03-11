<?php
require_once './lib/db/select.class.php';

/**
 * Třída pro výběr záznamů z MySQLi DB.
 * Třída obsahuje implementaci metody select z db-interfacu.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.2 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro výběr záznamů
 * @see           http://dev.mysql.com/doc/refman/5.1/en/select.html
 */

class Mysqli_Db_Select extends Mysqli_Db_Query implements Db_Select {
    /**
     * Proměnná určující obsah SQL dotazu
     */
   protected static $_sqlPartsInit = array(
      self::COLUMS_ARRAY			=> array(),
      self::SQL_COUNT				=> array(),
      self::INDEX_TABLE          => array(),
      self::SQL_JOIN             => array(),
      self::SQL_WHERE				=> array(),
      self::GROUP_BY_KEY			=> array(),
      self::ORDER_ORDER_KEY		=> array(),
      self::SQL_LIMIT				=> array());

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
    * @param string -- alias tabulky pro použití
    * @param boolean -- (option) jestli se májí tabulky zamknout
    * @return Db_Select
    */
   public function table($table, $alias = null, $lockTable = false){
      return parent::table($table, $alias, $lockTable);
   }

   /**
    * Metoda nastavuje z které tabulky se bude načítat
    * klauzule FROM
    *
    * @param string/array -- tabulka ze které se bude vybírat, u pole index označuje alias tabulky
    * @param string/array -- sloupce, které se mají vybrat
    *
    * @return Db_Select -- objekt Db_Select
    */
   public function colums($columnsArray = '*', $tableAlias = true) {
      if(!is_array($columnsArray)){
         $columnsArray = array($columnsArray);
      }

      // pokud se ukládá bez aliasu
      if($tableAlias == null OR $tableAlias == false){
         $arrName = parent::INDEX_COLUMS_NO_ALIAS;
      }
      // pokud má být použit hlavní alias tabulky
      else if($tableAlias == true){
         $arrName = key($this->_sqlQueryParts[self::INDEX_TABLE]);
      }
      // pokud se má vložit nějaký jiný zadaný alias
      else {
         $arrName = $tableAlias;
      }

      //  Vytvoření pole pokud neexistuje
      if(!isset ($this->_sqlQueryParts[self::COLUMS_ARRAY][$arrName])){
         $this->_sqlQueryParts[self::COLUMS_ARRAY][$arrName] = array();
      }

      foreach ($columnsArray as $key => $column) {
         if(is_int($key)){
            array_push($this->_sqlQueryParts[self::COLUMS_ARRAY][$arrName], $column);
         } else {
            $this->_sqlQueryParts[self::COLUMS_ARRAY][$arrName][$key] = $column;
         }
      }
      return $this;
   }

   /**
    * Metody vytvoří část pro klauzuli JOIN
    * U pole označuje index alias prvku. Pokud je zadáno null, nebude načten žádný sloupec
    *
    * @param string/array -- název tabulky (alias je generován z prvních dvou písmen) nebo pole kde index je alias tabulky
    * @param string -- podmínka v klauzuli ON, je třeba zadat i s aliasy
    * @param string -- typ JOIN operace hodnoty jsou: JOIN, LEFT, RIGHT, INNER
    * @param string/array -- název sloupců, které se mají vypsatm, výchozí jsou všechny ("*").
    *
    * @return Db_Select -- objekt Db_Select
    */
   public function join($tableArray, $condition, $joinType = null, $columsArray = "*") {
      $tmpArray = array();
      if(is_array($tableArray)){
         $columKey = key($tableArray);
         $tmpArray[self::JOIN_TABLE_NAME_KEY] = $tableArray[$columKey];
         $tmpArray[self::JOIN_TABLE_CONDITION_KEY] = $condition;

      } else {

         $columKey = $tableArray[0].$tableArray[1].$tableArray[2];
         $tmpArray[self::JOIN_TABLE_NAME_KEY] = $tableArray;
         $tmpArray[self::JOIN_TABLE_CONDITION_KEY] = $condition;
      }


      $joinType = strtolower($joinType);
      switch ($joinType) {
         case "left":
            $this->_sqlQueryParts[self::SQL_JOIN][self::SQL_LEFT_JOIN][$columKey] = $tmpArray;
            break;
         case "right":
            $this->_sqlQueryParts[self::SQL_JOIN][self::SQL_RIGHT_JOIN][$columKey] = $tmpArray;
            break;
         case "inner":
            $this->_sqlQueryParts[self::SQL_JOIN][self::SQL_INNER_JOIN][$columKey] = $tmpArray;
            break;
         default:
            $this->_sqlQueryParts[self::SQL_JOIN][self::SQL_JOIN][$columKey] = $tmpArray;
            break;
      }

      //	přidání sloupců
      if($columsArray != null){
         if(is_array($columsArray)){
            $this->_sqlQueryParts[self::COLUMS_ARRAY][$columKey] = array();
            foreach ($columsArray as $columAlias => $columName) {
               if(!is_int($columAlias)){
                  $this->_sqlQueryParts[self::COLUMS_ARRAY][$columKey][$columAlias] = $columName;
               } else {
                  array_push($this->_sqlQueryParts[self::COLUMS_ARRAY][$columKey], $columName);
               }

            }

         } else {
            $this->_sqlQueryParts[self::COLUMS_ARRAY][$columKey] = array();
            array_push($this->_sqlQueryParts[self::COLUMS_ARRAY][$columKey], $columsArray);
            //			$this->_sqlParts[self::COLUMS_ARRAY][$columKey] = $coolsArray;
         }
      }

      return $this;
   }

   /**
    * Metoda přiřadí řazení sloupcu v SQL dotazu
    *
    * @param string -- sloupec, podle kterého se má řadit
    * @param string -- (option) jak se má sloupec řadit (ASC, DESC) (default: ASC)
    *
    * @return Db_Select -- objekt Db_Select
    */
   public function order($colum, $order = self::SQL_ASC) {
      $order = strtoupper($order);
      if(!is_array($this->_sqlQueryParts[self::ORDER_ORDER_KEY])){
         $this->_sqlQueryParts[self::ORDER_ORDER_KEY] = array();
      }

      $columArray = array();

      if($order == self::SQL_DESC){
         $columArray[self::ORDER_COLUM_KEY] = $colum;
         $columArray[self::ORDER_ORDER_KEY] = self::SQL_DESC;
      } else {
         $columArray[self::ORDER_COLUM_KEY] = $colum;
         $columArray[self::ORDER_ORDER_KEY] = self::SQL_ASC;
      }
      array_push($this->_sqlQueryParts[self::ORDER_ORDER_KEY], $columArray);
      return $this;
   }

   /**
    * Metoda přiřadí slouření sloupců v SQL dotazu pomocí klauzule GROUP BY
    *
    * @param string -- sloupec, podle kterého se má řadit
    * @param string -- (option) WITH ROLLUP false(default)/true
    *
    * @return Db_Select -- objekt Db_Select
    */
   public function group($colum, $withRollup = false) {
      if(!is_array($this->_sqlQueryParts[self::GROUP_BY_KEY])){
         $this->_sqlQueryParts[self::GROUP_BY_KEY] = array();
      }

      $groupTmpArray = array();
      $groupTmpArray[self::ORDER_COLUM_KEY] = $colum;

      if(!$withRollup){
         $groupTmpArray[self::GROUP_WITH_ROLLUP] = false;
      } else {
         $groupTmpArray[self::GROUP_WITH_ROLLUP] = true;
      }

      array_push($this->_sqlQueryParts[self::GROUP_BY_KEY], $groupTmpArray);

      return $this;

   }

   /**
    * Metoda přidá do SQL dotazu klauzuli LIMIT
    * @param integer -- počet záznamů
    * @param integer -- záčátek
    *
    * @return Db_Select -- objekt Db_Select
    */
   public function limit($rowCount, $offset) {
      return parent::limit($rowCount, $offset);
   }

   /**
    * Metoda přidává do dotazu sloupce s počem záznamů NEPOUŽÍVAT!!!
    * @param string -- alias pod kterým má být vrácena hodnota
    *
    * @return Db_Select -- objekt Db_Select
    */
   public function count($alias = null, $column = self::SQL_ALL_VALUES)	{
      if($alias == null){
         array_push($this->_sqlQueryParts[self::SQL_COUNT], self::SQL_ALL_VALUES);
      } else {
         $this->_sqlQueryParts[self::SQL_COUNT][$alias] = self::SQL_ALL_VALUES;
      }

      return $this;
   }

   /**
    * Metody vatváří podmínku WHERE. Pokud je třeba použít více podmínek, je
    * zadáváno pole v tomot poředí např:
    * where(array($cola,$valuea,'='),'AND',array(array($colb,$valueb,'='),'OR',array($colc,$valuec,'=')))
    * WHERE ($cola = $valuea) AND (($colb = $valueb) OR ($colc = $valuec))
    * @param string -- sloupcec
    * @param string -- hodnota
    * @param string -- typ porovnávání
    *
    * @return Db_Select -- objekt Db_Select
    */
   public function where($column, $value = null, $term = '=', $operator = self::SQL_AND) {
      if(is_array($column)){
         return parent::where(func_get_args());
      } else {
         return parent::where($column, $value, $term, $operator);
      }
   }
   /**
    * Metody vytvoří část SQL dotazu se sloupcy, které se mají vybírat
    * @return string -- část SQL dotazu se sloupci
    */
   protected function _createColums() {
      $columsString = null;
      $colum = null;

      if(!empty($this->_sqlQueryParts[self::COLUMS_ARRAY])){
         foreach ($this->_sqlQueryParts[self::COLUMS_ARRAY] as $columsTable => $colums) {
            foreach ($colums as $columAlias => $columString) {
               if(!$this->isMySQLFunction($columString)AND ($columString[0] != self::SQL_PARENTHESIS_L AND $columString[strlen($columString)-1] != self::SQL_PARENTHESIS_R)){
                  if($columString != parent::SQL_ALL_VALUES){
                     $columString = '.`' . $this->_connector->escapeString($columString) . '`';
                  } else {
                     $columString = '.' . $columString . '';
                  }

                  if(is_int($columAlias)){
                     $colum .= self::SQL_SEPARATOR . $columsTable . '' . $this->_connector->escapeString($columString) . ',';
                  } else {
                     $colum .= self::SQL_SEPARATOR . $columsTable . '' . $this->_connector->escapeString($columString) . "" . self::SQL_SEPARATOR . self::SQL_AS .
                     self::SQL_SEPARATOR . $columAlias . ',';
                  }
               }
               else if($columString[0] == self::SQL_PARENTHESIS_L AND $columString[strlen($columString)-1] == self::SQL_PARENTHESIS_R){
                  $colum .= self::SQL_SEPARATOR . $this->_connector->escapeString($columString) . self::SQL_SEPARATOR . self::SQL_AS .	self::SQL_SEPARATOR . $columAlias . ',';
               } else {
                  $colum .= self::SQL_SEPARATOR . $this->_connector->escapeString($columString) . self::SQL_SEPARATOR . self::SQL_AS .	self::SQL_SEPARATOR . $columAlias . ',';
               }
            }
            $columsString .= $colum;
            $colum = null;

         }
         //	odstranění poslední čárky
         $columsString = substr($columsString, 0, strlen($columsString)-1);

      } else {
         $columsString = self::SQL_SEPARATOR.self::SQL_ALL_VALUES.self::SQL_SEPARATOR;
      }

      // Co tohle dělá??
      if(empty($this->_sqlQueryParts[self::GROUP_BY_KEY])
         AND !empty($this->_sqlQueryParts[self::SQL_COUNT])){
         $columsString = null;
      }
      return $columsString;
   }

   /**
    * Metoda přidává do dotazu část s COUNT
    */
   protected function _createCount()
   {
      $counts = null;
      //TODO dodělat ošetření při použití sloupců bez group
      //		AND !empty($this->_sqlQueryParts[self::SQL_GROUP_BY])
      if(!empty($this->_sqlQueryParts[self::SQL_COUNT])){
         //TODO dodělat čekování jestli náhodou pole neobsahuje název jednoho sloupce
         if(!empty($this->_sqlQueryParts[self::COLUMS_ARRAY])
            AND !empty($this->_sqlQueryParts[self::GROUP_BY_KEY])){
            $counts = self::SQL_VALUE_SEPARATOR;
         }
         foreach ($this->_sqlQueryParts[self::SQL_COUNT] as $alias => $colum) {
            if(is_numeric($alias)){
               $counts.=self::SQL_SEPARATOR.self::SQL_COUNT.self::SQL_PARENTHESIS_L.$colum
               .self::SQL_PARENTHESIS_R;
            } else {
               $counts.=self::SQL_SEPARATOR.self::SQL_COUNT.self::SQL_PARENTHESIS_L.$colum
               .self::SQL_PARENTHESIS_R.self::SQL_SEPARATOR.self::SQL_AS.self::SQL_SEPARATOR.$alias;
            }
         }
      }
      return $counts;
   }


   /**
    * Metoda vytváří část dotazu sek FROM
    * @return string -- část SQL dotazu s částí FROM
    */
   protected function _createFrom() {
      $fromString = null;
      $fromString = self::SQL_SEPARATOR.self::SQL_FROM;
      foreach ($this->_sqlQueryParts[self::SQL_FROM] as $tableAlias => $table) {
         $fromString .= self::SQL_SEPARATOR . MySQLiDb::$_tablePrefix . $table . self::SQL_SEPARATOR .self::SQL_AS . self::SQL_SEPARATOR .$tableAlias . ',';
      }
      //			odstranění poslední čárky
      $fromString = substr($fromString, 0, strlen($fromString)-1);

      return $fromString;
   }

   /**
    * Metoda vygeneruje část SQL příkazu s klauzulemi JOIN
    * @return string -- řetězec s klauzulemi JOIN
    */
   protected function _createJoin() {
      $joinsString = null;
      $joinString = null;
      foreach ($this->_sqlQueryParts[self::SQL_JOIN] as $joinType => $joinArray) {
         foreach ($joinArray as $tableAlias => $table){
            $joinString = self::SQL_SEPARATOR . $joinType . self::SQL_SEPARATOR;
            $joinString .= '`'.MySQLiDb::$_tablePrefix . $table[self::JOIN_TABLE_NAME_KEY].'`' . self::SQL_SEPARATOR . self::SQL_AS . self::SQL_SEPARATOR
            .$tableAlias . self::SQL_SEPARATOR . self::SQL_ON . self::SQL_SEPARATOR . $table[self::JOIN_TABLE_CONDITION_KEY];

            $joinsString .= $joinString;
         }
      }
      return $joinsString;
   }

   /**
    * Metoda vygeneruje část SQL dotazu s klauzulí ORDER BY
    * @return string -- část SQL s kluzulí ORDER BY
    */
   protected function _createOrder(){
      $orderString = null;
      if(!empty($this->_sqlQueryParts[self::ORDER_ORDER_KEY])){
         $orderString = self::SQL_SEPARATOR . self::SQL_ORDER_BY;
         foreach ($this->_sqlQueryParts[self::ORDER_ORDER_KEY] as $index => $orderArray) {
            $orderString .= self::SQL_SEPARATOR . $orderArray[self::ORDER_COLUM_KEY] . self::SQL_SEPARATOR . $orderArray[self::ORDER_ORDER_KEY] . ',';
         }

         //			odstranění poslední čárky
         $orderString = substr($orderString, 0, strlen($orderString)-1);
      }
      return $orderString;
   }

   /**
    * Metoda vygeneruje část SQL dotazu s klauzulí GROUP BY
    * @return string -- část SQL s kluzulí GROUP BY
    */
   protected function _createGroup() {

      if(!empty($this->_sqlQueryParts[self::GROUP_BY_KEY])){
         $groupString = null;
         $groupString = self::SQL_SEPARATOR . self::SQL_GROUP_BY;

         foreach ($this->_sqlQueryParts[self::GROUP_BY_KEY] as $groupArray) {
            $groupString .= self::SQL_SEPARATOR . $groupArray[self::ORDER_COLUM_KEY];
            if($groupArray[self::GROUP_WITH_ROLLUP]){
               $groupString .= self::SQL_SEPARATOR . self::SQL_WITH_ROLLUP;
            }
            $groupString .= ',';

         }

         //			odstranění poslední čárky
         $groupString = substr($groupString, 0, strlen($groupString)-1);

         return $groupString;
      }
   }

   /**
     * Metoda převede objekt na řetězec
     *
     * @return string -- objekt jako řetězec
     */
   public function __toString()
   {
      $sql = parent::SQL_SELECT;

      // Sloupce
      $sql .= $this->_createColums();

      // Tabulka
      $sql .= parent::SQL_SEPARATOR.parent::SQL_FROM.parent::SQL_SEPARATOR;
      $sql .= $this->_createTable();

      // JOIN
      $sql .= $this->_createJoin();

      // where
      $sql .= $this->_createWhere();

      // Group by
      $sql .= $this->_createGroup();

      // ORDER
      $sql .= $this->_createOrder();

      // LIMIT
      $sql .= $this->_createLimit();
      
      return $sql;
   }
}
/*
 * SELECT
 *   [ALL | DISTINCT | DISTINCTROW ]
 *     [HIGH_PRIORITY]
 *     [STRAIGHT_JOIN]
 *     [SQL_SMALL_RESULT] [SQL_BIG_RESULT] [SQL_BUFFER_RESULT]
 *     [SQL_CACHE | SQL_NO_CACHE] [SQL_CALC_FOUND_ROWS]
 *   select_expr [, select_expr ...]
 *   [FROM table_references
 *   [WHERE where_condition]
 *   [GROUP BY {col_name | expr | position}
 *     [ASC | DESC], ... [WITH ROLLUP]]
 *   [HAVING where_condition]
 *   [ORDER BY {col_name | expr | position}
 *     [ASC | DESC], ...]
 *   [LIMIT {[offset,] row_count | row_count OFFSET offset}]
 *   [PROCEDURE procedure_name(argument_list)]
 *   [INTO OUTFILE 'file_name' export_options
 *     | INTO DUMPFILE 'file_name'
 *     | INTO var_name [, var_name]]
 *   [FOR UPDATE | LOCK IN SHARE MODE]]
 */

?>