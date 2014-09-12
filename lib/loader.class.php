<?php 
/**
 * Třída pro automatické nahrávání
 * @todo - přesunout sem autoloadery
 */
class Loader {
   const DIR_LIBS = 'nonvve';
   const FILE_BOOTSTRAP = 'bootstrap.php';
   const FILE_CACHE = 'classes.cache';
   const FILE_CACHE_HASH = 'classes.hash';

   protected static $classCache = array();
   protected static $classCacheHash = null;

   /**
    * Načtení knihovny která není součástí enginu
    * @param $name
    */
   public static function loadExternalLib($name)
   {
      $bootstrap = self::getExternalLibDir().strtolower($name).DIRECTORY_SEPARATOR.self::FILE_BOOTSTRAP;
      if(is_file($bootstrap)){
         include_once $bootstrap;
      }
   }

   /**
    * Meta vrací cestu k externím knihovnám
    * @return string
    */
   protected static function getExternalLibDir()
   {
      return AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR.self::DIR_LIBS.DIRECTORY_SEPARATOR;
   }
   
   /**
    * Metoda pro automatické načtení knihoven
    * @todo refaktoring nutný
    */
   public static function libAutoLoader($classOrigName)
   {
      $file = strtolower($classOrigName) . '.class.php';
      $classL = strtolower($classOrigName);
      $pathDirs = explode('_', $classL);
      $pathFull = implode('/', $pathDirs);
      array_pop($pathDirs); // remove last path item
      $pathShort = implode('/', $pathDirs);

      // short path
      if (is_file(AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR
            . DIRECTORY_SEPARATOR . $pathShort . DIRECTORY_SEPARATOR . $file)) {
         require AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR
            . DIRECTORY_SEPARATOR . $pathShort . DIRECTORY_SEPARATOR . $file;
         self::$classCache[$classOrigName] = AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR
            . DIRECTORY_SEPARATOR . $pathShort . DIRECTORY_SEPARATOR . $file;
         return true;
      }
      // full path
      if (is_file(AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR
      . DIRECTORY_SEPARATOR . $pathFull . DIRECTORY_SEPARATOR . $file)) {
         require AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR
            . DIRECTORY_SEPARATOR . $pathFull . DIRECTORY_SEPARATOR . $file;
         self::$classCache[$classOrigName] = AppCore::getAppLibDir() . AppCore::ENGINE_LIB_DIR
            . DIRECTORY_SEPARATOR . $pathFull . DIRECTORY_SEPARATOR . $file;
         return true;
      }
      return false;
   }
   
   /**
    * Metoda pro automatické načtení knihoven
    * @todo refaktoring nutný
    */
   public static function moduleAutoLoader($classOrigName)
   {
//      $file = strtolower($classOrigName) . '.class.php';
      $classL = strtolower($classOrigName);
      $pathDirs = explode('_', $classL);
      $moduleFile = end($pathDirs) . '.class.php';
      $pathFull = implode('/', $pathDirs);
      array_pop($pathDirs);
      $pathShort = implode('/', $pathDirs);
      // short path
      if (is_file(AppCore::getAppLibDir() . AppCore::MODULES_DIR
            . DIRECTORY_SEPARATOR . $pathShort . DIRECTORY_SEPARATOR . $moduleFile)) {
         require AppCore::getAppLibDir() . AppCore::MODULES_DIR
            . DIRECTORY_SEPARATOR . $pathShort . DIRECTORY_SEPARATOR . $moduleFile;
         self::$classCache[$classOrigName] = AppCore::getAppLibDir() . AppCore::MODULES_DIR
            . DIRECTORY_SEPARATOR . $pathShort . DIRECTORY_SEPARATOR . $moduleFile;
         return true;
      }
      // full path
      if (is_file(AppCore::getAppLibDir() . AppCore::MODULES_DIR
      . DIRECTORY_SEPARATOR . $pathFull . DIRECTORY_SEPARATOR . $moduleFile)) {
         require AppCore::getAppLibDir() . AppCore::MODULES_DIR
            . DIRECTORY_SEPARATOR . $pathFull . DIRECTORY_SEPARATOR . $moduleFile;
         self::$classCache[$classOrigName] = AppCore::getAppLibDir() . AppCore::MODULES_DIR
            . DIRECTORY_SEPARATOR . $pathFull . DIRECTORY_SEPARATOR . $moduleFile;
         return true;
      }
      return false;
   }
   
   public static function cacheAutoloader($className)
   {
      if(isset(self::$classCache[$className])){
         require self::$classCache[$className];
         return true;
      }
   }

   public static function loadCache()
   {
      if(is_file(AppCore::getAppCacheDir().self::FILE_CACHE)){
         include AppCore::getAppCacheDir().self::FILE_CACHE;
      }
   }
   
   public static function storeCache()
   {
      $curHash = md5(json_encode(self::$classCache));
      if($curHash != self::$classCacheHash){
         file_put_contents(AppCore::getAppCacheDir().self::FILE_CACHE, 
             '<?php'."\n"
             .'self::$classCacheHash = "'.$curHash.'";'."\n"
             .'self::$classCache = '.var_export(self::$classCache, true).';'."\n"
             );
      }
   }
}