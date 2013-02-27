<?
/**
 * Třída pro automatické nahrávání
 * @todo - přesunout sem autoloadery
 */
class Loader {
   const DIR_LIBS = 'nonvve';
   const FILE_BOOTSTRAP = 'bootstrap.php';

   /**
    * Načtení knihovny která není součástí enginu
    * @param $name
    */
   public static function loadLib($name)
   {
      $bootstrap = self::getLibDir().strtolower($name).DIRECTORY_SEPARATOR.self::FILE_BOOTSTRAP;
      if(is_file($bootstrap)){
         include_once $bootstrap;
      }

   }

   /**
    * Meta vrací cestu k externím knihovnám
    * @return string
    */
   protected static function getLibDir()
   {
      return AppCore::getAppLibDir().AppCore::ENGINE_LIB_DIR.DIRECTORY_SEPARATOR.self::DIR_LIBS.DIRECTORY_SEPARATOR;
   }
}