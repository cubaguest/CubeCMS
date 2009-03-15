<?php
/**
 * Třída pro tvorbu dotazů z MySQLi DB.
 * Třída obsahuje implementaci metody select z db-interfacu.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.2 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro výběr záznamů
 */

class Mysqli_Db_Query {
   /**
    * Konstanty pro příkazy SQL
    * @var string
    */
   const SQL_SELECT     		= 'SELECT';
	const SQL_INSERT     		= 'INSERT';

   const SQL_WHERE      		= 'WHERE';
   const SQL_NULL             = 'NULL';

   const SQL_FROM       		= 'FROM';
   const SQL_GROUP_BY   		= 'GROUP BY';
   const SQL_ORDER_BY   		= 'ORDER BY';
   const SQL_HAVING     		= 'HAVING';

   const SQL_AND        		= 'AND';
   const SQL_OR         		= 'OR';
   const SQL_LIKE         		= 'LIKE';
   const SQL_NOT         		= 'NOT';
   const SQL_BETWEEN      		= 'BETWEEN';
   const SQL_NOT_BETWEEN  		= 'NOT BETWEEN';
   const SQL_IS_NULL          = 'IS NULL';
   const SQL_IS_NOT_NULL      = 'IS NOT NULL';
   const SQL_USING       		= 'USING';

   const SQL_AS         		= 'AS';
   const SQL_ON               = 'ON';
   const SQL_IN               = 'IN';

   const SQL_ASC        		= 'ASC';
   const SQL_DESC       		= 'DESC';

   const SQL_SEPARATOR	 		= ' ';
   const SQL_PARENTHESIS_L    = '(';
   const SQL_PARENTHESIS_R    = ')';
   const SQL_VALUE_SEPARATOR	= ',';
   const SQL_ALL_VALUES       = '*';

   const SQL_WITH_ROLLUP		= 'WITH ROLLUP';
   const SQL_LIMIT		 		= 'LIMIT';
   const SQL_COUNT		 		= 'COUNT';

   const SQL_JOIN             = 'JOIN';
   const SQL_JOIN_LEFT        = 'LEFT';
   const SQL_JOIN_RIGHT       = 'RIGHT';
   const SQL_JOIN_INNER       = 'INNER';
   const SQL_JOIN_CROSS       = 'CROSS';

   const SQL_INTO       		= 'INTO';
   const SQL_VALUES      		= 'VALUES';

    /**
     * Konstanty pro ukládání do pole SQL dotazu
     * @var string
     */
   const COLUMS_ARRAY                 = 'COLUMS';
   const WHERE_CONDITION_NAME_KEY     = 'condition';
   const WHERE_CONDITION_OPERATOR_KEY = 'operator';
   const JOIN_TABLE_NAME              = 'table';
   const JOIN_TABLE_CONDITION_COLUMN1 = 'column1';
   const JOIN_TABLE_CONDITION_COLUMN2 = 'column2';
   const JOIN_TABLE_CONDITION_TYPE    = 'cond_type';
   const JOIN_TABLE_CONDITION_OPERATOR= 'operator';

   const ORDER_ORDER_KEY              = 'ORDER';
   const ORDER_COLUM_KEY              = 'colum';
   const GROUP_BY_KEY                 = 'GROUP';
   const GROUP_WITH_ROLLUP            = 'w_rolupp';
   const LIMT_COUNT_ROWS_KEY          = 'limit_count';
   const LIMT_OFFSET_KEY              = 'limit_offset';

    /**
     * Název indexů s částmi sql dotazu
     */
   const INDEX_TABLE             = 'table';
   const INDEX_COLUMS_NO_ALIAS   = 'noalias';
   const INDEX_WHERE_COLUMN      = 'column';
   const INDEX_WHERE_VALUE       = 'value';
   const INDEX_WHERE_TERM        = 'term';
   const INDEX_WHERE_OPERATOR    = 'operator';

