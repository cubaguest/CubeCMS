<?php
require_once './lib/db/select.class.php';

class Mysql_Db_Select extends Db_Select {
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
    const SQL_ON        		= 'ON';
    const SQL_ASC        		= 'ASC';
    const SQL_DESC       		= 'DESC';
    const SQL_SEPARATOR	 		= ' ';
    const SQL_PARENTHESIS_L	 	= '(';
    const SQL_PARENTHESIS_R	 	= ')';
    const SQL_VALUE_SEPARATOR	= ',';
    const SQL_ALL_VALUES		= '*';
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
    const COLUMS_ARRAY 					= 'COLUMS';
    const WHERE_CONDITION_NAME_KEY 		= 'condition';
    const WHERE_CONDITION_OPERATOR_KEY 	= 'operator';
    const JOIN_TABLE_NAME_KEY			= 'name';
    const JOIN_TABLE_CONDITION_KEY		= 'condition';
    const ORDER_ORDER_KEY				= 'ORDER';
    const ORDER_COLUM_KEY				= 'colum';
    const GROUP_BY_KEY					= 'GROUP';
    const GROUP_WITH_ROLLUP				= 'w_rolupp';
    const LIMT_COUNT_ROWS_KEY			= 'limit_count';
    const LIMT_OFFSET_KEY				= 'limit_offset';

    /**
     * Konstanta určující obsah SQL dotazu
     * Musí mít správné pořadí, jak se má SQL dotaz řadit!!!
     *
     */
	protected static $_sqlPartsInit = array(self::COLUMS_ARRAY			=> array(),
											self::SQL_COUNT				=> array(),
								 			self::SQL_FROM				=> array(),
								 			self::SQL_JOIN				=> array(),
								 			self::SQL_WHERE				=> array(),
											self::GROUP_BY_KEY			=> array(),
								 			self::ORDER_ORDER_KEY		=> array(),
								 			self::SQL_LIMIT				=> array());

	protected $_sqlQueryParts = array();

	protected $_connector = null;

//	public function __construct(MySQLDb $connector) {
	public function __construct(Db $conector) {
//		inicializace do zakladni podoby;
		$this->_connector = $conector;
		$this->_sqlQueryParts = self::$_sqlPartsInit;
	}

	/**
	 * Metoda nastavuje z které tabulky se bude načítat
	 * klauzule FROM
	 *
	 * @param string/array -- tabulka ze které se bude vybírat, u pole index označuje alias tabulky
	 */
	public function from($tableArray, $columsArray = "*") {
		if(is_array($tableArray)){
//			foreach ($tablesArray as $tableAlias => $table) {
				$this->_sqlQueryParts[ self::SQL_FROM][key($tableArray)] = $tableArray[key($tableArray)];
//			}
			$tableAlias = key($tableArray);
		} else {
			$tableAlias = $tableArray[0].$tableArray[1].$tableArray[2];
			$this->_sqlQueryParts[self::SQL_FROM][$tableAlias] = $tableArray;
		}

		if(!is_array($columsArray)){
			$columsArray = array($columsArray);
		}
		$this->_sqlQueryParts[self::COLUMS_ARRAY][$tableAlias] = $columsArray;

		return $this;
	}

	/**
	 * Metody vatváří podmínku WHERE
	 *
	 * @param string -- podmínka
	 * @param string -- typ spojení podmínky (AND, OR) (výchozí je AND)
	 * //TODO dodělat aby se doplňovali magické uvozovky do lauzule
	 */
	public function where($condition, $operator = self::SQL_AND) {
		$tmpArray = array();

		if($operator != self::SQL_AND AND $operator != self::SQL_OR){
			$operator = self::SQL_AND;
		}

		$tmpArray[self::WHERE_CONDITION_NAME_KEY] = $condition;
		$tmpArray[self::WHERE_CONDITION_OPERATOR_KEY] = strtoupper($operator);

//		pokud není vytvořeno pole podmínek -> vytvoř ho
		if(!is_array($this->_sqlQueryParts[self::SQL_WHERE])){
			$this->_sqlQueryParts[self::SQL_WHERE] = array();
		}

		array_push($this->_sqlQueryParts[self::SQL_WHERE], $tmpArray);

		return $this;
	}

	/**
	 * Metody vytvoří část pro klauzuli JOIN
	 *
	 * @param string/array -- název tabulky (alias je generován z prvních dvou písmen) nebo pole kde index je alias tabulky
	 * @param string -- podmínka v klauzuli ON, je třeba zadat i s aliasy
	 * @param string -- typ JOIN operace hodnoty jsou: JOIN, LEFT, RIGHT, INNER
	 * @param string/array -- název sloupců, které se mají vypsatm, výchozí jsou všechny ("*").
	 * 						  U pole označuje index alias prvku. Pokud je zadáno null, nebude načten žádný sloupec
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
	 */
	public function limit($rowCount, $offset) {
		$this->_sqlQueryParts[self::SQL_LIMIT][self::LIMT_COUNT_ROWS_KEY] = $rowCount;
		$this->_sqlQueryParts[self::SQL_LIMIT][self::LIMT_OFFSET_KEY] = $offset;

		return $this;
	}

