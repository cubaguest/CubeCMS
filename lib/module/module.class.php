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
   const TPLS_LIST_FILE = 'tpls.php';

   protected $name = null;
   protected $params = null;
   protected $dataDir = null;

   protected $version = '1.0.0';
   protected $depModules = array();

   protected $author = null;
   protected $desc = null;

   protected $coreModule = false;  // vypíná aktualizace, protože ty jsou obsaženy v aktualizaci jádra


   protected $customTemplates = false;
   /**
    * Pole s šablonama
    * @var array
    */
   protected $templatesMain = array();
   /**
    * Pole s šablonama
    * @var array
    */
   protected $templatesPanel = array();
   /**
    * Pole s parametry šablon
    * @var array
    */
   protected $templatesParams = array();

   /**
    * @param $name
    * @param array $params
    * @param null $version - verze instalovaného modulu
    */
   public function  __construct($name, $params = array(), $version = null)
   {
      $this->name = $name;
      $this->params = $params;
      $this->dataDir = $name;
      $this->checkVersion($version);
      if($this->customTemplates){
         $this->loadTemplates();
      }
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
   public function getDataDir($webAddress = false)
   {
      if ($webAddress) {
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

   /**
    * Metoda vrací adresář modulu (ve face)
    * @return string -- cesta do modulu
    */
   public function getFaceDir()
   {
      return Face::getCurrent()->getDir() . AppCore::MODULES_DIR . DIRECTORY_SEPARATOR . $this->getName() . DIRECTORY_SEPARATOR;
   }

   public function getVersion()
   {
      return $this->version;
   }

   protected function checkVersion($currentVersion)
   {
      if($currentVersion == null){
         if(!Model_Module::isInstalled($this->getName())){
            $this->install();
         }
      } else if(version_compare($this->version, $currentVersion) == 1){
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
   protected function installDependentModules()
   {
      foreach ($this->depModules as $module) {
         Debug::log($module, Model_Module::isInstalled($module));
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

   protected function replaceDBPrefix($cnt)
   {
      return str_replace(Install_Core::SQL_TABLE_PREFIX_REPLACEMENT, VVE_DB_PREFIX, $cnt);
   }

   /**
    * Metoda pro instalaci SQL patchů
    * @param string $SQL -- SQL patch
    */
   protected function runSQLCommand($SQL)
   {
      $model = new Model_DbSupport();
      $model->runSQL($SQL);
   }

   /**
    * Metoda provede načtení šablon a jejich parametrů
    */
   protected function loadTemplates()
   {
      $someLoaded = false;
      $class = get_class($this);
      while ($class != 'Module') {
         $module = strtolower( substr($class, 0, -7));
         // zkus existenci souborů
         // soubor z face
         if(is_file(Template::faceDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
            .$module.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE)){

            include Template::faceDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
               .$module.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE;
            $someLoaded = true;
            break;
         }
         // soubor z hlavního vzhledu
         else if(is_file(Template::faceDir(true).AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
            .$module.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE)){

            include Template::faceDir(true).AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
               .$module.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE;
            $someLoaded = true;
            break;
         }
            // soubor z modulu
         else if(is_file(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module
            .DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE)){

            include AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module
                  .DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE;
            $someLoaded = true;
            break;
         }
         // načti rodiče
         $class = get_parent_class($class);
      }
     
      if($someLoaded){
         // přeřazení proměnných pro kompatibilitu
         if(isset($this->main) && $this->main != null){
             $this->templatesMain = $this->main;
         }
         if(isset($this->panel) && $this->panel != null){
             $this->templatesPanel = $this->panel;
         }
         $tpls = $this->templatesMain;
         $this->templatesMain = array();
         foreach($tpls as $tpl => $labels){
            $this->templatesMain[$tpl] = $labels[Locales::getLang()];
         }
         $tpls = $this->templatesPanel;
         $this->templatesPanel = array();
         foreach($tpls as $tpl => $labels){
            $this->templatesPanel[$tpl] = $labels[Locales::getLang()];
         }
      }

   }

   /**
    * Metoda vrací šablony modulu
    * @return array
    */
   public function getTemplates()
   {
      if($this->templatesMain == false) {
         $this->loadTemplates();
      }
      return $this->templatesMain;
   }

   /**
    * Metoda vrací šablony modulu pro panel
    * @return array
    */
   public function getPanelTemplates()
   {
      if($this->templatesPanel == false) {
         $this->loadTemplates();
      }
      return $this->templatesPanel;
   }

   /**
    * Metoda vrací parametr šablony
    * @param $tpl
    * @param $param
    * @return mixed
    */
   public function getTemplateParam($tpl, $param, $default = null)
   {
      if(isset($this->templatesParams[$tpl]) && isset($this->templatesParams[$tpl][$param])){
         return $this->templatesParams[$tpl][$param];
      }
      return $default;
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
