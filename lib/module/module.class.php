<?php
/**
 * Třída pro obsluhu vlastností mmodulu
 *
 * @copyright     Copyright (c) 2008-2009 Jakub Matas
 * @version       $Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu vlastností modulu
 */

class Module
{
   protected $name = null;
   protected $params = null;
   protected $dataDir = null;

   protected $version = null;
   protected $depModules = array();

   protected $author = null;
   protected $desc = null;

   protected $coreModule = false;  // vypíná aktualizace, protože ty jsou obsaženy v aktualizaci jádra

   public function  __construct($name, $params = array(), $version = null)
   {
      $this->name = $name;
      $this->params = $params;
      $this->dataDir = $name;

      if($version == null){
         if(is_file($this->getModuleDir().'docs'.DIRECTORY_SEPARATOR.'version.txt')){
            $version = file_get_contents($this->getModuleDir().'docs'.DIRECTORY_SEPARATOR.'version.txt').'.0';
         }
         if($version == null){
            $this->install();
         }
      }
      $this->checkVersion($version);
   }

   public function __toString()
   {
      return (string)$this->name;
   }

   /**
    * Metoda vrací název modulu
    * @return string
    */
   public function getName()
   {
      return $this->name;
   }

   /**
    * Metoda nastaví název datového adresáře
    * @return string
    */
   public function setDataDir($name)
   {
      $this->dataDir = $name;
   }

   /**
    * Metoda vrací absolutní cestu k adresáři modulu
    * @return string
    */
   public function getModuleDir()
   {
      return AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->getName() . DIRECTORY_SEPARATOR;
   }

   /**
    * Metoda vrací požadovaný parametr
    * @param string $param -- index parametru
    * @param mixed $defaultParam -- výchozí hodnota
    * @return string -- parametr
    */
   public function getParam($param, $defaultParam = null)
   {
      if (isset($this->params[$param])) {
         return $this->params[$param];
      } else {
         return $defaultParam;
      }
   }

   /**
    * Metoda datový vrací adresář modulu
    * @return string
    * @todo -- ověřit vytváření adresáře
    */
   public function getDataDir($webAddres = false)
   {
      if ($webAddres) {
         return Url_Request::getBaseWebDir() . VVE_DATA_DIR . URL_SEPARATOR . $this->dataDir . URL_SEPARATOR;
      } else {
         $dir = new Filesystem_Dir(AppCore::getAppWebDir() . VVE_DATA_DIR . DIRECTORY_SEPARATOR . $this->dataDir . DIRECTORY_SEPARATOR);
         //$dir->checkDir(); -- TODO
         return (string)$dir;
      }
   }

   /**
    * Metoda vrací adresář modulu (v knihovnách)
    * @return string -- cesta do modulu
    */
   public function getLibDir()
   {
      return AppCore::getAppLibDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->getName() . DIRECTORY_SEPARATOR;
   }

   public function getVersion()
   {
      return $this->version;
   }

   protected function checkVersion($currentVersion)
   {
      if(version_compare($this->version, $currentVersion) == 1){
//         Debug::log('Update module: '.$this->getName()." from: ".$currentVersion." to: ".$this->version);
         $this->update($currentVersion);
      }
   }

   public function install()
   {
      if(!$this->coreModule){

      }
      // install dependecies
      $this->installDependentModules();
      // install module
      $installDir = $this->getModuleDir() . 'install' . DIRECTORY_SEPARATOR;
      $phpFile = 'install.php';
      $sqlFile = 'install.sql';
      // install sql
      if (is_file($installDir.$sqlFile) && filesize($installDir.$sqlFile) > 0) {
         $this->runSQLCommand($this->replaceDBPrefix(file_get_contents($installDir.$sqlFile)));
      }
      // install php
      if (is_file($installDir.$phpFile)) {
         include $installDir.$phpFile;
      }

      // register module
      $model = new Model_Module();
      $model->registerInstaledModule($this->name, $this->version);
   }

   /**
    * Metoda pro instalaci závislostí
    */
   protected function installDependentModules() {
      foreach ($this->depModules as $module) {
         if(!Model_Module::isInstalled($module)){
            $mClass = ucfirst($module) . '_Module';
            if(!class_exists($mClass)){
               $mClass = 'Module';
            }
            $inst = new $mClass($module);
         }
      }
   }

   public function update($currentVersion)
   {
      $updateDir = $this->getModuleDir() . 'upgrade' . DIRECTORY_SEPARATOR;

      $versions = array();
      if(is_dir($updateDir)){
         foreach (glob($updateDir."*.php") as $filename) {
            $version = str_replace(".php", "", basename($filename));
            if(version_compare($version, $currentVersion) == 1){
               $versions[$version] = null;
            }
         }
         foreach (glob($updateDir."*.sql") as $filename) {
            $version = str_replace(".sql", "", basename($filename));
            if(version_compare($version, $currentVersion) == 1){
               $versions[$version] = null;
            }
         }
      }

      $upgradeVersions = array_keys($versions);
      natcasesort($upgradeVersions);

      if(!empty($upgradeVersions)){

         $model = new Model_Module();
         foreach($upgradeVersions as $version){
            /* php prepare update */
            $phpFile = $version.'.php';
            $phpPreFile = $version.'_pre.php';
            $sqlFile = $version.'.sql';

            try {
               // pre patch run before sql
               if (is_file($updateDir.$phpPreFile)) {
                  include $updateDir.$phpPreFile;
               }

               // update sql
               if (is_file($updateDir.$sqlFile) && filesize($updateDir.$sqlFile) > 0) {
                  $this->runSQLCommand($this->replaceDBPrefix(file_get_contents($updateDir.$sqlFile)));
               }
               // normal patch
               if (is_file($updateDir.$phpFile)) {
                  include $updateDir.$phpFile;
               }

               $model
                  ->where(Model_Module::COLUMN_NAME." = :mname", array('mname' => $this->getName()))
                  ->update(array(Model_Module::COLUMN_VERSION => $version));

            } catch (Exception $exc) {
               new CoreErrors($exc);
               break;
            }
         }
         AppCore::getInfoMessages()->addMessage(sprintf('Aktualizace modulu %s broběhla úspěšně.', $this->getName()));
      }
   }

   protected function replaceDBPrefix($cnt) {
      return str_replace(Install_Core::SQL_TABLE_PREFIX_REPLACEMENT, VVE_DB_PREFIX, $cnt);
   }

   /**
    * Metoda pro instalaci SQL patchů
    * @param string $SQL -- SQL patch
    */
   protected function runSQLCommand($SQL) {
      $model = new Model_DbSupport();
      $model->runSQL($SQL);
   }

   /**
    * Metoda pro implementaci updatu v modulu (např. přesun souborů)
    * @param int $major -- major verzen na kterou se updatuje
    * @param int $minor -- minor verzen na kterou se updatuje
    */
//   protected function modulePreUpdate($version) {}
//
//   protected function modulePostUpdate($version) {}


}
