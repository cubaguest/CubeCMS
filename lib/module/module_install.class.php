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

 class Module_Install {
    const MODULE_INSTALL_DIR = 'install';

    protected $depModules = array();

    protected $moduleName = null;

    protected $moduleTablesPrefix = '{PREFIX}';

    public function  __construct() {
       $tmp = explode('_', get_class($this));
       $this->moduleName = strtolower($tmp[0]);
    }

    /**
     * Metoda provede instalaci modulu, vybere jestli se modul aktualizuje nebo
     * instaluje nový
     * @todo dořešit aktualizace modulů
     */
    final public function installModule() {
       /* zjištění jestli je modul již instalován, pokud ne provede se install()
        * pokud ano provede se update()
        * Asi řešit přes tabulku s insstalovanými moduly, protože je třeba kontrolovat
        * i verzi instalovaného modulu při update, tak aby se popřípadě upravili potřebné parametry 
        */

       $this->installDepModules();
       $this->install();
       //$this->update();
    }

    /**
     * metoda pro instalaci modulu
     */
    public function install() {

    }

    /**
     * Metoda pro update modulu
     */
    public function update() {

    }

    /**
     * Metoda pro instalaci SQL patchů
     * @param string $SQL -- SQL patch
     */
    protected function runSQLCommand($SQL) {
       $model = new Model_DbSupport();
       $model->runSQL($SQL);
    }

    protected function getSQLFileContent($file = 'install.sql'){
       if(file_exists($this->getInstallDir().$file)){
         return file_get_contents($this->getInstallDir().$file);
       } else {
         return null;
       }
    }

    /**
     * Metoda pro instalaci závislostí
     */
    private function installDepModules(){
       foreach ($this->depModules as $module) {
          $instCalss = ucfirst($module).'_Install';
          $inst = new $instCalss();
          $inst->installModule();
       }
    }

    /**
     * Metoda nastaví název datového adresáře
     * @return string
     */
    public function getInstallDir() {
       return AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
       .$this->moduleName.DIRECTORY_SEPARATOR.self::MODULE_INSTALL_DIR.DIRECTORY_SEPARATOR;
    }

    protected function replaceDBPrefix($cnt) {
      return str_replace($this->moduleTablesPrefix, VVE_DB_PREFIX, $cnt);
    }
 }
?>
