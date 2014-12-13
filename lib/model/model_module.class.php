<?php
/**
 * Třída Modelu pro práci s moduly.
 * Třída, která umožňuje pracovet s modelem modulů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 * 						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro vytvoření modelu pro práci s moduly
 */
class Model_Module extends Model_ORM {
   const DB_TABLE = 'modules_instaled';
   const COLUMN_ID = 'id_module';
   const COLUMN_NAME = 'name';
   const COLUMN_VERSION = 'version';
   const COLUMN_VERSION_MAJOR = 'version_major';
   const COLUMN_VERSION_MINOR = 'version_minor';

   protected static $modules = false;

   protected function _initTable()
   {
      $this->setTableName(self::DB_TABLE, 't_mods');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(30)', 'nn' => true, 'index' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_VERSION, array('datatype' => 'varchar(5)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'default' => '1.0.0'));
      $this->addColumn(self::COLUMN_VERSION_MAJOR, array('datatype' => 'tinyint(3)', 'nn' => true,
          'pdoparam' => PDO::PARAM_INT, 'default' => 1));
      $this->addColumn(self::COLUMN_VERSION_MINOR, array('datatype' => 'tinyint(3)', 'nn' => true,
          'pdoparam' => PDO::PARAM_INT, 'default' => 0));

      $this->setPk(self::COLUMN_ID);
   }

   /**
    * Metoda načte moduly
    * @return array -- pole s moduly
    */
   public function getModules()
   {
      $handle = opendir(AppCore::getAppLibDir() . AppCore::MODULES_DIR);
      if (!$handle) {
         trigger_error(_('Nepodařilo se otevřít adresář s moduly'));
      }

      $directories = array();
      while (false !== ($file = readdir($handle))) {
         if ($file != "." AND $file != ".." AND is_dir(AppCore::getAppLibDir()
                 . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $file)) {
            array_push($directories, $file);
         }
      }
      closedir($handle);
      return $directories;
   }

   public function getModule($name)
   {
//      $dbc = Db_PDO::getInstance();
//      $dbst = $dbc->prepare("SELECT * FROM " . Db_PDO::table(self::DB_TABLE)
//          . " WHERE " . self::COLUMN_NAME . " = :name");
//      $dbst->execute(array(':name' => $name));
      return isset(self::$modules[$name]) ? self::$modules[$name] : null;
   }

   /**
    * MEtoda zjišťuje jestli je daný modul instalován
    * @param string $name -- název modulu
    * @return bool -- true pokud je již modul instalován
    */
   public function isModuleInstaled($name)
   {
      return self::isInstalled($name);
   }

   /**
    * Metoda vrací verzi modulu
    * @param string $name -- název modulu
    * @return string -- verze
    * @todo dodělat kešování nebo načítání pouze jednou, takhle se tato metoda 
    * volá několikrát pro každý modul zvláště. Možností je přesunout tohle do joinu 
    * ke kategoriím a přiřazovat to s tama. Rychlá by byla potom asi i aktulaizace 
    * nazpět do objektu kategorie.
    */
   public static function getVersion($name, $dbPrefix = null)
   {
      if($dbPrefix){
         // kontrola nad jinou tabulkou
         $dbc = Db_PDO::getInstance();

         $dbst = $dbc->prepare('SELECT `'.self::COLUMN_VERSION.'` FROM ' . Db_PDO::table(self::DB_TABLE, $dbPrefix) . " "
          . " WHERE `".self::COLUMN_NAME."` = :name");

         $dbst->execute(array(':name' => $name));
         $obj = $dbst->fetchObject();
         if(is_object($obj) ){
            return $obj->{self::COLUMN_VERSION};
         }
         return null;
      }
      
      self::loadModulesData();
      return isset(self::$modules[$name]) ? self::$modules[$name] : null;
   }

   public function registerInstaledModule($name, $version, $dbPrefix = false)
   {
      $dbc = Db_PDO::getInstance();

      $dbst = $dbc->prepare('INSERT INTO ' . ( Db_PDO::table(self::DB_TABLE, $dbPrefix) ) . " "
          . "(" . self::COLUMN_NAME . ", " . self::COLUMN_VERSION . ")"
          . " VALUES (:name, :version)");
      
      $ret = $dbst->execute(array(':name' => $name, ':version' => $version));
      self::loadModulesData(true);
      return $ret;
   }

   public function getInstalledModules()
   {
      self::loadModulesData();
      return self::$modules;
   }

   /**
    * Metoda detekuje jestli je modul nainstalován
    * @param $module
    * @return bool
    */
   public static function isInstalled($module, $dbPrefix = null)
   {
      if($dbPrefix){
         // kontrola nad jinou tabulkou
         $dbc = Db_PDO::getInstance();

         $dbst = $dbc->prepare('SELECT COUNT(*) AS counter FROM ' . Db_PDO::table(self::DB_TABLE, $dbPrefix) . " "
          . " WHERE `".self::COLUMN_NAME."` = :name");

         $dbst->execute(array(':name' => $module));
         $obj = $dbst->fetchObject();
         if(is_object($obj) ){
            return (bool)$obj->counter;
         }
         return false;
      }
      self::loadModulesData();
      return isset(self::$modules[$module]);
   }

   protected static function loadModulesData($force = false)
   {
      if (!self::$modules || $force) {
         self::$modules = array();
         $m = new self();
         $list = $m->records();
         foreach ($list as $module) {
            self::$modules[$module->{Model_Module::COLUMN_NAME}] = $module->{Model_Module::COLUMN_VERSION};
         }
      }
   }

}
