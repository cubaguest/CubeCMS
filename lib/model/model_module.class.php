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

class Model_Module extends Model_PDO {
   const DB_TABLE = 'modules_instaled';

   const COLUMN_NAME = 'name';
   const COLUMN_VERSION_MAJOR = 'version_major';
   const COLUMN_VERSION_MINOR = 'version_minor';

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

   /**
    * Metoda pro provedení sql příkazu (externího)
    * @param string $sql
    */
   public function installModuleTable($module){
      if(file_exists(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module
              .DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'install.sql')){
         $sqlQuery = file_get_contents(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module
              .DIRECTORY_SEPARATOR.'install'.DIRECTORY_SEPARATOR.'install.sql');
         // přepsání prefixu
         $sqlQuery = str_replace('{PREFIX}', VVE_DB_PREFIX, $sqlQuery);
         $dbc = new Db_PDO();
         return $dbc->exec($sqlQuery);
      }
   }

   /**
    * MEtoda zjišťuje jestli je daný modul instalován
    * @param string $name -- název modulu
    * @return bool -- true pokud je již modul instalován
    */
   public function isModuleInstaled($name) {
      $dbc = new Db_PDO();
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


   public function registerInstaledModule($name, $vMajor = 1, $vMinor = 0) {
      $dbc = new Db_PDO();

      $dbst = $dbc->prepare('INSERT INTO '.Db_PDO::table(self::DB_TABLE)." "
                 ."(".self::COLUMN_NAME.", ".self::COLUMN_VERSION_MAJOR.", ".self::COLUMN_VERSION_MINOR.")"
                 ." VALUES (:name, :vmajor, :vminor)");

      return $dbst->execute(array(':name' => $name, ':vmajor' => $vMajor, ':vminor' => $vMinor));
   }
}
?>