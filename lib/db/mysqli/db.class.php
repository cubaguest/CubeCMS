<?php
require_once './lib/db/db.interface.php';
require_once './lib/db/mysqli/query.class.php';
require_once './lib/db/mysqli/select.class.php';
require_once './lib/db/mysqli/insert.class.php';
require_once './lib/db/mysqli/delete.class.php';
require_once './lib/db/mysqli/update.class.php';

/**
 * Třída implementující databázový objekt typu MySQLi.
 * Třída implementuje objet db konektoru k MySQLi. Obsahuje implementace
 * metod pro práci s SQL dotazy a samotné připojování k databázi.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.2 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro MySQL DB
 */

class MySQLiDb extends Db implements DbInterface {
	/**
	 * statické pole se specílními SQL funkcemi
	 * @var array
    * @deprecated
	 */
	public static $specialSqlFunctions = array("NOW", "TIMESTAMPDIFF", "COUNT", "IFNULL", "IF");
	
	/**
	 * Link k databázovému spojení
	 * @var Mysql link
	 */
//	private $_mysqlLink = null;

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
	
	/**
	 * Počet vrácených záznamů naposledy provedeným dotazem
	 *
	 * @var integer
	 */
	private $_numberOfReturnRows = null;

	/**
	 * id posledního přidaného záznamu
	 * @var integer
	 */
	private $_lastInsertedId = null;

   /**
    * Předchozí SQl dotaz
    * @var string
    */
   private $_previousSqlQuery = null;

   /**
    * Předcho SQl result
    * @var MySQLi
    */
   private $_previousResult = null;

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
		$this->_mysqliObject = new mysqli(parent::$_serverName, parent::$_userName, parent::$_userPassword, parent::$_dbName);
	}

	/**
	 * Metoda provede připojení k databázi a nastaví link na spojení
	 * @todo dodělat nastavení kódování
	 */
	private function _connect() {
      try {
         if (mysqli_connect_errno()) {
            throw new DBException(mysqli_connect_error(), 102);
         }
      } catch (DBException $e) {
         new CoreErrors($e);
      }
		$this->_mysqliObject->set_charset("utf8");
	}

	/**
	 * Metoda provede odpojení od MySQL serveru
	 * @deprecated
	 */
	private function _disconect() {
      try {
         if($this->_mysqliObject == null){
            throw new DBException(_('Databázový server není připojen'), 103);
         }
         $this->_mysqliObject->close();
      } catch (DBException $e) {
         new CoreErrors($e);
      }
//      if($this->_mysqliObject != null){
//			$this->_mysqliObject->close();
//		} else {
//			throw new CoreException("Database server not connected", 203);
//		}
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
      if($sqlQuery != $this->_previousSqlQuery){
         $this->_previousSqlQuery = $sqlQuery;

         $this->_connect();
         $this->_setDefault();

         Db::addQueryCount();
         $result = $this->_mysqliObject->query($sqlQuery);

         try {
            if(!$result){
               throw new DBException('('.$this->_mysqliObject->errno.') '.$this->_mysqliObject->error
                  ._(' v dotazu ').$sqlQuery, 104);
            }

            $queryType = strtolower(substr($sqlQuery, 0, 6));
            if ($queryType == "select"){
               $this->_numberOfReturnRows=$result->num_rows;
            } else if($queryType == "insert"){
               $this->_lastInsertedId=$this->_mysqliObject->insert_id;
               $this->_numberOfAfectedRows = $this->_mysqliObject->affected_rows;
            } else if($queryType == "delete"){
               $this->_numberOfAfectedRows = $this->_mysqliObject->affected_rows;
            } else if($queryType == "update"){
               $this->_numberOfAfectedRows = $this->_mysqliObject->affected_rows;
            }
         } catch (DBException $e) {
            new CoreErrors($e);
         }
         $this->_previousResult = $result;
         return $result;
      } else {
         return $this->_previousResult;
      }
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
		return new Mysqli_Db_Select($this);
	}
	
	/**
	 * Metoda vrací počet záznamů v zadané tabulce
	 *
	 * @param string -- název tabulky
	 * @param string -- podmínka
	 * @return integer -- počet záznamů v tabulce
	 */
	public function count($table,$condition = null){
		$select = new Mysqli_Db_Select($this);
		$sqlCount = $select->from(array("tbl"=>$table))->count("count");
		
		if($condition != null){
			$sqlCount = $sqlCount->where($condition);
		}
		$result = $this->query($sqlCount);
		
		if($result != null){
			return $result->fetch_object()->count;
		} else {
			return 0;
		}
	}
	
	/**
	 * Metoda pro generování SQL dotazů typu INSERT
	 *
	 * @return Mysql_Db_Insert/string -- vrací objekt nebo řetězec dotazu INSERT
	 *
	 */
	public function insert() {
		return new Mysqli_Db_Insert($this);
	}
	
	/**
	 * Metoda pro generování SQL dotazů typu DELETE
	 *
	 * @return Mysql_Db_Delete/string -- vrací objekt nebo řetězec dotazu DELETE
	 *
	 */
	public function delete() {
		return new Mysqli_Db_Delete($this);
	}

	/**
	 * Metoda pro generování SQL dotazů typu UPDATE
	 *
	 * @return Mysql_Db_Update/string -- vrací objekt nebo řetězec dotazu UPDATE
	 *
	 */
	public function update() {
		return new Mysqli_Db_Update($this);
	}

