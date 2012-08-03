<?php
class Face {
   const BASE_DIR = "faces";
   
   protected static $current = null;

   protected $name = 'default';
   protected $desc = null; 
   protected $version = null;
    
   protected $params = null;
   protected $modulesParams = null;
   
   /**
    * 
    * @param unknown_type $name
    */
   public function __construct($name = null) {
      // load face config
      if($name == null){
         $name = VVE_TEMPLATE_FACE; 
      }
      $this->name = $name;
      $this->loadParams();

      if(self::$current == null && $name == VVE_TEMPLATE_FACE){
         self::$current = $this;
      }
   }
   
   protected function loadParams() {
      if($this->params == null){
         $file = AppCore::getAppWebDir().Template::FACES_DIR.DIRECTORY_SEPARATOR.$this->name.DIRECTORY_SEPARATOR."face.php";
         if(is_file($file)){
            $face = $modules = array();
            include_once $file;
            $this->params = $face;
            $this->modulesParams = $modules;
         }
      }
   } 
   
   /**
    * Metoda vrací název vzhledu
    * @return string
    */
   public function getName()
   {
      return $this->name;
   }
   
   /**
    * Metoda vrací parametr vzhledu
    * @param string $name -- název parametru
    * @param string $module -- název modulu pokud se má vybrat modul
    * @param mixed $defaultValue -- výchozí hodnota
    */
   public function getParam($name, $module = null, $defaultValue = null)
   {
      if($module == null){ // face param
         if(isset($this->params[$name])){
            return $this->params[$name];
         }
      } else { // module param
         if(isset($this->modulesParams[$module]) && isset($this->modulesParams[$module][$name]) ){
            return $this->modulesParams[$module][$name];
         }
      }
      return $defaultValue;
   }
   
   /* MAGIC METHODS */
   public function __get($name)
   {
      if (array_key_exists($name, $this->params)) {
         return $this->params[$name];
      }
      return null;
   }
   
   public function __isset($name)
   {
      return isset($this->params[$name]);
   }
   
   /**
    * Statická metoda pro parametr aktuální vzhledu
    * @param string $name -- název parametru
    * @param string $module -- název modulu pokud se má vybrat modul
    * @param mixed $defaultValue -- výchozí hodnota
    */
   public static function getParamStatic($name, $module = null, $defaultValue = null)
   {
      return self::getCurrent()->getParam($name, $module, $defaultValue);
   }
   
   /**
    * Metoda vrací aktuální objek vzhledu
    * @return Face
    */
   public static function getCurrent()
   {
      if(self::$current == null){
         self::$current = new self();
      }
      return self::$current;
   }
   
   /**
    * Metoda vrací URL adresu vzhledu
    * @return string
    */
   public function getURL()
   {
      return Url_Request::getBaseWebDir().self::BASE_DIR."/".$this->getName()."/";
   }
   
   /**
    * Matoda vací adresář ke vzhledu
    * @return string
    */
   public function getDir() 
   {
      return AppCore::getAppWebDir().self::BASE_DIR.DIRECTORY_SEPARATOR.$this->getName().DIRECTORY_SEPARATOR;
   }
   
}