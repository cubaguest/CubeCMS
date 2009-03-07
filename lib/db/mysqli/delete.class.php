<?php
require_once './lib/db/delete.class.php';
/**
 * Třída pro odstraňování záznamů v MySQL DB.
 * Třída obsahuje implementaci metody delete z db.interfacu.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: delete.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro mazání záznamů
 */

class Mysqli_Db_Delete extends Db_Delete {
	/**
	 * Konstanty pro příkazy SQL
	 * @var string
	 */
	const SQL_DELETE     		= 'DELETE';
    const SQL_FROM       		= 'FROM';
    const SQL_WHERE      		= 'WHERE';
    const SQL_ORDER_BY   		= 'ORDER BY';
    const SQL_AND        		= 'AND';
    const SQL_OR        		= 'OR';
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
    const COLUMS_ARRAY 					= 'COLUMS';
    const WHERE_CONDITION_NAME_KEY 		= 'condition';
    const WHERE_CONDITION_OPERATOR_KEY 	= 'operator';
    const ORDER_ORDER_KEY				= 'ORDER';
    const ORDER_COLUM_KEY				= 'colum';
    const LIMT_COUNT_ROWS_KEY			= 'limit_count';
    const LIMT_OFFSET_KEY				= 'limit_offset';
    
    /**
     * Konstanta určující obsah SQL dotazu
     * Musí mít správné pořadí, jak se má SQL dotaz řadit!!!
     *
     */
	protected static $_sqlPartsInit = array(
//											self::COLUMS_ARRAY			=> array(),
								 			self::SQL_FROM				=> array(),
								 			self::SQL_WHERE				=> array(),
								 			self::ORDER_ORDER_KEY		=> array(),
								 			self::SQL_LIMIT				=> array());

	/**
	 * Pole s zadávanými částmi SQL dotazu
	 * @var array
	 */							 			
	protected $_sqlQueryParts = array();

	/**
	 * Objet Db konektoru
	 *
	 * @var Db
	 */	
	protected $_connector = null;

	/**
	 * Konstruktor nastaví základní parametry a odkaz na dbkonektor
	 *
	 * @param Db -- konektor databáze
	 */
	public function __construct(Db $conector) {
//		inicializace do zakladni podoby;
		$this->_connector = $conector;
		$this->_sqlQueryParts = self::$_sqlPartsInit;
	}

	/**
	 * Metoda nastavuje z které tabulky se bude mazat
	 * klauzule FROM
	 *
	 * @param string/array -- tabulka ze které se bude vybírat, u pole index označuje alias tabulky
	 * 
	 * @return Db_Delete -- objekt Db_Delete
	 */
	public function from($table, $columsArray = null) {
		$this->_sqlQueryParts[self::SQL_FROM] = $table;
		return $this;
	}

	/**
	 * Metody vatváří podmínku WHERE
	 *
	 * @param string -- podmínka
	 * @param string -- typ spojení podmínky (AND, OR) (výchozí je AND)
	 * 
	 * @return Db_Delete -- objekt Db_Delete
	 * @todo dodělat aby se doplňovali magické uvozovky do lauzule
	 */
	public function where($condition, $operator = self::SQL_AND) {
		$tmpArray = array();

		if($operator != self::SQL_AND AND $operator != self::SQL_OR){
			$operator = self::SQL_AND;
			echo $operator;
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
	 * @return Db_Delete -- objekt Db_Delete
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
	 * @return Db_Delete -- objekt Db_Delete
	 */
	public function limit($rowCount, $offset) {
		$this->_sqlQueryParts[self::SQL_LIMIT][self::LIMT_COUNT_ROWS_KEY] = $rowCount;
		$this->_sqlQueryParts[self::SQL_LIMIT][self::LIMT_OFFSET_KEY] = $offset;

		return $this;
	}

	/**
	 * Metoda vytváří část dotazu sek FROM
	 * @return string -- část SQL dotazu s částí FROM
	 */
	private function _createFrom() {
		$fromString = null;
		$fromString = self::SQL_SEPARATOR.self::SQL_FROM;
		
		$fromString .= self::SQL_SEPARATOR . MySQLiDb::$_tablePrefix . $this->_sqlQueryParts[self::SQL_FROM] . self::SQL_SEPARATOR;
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
        $sql = self::SQL_DELETE;
        foreach ($this->_sqlQueryParts as $partKey => $partValue) {
        	$createMethod = '_create' . ucfirst($partKey);
        	if(method_exists($this, $createMethod)){
        		$sql .= $this->$createMethod();
        	}
        }
        return $sql;
    }
}
?>