    /**
     * Konstanta určující obsah SQL dotazu
     * Musí mít správné pořadí, jak se má SQL dotaz řadit!!!
     *
     */
   protected static $_sqlPartsInit = array(self::INDEX_TABLE   => array(),
      self::SQL_WHERE		=> array(),
      self::SQL_LIMIT		=> array());

   /**
    * Pole s částmi SQL dotazu ze kterých se bude při výstupu generovat samotná SQL dotaz
    *
    * @var array
    */
   protected $_sqlQueryParts = array();

   protected $_connector = null;

   /**
    * Proměnná obsahuje jestli se mají zamknout tabulky
    * @var boolean
    */
   protected $_lockTables = false;

   /**
    * statické pole se specílními SQL funkcemi
    * @var array
    */
   protected $specialSqlFunctions = array("NOW", "TIMESTAMPDIFF", "COUNT", "IFNULL", "IF");


   protected $whereTermConditions = array('=', '<', '>', '<>', '>=', '<=');

   public function __construct(Db $conector) {
      //		inicializace do zakladni podoby;
      $this->_connector = $conector;
      $this->init();
   }

   /**
    * Inicializace proměných
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
    * @return
    */
   protected function setTable($table, $alias = null, $lockTable = false){
      $this->_lockTables = $lockTable;
      if($alias == null){
         $alias = substr($table, 0, 5);
      }
      $this->_sqlQueryParts[self::INDEX_TABLE][$alias] = $table;
      return $this;
   }

   /**
    * Metody vytváří podmínku WHERE
    *
    * @param string -- název sloupce
    * @param string/integer -- hodnota
    * @param char/int -- operátor porovnávání konstatna Db::OPERATOR_XXX
    * @param int -- operátor porovnávání Db::COND_OPERATOR_XXX
    *
    * @return Db_Query -- objekt Db_Query
    * @todo doladit
    */
   public function where($column, $value = null, $term = '=', $operator = Db::COND_OPERATOR_AND) {
      if(is_array($column)){
         $this->_sqlQueryParts[self::SQL_WHERE] = array_merge($this->_sqlQueryParts[self::SQL_WHERE], $column);
      } else {
         if($operator == Db::COND_OPERATOR_AND){
            $operator = self::SQL_AND;
         } else if($operator == Db::COND_OPERATOR_OR){
            $operator = self::SQL_OR;
         } else {
            throw new InvalidArgumentException(_('Zadán nesprávný operátor porovnávání')." $operator ", 1);
         }

         $arr = array($column,
                      $value,
                      $term);
         array_push($this->_sqlQueryParts[self::SQL_WHERE], $arr);
         array_push($this->_sqlQueryParts[self::SQL_WHERE], $operator);
      }
      return $this;
   }

   /**
    * Metoda přidá do SQL dotazu klauzuli LIMIT
    * @param integer -- počet záznamů
    * @param integer -- záčátek
    *
    * @return this -- objekt sebe
    */
   public function limit($rowCount, $offset) {
      $this->_sqlQueryParts[self::SQL_LIMIT][self::LIMT_COUNT_ROWS_KEY] = $rowCount;
      $this->_sqlQueryParts[self::SQL_LIMIT][self::LIMT_OFFSET_KEY] = $offset;

      return $this;
   }

   /**
    * Metoda vygeneruje část SQL dotazu s klauzulí WHERE
    * @return string -- část s kluzulí WHERE
    */
   protected function _createWhere(){
      $wheresString = null;

      if(!empty($this->_sqlQueryParts[self::SQL_WHERE])){
         $wheresString = self::SQL_SEPARATOR . self::SQL_WHERE . self::SQL_SEPARATOR;
         $wheresString .= $this->_createWhereHelp($this->_sqlQueryParts[self::SQL_WHERE]);
      }
//      echo $wheresString .' <br>';
      return $wheresString;
   }

