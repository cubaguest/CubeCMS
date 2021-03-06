<?php
/**
 * Třída obsluhuje db konektor k dabazázi, podle zvoleného typu vytvoří objekt
 *
 * @package       Action class
 * @copyright     Copyright (c) 2008-2009 Jakub Matas
 * @version       $Id: db.class.php 582 2009-04-20 11:17:17Z jakub $ VVE3.9.4 $Revision: 582 $
 * @author        $Author: jakub $ $Date: 2009-04-20 11:17:17 +0000 (Po, 20 dub 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-04-20 11:17:17 +0000 (Po, 20 dub 2009) $
 * @abstract      Třída pro vytvoření db konektoru
 */

include_once dirname(__FILE__).DIRECTORY_SEPARATOR."db_pdo_statement.class.php" ;

class Db_PDO extends PDO {
   /**
    * statické proměné určující připojení k db
    * @var string
    */
   private static $serverName;
   private static $serverPort = null;
   private static $userName;
   private static $userPassword;
   private static $dbName;
   private static $tablePrefix;
   private static $connectorType;

   /**
    * Interní počítadlo dotazů
    * @var integer
    */
   protected static $numberOfSqlQueries = 0;

   protected static $instance;

   public function  __construct() {
      try {
         if(func_num_args() == 0) { // default connector
            switch (self::$connectorType) {
               case 'mysqli':
                  parent::__construct("mysql:host=".self::$serverName.";".(self::$serverPort ? 'port='.self::$serverPort.';' : '')."dbname=".self::$dbName,
                     self::$userName, self::$userPassword, array(
                        PDO::ATTR_PERSISTENT => false,
                        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                        PDO::MYSQL_ATTR_INIT_COMMAND => 
                           CUBE_CMS_DEBUG_SQL 
                           ? "SET NAMES utf8;"
                           : "SET NAMES utf8, SESSION sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';"
                       
                     ));
                  break;
               case 'pgsql':
                  parent::__construct("pgsql:dbname=".self::$dbName.";host=".self::$serverName,
                     self::$userName,self::$userPassword);
                  break;
               case 'sqllite':
                  parent::__construct("sqlite:".self::$dbName);
                  break;
               default:
                  throw new PDOException(sprintf('Databázový engine "%s" není v PDO podporován',self::$connectorType), 101);
                  break;
            }
            self::$instance = $this;
         } else {
            $args = func_get_args();
            $reflector = new ReflectionClass(get_class($this));
            $parent = $reflector->getParentClass();
            $method = $parent->getMethod('__construct');
            $method->invokeArgs($this, $args);
         }
         $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
         $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('Db_PDO_Statement', array($this)));
      } catch (PDOException $e) {
         throw new PDOException("Nelze se připojit k databázi nebo připojení neproběhlo korektně. \n"
            ."Prosíme zkuste to za chvíli znovu. \nPokud i přesto se stránku nepodaří načíst kontaktujte webmastera.");
      }
   }

   public function query($statement, $pdoFetch = PDO::FETCH_OBJ , $classname = null , $ctorargs = array()){
      self::$numberOfSqlQueries++;
      $args = func_get_args();
      return call_user_func_array(array($this, "parent::query"), $args);
   }

   public function exec($statement){
      self::$numberOfSqlQueries++;
      $args = func_get_args();
      return call_user_func_array(array($this, "parent::exec"), $args);
   }

   /**
    * Metoda pro sestavení spojení a základní nastavení db konektoru
    * @return Db Konektory k danému databázovému stroji
    */
   public static function factory() {
      self::$serverName = defined('CUBE_CMS_DB_SERVER') ? CUBE_CMS_DB_SERVER : VVE_DB_SERVER;
      self::$serverPort = defined('CUBE_CMS_DB_SERVER_PORT') ? CUBE_CMS_DB_SERVER_PORT : (defined("VVE_DB_SERVER_PORT") ? VVE_DB_SERVER_PORT : self::$serverPort);
      self::$userName = defined('CUBE_CMS_DB_USER') ? CUBE_CMS_DB_USER : VVE_DB_USER;
      self::$userPassword = defined('CUBE_CMS_DB_PASSWD') ? CUBE_CMS_DB_PASSWD : VVE_DB_PASSWD;
      self::$dbName = defined('CUBE_CMS_DB_NAME') ? CUBE_CMS_DB_NAME : VVE_DB_NAME;
      self::$tablePrefix = defined('CUBE_CMS_DB_PREFIX') ? CUBE_CMS_DB_PREFIX : VVE_DB_PREFIX;
      self::$connectorType = defined('CUBE_CMS_DB_TYPE') ? CUBE_CMS_DB_TYPE : VVE_DB_TYPE;
      if (!isset(self::$instance) || self::$instance == null)
      {
         self::$instance = new Db_PDO();
      }
   }

   /**
    * Metoda přičte k internímu počítadlu jedna
    */
   public static function increaseQueryCount() {
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
   public static function table($name, $dbPrefix = null) {
      return ($dbPrefix ? $dbPrefix : self::$tablePrefix).$name;
   }

   /**
    * Vrací aktuální instanci DB konektoru
    * @return Db_PDO
    */
   public static function getInstance()
   {
      if (!isset(self::$instance) || self::$instance == null)
      {
         self::$instance = new self();
      }
      return self::$instance;
   }
}