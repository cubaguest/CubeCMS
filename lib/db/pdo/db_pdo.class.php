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
                  parent::__construct("mysql:host=".self::$serverName.";dbname=".self::$dbName,
                     self::$userName, self::$userPassword, array(
                        PDO::ATTR_PERSISTENT => false,
                        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
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
            call_user_func_array(array($this, "__construct"), func_get_args());
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
      return call_user_func_array(array($this, "parent::query"), func_get_args());
   }

   public function exec($statement){
      self::$numberOfSqlQueries++;
      return call_user_func_array(array($this, "parent::exec"), func_get_args());
   }

   /**
    * Metoda pro sestavení spojení a základní nastavení db konektoru
    * @return Db Konektory k danému databázovému stroji
    */
   public static function factory() {
      self::$serverName = VVE_DB_SERVER;
      self::$userName = VVE_DB_USER;
      self::$userPassword = VVE_DB_PASSWD;
      self::$dbName = VVE_DB_NAME;
      self::$tablePrefix = VVE_DB_PREFIX;
      self::$connectorType = VVE_DB_TYPE;
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
   public static function table($name) {
      return self::$tablePrefix.$name;
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
?>