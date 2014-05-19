<?php
require_once AppCore::getAppLibDir()."lib".DIRECTORY_SEPARATOR."nonvve".DIRECTORY_SEPARATOR."lessphp".DIRECTORY_SEPARATOR."lessc.inc.php";

class Template_Stream_Css {
   protected $params = array();
   
   public $context;
   
   protected static $translator = false;

   protected $partDir = Template::STYLESHEETS_DIR;

   public function stream_open($path, $mode, $options, &$opened_path) {
      $url = parse_url($path);
      if(!self::$translator){
         self::$translator = new Translator();
      }
      /**
       * $url contain
       * 'scheme' => string 'css' (length=3)
       * 'host' => string 'style.css' (length=9)
       * 'user' => string 'articles' (length=8)
       * 'query' => string 'original=1'
       */
      if(isset($url['query'])){
         parse_str($url['query'], $this->params);
      }
      $filePath =  $url['host']. ( isset($url['path']) ? $url['path'] : '' );
      $fileInfo = pathinfo($filePath);
      
      // get file path
      if(!isset($url['user']) || $url['user'] == 'engine'){
         $fileUrl = $this->getFileUrl($filePath, isset($this->params['original']) );
      } else {
         $fileUrl = $this->getModuleFileUrl($filePath, $url['user'], isset($this->params['original']) );
      }
      
      if($fileUrl){
         Template::addCss($fileUrl.( !empty($this->params) ? '?'.http_build_query($this->params) : '' ));
      }
      var_dump($path, $filePath, $fileUrl, $fileInfo, $url, $this->params);
      echo 'DEBUG';
      $caller = debug_backtrace();
      var_dump($caller[0]['file']);
      
      $match = array();
      preg_match('/modules\/([a-z0-9.]+)?\//', $caller[0]['file'], $match);
      var_dump($match[1]);
      
      die;
      return true;
   }
   
   /**
    * vrací cestu souboru modulu
    * @param type $file
    * @param type $module
    * @param type $original
    * @return string|boolean
    */
   private function getModuleFileUrl($file, $module, $original = false)
   {
      $file = str_replace('/', DIRECTORY_SEPARATOR, $file);
      $faceDir = $parentFaceDir = Template::faceDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$this->partDir.DIRECTORY_SEPARATOR;
      if(VVE_SUB_SITE_DIR != null){
         $parentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $faceDir);
      }
      $mainDir = AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR.$this->partDir.DIRECTORY_SEPARATOR;
      