   /**
    * Pomocná rekurzivní funkce pro vytváření klauzule WHERE
    * @param array $array -- pole s parametry WHERE
    * @return string -- vytvořené pole
    */
   private function _createWhereHelp($array) {
      $return = null;
      foreach ($array as $where){
         if(is_array($where)){
            if(is_array($where[key($where)])){
               $return .= self::SQL_PARENTHESIS_L;
               $return .= $this->_createWhereHelp($where);
//               echo "'".substr($return, 0, strlen($return)-strlen(self::SQL_AND)-2)."'";
//               if(substr($return, strlen($return)-strlen(self::SQL_AND)-1, strlen(self::SQL_AND)) == self::SQL_AND){
//                  $return = substr($return, 0, strlen($return)-strlen(self::SQL_AND)-2);
//               } else if(substr($return, strlen($return)-strlen(self::SQL_OR)-1, strlen(self::SQL_OR)) == self::SQL_OR){
//                  $return = substr($return, 0, strlen($return)-strlen(self::SQL_OR)-2);
//               }
               $return .= self::SQL_PARENTHESIS_R;
            } else {
               $value = null;
               if(is_int($where[1])){
                  $value = $where[1];
               } else if (is_array($where[1])) {
                  foreach ($where[1] as $var){
                     if(is_int($var)){
                        $value[] .= $var.self::SQL_VALUE_SEPARATOR;
                     } else {
                        $value[] .= "'".$this->_connector->escapeString($var)."'".self::SQL_VALUE_SEPARATOR;
                     }
                  }
                  $value = substr($value, 0, strlen($value)-1);
               } else {
                  $value = "'".$this->_connector->escapeString($where[1])."'";
               }
               if(!isset ($where[2])){
                  $where[2] = '=';
               }

               // Podle operátoru volíme podmínku
               // pokud se jedná o některý typ porovnávání (=; <; ...)
               if(in_array($where[2], $this->whereTermConditions)){
                  $whereCond = $where[0] . self::SQL_SEPARATOR . $where[2] . self::SQL_SEPARATOR . $value;
               }
               // pokud se jedná o některý jiný operátor
               else {
                  switch ($where[2]) {
                     case Db::OPERATOR_LIKE:
                        $whereCond = $where[0].self::SQL_SEPARATOR.self::SQL_LIKE.self::SQL_SEPARATOR.$value;
                        break;
                     case Db::OPERATOR_NOT_LIKE:
                        $whereCond = self::SQL_NOT.self::SQL_SEPARATOR.$where[0]
                           .self::SQL_SEPARATOR.self::SQL_LIKE.self::SQL_SEPARATOR.$value;
                        break;
                     case Db::OPERATOR_BETWEEN:
                        $whereCond = $where[0].self::SQL_SEPARATOR.self::SQL_BETWEEN
                           .self::SQL_SEPARATOR.$value[0].self::SQL_SEPARATOR.self::SQL_AND
                           .self::SQL_SEPARATOR.$value[1];
                        break;
                     case Db::OPERATOR_NOT_BETWEEN:
                        $whereCond = $where[0].self::SQL_SEPARATOR.self::SQL_NOT_BETWEEN
                           .self::SQL_SEPARATOR.$value[0].self::SQL_SEPARATOR.self::SQL_AND
                           .self::SQL_SEPARATOR.$value[1];
                        break;
                     case Db::OPERATOR_IN:
                        $whereCond = $where[0].self::SQL_SEPARATOR.self::SQL_IN
                           .self::SQL_SEPARATOR.self::SQL_PARENTHESIS_L;
                        foreach ($value as $val) {
                           $whereCond .= $val.self::SQL_VALUE_SEPARATOR;
                        }
                        $whereCond = substr($whereCond, 0, strlen($whereCond)-1);
                        $whereCond .= self::SQL_PARENTHESIS_R;
                        break;
                     case Db::OPERATOR_IS_NULL:
                        $whereCond = $where[0].self::SQL_SEPARATOR.self::SQL_IS_NULL;
                        break;
                     case Db::OPERATOR_IS_NOT_NULL:
                        $whereCond = $where[0].self::SQL_SEPARATOR.self::SQL_IS_NOT_NULL;
                        break;
                     default:
                        break;
                  }
               }
               $return .= self::SQL_PARENTHESIS_L . $whereCond . self::SQL_PARENTHESIS_R;
            }
         } else {
            $return .= self::SQL_SEPARATOR.$where.self::SQL_SEPARATOR;
            // odstranění posledního operátoru AND nebo OR
         }
      }
      if(substr($return, strlen($return)-strlen(self::SQL_AND)-1, strlen(self::SQL_AND)) == self::SQL_AND){
         $return = substr($return, 0, strlen($return)-strlen(self::SQL_AND)-2);
      } else if(substr($return, strlen($return)-strlen(self::SQL_OR)-1, strlen(self::SQL_OR)) == self::SQL_OR){
         $return = substr($return, 0, strlen($return)-strlen(self::SQL_OR)-2);
      }
      return $return;
   }

