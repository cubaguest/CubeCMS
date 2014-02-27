<?php
/**
 * Třída Modelu pro práci s moduly.
 * Třída, která umožňuje pracovet s modelem modulů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro vytvoření modelu pro práci s moduly
 */

class Model_Module extends Model_ORM {
   const DB_TABLE = 'modules_instaled';

   const COLUMN_ID = 'id_module';
   const COLUMN_NAME = 'name';
   const COLUMN_VERSION = 'version';
   const COLUMN_VERSION_MAJOR = 'version_major';
   const COLUMN_VERSION_MINOR = 'version_minor';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_mods');
   
      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(30)', 'nn' => true, 'index' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_VERSION, array('datatype' => 'varchar(5)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'default' => '1.0.0'));
      $this->addColumn(self::COLUMN_VERSION_MAJOR, array('datatype' => 'tinyint(3)', 'nn' => true,
            'pdoparam' => PDO::PARAM_INT,'default' => 1));
      $this->addColumn(self::COLUMN_VERSION_MINOR, array('datatype' => 'tinyint(3)', 'nn' => true,
            'pdoparam' => PDO::PARAM_INT,'default' => 0));
   
      $this->setPk(self::COLUMN_ID);
   }
   
/**
 * Metoda načte moduly
 * @return array -- pole s moduly
 */
   public function getModules() {
      $handle = opendir(AppCore::getAppLibDir().AppCore::MODULES_DIR);
      if (!$handle) {
         trigger_error(_('Nepodařilo se otevřít adresář s moduly'));
      }

      $directories = array();
      while (false !== ($file = readdir($handle))) {
         if ($file != "." AND $file != ".." AND is_dir(AppCore::getAppLibDir()
               .AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$file)) {
            array_push($directories, $file);
         }
      }
      closedir($handle);
      return $directories;
   }

   public function getModule($name) {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_NAME." = :name");
      $dbst->execute(array(':name' => $name));
      return $dbst->fetchObject();
   }

   /**
    * MEtoda zjišťuje jestli je daný modul instalován
    * @param string $name -- název modulu
    * @return bool -- true pokud je již modul instalován
    */
   public function isModuleInstaled($name) {
      $dbc = Db_PDO::getInstance();
      // kontrola jestli místo již neexistuje
      $dbst = $dbc->prepare("SELECT COUNT(*) AS count FROM ".Db_PDO::table(self::DB_TABLE)
              ." WHERE ".self::COLUMN_NAME." = :name");
      $dbst->execute(array(':name' => $name));
      $counter = $dbst->fetchObject();

      if($counter->count == 0){
         return false;
      }
      return true;
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
   public static function getVersion($name) {
      $m = new self();
      $module = $m->where(self::COLUMN_NAME." = :name", array('name' => $name))->record();

      if($module){
         return $module->{self::COLUMN_VERSION};
      }
      return null;
   }


   public function registerInstaledModule($name, $version) {
      $dbc = Db_PDO::getInstance();

      $dbst = $dbc->prepare('INSERT INTO '.Db_PDO::table(self::DB_TABLE)." "
                 ."(".self::COLUMN_NAME.", ".self::COLUMN_VERSION.")"
                 ." VALUES (:name, :version)");

      return $dbst->execute(array(':name' => $name, ':version' => $version));
   }

   public function getInstalledModules() {
      $dbc = Db_PDO::getInstance();
      $dbst = $dbc->prepare("SELECT * FROM ".Db_PDO::table(self::DB_TABLE)
              ." ORDER BY ".self::COLUMN_NAME." ASC");
      $dbst->setFetchMode(PDO::FETCH_OBJ);
      $dbst->execute();

      return $dbst;
   }

   /**
    * Metoda detekuje jestli je modul nainstalován
    * @param $module
    * @return bool
    */
   public static function isInstalled($module)
   {
      $m = new self();
      return (bool)$m->where(self::COLUMN_NAME." = :name", array('name' => $module))->count();
   }
}
