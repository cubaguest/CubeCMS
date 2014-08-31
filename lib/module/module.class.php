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

   private $currentModule = null;


   /**
    * Šablony modulu
    * @var array
    */
   protected static $templates = array();
   /**
    * Šablony panelu modulu
    * @var array
    */
   protected static $panelTemplates = array();

   /**
    * @param $name
    * @param array $params
    * @param null $version - verze instalovaného modulu
    */
   public function  __construct($name = null, $params = array(), $version = null)
   {
      if(!$name){
         $name = strtolower(str_replace('_Module', '', get_class($this)));
      }
      $this->name = $name;
      $this->params = $params;
      $this->dataDir = $name;
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
      $this->updateDependModules();
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
      // install langs
      foreach (Locales::getAppLangs() as $lang) {
         $this->installLang($lang);
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
         if(!Model_Module::isInstalled($module)){
            $mClass = ucfirst($module) . '_Module';
            /**
             * @todo dodělat dinamické generování třídy modulu
             */
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
         $phpFiles = glob($updateDir."*.php");
         if(is_array($phpFiles) && sizeof($phpFiles) > 0){
            foreach ($phpFiles as $filename) {
               $version = str_replace(".php", "", basename($filename));
               if(version_compare($version, $currentVersion) == 1){
                  $versions[$version] = null;
               }
            }
         }
         $sqlFiles = glob($updateDir."*.sql");
         if(is_array($sqlFiles) && sizeof($sqlFiles) > 0){
            foreach ($sqlFiles as $filename) {
               $version = str_replace(".sql", "", basename($filename));
               if(version_compare($version, $currentVersion) == 1){
                  $versions[$version] = null;
               }
            }
         }
      }

      $upgradeVersions = array_keys($versions);
      natcasesort($upgradeVersions);

      if(!empty($upgradeVersions)){

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
               $model = new Model_Module();
               $model
                  ->where(Model_Module::COLUMN_NAME." = :mname", array('mname' => $this->getName()))
                  ->update(array(Model_Module::COLUMN_VERSION => $version));

            } catch (Exception $exc) {
               new CoreErrors($exc);
               break;
            }
         }
         AppCore::getInfoMessages()->addMessage(sprintf('Aktualizace modulu %s broběhla úspěšně.', $this->getName()));
         // tohle by šlo řešit také aktualizací verze přímo v objektu modulu
         $link = new Url_Link();
         $link->redirect();
      }
   }
   
   protected function updateDependModules()
   {
      if(empty($this->depModules)){
         return;
      }
      foreach ($this->depModules as $moduleName) {
         $class = $moduleName.'_Module';
         $module = new $class($moduleName, array(), self::getModuleVersion($moduleName));
      }
   }
   
   protected static function getModuleVersion($moduleName)
   {
      return Model_Module::getVersion($moduleName);
   }
   
   /**
    * metoda pro instalaci požadovaného jazyka
    * @param type $lang
    */
   public function installLang($lang)
   {
      $models = $this->getModels();
      if(!empty($models)){
         foreach ($models as $modelName) {
            $model = new $modelName();
            if($model instanceof Model_ORM){
               $model->updateLangColumns($lang);
            }
         }
      }
   }

   protected function getModels() {
      $dir = $this->getModuleDir().'model'.DIRECTORY_SEPARATOR;
      $files = glob($dir.'*.php');
      if(empty($files)){
         return;
      }
      
      $models = array();
      foreach ($files as $file) {
         $file = str_replace('.class.php', '', basename($file));
         if($file == 'model'){
            $modelClass = ucfirst($this->getName()).'_'.ucfirst($file);
         } else {
            $modelClass = ucfirst($this->getName()).'_Model_'.ucfirst($file);
         }
         if(class_exists($modelClass)){
            $models[] = $modelClass;
         }
      }
      return $models;
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
    * @todo tady by šlo nádherně kešovat přes soubor
    */
   public function loadTemplates()
   {
      if(isset(self::$templates[$this->getName()])){
         return;
      } else {
         self::$templates[$this->getName()] = array();
         self::$panelTemplates[$this->getName()] = array();
      }
      
      $someLoaded = false;
      $class = get_class($this);
      $modules = array();
      // @todo dořešit načítání z modulů od kterých se dědí
      while ($class != 'Module') {
         $module = strtolower( substr($class, 0, -7));
         $modules[] = $module;
         // načti rodiče
         $class = get_parent_class($class);
      }
//      $modules = array_reverse($modules);
      
      foreach ($modules as $name) {
         $this->currentModule = $name;
         if(is_file(Template::faceDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
            .$name.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE)){
            include Template::faceDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
               .$name.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE;
            $someLoaded = true;
         }
         // soubor z hlavního vzhledu
         else if(is_file(Template::faceDir(true).AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
            .$name.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE)){
            include Template::faceDir(true).AppCore::MODULES_DIR.DIRECTORY_SEPARATOR
               .$name.DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE;
            $someLoaded = true;
         }
            // soubor z modulu
         else if(is_file(AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$name
            .DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE)){
            include AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$name
                  .DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.self::TPLS_LIST_FILE;
            $someLoaded = true;
         }
      }
      $this->currentModule = null;
      
      /**
       * Backward compatibility
       */
      if($someLoaded){
         // přeřazení proměnných pro kompatibilitu
         if(isset($this->main) && is_array($this->main)){
            foreach($this->main as $tpl => $labels){
               $this->addTemplate('main', $tpl, $labels);
            }
         }
         if(isset($this->panel) && is_array($this->panel)){
            foreach($this->panel as $tpl => $labels){
               $this->addPanelTemplate($tpl, $labels);
            }
         }
      }
   }
   
   /**
    * 
    * @param type $action
    * @param type $file
    * @param type $labels
    * @param type $params
    * @todo dořešit načítání z modulů od kterých se dědí
    */
   protected function addTemplate($action, $file, $labels, $params = array())
   {
      // kvůli pozdější implementaci překladače, tady nechme převod na řetězec
      $l = null;
      if(is_array($labels)){
         $l = isset($labels[Locales::getLang()]) ? $labels[Locales::getLang()] : 
            ( isset($labels[Locales::getDefaultLang()]) ? $labels[Locales::getDefaultLang()] : $labels['cs'] );
      } else {
         $l = (string)$labels;
      }
      if($this->currentModule != null && $this->currentModule != $this->getName()){
         if(strpos($file, ':') === false){ // neobsahuje děděný modul
            $file = $this->currentModule.':'.$file;
         }
      }
      
      if( ($pos = strpos($file, ':')) !== false){
         $tr = new Translator();
         $l .= $tr->tr(' - děděno z modulu ').  substr($file, 0, $pos);
      }
      
      if(!isset(self::$templates[$this->getName()][$action])){
         self::$templates[$this->getName()][$action][$file] = array();
      }
      
      self::$templates[$this->getName()][$action][$file] = array(
         'name' => $l, // možná bude lepší detekovat jinak
         'params' => $params
      );
   }
   
   protected function addPanelTemplate($file, $labels, $params = array())
   {
      // kvůli pozdější implementaci překladače, tady nechme převod na řetězec
      $l = null;
      if(is_array($labels)){
         $l = isset($labels[Locales::getLang()]) ? $labels[Locales::getLang()] : 
            ( isset($labels[Locales::getDefaultLang()]) ? $labels[Locales::getDefaultLang()] : $labels['cs'] );
      } else {
         $l = (string)$labels;
      }
      if($this->currentModule != null && $this->currentModule != $this->getName()){
         if(strpos($file, ':') === false){ // neobsahuje děděný modul
            $file = $this->currentModule.':'.$file;
         }
      }
      
      if( ($pos = strpos($file, ':')) !== false){
         $tr = new Translator();
         $l .= $tr->tr(' - děděno z modulu ').  substr($file, 0, $pos);
      }
      
      if(!isset(self::$panelTemplates[$this->getName()][$file])){
         self::$panelTemplates[$this->getName()][$file] = array();
      }
      
      self::$panelTemplates[$this->getName()][$file] = array(
            'name' => $l, // možná bude lepší detekovat jinak
            'params' => $params
         );   
   }
   

   /**
    * Metoda vrací šablony modulu
    * @return array
    */
   public function getTemplates($action = 'main')
   {
       if(isset(self::$templates[$this->getName()][$action])){
          return self::$templates[$this->getName()][$action];
       }
       return false;
   }
   
   public function getAllTemplates()
   {
      return self::$templates[$this->getName()];
   }

   /**
    * Metoda vrací šablony modulu pro panel
    * @return array
    */
   public function getPanelTemplates()
   {
      if(isset(self::$panelTemplates[$this->getName()])){
         return self::$panelTemplates[$this->getName()];
      }
      return false;
   }

   /**
    * Metoda vrací parametr šablony
    * @param $tpl
    * @param $param
    * @return mixed
    */
   public function getTemplateParam($action, $tpl, $param, $default = null)
   {
      if(isset(self::$templates[$this->getName()][$action]) 
          && isset(self::$templates[$this->getName()][$action][$tpl])
          && isset(self::$templates[$this->getName()][$action][$tpl]['params'][$param])
          ){
         return self::$templates[$this->getName()][$action][$tpl]['params'][$param];
      }
      return $default;
   }
}
