<?php
require_once './lib/db/db.interface.php';

require_once './lib/db/mysql/select.class.php';
require_once './lib/db/mysql/insert.class.php';
require_once './lib/db/mysql/delete.class.php';
require_once './lib/db/mysql/update.class.php';

class MySQLDb extends Db implements DbInterface {
	/**
	 * statické proměné určující připojení k db
	 * @var string
	 */
//	protected $_serverName = null;
//	protected $_userName = null;
//	protected $_userPassword = null;
//	protected $_dbName = null;
//	protected $_tablePrefix = null;

	/**
	 * Link k databázovému spojení
	 * @var Mysql link
	 */
	private $_mysqlLink = null;

	/**
	 * Objekt mysqli
	 * @var Mysqli
	 */
	private $_mysqliObject = null;

	/**
	 * Počet vrácených a ovlivněných záznamů záznamu
	 * @var integer
	 */
	private $_numberOfAfectedRows = null;
	private $_numberOfReturnRows = null;

	/**
	 * id posledního přidaného záznamu
	 * @var integer
	 */
	private $_lastInsertedId = null;

	/**
	 * Konstruktor třídy nastaví základní parametry
	 *
	 * @param string -- adresa serveru
	 * @param string -- uživatelské jméno
	 * @param string -- uživatelské heslo
	 * @param string -- název databáze
	 * @param string -- prefix tabulek (option)
	 */
	function __construct(){
//		$this->_serverName = $serverName;
//		$this->_userName = $userName;
//		$this->_userPassword = $userPassword;
//		$this->_dbName = $dbName;
//		$this->_tablePrefix = $tablePrefix;

		$this->_mysqliObject = new mysqli(parent::$_serverName, parent::$_userName, parent::$_userPassword, parent::$_dbName);
	}

	/**
	 * Metoda provede připojení k databázi a nastaví link na spojení
	 */
	private function _connect() {
//		if(!($this->_mysqlLink = mysqli_connect(parent::$_serverName, parent::$_userName, parent::$_userPassword))){
//				throw new CoreException("Cannot connect database host", 1);
//				throw new CoreException(mysqli_error($this->_mysqlLink), 1);
//		}
		if (mysqli_connect_errno()) {
			throw new CoreException(mysqli_connect_error(), 201);
		}
		
//		//TODO Nastaveni kódování
		$this->_mysqliObject->set_charset("utf8");

//		if(!mysqli_select_db($this->_mysqlLink, parent::$_dbName)){
//			throw new CoreException("Cannot select database", 2);
//		}
	}

	/**
	 * Metoda provede odpojení od MySQL serveru
	 * @deprecated
	 */
	private function _disconect() {
		if($this->_mysqliObject != null){
//			mysqli_close($this->_mysqlLink);
			$this->_mysqliObject->close();
		} else {
			throw new CoreException("Database server not connected", 203);
		}
	}

	/**
	 * Metoda nastaví výchozí hodnoty pro počítání záznamů
	 */
	private function _setDefault() {
		$this->_numberOfAfectedRows = null;
		$this->_numberOfReturnRows = null;
		$this->_lastInsertedId = null;
	}


	/**
	 * Metoda provede daný sql dotaz na databázi
	 *
	 * @param string -- sql dotaz
	 */
	public function query($sqlQuery) {
		$this->_connect();
		$this->_setDefault();

		$result = $this->_mysqliObject->query($sqlQuery);
		if($result != null){
			$queryType = strtolower(substr($sqlQuery, 0, 6));

			if ($queryType == "select"){
//				$this->_numberOfReturnRows=mysqli_num_rows($result);
				$this->_numberOfReturnRows=$result->num_rows;
			} else if($queryType == "insert"){
				$this->_lastInsertedId=$this->_mysqliObject->insert_id;
				$this->_numberOfAfectedRows = $this->_mysqliObject->affected_rows;
			} else if($queryType == "delete"){
				$this->_numberOfAfectedRows = $this->_mysqliObject->affected_rows;
			} else if($queryType == "update"){
				$this->_numberOfAfectedRows = $this->_mysqliObject->affected_rows;
			}
		} else {
			new CoreException($this->_mysqliObject->error, 204);
			$result = false;
		}

//		$this->_disconect();
		return $result;
	}

	/**
	 * Metoda vrací počet ovlivněných záznamů příkazy INSERT, UPDATE, DELETE, REPLACE
	 * @return integer -- počet ovlivněných záznamu
	 */
	public function getAffectedRows(){
		return $this->_numberOfAfectedRows;
	}

	/**
	 * Metoda vrací počet vrácených záznamů příkazy SELECT
	 * @return integer -- počet vrácených záznamu
	 */
	public function getNumRows(){
		return $this->_numberOfReturnRows;
	}

	/**
	 * Metoda vrací id posledníího vloženého záznamu pomocí INSERT
	 * @return integer -- id posledního záznamu
	 */
	public function getLastInsertedId() {
		return $this->_lastInsertedId;
	}



	/**
	 * Metoda pro generování SQL dotazů typu SELECT
	 *
	 * @return Mysql_Db_Select/string -- vrací objekt nebo řetězec dotazu SELECT
	 *
	 */
	public function select() {
		return new Mysql_Db_Select($this);
	}
	
	/**
	 * Metoda vrací počet záznamů v zadané tabulce
	 *
	 * @param string -- název tabulky
	 * @return integer -- počet záznamů v tabulce
	 */
	public function count($table)
	{
		$select = new Mysql_Db_Select($this);
		$sqlCount = $select->from($table)->count("count");
		$result = $this->query($sqlCount);
		
		return $result->fetch_object()->count;
	}
	
	
	/**
	 * Metoda pro generování SQL dotazů typu INSERT
	 *
	 * @return Mysql_Db_Insert/string -- vrací objekt nebo řetězec dotazu INSERT
	 *
	 */
	public function insert() {
		return new Mysql_Db_Insert($this);
	}
	
	/**
	 * Metoda pro generování SQL dotazů typu DELETE
	 *
	 * @return Mysql_Db_Delete/string -- vrací objekt nebo řetězec dotazu DELETE
	 *
	 */
	public function delete() {
		return new Mysql_Db_Delete($this);
	}

	/**
	 * Metoda pro generování SQL dotazů typu UPDATE
	 *
	 * @return Mysql_Db_Update/string -- vrací objekt nebo řetězec dotazu UPDATE
	 *
	 */
	public function update() {
		return new Mysql_Db_Update($this);
	}
	
	

	/**
	 * Metoda provede sql dotaz a výstup doplní do asociativního pole
	 *
	 * @param string -- SQL dotaz
	 * @return array/boolean -- asociativní pole s výsledky sql dotazu nebo false při prázdném výsledku
	 */
	public function fetchAssoc($sqlQuery) {

		$queryResult = $this->query($sqlQuery);

		if($queryResult){
			$resultArray = array();
			while ($sqlData=$queryResult->fetch_assoc()) {
				array_push($resultArray, $sqlData);
			}

			return $resultArray;
		} else {
			return false;
		}
	}

	/**
	 * Metoda provede sql dotaz a výstup přiřadí do pole objektů mysqli
	 *
	 * @param string -- SQL dotaz
	 * @return array -- pole objektů MySQLi_Result
	 */
	public function fetchObjectArray($sqlQuery) {

		$queryResult = $this->query($sqlQuery);

		if($queryResult){
			$resultArray = array();
			while ($sqlObject=$queryResult->fetch_object()) {
				array_push($resultArray, $sqlObject);
			}

			return $resultArray;
		} else {
			return false;
		}
	}


}

?>