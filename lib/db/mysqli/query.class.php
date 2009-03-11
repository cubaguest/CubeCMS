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
   const SQL_FROM       		= 'FROM';
   const SQL_WHERE      		= 'WHERE';
   const SQL_GROUP_BY   		= 'GROUP BY';
   const SQL_ORDER_BY   		= 'ORDER BY';
   const SQL_HAVING     		= 'HAVING';
   const SQL_AND        		= 'AND';
   const SQL_AS         		= 'AS';
   const SQL_OR         		= 'OR';
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

    /**
     * Konstanty typů joinu
     * @var string
     */
   const SQL_JOIN			= 'JOIN';
   const SQL_LEFT_JOIN 	= 'LEFT JOIN';
   const SQL_RIGHT_JOIN	= 'RIGHT JOIN';
   const SQL_INNER_JOIN	= 'INNER JOIN';

    /**
     * Konstanty pro ukládání do pole SQL dotazu
     * @var string
     */
   const COLUMS_ARRAY                 = 'COLUMS';
   const WHERE_CONDITION_NAME_KEY 		= 'condition';
   const WHERE_CONDITION_OPERATOR_KEY = 'operator';
   const JOIN_TABLE_NAME_KEY          = 'name';
   const JOIN_TABLE_CONDITION_KEY     = 'condition';
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
   public function table($table, $alias = null, $lockTable = false){
      $this->_lockTables = $lockTable;
      if($alias == null){
         $alias = substr($table, 0, 5);
      }
      $this->_sqlQueryParts[self::INDEX_TABLE][$alias] = $table;
      return $this;
   }

   /**
    * Metody vatváří podmínku WHERE
    *
    * @param string -- podmínka
    * @param string -- typ spojení podmínky (AND, OR) (výchozí je AND)
    *
    * @return Db_Query -- objekt Db_Query
    */
   //	public function where($condition, $operator = self::SQL_AND) {
   public function where($column, $value = null, $term = '=', $operator = self::SQL_AND) {
      if(is_array($column)){
         $this->_sqlQueryParts[self::SQL_WHERE] = array_merge($this->_sqlQueryParts[self::SQL_WHERE], $column);
      } else {
         $arr = array($column,
            $value,
            $term);
         array_push($this->_sqlQueryParts[self::SQL_WHERE], $arr);
         array_push($this->_sqlQueryParts[self::SQL_WHERE], $operator);
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
   //	public function join($tableArray, $condition, $joinType = null, $columsArray = "*") {
   //		$tmpArray = array();
   //		if(is_array($tableArray)){
   //			$columKey = key($tableArray);
   //			$tmpArray[self::JOIN_TABLE_NAME_KEY] = $tableArray[$columKey];
   //			$tmpArray[self::JOIN_TABLE_CONDITION_KEY] = $condition;
   //
   //		} else {
   //
   //			$columKey = $tableArray[0].$tableArray[1].$tableArray[2];
   //			$tmpArray[self::JOIN_TABLE_NAME_KEY] = $tableArray;
   //			$tmpArray[self::JOIN_TABLE_CONDITION_KEY] = $condition;
   //		}
   //
   //
   //		$joinType = strtolower($joinType);
   //		switch ($joinType) {
   //			case "left":
   //			    $this->_sqlQueryParts[self::SQL_JOIN][self::SQL_LEFT_JOIN][$columKey] = $tmpArray;
   //				break;
   //			case "right":
   //			    $this->_sqlQueryParts[self::SQL_JOIN][self::SQL_RIGHT_JOIN][$columKey] = $tmpArray;
   //				break;
   //			case "inner":
   //			    $this->_sqlQueryParts[self::SQL_JOIN][self::SQL_INNER_JOIN][$columKey] = $tmpArray;
   //				break;
   //			default:
   //				$this->_sqlQueryParts[self::SQL_JOIN][self::SQL_JOIN][$columKey] = $tmpArray;
   //				break;
   //		}
   //
   //		//	přidání sloupců
   //		if($columsArray != null){
   //			if(is_array($columsArray)){
   //				$this->_sqlQueryParts[self::COLUMS_ARRAY][$columKey] = array();
   //				foreach ($columsArray as $columAlias => $columName) {
   //					if(!is_int($columAlias)){
   //						$this->_sqlQueryParts[self::COLUMS_ARRAY][$columKey][$columAlias] = $columName;
   //					} else {
   //						array_push($this->_sqlQueryParts[self::COLUMS_ARRAY][$columKey], $columName);
   //					}
   //
   //				}
   //
   //			} else {
   //				$this->_sqlQueryParts[self::COLUMS_ARRAY][$columKey] = array();
   //				array_push($this->_sqlQueryParts[self::COLUMS_ARRAY][$columKey], $columsArray);
   //				//			$this->_sqlParts[self::COLUMS_ARRAY][$columKey] = $coolsArray;
   //			}
   //		}
   //
   //		return $this;
   //	}

   /**
    * Metoda přiřadí řazení sloupcu v SQL dotazu
    *
    * @param string -- sloupec, podle kterého se má řadit
    * @param string -- (option) jak se má sloupec řadit (ASC, DESC) (default: ASC)
    *
    * @return Db_Select -- objekt Db_Select
    */
   //	public function order($colum, $order = self::SQL_ASC) {
   //		$order = strtoupper($order);
   //		if(!is_array($this->_sqlQueryParts[self::ORDER_ORDER_KEY])){
   //				$this->_sqlQueryParts[self::ORDER_ORDER_KEY] = array();
   //			}
   //
   //		$columArray = array();
   //
   //		if($order == self::SQL_DESC){
   //			$columArray[self::ORDER_COLUM_KEY] = $colum;
   //			$columArray[self::ORDER_ORDER_KEY] = self::SQL_DESC;
   //		} else {
   //			$columArray[self::ORDER_COLUM_KEY] = $colum;
   //			$columArray[self::ORDER_ORDER_KEY] = self::SQL_ASC;
   //		}
   //		array_push($this->_sqlQueryParts[self::ORDER_ORDER_KEY], $columArray);
   //		return $this;
   //	}

   /**
    * Metoda přiřadí slouření sloupců v SQL dotazu pomocí klauzule GROUP BY
    *
    * @param string -- sloupec, podle kterého se má řadit
    * @param string -- (option) WITH ROLLUP false(default)/true
    *
    * @return Db_Select -- objekt Db_Select
    */
   //	public function group($colum, $withRollup = false) {
   //		if(!is_array($this->_sqlQueryParts[self::GROUP_BY_KEY])){
   //			$this->_sqlQueryParts[self::GROUP_BY_KEY] = array();
   //		}
   //
   //		$groupTmpArray = array();
   //		$groupTmpArray[self::ORDER_COLUM_KEY] = $colum;
   //
   //		if(!$withRollup){
   //			$groupTmpArray[self::GROUP_WITH_ROLLUP] = false;
   //		} else {
   //			$groupTmpArray[self::GROUP_WITH_ROLLUP] = true;
   //		}
   //
   //		array_push($this->_sqlQueryParts[self::GROUP_BY_KEY], $groupTmpArray);
   //
   //		return $this;
   //
   //	}

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
    * Metoda přidává do dotazu sloupce s počem záznamů
    * @param string -- alias pod kterým má být vrácena hodnota
    *
    * @return Db_Select -- objekt Db_Select
    */
   //	public function count($alias = null, $colum = self::SQL_ALL_VALUES)	{
   //		if($alias == null){
   //			array_push($this->_sqlQueryParts[self::SQL_COUNT], self::SQL_ALL_VALUES);
   //		} else {
   //			$this->_sqlQueryParts[self::SQL_COUNT][$alias] = self::SQL_ALL_VALUES;
   //		}
   //
   //		return $this;
   //	}


   /**
    * Metody vytvoří část SQL dotazu se sloupcy, které se mají vybírat
    * @return string -- část SQL dotazu se sloupci
    */
   //	private function _createColums() {
   //		$columsString = null;
   //		$colum = null;
   //
   //		if(!empty($this->_sqlQueryParts[self::COLUMS_ARRAY])){
   //			foreach ($this->_sqlQueryParts[self::COLUMS_ARRAY] as $columsTable => $colums) {
   //				foreach ($colums as $columAlias => $columString) {
   //					//TODO NUTNÁ OPTIMALIZACE !!!!!!!!!!!!!!
   //					$isFunction = false;
   //					foreach (MySQLiDb::$specialSqlFunctions as $function) {
   //						if($function == substr($columString, 0, strlen($function))){
   //							$isFunction = true;
   //						}
   //					}
   //
   //					if(!$isFunction AND ($columString[0] != self::SQL_PARENTHESIS_L AND $columString[strlen($columString)-1] != self::SQL_PARENTHESIS_R)){
   //						if($columString != "*"){
   //							$columString = '.`' . $columString . '`';
   //						} else {
   //							$columString = '.' . $columString . '';
   //						}
   //
   //						if(is_int($columAlias)){
   //							$colum .= self::SQL_SEPARATOR . $columsTable . '' . $columString . ',';
   //						} else {
   //							$colum .= self::SQL_SEPARATOR . $columsTable . '' . $columString . "" . self::SQL_SEPARATOR . self::SQL_AS .
   //							self::SQL_SEPARATOR . $columAlias . ',';
   //						}
   //					} else if($columString[0] == self::SQL_PARENTHESIS_L AND $columString[strlen($columString)-1] == self::SQL_PARENTHESIS_R){
   //						$colum .= self::SQL_SEPARATOR . $columString . self::SQL_SEPARATOR . self::SQL_AS .	self::SQL_SEPARATOR . $columAlias . ',';
   //					} else {
   //						$colum .= self::SQL_SEPARATOR . $columString . self::SQL_SEPARATOR . self::SQL_AS .	self::SQL_SEPARATOR . $columAlias . ',';
   //					}
   //
   //
   //				}
   //				$columsString .= $colum;
   //				$colum = null;
   //
   //			}
   ////			odstranění poslední čárky
   //			$columsString = substr($columsString, 0, strlen($columsString)-1);
   //
   //		} else {
   //			$columsString = ' * ';
   //		}

   //		pokud je count odstranit sloupce
   //TODO dodělat čekování jestli náhodou pole neobsahuje název jednoho sloupce
   //		if(!empty($this->_sqlQueryParts[self::SQL_COUNT])
   //			AND sizeof($this->_sqlQueryParts[self::COLUMS_ARRAY], true) == 2){
   //			$columsString = null;
   //		}
   //		if(empty($this->_sqlQueryParts[self::GROUP_BY_KEY])
   //			AND !empty($this->_sqlQueryParts[self::SQL_COUNT])){
   //			$columsString = null;
   //		}

   //		echo $columsString."<br />";

   //		return $columsString;
   //	}

   /**
    * Metoda přidává do dotazu část s COUNT
    */
   //	private function _createCount()
   //	{
   //		$counts = null;
   //		//TODO dodělat ošetření při použití sloupců bez group
   ////		AND !empty($this->_sqlQueryParts[self::SQL_GROUP_BY])
   //		if(!empty($this->_sqlQueryParts[self::SQL_COUNT])){
   //			//TODO dodělat čekování jestli náhodou pole neobsahuje název jednoho sloupce
   //			if(!empty($this->_sqlQueryParts[self::COLUMS_ARRAY])
   //				AND !empty($this->_sqlQueryParts[self::GROUP_BY_KEY])){
   //				$counts = self::SQL_VALUE_SEPARATOR;
   //			}
   //			foreach ($this->_sqlQueryParts[self::SQL_COUNT] as $alias => $colum) {
   //				if(is_numeric($alias)){
   //					$counts.=self::SQL_SEPARATOR.self::SQL_COUNT.self::SQL_PARENTHESIS_L.$colum
   //							.self::SQL_PARENTHESIS_R;
   //				} else {
   //					$counts.=self::SQL_SEPARATOR.self::SQL_COUNT.self::SQL_PARENTHESIS_L.$colum
   //							.self::SQL_PARENTHESIS_R.self::SQL_SEPARATOR.self::SQL_AS.self::SQL_SEPARATOR.$alias;
   //				}
   //			}
   //		}
   //		return $counts;
   //	}


   /**
    * Metoda vytváří část dotazu sek FROM
    * @return string -- část SQL dotazu s částí FROM
    */
   //	private function _createFrom() {
   //		$fromString = null;
   //		$fromString = self::SQL_SEPARATOR.self::SQL_FROM;
   //		foreach ($this->_sqlQueryParts[self::SQL_FROM] as $tableAlias => $table) {
   //			$fromString .= self::SQL_SEPARATOR . MySQLiDb::$_tablePrefix . $table . self::SQL_SEPARATOR .self::SQL_AS . self::SQL_SEPARATOR .$tableAlias . ',';
   //		}
   //		//			odstranění poslední čárky
   //		$fromString = substr($fromString, 0, strlen($fromString)-1);
   //
   //		return $fromString;
   //	}

   /**
    * Metoda vygeneruje část SQL příkazu s klauzulemi JOIN
    * @return string -- řetězec s klauzulemi JOIN
    */
   //	private function _createJoin() {
   //		$joinsString = null;
   //		$joinString = null;
   //
   //
   //		foreach ($this->_sqlQueryParts[self::SQL_JOIN] as $joinType => $joinArray) {
   //
   //			foreach ($joinArray as $tableAlias => $table){
   //				$joinString = self::SQL_SEPARATOR . $joinType . self::SQL_SEPARATOR;
   //				$joinString .= MySQLiDb::$_tablePrefix . $table[self::JOIN_TABLE_NAME_KEY] . self::SQL_SEPARATOR . self::SQL_AS . self::SQL_SEPARATOR
   //							  .$tableAlias . self::SQL_SEPARATOR . self::SQL_ON . self::SQL_SEPARATOR . $table[self::JOIN_TABLE_CONDITION_KEY];
   //
   //				$joinsString .= $joinString;
   //			}
   //		}
   //		return $joinsString;
   //
   //	}

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
                        $value .= $var.self::SQL_VALUE_SEPARATOR;
                     } else {
                        $value .= "'".$this->_connector->escapeString($var)."'".self::SQL_VALUE_SEPARATOR;
                     }
                  }
                  $value = substr($value, 0, strlen($value)-1);
               } else {
                  $value = "'".$this->_connector->escapeString($where[1])."'";
               }
               if(!isset ($where[2])){
                  $where[2] = '=';
               }

               $return .= self::SQL_PARENTHESIS_L.$where[0]
               .self::SQL_SEPARATOR.$where[2].self::SQL_SEPARATOR
               .$value.self::SQL_PARENTHESIS_R;
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
    * Metoda vygeneruje část SQL dotazu s klauzulí ORDER BY
    * @return string -- část SQL s kluzulí ORDER BY
    */
   //	private function _createOrder(){
   //		$orderString = null;
   //		if(!empty($this->_sqlQueryParts[self::ORDER_ORDER_KEY])){
   //			$orderString = self::SQL_SEPARATOR . self::SQL_ORDER_BY;
   //			foreach ($this->_sqlQueryParts[self::ORDER_ORDER_KEY] as $index => $orderArray) {
   //				$orderString .= self::SQL_SEPARATOR . $orderArray[self::ORDER_COLUM_KEY] . self::SQL_SEPARATOR . $orderArray[self::ORDER_ORDER_KEY] . ',';
   //			}
   //
   //			//			odstranění poslední čárky
   //			$orderString = substr($orderString, 0, strlen($orderString)-1);
   //		}
   //		return $orderString;
   //	}

   /**
    * Metoda vygeneruje část SQL dotazu s klauzulí GROUP BY
    * @return string -- část SQL s kluzulí GROUP BY
    */
   //	private function _createGroup() {
   //
   //		if(!empty($this->_sqlQueryParts[self::GROUP_BY_KEY])){
   //			$groupString = null;
   //			$groupString = self::SQL_SEPARATOR . self::SQL_GROUP_BY;
   //
   //			foreach ($this->_sqlQueryParts[self::GROUP_BY_KEY] as $groupArray) {
   //				$groupString .= self::SQL_SEPARATOR . $groupArray[self::ORDER_COLUM_KEY];
   //				if($groupArray[self::GROUP_WITH_ROLLUP]){
   //					$groupString .= self::SQL_SEPARATOR . self::SQL_WITH_ROLLUP;
   //				}
   //				$groupString .= ',';
   //
   //			}
   //
   //			//			odstranění poslední čárky
   //			$groupString = substr($groupString, 0, strlen($groupString)-1);
   //
   //			return $groupString;
   //		}
   //	}

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
    *
    * @return string -- klauzule TABLE
    */
   protected function _createTable() {
      $table = null;
      if(!empty($this->_sqlQueryParts[self::INDEX_TABLE])){
         $table = self::SQL_SEPARATOR . '`' . MySQLiDb::$_tablePrefix .
         $this->_sqlQueryParts[self::INDEX_TABLE][key($this->_sqlQueryParts[self::INDEX_TABLE])] . '`'
         . self::SQL_SEPARATOR . self::SQL_AS . self::SQL_SEPARATOR
         . key($this->_sqlQueryParts[self::INDEX_TABLE]);

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
}

?>