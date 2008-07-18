<?php
require_once './lib/db/insert.class.php';

class Mysql_Db_Insert extends Db_Insert {
	/**
	 * Konstanty pro příkazy SQL
	 * @var string
	 */
	const SQL_INSERT     		= 'INSERT';
    const SQL_INTO       		= 'INTO';
    const SQL_VALUES      		= 'VALUES';
    const SQL_SEPARATOR	 		= ' ';
    const SQL_PARENTHESIS_L	 	= '(';
    const SQL_PARENTHESIS_R	 	= ')';
    const SQL_VALUE_SEPARATOR	= ',';

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
	protected static $_sqlPartsInit = array(self::SQL_TABLE				=> null,
											self::COLUMS_ARRAY			=> array(),
								 			self::VALUES_ARRAY			=> array());

	/**
	 * Pole s zadávanými částmi SQL dotazu
	 * @var array
	 */							 			
	protected $_sqlQueryParts = array();

	/**
	 * Proměná s počtem sloupců/hodnot
	 * @var integer
	 */
	protected $_numberOfColums = null;
	
	protected $_connector = null;

//	public function __construct(MySQLDb $connector) {
	public function __construct(Db $conector) {
//		inicializace do zakladni podoby;
		$this->_connector = $conector;
		$this->_sqlQueryParts = self::$_sqlPartsInit;
	}

	/**
	 * Metoda nastavuje do které tabulky se bude zapisovat
	 * klauzule INTO
	 *
	 * @param string -- tabulka do které se bude zapisovat
	 */
	public function into($table) {
		$this->_sqlQueryParts[self::SQL_TABLE] = $table;
		return $this;
	}

	/**
	 * Metody vatváří sloupce, které se budou zapisovat 
	 *
	 * @param string -- sloupce
	 * @param string -- sloupce (neomezený počet parametrů)
	 */
	public function colums() {
		$this->_numberOfColums = func_num_args();
		
		$arrayColums = func_get_args();
		//Doplnění pole se sloupcy do pole sloupců
		$this->_sqlQueryParts[self::COLUMS_ARRAY] = $arrayColums;

		return $this;
	}

	/**
	 * Metoda přiřadí hodnoty sloupscům
	 *
	 * @param string -- hodnota sloupce (proměnný počet parametrů)
	 */
	public function values() {
		//TODO možná vyřešit naplňováním null na nezadané sloupce
		if(func_num_args() != $this->_numberOfColums){
			new CoreException(_("Nesouhlasí počet zadaných sloupců a hodnot"));
		} else {
			$valuesArray = func_get_args();
			array_push($this->_sqlQueryParts[self::VALUES_ARRAY], $valuesArray);
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

	/**
	 * Metoda vytváří část dotazu sek FROM
	 * @return string -- část SQL dotazu s částí FROM
	 */
	private function _createTable() {
		$intoString = null;
		$intoString = self::SQL_SEPARATOR.self::SQL_INTO.self::SQL_SEPARATOR."`".MySQLDb::$_tablePrefix.$this->_sqlQueryParts[self::SQL_TABLE]."`";

		return $intoString;
	}

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
				$valuesString.=self::SQL_SEPARATOR."'".$value.self::SQL_SEPARATOR."'".self::SQL_VALUE_SEPARATOR;
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
    public function __toString()
    {
        $sql = self::SQL_INSERT;

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