//   public function fetch($sqlQuery, $typeResult = MYSQLI_NUM) {
//      $queryResult = $this->query($sqlQuery);
//
////      return $queryResult->fetch_all($typeResult);
//return mysqli;
//   }

	/**
	 * Metoda provede sql dotaz a výstup doplní do asociativního pole v numerickém
    * poli podle záznamů
	 *
	 * @param string -- SQL dotaz
	 * @return array/boolean -- asociativní/numerické pole s výsledky sql dotazu
    * nebo false při prázdném výsledku
	 */
	public function fetchAll($sqlQuery) {
      $result = $this->query($sqlQuery);

      if($result){
         $resultArray = array();
         while ($sqlData=$result->fetch_assoc()) {
            array_push($resultArray, $sqlData);
         }
         //          $this->fetch($sqlQuery, MYSQLI_ASSOC); METODA V PHP 5.3.0
         return $resultArray;
      } else {
         return false;
      }
	}

	/**
	 * Metoda provede sql dotaz a výstup doplní do asociativního pole (např. do while)
	 *
	 * @param string -- SQL dotaz
	 * @return array/boolean -- asociativní pole s výsledky sql dotazu nebo false při prázdném výsledku
	 */
	public function fetchAssoc($sqlQuery) {
//      try {
         $result = $this->query($sqlQuery);
//      } catch (DbException $e) {
//         CoreErrors($e);
//      }
      if($result){
         return $result->fetch_assoc();
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
      $result = $this->query($sqlQuery);

      if($result){
         $resultArray = array();
         while ($sqlObject=$result->fetch_object()) {
            array_push($resultArray, $sqlObject);
         }
         return $resultArray;
      } else {
         return false;
      }
	}

	/**
	 * Metoda vrací řádek z db jako objekt
	 * @param string -- sql dotaz
	 * @return MySQLi_Result -- objekt s prvky z db
	 */
	public function fetchObject($sqlQuery){
      $result = $this->query($sqlQuery);
      if($result){
         return $result->fetch_object();
      } else {
         return false;
      }
	}

   /**
    * Metoda zakóduje řetězec pro použití v mysql (SQL injection)
    * @param string $string -- řetězec určený k zakódování
    */
   public function escapeString($string) {
      return $this->_mysqliObject->real_escape_string($string);
   }
}
?>