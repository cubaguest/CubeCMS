<?
/**
 * Základní třída vzhledu
 * 
 * Obsahuje nastavení vhledu, jeho název atd.
 *
 */

class Template_Face {
   private static $name;
   
   protected static $settings = array('name' => 'default', 'version' => "1.0");
   
   protected static $moduleSettings = array();
   
   public static function factory(){
      self::$name = Template::face(true);
      self::loadFaceSettings();
   }
   
   private static function loadFaceSettings(){
      $file = AppCore::getAppWebDir()."face".DIRECTORY_SEPARATOR.self::$name.DIRECTORY_SEPARATOR."face.php";
      if(is_file($file)){
         $face = $modules = array();
         include_once $file;
         self::$settings = $face;
         self::$moduleSettings = $modules;
      }
   }
   
   public static function moduleParam($module, $param, $template = null)
   {
      if($template != null && isset(self::$moduleSettings[$module][$template][$param])){
          return self::$moduleSettings[$module][$template][$param];
      } else if(isset(self::$moduleSettings[$module][$param])){
         return self::$moduleSettings[$module][$param];
      }
      return null;
   }
   
   /* 
    * MAGIC METHODS 
    */
   public static function get($name)
   {
      if (array_key_exists($name, self::$settings)) {
         return self::$settings[$name];
      }
      return null;
   }
}


?>
