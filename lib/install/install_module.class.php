<?php

/**
 * Třída pro instalaci modulů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.1 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro instalaci modulů
 */
class Install_Module {

   public function __construct() {
   }

   /**
    * Metoda provede instalaci modulu
    */
   final public static function install($moduleName) {
   }

   /**
    * Metoda pro update modulu
    */
   final public static function update() {
   }
   
   /**
    * Metoda pro instalaci závislostí
    */
   private function installDepModules() {
      foreach ($this->depModules as $module) {
         $instCalss = ucfirst($module) . '_Install';
         $inst = new $instCalss();
         $inst->installModule();
      }
   }

   /**
    * Metoda nastaví název datového adresáře
    * @return string
    */
   public function getInstallDir() {
      return AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR
      . $this->moduleName . DIRECTORY_SEPARATOR . self::MODULE_INSTALL_DIR . DIRECTORY_SEPARATOR;
   }

   protected function replaceDBPrefix($cnt) {
      return str_replace($this->moduleTablesPrefix, VVE_DB_PREFIX, $cnt);
   }

   public static function updateAllModules() 
   {
//      // načtení instalovaných modulů
//      $model = new Model_Module();
//      $modules = $model->records();
//
//      $modulesForUpgrade = array();
//
//      foreach ($modules as $module) {
//         $vers = explode('.', file_get_contents(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
//               .$module->{Model_Module::COLUMN_NAME}.DIRECTORY_SEPARATOR
//               .AppCore::DOCS_DIR.DIRECTORY_SEPARATOR.self::VERSION_FILE));
//
//         if($vers[0] > $module->{Model_Module::COLUMN_VERSION_MAJOR}
//            || $vers[1] > $module->{Model_Module::COLUMN_VERSION_MINOR}){
//            $modulesForUpgrade[] = $module->{Model_Module::COLUMN_NAME};
//         }
//      }
//
//      if(!empty($modulesForUpgrade)) {
//         foreach ($modulesForUpgrade as $module) {
//            try {
//               $instObjName = ucfirst($module).'_Install';
//               $instObj = new $instObjName;
//               $instObj->update();
//            } catch (Exception $e) {
//               echo 'ERROR: Chyba při aktualizaci modulu: '.$module.'<br />';
//               echo $e->getMessage().'<br />';
//               echo "DEBUG: <br/ >";
//               echo $e->getTraceAsString();
//               die ();
//            }
//         }
//      }
      
   }
}
?>
