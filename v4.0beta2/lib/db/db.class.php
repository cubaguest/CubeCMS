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
	 * statické proměné určující připojení k db
	 * @var string
	 */
	static $_serverName = null;
	static $_userName = null;
	static $_userPassword = null;
	static $_dbName = null;
	static $_tablePrefix = null;
	static $_connectorType = null;

   /**
    * Interní počítadlo příkazů
    * @var integer
    */
	static $_numberOfSqlQueries = 0;

   /**
    * Metoda pro sestavení spojení a základní nastavení db konektoru
    * @param string $typ -- typ spojení
    * @param string $serverName -- název serveru
    * @param string $userName -- jméno uživatele
    * @param string $userPasswd -- heslo pro připojení
    * @param string $dbName -- název databáze
    * @param string $tablePrefix -- prefix pro tabulky
    * @return Db Konektory k danému databázovému stroji
    */
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
}
?>