	/**
	 * Metoda přidává do dotazu sloupce s počem záznamů
	 * @param string -- alias pod kterým má být vrácena hodnota
	 */
	public function count($alias = null, $colum = self::SQL_ALL_VALUES)
	{
		if($alias == null){
			array_push($this->_sqlQueryParts[self::SQL_COUNT], self::SQL_ALL_VALUES);
		} else {
			$this->_sqlQueryParts[self::SQL_COUNT][$alias] = self::SQL_ALL_VALUES;
		}
		
		return $this;
	}
	
	
	/**
	 * Metody vytvoří část SQL dotazu se sloupcy, které se mají vybírat
	 * @return string -- část SQL dotazu se sloupci
	 */
	private function _createColums() {
		$columsString = null;
		$colum = null;

		if(!empty($this->_sqlQueryParts[self::COLUMS_ARRAY])){
			foreach ($this->_sqlQueryParts[self::COLUMS_ARRAY] as $columsTable => $colums) {
				foreach ($colums as $columAlias => $columString) {
					if($columString != "*"){
						$columString = '.`' . $columString . '`';
					} else {
						$columString = '.' . $columString . '';
					}

					if(is_int($columAlias)){
						$colum .= self::SQL_SEPARATOR . $columsTable . '' . $columString . ',';
					} else {
						$colum .= self::SQL_SEPARATOR . $columsTable . '' . $columString . "" . self::SQL_SEPARATOR . self::SQL_AS .
								  self::SQL_SEPARATOR . $columAlias . ',';
					}

				}
				$columsString .= $colum;
				$colum = null;

			}
//			odstranění poslední čárky
			$columsString = substr($columsString, 0, strlen($columsString)-1);

		} else {
			$columsString = ' * ';
		}
		
//		pokud je count odstranit sloupce
//TODO dodělat čekování jestli náhodou pole neobsahuje název jednoho sloupce
//		if(!empty($this->_sqlQueryParts[self::SQL_COUNT]) 
//			AND sizeof($this->_sqlQueryParts[self::COLUMS_ARRAY], true) == 2){
//			$columsString = null;
//		}
		if(empty($this->_sqlQueryParts[self::GROUP_BY_KEY]) 
			AND !empty($this->_sqlQueryParts[self::SQL_COUNT])){
			$columsString = null;
		}

		return $columsString;
	}

	/**
	 * Metoda přidává do dotazu část s COUNT
	 */
	private function _createCount()
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
	private function _createFrom() {
		$fromString = null;
		$fromString = self::SQL_SEPARATOR.self::SQL_FROM;
		foreach ($this->_sqlQueryParts[self::SQL_FROM] as $tableAlias => $table) {
			$fromString .= self::SQL_SEPARATOR . MySQLDb::$_tablePrefix . $table . self::SQL_SEPARATOR .self::SQL_AS . self::SQL_SEPARATOR .$tableAlias . ',';
		}
		//			odstranění poslední čárky
		$fromString = substr($fromString, 0, strlen($fromString)-1);

		return $fromString;
	}

	/**
	 * Metoda vygeneruje část SQL příkazu s klauzulemi JOIN
	 * @return string -- řetězec s klauzulemi JOIN
	 */
	private function _createJoin() {
		$joinsString = null;
		$joinString = null;


		foreach ($this->_sqlQueryParts[self::SQL_JOIN] as $joinType => $joinArray) {

			foreach ($joinArray as $tableAlias => $table){
				$joinString = self::SQL_SEPARATOR . $joinType . self::SQL_SEPARATOR;
				$joinString .= MySQLDb::$_tablePrefix . $table[self::JOIN_TABLE_NAME_KEY] . self::SQL_SEPARATOR . self::SQL_AS . self::SQL_SEPARATOR
							  .$tableAlias . self::SQL_SEPARATOR . self::SQL_ON . self::SQL_SEPARATOR . $table[self::JOIN_TABLE_CONDITION_KEY];

				$joinsString .= $joinString;
			}
		}
		return $joinsString;

	}

	/**
	 * Metoda vygeneruje část SQL dotazu s klauzulí WHERE
	 * @return string -- část s kluzulí WHERE
	 */
	private function _createWhere(){
		$wheresString = null;

		if(!empty($this->_sqlQueryParts[self::SQL_WHERE])){
			$wheresString = self::SQL_SEPARATOR . self::SQL_WHERE;

			foreach ($this->_sqlQueryParts[self::SQL_WHERE] as $whereKey => $whereCondition){
				if($whereKey != 0){
					$wheresString .= $whereCondition[self::WHERE_CONDITION_OPERATOR_KEY];
				}

				$wheresString .= self::SQL_SEPARATOR . $whereCondition[self::WHERE_CONDITION_NAME_KEY] . self::SQL_SEPARATOR;
			}
		}

		return $wheresString;
	}

	/**
	 * Metoda vygeneruje část SQL dotazu s klauzulí ORDER BY
	 * @return string -- část SQL s kluzulí ORDER BY
	 */
	private function _createOrder(){
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
	private function _createGroup() {

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
	 * Metoda vygeneruje čás SQL dotazu s klauzulí LIMIT
	 *
	 * @return string -- klauzule LIMIT
	 */
	private function _createLimit() {

		if(!empty($this->_sqlQueryParts[self::SQL_LIMIT])){
			$limitString = null;
			$limitString = self::SQL_SEPARATOR . self::SQL_LIMIT . self::SQL_SEPARATOR . $this->_sqlQueryParts[self::SQL_LIMIT][self::LIMT_COUNT_ROWS_KEY]
			. ',' . self::SQL_SEPARATOR .$this->_sqlQueryParts[self::SQL_LIMIT][self::LIMT_OFFSET_KEY];

			return $limitString;
		}
	}


   /**
     * Metoda převede objekt na řetězec
     *
     * @return string -- objekt jako řetězec
     */
    public function __toString()
    {
        $sql = self::SQL_SELECT;

        foreach ($this->_sqlQueryParts as $partKey => $partValue) {
        	$createMethod = '_create' . ucfirst($partKey);
        	if(method_exists($this, $createMethod)){
        		$sql .= $this->$createMethod();
        	}
        	;
        }

//		echo "<pre>";
//		print_r($this);
//		echo "</pre>";
        return $sql;
    }
}

?>