         /**
    * Metoda převede operátor na daný operátor pro SQL dotaz
    * @param integer $operator -- operátor Db:OPERATOR_X
    * @return string -- zvolený operátor
    */
   protected function choseWhereTermOperator($operator) {
      if(in_array($operator, $this->whereTermConditions)){
         return $operator;
      } else {
         switch ($operator) {
            case Db::OPERATOR_LIKE:
               return self::SQL_LIKE;
               break;
            case Db::OPERATOR_NOT_LIKE:
               return self::SQL_NOT;
               break;
            case Db::OPERATOR_IS_NULL:
               return self::SQL_IS_NULL;
               break;
            case Db::OPERATOR_BETWEEN:
               return self::SQL_BETWEEN;
               break;
            case Db::OPERATOR_NOT_BETWEEN:
               return self::SQL_NOT_BETWEEN;
               break;
            case Db::OPERATOR_AND:
            default:
               return self::SQL_AND;
               break;
         }
      }
   }

   /**
    * Metoda vygeneruje čás SQL dotazu s klauzulí LIMIT
    *
    * @return string -- klauzule LIMIT
    */
   protected function _createLimit() {

      if(!empty($this->_sqlQueryParts[self::SQL_LIMIT])){
         $limitString = null;
         $limitString = self::SQL_SEPARATOR . self::SQL_LIMIT . self::SQL_SEPARATOR . $this->_sqlQueryParts[self::SQL_LIMIT][self::LIMT_COUNT_ROWS_KEY]
         . ',' . self::SQL_SEPARATOR .$this->_sqlQueryParts[self::SQL_LIMIT][self::LIMT_OFFSET_KEY];

         return $limitString;
      }
   }

   /**
    * Metoda vrací název tabulky
    * Nutně potřebuje oimplementovat zvlášť v každé podtřídě
    * @param boolean $withAlias -- (option) jestli se má použít i alias tabulky
    * @return string -- klauzule TABLE
    */
   protected function _createTable($withAlias = true) {
      $table = null;
      if(!empty($this->_sqlQueryParts[self::INDEX_TABLE])){
         $table = self::SQL_SEPARATOR . '`' . MySQLiDb::$_tablePrefix .
         $this->_sqlQueryParts[self::INDEX_TABLE][key($this->_sqlQueryParts[self::INDEX_TABLE])] . '`';
         if($withAlias){
            $table .= self::SQL_SEPARATOR.self::SQL_AS.self::SQL_SEPARATOR
               .key($this->_sqlQueryParts[self::INDEX_TABLE]);
         }

      }
      return $table;
   }

   /**
    * Metoda kontroluje, jestli se nejedná o vnitřní funkci MYSQL, pokud ano vrací true
    * @param string $string -- testovaný řetězec
    * @todo -- nutná optimalizace
    */
   protected function isMySQLFunction($string) {
      foreach ($this->specialSqlFunctions as $function) {
         if($function == substr($string, 0, strlen($function))){
            return true;
         }
      }
      return false;
   }

   /**
    * Metoda vrací Db konektor k Mysqli
    * @return MySQLiDb
    */
   protected function getDbConnector() {
      return $this->_connector;
   }
}

?>