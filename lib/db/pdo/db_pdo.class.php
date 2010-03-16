<?php
/**
 * Třída obsluhuje db konektor k dabazázi, podle zvoleného typu vytvoří objekt
 *
 * @package    	Action class
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: db.class.php 582 2009-04-20 11:17:17Z jakub $ VVE3.9.4 $Revision: 582 $
 * @author        $Author: jakub $ $Date: 2009-04-20 11:17:17 +0000 (Po, 20 dub 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-04-20 11:17:17 +0000 (Po, 20 dub 2009) $
 * @abstract 		Třída pro vytvoření db konektoru
 */

class Db_PDO extends PDO {
   /**
    * Název sekce v konf. souboru s konfigurací databáze
    */
   const CONFIG_DB_SECTION = "db";
   /**
    * statické proměné určující připojení k db
    * @var string
    */
   private static $serverName = null;
   private static $userName = null;
   private static $userPassword = null;
   private static $dbName = null;
   private static $tablePrefix = null;
   private static $connectorType = null;

   /**
    * Interní počítadlo příkazů
    * @var integer
    */
   static $numberOfSqlQueries = 0;


   public function  __construct() {
      try {
         if(func_num_args() == 0) {
            switch (self::$connectorType) {
               case 'mysqli':
               //(self::$_serverName, self::$_userName, self::$_userPassword, self::$_dbName, self::$_tablePrefix);
                  parent::__construct("mysql:host=".self::$serverName.";dbname=".self::$dbName,
                          self::$userName, self::$userPassword);
                  $this->exec('SET CHARACTER SET utf8');
                  $this->exec('SET character_set_connection = utf8;');
                  break;
               case 'pgsql':
                  parent::__construct("pgsql:dbname=".self::$dbName.";host=".self::$serverName,
                          self::$userName,self::$userPassword);
                  break;
               case 'sqllite':
                  parent::__construct("sqlite:".self::$dbName);
                  break;
               default:
                  throw new PDOException(sprintf(_('Databázový engine "%s" není v PDO podporován'),self::$connectorType), 101);
                  break;
            }

         }
         else {
            // TODO dodělat vytváření podle zadaných hodnot
         }
         $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
         new CoreErrors($e);
      }
   }

   /**
    * Metoda pro sestavení spojení a základní nastavení db konektoru
    * @return Db Konektory k danému databázovému stroji
    */
   public static function factory() {
//      self::$serverName = AppCore::sysConfig()->getOptionValue("dbserver", self::CONFIG_DB_SECTION);
//      self::$userName = AppCore::sysConfig()->getOptionValue("dbuser", self::CONFIG_DB_SECTION);
//      self::$userPassword = AppCore::sysConfig()->getOptionValue("dbpasswd", self::CONFIG_DB_SECTION);
//      self::$dbName = AppCore::sysConfig()->getOptionValue("dbname", self::CONFIG_DB_SECTION);
//      self::$tablePrefix = AppCore::sysConfig()->getOptionValue("tbprefix", self::CONFIG_DB_SECTION);
//      self::$connectorType = AppCore::sysConfig()->getOptionValue("dbhandler", self::CONFIG_DB_SECTION);
      self::$serverName = VVE_DB_SERVER;
      self::$userName = VVE_DB_USER;
      self::$userPassword = VVE_DB_PASSWD;
      self::$dbName = VVE_DB_NAME;
      self::$tablePrefix = VVE_DB_PREFIX;
      self::$connectorType = VVE_DB_TYPE;
   }

   /**
    * Metoda přičte k internímu počítadlu jedna
    */
   public static function addQueryCount() {
      Db_PDO::$numberOfSqlQueries++;
   }

   /**
    * metoda vrací počet provedených SQL dotazů
    * @return integer
    */
   public static function getCountQueries() {
      return Db_PDO::$numberOfSqlQueries;
   }

   /**
    * Metoda vrátí upravený název tabulky
    * @param string $name -- název tabulky (nejčastěji konstanta)
    * @return string -- upravený název tabulky
    */
   public static function table($name) {
      return self::$tablePrefix.$name;
   }
}
?>