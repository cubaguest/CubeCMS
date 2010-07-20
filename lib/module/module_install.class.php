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
    const VERSION_FILE = 'version.txt';

    const FILE_SQL_UPGRADE = 'upgrade_{from}_{to}.sql';

    protected $depModules = array();

    protected $moduleName = null;

    protected $moduleTablesPrefix = '{PREFIX}';

    protected $version = array('major' => 1, 'minor' => 0);

    public function  __construct() {
       $tmp = explode('_', get_class($this));
       $this->moduleName = strtolower($tmp[0]);

       // načtení verze
       $verStr = file_get_contents(AppCore::getAppWebDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
               .$this->moduleName.DIRECTORY_SEPARATOR.AppCore::DOCS_DIR.DIRECTORY_SEPARATOR.self::VERSION_FILE);
       $versionArr = explode('.', $verStr);
       $this->version['major'] = $versionArr[0];
       $this->version['minor'] = $versionArr[1];
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

       $model = new Model_Module();
       if($model->isModuleInstaled($this->moduleName) == false){
         $this->installDepModules();
         $this->install();
         $model->registerInstaledModule($this->moduleName, $this->version['major'], $this->version['minor']);
       } else {
         $this->update();
       }
    }

    /**
     * metoda pro instalaci modulu
     */
    public function install() {

    }

    /**
     * Metoda pro update modulu
     * @param int $fromVersion -- původní verze
     * @param int $toVersion -- nová verze
     */
    public function update($fromVersion, $toVersion) {
       // update tabulky
       $model = new Model_Module();
       $ver = explode('.', $toVersion);
       $model->registerUpdatedModule($this->moduleName, $ver[0], $ver[1]);

       $fileName = preg_replace(array('/{from}/', '/{to}/'), array($fromVersion, $toVersion),
               self::FILE_SQL_UPGRADE);

       $file = new Filesystem_File_Text($fileName, $this->getInstallDir().$file,false);
       if($file->exist()){
         $this->runSQLCommand($this->replaceDBPrefix($file->getContent()));
       }
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