      $path = null;
      if($original == false AND is_file($faceDir.$file)){ // soubor z face webu
         $path = Template::faceUrl().AppCore::MODULES_DIR."/".$module."/".$this->partDir."/".$file;
      } else if($original == false AND VVE_SUB_SITE_DIR != null AND is_file($parentFaceDir.$file)) { // soubor z nadřazeného face (subdomains)
         $path = Template::faceUrl(true).AppCore::MODULES_DIR."/".$module."/".$this->partDir."/".$file;
      } else if(is_file($mainDir.$file)) { // soubor v knihovnách
         $path = Url_Request::getBaseWebDir().AppCore::MODULES_DIR."/".$module."/".$this->partDir."/".$file;
      } else {
         return false;
      }
      return $path;
   }
   
   /**
    * Vrací cestu s enginu
    * @param type $file
    * @param type $original
    * @return string|boolean
    */
   protected function getFileUrl($file, $original = false)
   {
      $rpFile = str_replace('/', DIRECTORY_SEPARATOR, $file);
      $rpFaceDir = $rpParentFaceDir = Template::faceDir().$this->partDir.DIRECTORY_SEPARATOR;
      if(VVE_SUB_SITE_DIR != null){
         $rpParentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $rpFaceDir);
      }
      $rpMainDir = AppCore::getAppLibDir().$this->partDir.DIRECTORY_SEPARATOR;
      $path = null;

      if($original == false AND is_file($rpFaceDir.$rpFile)){ // soubor z face webu
         $path = Template::face(false).$this->partDir.'/'.$file;
      } else if($original == false AND VVE_SUB_SITE_DOMAIN != null AND is_file($rpParentFaceDir.$rpFile)) { // soubor z nadřazeného face (subdomains)
         $path = str_replace(Url_Request::getBaseWebDir(), Url_Request::getBaseWebDir(true), Template::face(false)).$this->partDir.'/'.$file;
      } else if(is_file($rpMainDir.str_replace('/', DIRECTORY_SEPARATOR, $file))) { // soubor v knihovnách
         if(VVE_SUB_SITE_DOMAIN == null){
            $path = Url_Request::getBaseWebDir().$this->partDir.'/'.$file;
         } else {
            $path = Url_Request::getBaseWebDir(true).$this->partDir.'/'.$file;
         }
      } else {
         return false;
      }
      return $path;
   }

   /**
    * vrací cestu pro less soubory (provede kompilaci)
    * @param type $file
    * @param type $module
    * @param type $original
    * @return string
    * @throws Template_Exception
    */
   protected function getLesscCssFromModule($file, $module, $original = false) {

      $rpFile = str_replace('/', DIRECTORY_SEPARATOR, $file);
      $rpFaceDir = $rpParentFaceDir = Template::faceDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module
         .DIRECTORY_SEPARATOR.self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
      if(VVE_SUB_SITE_DIR != null){
         $rpParentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $rpFaceDir);
      }
      $rpMainDir = AppCore::getAppLibDir().AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$module.DIRECTORY_SEPARATOR
         .self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
      $path = null;

      if($original == false AND is_file($rpFaceDir.$rpFile)){ // soubor z face webu
         $path = $rpFaceDir;
      } else if($original == false AND VVE_SUB_SITE_DOMAIN != null AND is_file($rpParentFaceDir.$rpFile)) { // soubor z nadřazeného face (subdomains)
         $path = $rpParentFaceDir;
      } else if(is_file($rpMainDir.$file)) { // soubor v knihovnách
         $path = $rpMainDir;
      } else {
         throw new Template_Exception(sprintf($this->tr('Soubor "%s%s" nebyl nalezen'), $rpMainDir, $file));
      }

      $cachePath = AppCore::getAppCacheDir().Template::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
      if(!is_dir($cachePath) || !is_writable($cachePath)){
         @mkdir($cachePath);
         @mkdir($cachePath.DIRECTORY_SEPARATOR.'shop'); // for shop styles
      }

      $hash = md5(Url_Request::getDomain().$path.$file);
      $url = Url_Request::getBaseWebDir(false).AppCore::ENGINE_CACHE_DIR."/".self::STYLESHEETS_DIR."/";
      $compiledFileUrl = $url.$file.'-'.$module."-".$hash.".css";

      try {
         $less = new lessc();
         $less->setVariables($this->getLessVariables());
         if(VVE_DEBUG_LEVEL == 0){
            $less->setFormatter("compressed");
         }

         $cacheFile = $cachePath.$rpFile.'-'.$module."-".$hash.".cache";
         if (file_exists($cacheFile)) {
            $cache = unserialize(file_get_contents($cacheFile));
         } else {
            $cache = $path.$rpFile;
         }

         $newCache = $less->cachedCompile($cache);

         if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
            file_put_contents($cacheFile, serialize($newCache));
            file_put_contents($cachePath.$rpFile.'-'.$module."-".$hash.".css", $newCache['compiled']);
         }

         $less = new lessc();
         $less->checkedCompile($path.$rpFile, $cachePath.$rpFile.'-'.$module.".css");

      } catch (Exception $exc) {
         new CoreErrors($exc);
      }
      return $compiledFileUrl;
   }

   protected function getLesscCssFromEngine($file, $original = false) {
      
      $rpFile = str_replace('/', DIRECTORY_SEPARATOR, $file);
      $rpFaceDir = Template::faceDir().self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;
      $rpParentFaceDir = Template::faceDir(true).self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;

      if(VVE_SUB_SITE_DIR != null){
         $rpParentFaceDir = str_replace(AppCore::getAppWebDir(), AppCore::getAppLibDir(), $rpFaceDir);
      }
      $rpMainDir = AppCore::getAppLibDir().self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;

      $path = $url = null;
      if($original == false AND is_file($rpFaceDir.$rpFile)){ // soubor z face webu
         $path = $rpFaceDir;
//         $url = Template::face(false).self::STYLESHEETS_DIR."/";
      } else if($original == false AND VVE_SUB_SITE_DOMAIN != null AND is_file($rpParentFaceDir.$rpFile)) { // soubor z nadřazeného face (subdomains)
         $path = $rpParentFaceDir;
//         $url = str_replace(Url_Request::getBaseWebDir(), Url_Request::getBaseWebDir(true), Template::face(false)).self::STYLESHEETS_DIR."/";
      } else if(is_file($rpMainDir.$rpFile)) { // soubor v knihovnách
         $path = $rpMainDir;
//         $url = Url_Request::getBaseWebDir(true).self::STYLESHEETS_DIR."/";
      } else {
         throw new Template_Exception(sprintf($this->tr('Soubor "%s%s" nebyl nalezen'), $rpMainDir, $file));
      }

      $url = Url_Request::getBaseWebDir(false).AppCore::ENGINE_CACHE_DIR."/".self::STYLESHEETS_DIR."/";
      $targetPath = AppCore::getAppCacheDir().Template::STYLESHEETS_DIR.DIRECTORY_SEPARATOR;

      if(!is_dir($targetPath) || !is_writable($targetPath)){
         @mkdir($targetPath);
         @mkdir($targetPath.DIRECTORY_SEPARATOR.'shop'); // for shop styles
      }
      $hash = md5(Url_Request::getDomain().$path.$file);
      $compiledFileUrl = $url.$file.".".$hash.".css";

      try {
         $less = new lessc();
         $less->setVariables($this->getLessVariables());
         $less->setImportDir(array($rpFaceDir, AppCore::getAppWebDir().self::STYLESHEETS_DIR."/", ));
         if(VVE_DEBUG_LEVEL == 0){
            $less->setFormatter("compressed");
         }

         $cacheFile = AppCore::getAppCacheDir().self::STYLESHEETS_DIR.DIRECTORY_SEPARATOR.$rpFile."-".$hash.".cache";
         if (file_exists($cacheFile)) {
            $cache = unserialize(file_get_contents($cacheFile));
         } else {
            $cache = $path . $rpFile;
         }

         $newCache = $less->cachedCompile($cache);

         if (!is_array($cache) || $newCache["updated"] > $cache["updated"]) {
            file_put_contents($cacheFile, serialize($newCache));
            file_put_contents($targetPath . $rpFile . ".".$hash.".css", $newCache['compiled']);
         }

      } catch (Exception $exc) {
         new CoreErrors($exc);
      }
      return $compiledFileUrl;
   }
   
   /**
    * Připraví pole s proměnými pro šablonu
    * @return array
    */
   private function getLessVariables()
   {
      return array(
         'dirFace' => "'".Template::faceUrl(false)."'",
         'dirFaceParent' => "'".Template::faceUrl(true)."'",
         'dirCore' => "'".Url_Request::getBaseWebDir()."'",
         'dirCoreImages' => "'".Url_Request::getBaseWebDir()."images/'",
         'dirFaceCss' => "'".Template::faceUrl(true).self::STYLESHEETS_DIR."/'",
         'dirCoreCss' => "'".Url_Request::getBaseWebDir().self::STYLESHEETS_DIR."/'",
      );
   }
   
   public function stream_read($count) {
      return null;
   }
   
   public function stream_write($data){
      return 0;
   }
   
   public function stream_tell() {
      return 0;
   }
   
   public function stream_eof() {
      return true;
   }
   
   public function stream_stat() {
      return array();
   }
   
//   public function stream_seek($offset, $whence) {
//      return null;
//   }
}