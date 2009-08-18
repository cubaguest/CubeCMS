<?php
/**
 * Třída obsluhuje db konektor k dabazázi, podle zvoleného typu vytvoří objekt 
 *
 * @package    	Action class
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro vytvoření db konektoru
 */

class Db {
   /**
    * Název sekce v konf. souboru s konfigurací databáze
    */
   const CONFIG_DB_SECTION = "db";

   /**
    * Operátor AND
    */
   const COND_OPERATOR_AND = 'AND';

   /**
    * Operátor OR
    */
   const COND_OPERATOR_OR = 'OR';

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
	 * statické proměné určující připojení k db
	 * @var string
	 */
	static $_serverName = null;
	static $_serverNameBackup = null;
	static $_userName = null;
	static $_userNameBackup = null;
	static $_userPassword = null;
	static $_userPasswordBackup = null;
	static $_dbName = null;
	static $_dbNameBackup = null;
	static $_tablePrefix = null;
	static $_connectorType = null;

   /**
    * Interní počítadlo příkazů
    * @var integer
    */
	static $_numberOfSqlQueries = 0;

   /**
    * Metoda pro sestavení spojení a základní nastavení db konektoru
    * @return Db Konektory k danému databázovému stroji
    */
	public static function factory() {
      self::$_serverName = AppCore::sysConfig()->getOptionValue("dbserver", self::CONFIG_DB_SECTION);
      self::$_serverNameBackup = AppCore::sysConfig()->getOptionValue("dbserverbackup", self::CONFIG_DB_SECTION);
		self::$_userName = AppCore::sysConfig()->getOptionValue("dbuser", self::CONFIG_DB_SECTION);
		self::$_userNameBackup = AppCore::sysConfig()->getOptionValue("dbuserbackup", self::CONFIG_DB_SECTION);
		self::$_userPassword = AppCore::sysConfig()->getOptionValue("dbpasswd", self::CONFIG_DB_SECTION);
		self::$_userPasswordBackup = AppCore::sysConfig()->getOptionValue("dbpasswdbackup", self::CONFIG_DB_SECTION);
		self::$_dbName = AppCore::sysConfig()->getOptionValue("dbname", self::CONFIG_DB_SECTION);
		self::$_dbNameBackup = AppCore::sysConfig()->getOptionValue("dbnamebackup", self::CONFIG_DB_SECTION);
		self::$_tablePrefix = AppCore::sysConfig()->getOptionValue("tbprefix", self::CONFIG_DB_SECTION);
		self::$_connectorType = AppCore::sysConfig()->getOptionValue("dbhandler", self::CONFIG_DB_SECTION);

      switch (self::$_connectorType) {
         case 'mysqli':
            require_once './lib/db/mysqli/db.class.php';
            return new MySQLiDb(self::$_serverName, self::$_userName, self::$_userPassword, self::$_dbName, self::$_tablePrefix);
            break;
         default:
            throw new UnexpectedValueException(sprintf(_('Databázový engine "%s" nebyl implementován'),$typ), 101);
            break;
      }
	}

   /**
    * Metoda přičte k internímu počítadlu jedna
    */
	public static function addQueryCount() {
		Db::$_numberOfSqlQueries++;
	}

   /**
    * metoda vrací počet provedených SQL dotazů
    * @return integer
    */
	public static function getCountQueries() {
		return Db::$_numberOfSqlQueries;
	}

   /**
    * Metoda vrátí upravený název tabulky
    * @param string $name -- název tabulky (nejčastěji konstanta)
    * @return string -- upravený název tabulky
    */
   public static function table($name) {
      return $name;
   }
}
?>