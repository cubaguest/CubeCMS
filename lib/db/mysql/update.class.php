<?php
require_once './lib/db/update.class.php';

class Mysql_Db_Update extends Db_Update{
	/**
	 * Konstanty pro příkazy SQL
	 * @var string
	 */
	const SQL_UPDATE     		= 'UPDATE';
    const SQL_SET       		= 'SET';
    const SQL_WHERE      		= 'WHERE';
    const SQL_ORDER_BY   		= 'ORDER BY';
    const SQL_AND        		= 'AND';
    const SQL_OR         		= 'OR';
    const SQL_ASC        		= 'ASC';
    const SQL_DESC       		= 'DESC';
    const SQL_LIMIT		 		= 'LIMIT';
    const SQL_SEPARATOR	 		= ' ';
    const SQL_PARENTHESIS_L	 	= '(';
    const SQL_PARENTHESIS_R	 	= ')';
    const SQL_VALUE_SEPARATOR	= ',';


    /**
     * Konstanty pro ukládání do pole SQL dotazu
     * @var string
     */
    const COLUMS_ARRAY 					= 'SET';
    const WHERE_CONDITION_NAME_KEY 		= 'condition';
    const WHERE_CONDITION_OPERATOR_KEY 	= 'operator';
    const ORDER_ORDER_KEY				= 'ORDER';
    const ORDER_COLUM_KEY				= 'colum';
    const LIMT_COUNT_ROWS_KEY			= 'limit_count';
    const LIMT_OFFSET_KEY				= 'limit_offset';
    const TABLE							= 'TABLE';

    /**
     * Konstanta určující obsah SQL dotazu
     * Musí mít správné pořadí, jak se má SQL dotaz řadit!!!
     *
     */
	protected static $_sqlPartsInit = array(self::TABLE					=> array(),
											self::COLUMS_ARRAY			=> array(),
								 			self::SQL_WHERE				=> array(),
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
	 * Metoda nastavuje v které tabulce se bude upravovat
	 * klauzule UPDATE table
	 *
	 * @param string -- tabulka která se bude upravovat
	 * 
	 * @return Db_Update -- objekt Db_Update
	 */
	public function table($table) {
		
		$this->_sqlQueryParts[ self::TABLE] = $table;

		return $this;
	}

	/**
	 * Metoda nastavuje, které hodnoty se upraví
	 * (název sloupce) => (hodnota)
	 *
	 * @param array -- pole s hodnotamik
	 * 
	 * @return Db_Update -- objekt Db_Update
	 */
	public function set($values)
	{
		foreach ($values as $key => $value){
			$this->_sqlQueryParts[self::COLUMS_ARRAY][$key] = $value;
		}
		
		return $this;
	}
	
	
	/**
	 * Metody vatváří podmínku WHERE
	 *
	 * @param string -- podmínka
	 * @param string -- typ spojení podmínky (AND, OR) (výchozí je AND)
	 * 
	 * @return Db_Update -- objekt Db_Update
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
	 * Metoda přiřadí řazení sloupcu v SQL dotazu
	 *
	 * @param string -- sloupec, podle kterého se má řadit
	 * @param string -- (option) jak se má sloupec řadit (ASC, DESC) (default: ASC)
	 * 
	 * @return Db_Update -- objekt Db_Update
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
	 * Metoda přidá do SQL dotazu klauzuli LIMIT
	 * @param integer -- počet záznamů
	 * @param integer -- záčátek
	 * 
	 * @return Db_Update -- objekt Db_Update
	 */
	public function limit($rowCount, $offset) {
		$this->_sqlQueryParts[self::SQL_LIMIT][self::LIMT_COUNT_ROWS_KEY] = $rowCount;
		$this->_sqlQueryParts[self::SQL_LIMIT][self::LIMT_OFFSET_KEY] = $offset;

		return $this;
	}


	/**
	 * Metody vytvoří část SQL dotazu se sloupcy, které se mají vybírat
	 * @return string -- část SQL dotazu se sloupci
	 */
	private function _createSet() {
		$columsString = self::SQL_SEPARATOR.self::SQL_SET;

		if(!empty($this->_sqlQueryParts[self::COLUMS_ARRAY])){
			foreach ($this->_sqlQueryParts[self::COLUMS_ARRAY] as $colum => $value) {
				if($value != null){
					$columsString.=self::SQL_SEPARATOR."`".$colum."`= '".$value."'".self::SQL_VALUE_SEPARATOR;
				} else {
					$columsString.=self::SQL_SEPARATOR."`".$colum."`= null".self::SQL_VALUE_SEPARATOR;
				}
			}
//			odstranění poslední čárky
			$columsString = substr($columsString, 0, strlen($columsString)-1);

		}

		return $columsString;
	}

	/**
	 * Metoda vytváří část dotazu sek FROM
	 * @return string -- část SQL dotazu s částí FROM
	 */
	private function _createTable() {
		$fromString = null;
		$fromString .= self::SQL_SEPARATOR . MySQLDb::$_tablePrefix . $this->_sqlQueryParts[self::TABLE];

		return $fromString;
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
        $sql = self::SQL_UPDATE;

        foreach ($this->_sqlQueryParts as $partKey => $partValue) {
        	$createMethod = '_create' . ucfirst($partKey);
        	if(method_exists($this, $createMethod)){
        		$sql .= $this->$createMethod();
        	}
        }

//		echo "<pre>";
//		print_r($this);
//		echo "</pre>";
        return $sql;
    }
}

?>