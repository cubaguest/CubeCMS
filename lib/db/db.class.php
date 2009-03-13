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
    * Konstanty pro tvorbu dotazů, které jsou globální pro všechny DB
    */
   const SQL_AND     = 'AND';
   const SQL_OR      = 'OR';
   const SQL_IN      = 'IN';
   const SQL_LIKE    = 'LIKE';
   const SQL_ALL     = '*';
   const SQL_ASC     = 'ASC';
   const SQL_DESC    = 'DESC';
   const SQL_NULL    = 'NULL';

    /**
     * Konstanty typů joinu
     * @var string
     */
   const SQL_JOIN			= 'JOIN';
   const SQL_JOIN_LEFT 	= 'LEFT JOIN';
   const SQL_JOIN_RIGHT	= 'RIGHT JOIN';
   const SQL_JOIN_INNER	= 'INNER JOIN';

   /**
    * Typ podmínky ON pro JOIN
    */
   const SQL_JOIN_COND_TYPE_ON = 'ON';

   /**
    * Typ podmínky USING pro JOIN
    */
   const SQL_JOIN_COND_TYPE_USING = 'USING';


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