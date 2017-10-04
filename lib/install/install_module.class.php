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
      return str_replace($this->moduleTablesPrefix, defined('CUBE_CMS_DB_PREFIX') ? CUBE_CMS_DB_PREFIX : VVE_DB_PREFIX, $cnt);
   }

   public static function updateAllModules() 
   {
      // načtení instalovaných modulů
      $model = new Model_Module();
      $modules = $model->records();
      // prevent redirect after upgrade
      Module::$redirectAfterUpgrade = false;
      
      foreach ($modules as $module) {
         try {
            $className = ucfirst($module->{Model_Module::COLUMN_NAME}.'_Module');
            /* @var $className Module */
            if(class_exists($className)){
               $modObj = new $className();
            }
         } catch (Exception $e) {
            echo 'ERROR: Chyba při aktualizaci modulu: '.$module.'<br />';
            echo $e->getMessage().'<br />';
            echo "DEBUG: <br/ >";
            echo $e->getTraceAsString();
            die ();
         }
      }
      
   }
}
