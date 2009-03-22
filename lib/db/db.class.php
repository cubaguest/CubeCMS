<?php
/**
 * Třída obsluhuje db konektor k dabazázi, podle zvoleného typu vytvoří objekt 
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Action class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: db.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro vytvoření db konektoru
 */

class Db {
   /**
    * Operátor AND
    */
   const COND_OPERATOR_AND = 1;

   /**
    * Operátor OR
    */
   const COND_OPERATOR_OR = 2;

   /**
    * Operátor IS NOT NULL
    */
   const OPERATOR_IS_NOT_NULL = 1;

   /**
    * Oparátor LIKE
    */
   const OPERATOR_LIKE = 2;

   /**
    * Oparátor NOT LIKE
    */
   const OPERATOR_NOT_LIKE = 3;

   /**
    * Operátor BETWEEN
    */
   const OPERATOR_BETWEEN = 4;

   /**
    * Operátor NOT BETWEEN
    */
   const OPERATOR_NOT_BETWEEN = 5;

   /**
    * Operátor IN
    */
   const OPERATOR_IN = 6;
   
   /**
    * Operátor IS NULL
    */
   const OPERATOR_IS_NULL = 7;

   /**
    * Spojení JOIN bez omezení
    */
   const JOIN_NORMAL = 1;

   /**
    * Spojení JOIN LEFT
    */
   const JOIN_LEFT = 2;

   /**
    * Sojení JOIN RIGHT
    */
   const JOIN_RIGHT = 3;

   /**
    * Spojení JOIN INNER
    */
   const JOIN_INNER = 4;

   /**
    * Spojení JOIN CROSS
    */
   const JOIN_CROSS = 5;

   /**
    * Operátor spojení JOIN ON
    */
   const JOIN_OPERATOR_ON = 1;

   /**
    * Operátor spojení JOIN USING
    */
   const JOIN_OPERATOR_USING = 2;

   /**
    * Konstanta pro výběr všech sloupců
    */
   const COLUMN_ALL = '*';

   /**
    * Konstanta pro řazení ASC
    */
   const ORDER_ASC = 1;

   /**
    * Konstanta pro řazení DESC
    */
   const ORDER_DESC = 2;

   /**
    * Konstanty pro tvorbu dotazů, které jsou globální pro všechny DB
    */
//   const SQL_AND     = 'AND';
//   const SQL_OR      = 'OR';
//   const SQL_IN      = 'IN';
//   const SQL_LIKE    = 'LIKE';
//   const SQL_ALL     = '*';
//   const SQL_ASC     = 'ASC';
//   const SQL_DESC    = 'DESC';
//   const SQL_NULL    = 'NULL';

    /**
     * Konstanty typů joinu
     * @var string
     */
//   const SQL_JOIN			= 'JOIN';
//   const SQL_JOIN_LEFT 	= 'LEFT JOIN';
//   const SQL_JOIN_RIGHT	= 'RIGHT JOIN';
//   const SQL_JOIN_INNER	= 'INNER JOIN';

   /**
    * Typ podmínky ON pro JOIN
    */
//   const SQL_JOIN_COND_TYPE_ON = 'ON';

   /**
    * Typ podmínky USING pro JOIN
    */
//   const SQL_JOIN_COND_TYPE_USING = 'USING';


	/**
	 * statické proměné určující připojení k db
	 * @var string
	 */
	static $_serverName = null;
	static $_userName = null;
	static $_userPassword = null;
	static $_dbName = null;
	static $_tablePrefix = null;
	static $_connectorType = null;

	static $_numberOfSqlQueries = 0;

	public static function factory($typ, $serverName, $userName, $userPasswd, $dbName, $tablePrefix) {
		self::$_serverName = $serverName;
		self::$_userName = $userName;
		self::$_userPassword = $userPasswd;
		self::$_dbName = $dbName;
		self::$_tablePrefix = $tablePrefix;
		self::$_connectorType = $typ;

      switch ($typ) {
         case 'mysqli':
            require_once './lib/db/mysqli/db.class.php';
            return new MySQLiDb(self::$_serverName, self::$_userName, self::$_userPassword, self::$_dbName, self::$_tablePrefix);
            break;

         default:
            throw new UnexpectedValueException(_("Databázový engine ").$typ._(" nebyl implementován"), 101);
            break;
      }
	}
	
	public static function addQueryCount() {
		Db::$_numberOfSqlQueries++;
	}
	
	public static function getCountQueries() {
		return Db::$_numberOfSqlQueries;
	}
	
	

}
?>