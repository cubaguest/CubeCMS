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

		if($typ == "mysql"){
			require_once './lib/db/mysql/db.class.php';

			return new MySQLDb(self::$_serverName, self::$_userName, self::$_userPassword, self::$_dbName, self::$_tablePrefix);
		} else {
			return false;
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