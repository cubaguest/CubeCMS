<?php
/**
 * Třída pro práci s šablonami modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem).
 * Umožňuje všechny základní operace při volbě a plnění šablony a jejímu zobrazení.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ Cube CMS 7.7 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu šablony
 */

class Template_Core extends Template {
   /**
    * Výchozí šablona systému
    */
   const INDEX_DEFAULT_TEMPLATE = 'index.phtml';

   /**
    * Šablona systému pro výstup v html (tisk)
    */
   const INDEX_PRINT_TEMPLATE = 'index_print.phtml';

   /**
    * Šablona pro ajax requesty v iframe
    */
   const INDEX_AJAXIFRAME_TEMPLATE = 'index_ajax_iframe.phtml';

   /**
    * Šablona pro mobilní zařízení
    */
   const INDEX_HANDLE_TEMPLATE = 'index_handle.phtml';

   /**
    * Nastavená šablony systému
    * @var string
    */
   private static $indexFile = self::INDEX_DEFAULT_TEMPLATE;

   /**
    * Proměnná s titulkem stránky
    * @var array
    */
   private static $pageTitle = array();

   /**
    * Pole s metatagy, které se doplní do hlavní šablony
    * @var array
    */
   private static $metaTags = array();

   /**
    * Cesta ke Cover obrázku
    * @var string
    */
   private static $coverImagePath = null;

   /**
    * Konstruktor
    */
   function  __construct() {
      ob_start();
      parent::__construct(new Url_Link());
      // pokud je titulek nastavíme jej
      $link = new Url_Link(true);
      if(Url_Request::getCurrentUrl() == (string)$link->category()){
         array_push(self::$pageTitle, VVE_MAIN_PAGE_TITLE);
      } else {
         //@todo možná dávat celou cestu ke kategorii
         if(empty (self::$pageTitle)){
            array_push(self::$pageTitle, VVE_WEB_NAME);
            if(defined('VVE_USE_CATEGORY_ALT_IN_TITLE') && VVE_USE_CATEGORY_ALT_IN_TITLE == true){
               array_push(self::$pageTitle, Category::getSelectedCategory()->getName(true));
            } else {
               array_push(self::$pageTitle, Category::getSelectedCategory()->getName());
            }
         } else {
            self::$pageTitle = array_merge(array(VVE_WEB_NAME), self::$pageTitle);
         }
      }

      // prepare base Tags
      self::setMetaTag('generator', 'Cube-CMS '.AppCore::ENGINE_VERSION);
      self::setMetaTag('canonical', $this->link()->rmParam()); // drop every page params
      // base image
      if(Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_ICON} != null){
         self::setCoverImage(Category::getImageDir(Category::DIR_IMAGE)
            .Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_ICON} );
      }
   }

   /**
    * Metoda vkládá cestu k css souboru i s názvem souboru
    * @param string $name -- název souboru
    * @return string -- cesta k souboru
    */
   public function style($name) {
      $path = null;
      try {
         $path = $this->getLinkPathFromEngine($name);
      } catch(Template_Exception $e) {
      }
      return $path;
   }

   /**
    * Magická metoda převede šablonu na řetězec
    * @return string -- vygenerovaný řetězec z šablon
    */
   public function   __toString() {
      // odeslání hlaviček
      try {
         Template_Output::sendHeaders();
      } catch (BadMethodCallException $exc) {
         CoreErrors::addException($exc);
      }

      // zastavení výpisu buferu
      if (defined('VVE_USE_GZIP') AND VVE_USE_GZIP == true AND
         isset ($_SERVER['HTTP_ACCEPT_ENCODING']) AND substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
         ob_start("ob_gzhandler");
      } else {
         ob_start();
      }
      echo parent::__toString();
      $contents = ob_get_clean();

      if($contents == null){
         $errorTpl = new Template(new Url_Link(true));
         $errorTpl->addFile('tpl://error/coreerrorbody.phtml');
         $errorTpl->renderTemplate();
         $contents = ob_get_clean();
      }

      // css
      if(VVE_DEBUG_LEVEL == 0){
         $css = $this->getCombinedCss();
      } else {
         $css = Template::getStylesheets();
      }
      array_walk($css, create_function('&$i,$k','$i = "<link rel=\"stylesheet\" type=\"text/css\" href=\"$i\" />\n";'));
      $cssfiles = implode('', $css);
      unset ($css);
      // js
      $js = Template::getJavascripts();
      array_walk($js, create_function('&$i,$k','$i = "<script type=\"text/javascript\" src=\"$i\"></script>\n";'));
      $jscripts = implode('', $js);
      unset ($js);

      // doplníme titulek stránky
      $title = implode(' '.VVE_PAGE_TITLE_SEPARATOR.' ', array_reverse (self::$pageTitle));

      // dovypsání CoreErrors
      $errCnt = null;
      if(!CoreErrors::isEmpty() AND VVE_DEBUG_LEVEL > 0){
         $tpl = new Template(new Url_Link(true));
         $tpl->addFile('tpl://error/coreerrors.phtml');
         ob_start();
         $tpl->renderTemplate();
         $errCnt = ob_get_clean();
         ob_end_clean();
      }

//      var_dump('tady',self::$metaTags);ob_flush();
      // desc a keywords pokud nejsou zadány, vyberou se z kategorie
      if(self::getMetaTag('description') == null AND Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION} != null){
         self::setMetaTag('description', Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION});
      }
      if(self::getMetaTag('keywords') == null AND Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_KEYWORDS} != null){
         self::setMetaTag('keywords',Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_KEYWORDS});
      }

      $metaTags = null;

      if(self::$coverImagePath != null ){
         $metaTags .= '<link rel="image_src" href="'.self::$coverImagePath.'"/>'."\n";
         self::setMetaTag('og:image', self::$coverImagePath);
      } else if(isset(self::$metaTags['og:image']) && self::$coverImagePath == null){
         $metaTags .= '<link rel="image_src" href="'.self::$metaTags['og:image'].'"/>'."\n";
      }

      if(!empty (self::$metaTags)){
         foreach (self::$metaTags as $key => $value) {
            if((string)$value == null){
               continue;
            }
            /**
             * @todo tady asi přidat spíše hodnotu do metatagů o jaký se jedná
             */
            if(strpos($key, ':') === false){
               $metaTags .= str_repeat(' ', 6).'<meta name="'.htmlspecialchars(strip_tags($key)).'" content="'.htmlspecialchars(strip_tags($value)).'" />'."\n";
            } else {
               $metaTags .= str_repeat(' ', 6).'<meta property="'.htmlspecialchars(strip_tags($key)).'" content="'.htmlspecialchars(strip_tags($value)).'" />'."\n";
            }
         }
      }

      // replacements vars
      $contents = preg_replace(array(
         // basic
         '/(<!-- *)?\{\*-STYLESHEETS-\*\}( *-->)?/',
         '/(<!-- *)?\{\*-JAVASCRIPTS-\*\}( *-->)?/',
         // basic meta
         '/(<!-- *)?\{\*-PAGE_TITLE-\*\}( *-->)?/',
         '/(<!-- *)?\{\*-PAGE_HEADLINE-\*\}( *-->)?/',
         // user meta tags
         '/(<!-- *)?\{\*-PAGE_META_TAGS-\*\}( *-->)?/',
         // CORE ERRORS
         '/(<!-- *)?\{\*-CORE_ERRORS-\*\}( *-->)?/',
         //  remove not used vars
         '/(<!-- *)?\{\*\-[A-Z0-9_-]+\-\*\}( *-->)?/',
         // remove empty meta tags
         '/[ ]*<meta name="[a-z]+" content="" ?\/>\n/i'
      ), array(
         // basic
         $cssfiles,
         $jscripts,
         // basic meta
         $title,
         htmlspecialchars(strip_tags(self::$pageHeadline)),
         $metaTags,
         // CORE ERRORS
         $errCnt,
         //  remove not used vars
         '',
         // remove empty meta tags
         ''
      ), $contents);
      ob_end_clean();
      return ((string)$contents);
   }

   public function getCombinedCss()
   {
      $files = array();
      $filesForCompress = array();
      $filesHash = null;
      $cssFiles = Template::getStylesheets();
      foreach ($cssFiles as $css) {
//         pokud je soubor s enginu
         if(strpos($css, Url_Request::getBaseWebDir(false)) !== false && strpos($css, 'nocompress') === false){
            // create absolute path
            $fileAbs = AppCore::getAppWebDir().str_replace(array(Url_Request::getBaseWebDir(false), '/'),array('', DIRECTORY_SEPARATOR), $css );
            $filesForCompress[] = $fileAbs;
            $filesHash .= $css.filemtime($fileAbs);
         } else if(strpos($css, Url_Request::getBaseWebDir(true)) !== false && strpos($css, 'nocompress') === false){
            // create absolute path
            $fileAbs = AppCore::getAppLibDir().str_replace(array(Url_Request::getBaseWebDir(true), '/'),array('', DIRECTORY_SEPARATOR), $css );
            $filesForCompress[] = $fileAbs;
            $filesHash .= $css.filemtime($fileAbs);
         } else {
            $files[] = $css;
         }
      }

      $fileName = md5($filesHash)."s.css";

      if(!is_file(AppCore::getAppCacheDir()."stylesheets".DIRECTORY_SEPARATOR. $fileName) || true == true){
         file_put_contents(AppCore::getAppCacheDir(). "stylesheets".DIRECTORY_SEPARATOR. $fileName, "");
         // generate file
         foreach($filesForCompress as $file){
            $cssCnt = file_get_contents($file);
            // replace relative paths
            $dir = dirname($file);
            $url = str_replace(
               array(AppCore::getAppLibDir(),AppCore::getAppWebDir(), DIRECTORY_SEPARATOR),
               array(Url_Request::getBaseWebDir(), Url_Request::getBaseWebDir(), '/'), $dir)."/";
            $cssCnt = preg_replace('#url\((?!\s*[\'"]?(?:https?:)?//)\s*([\'"])?#', "url($1{$url}", $cssCnt);
//            var_dump($file, $dir, $url);flush();

            file_put_contents(AppCore::getAppCacheDir(). "stylesheets".DIRECTORY_SEPARATOR. $fileName,
               ' /* file: '.$file." mtime: ".filemtime($file).' */'."\n".$cssCnt, FILE_APPEND);
         }
      }
      array_unshift($files, Url_Request::getBaseWebDir().AppCore::ENGINE_CACHE_DIR."/stylesheets/".$fileName);
      return $files;
   }

   /**
    * Metoda vrací nasatvenou hlavní šablonu
    * @return string
    */
   public static function getMainIndexTpl(){
      return self::$indexFile;
   }

   /**
    * Metoda nastaví hlavní šablonu systému
    * @param string $tplFile -- název hlavní šablony systému
    */
   public static function setMainIndexTpl($tplFile){
      self::$indexFile = $tplFile;
   }

   /**
    * Metoda nastavuje titulek stránky (zatím nefunkční)
    * @param string $title -- titulek stránky
    * @param bool $merge -- jestli se má titulek připojit k již nasatvenému
    */
   public static function addToPageTitle($title, $pos = null) {
      if($pos === null){
         array_push(self::$pageTitle, $title);
      } else {
         array_splice(self::$pageTitle, $pos, 0, $title);
      }
   }

   /**
    * Metoda nastavuje titulek stránky (zatím nefunkční)
    * @param string $title -- titulek stránky
    * @param bool $merge -- jestli se má titulek připojit k již nasatvenému
    */
   public static function setPageTitle($title) {
      self::$pageTitle = array($title);
   }

   /**
    * Metoda vrací titulek stránky
    * @return string
    */
   public static function getPageTitle() {
      return self::$pageTitle;
   }

   /**
    * Metoda nastavuje klíčová slova stránky
    * @param string $title -- klíčová slova stránky
    */
   public static function setPageKeywords($keywords) {
      self::setMetaTag('keywords', (string)$keywords);
   }

   /**
    * Metoda vrací klíčová slova stránky
    * @return string
    */
   public static function getPageKeyword() {
      return self::getMetaTag('keywords');
   }

   /**
    * Metoda nastavuje popisek stránky
    * @param string $title -- popisek stránky
    */
   public static function setPageDescription($desc) {
      self::setMetaTag('description', (string)$desc);
   }

   /**
    * Metoda vrací popisek stránky
    * @return string
    */
   public static function getPageDescription() {
      return self::getMetaTag('description');
   }

   /**
    * Metoda nastavuje meta tag stránky
    * @param string $name -- název tagu
    * @param string $value -- hodnota tagu
    *
    * @example
    * <b>Facebook</b><br/>
    * <meta property="og:title" content="Article title" /><br/>
    * <meta property="og:type" content="movie"/><br/>
    * <meta property="og:url" content="http://www.imdb.com/title/tt0117500/"/><br/>
    * <meta property="og:image" content="http://ia.media-imdb.com/rock.jpg"/><br/>
    * <meta property="og:site_name" content="IMDb"/><br/>
    * <meta property="fb:admins" content="USER_ID"/><br/>
    * <meta property="og:description" content="Article description" /><br/>
    * <meta property="og:video:width" content="Article video width" /><br/>
    * <meta property="og:video:height" content="Article video height" /><br/>
    * @see http://developers.facebook.com/docs/opengraph/
    */
   public static function setMetaTag($name, $value = null) {
      if($value == null){
         unset (self::$metaTags[$name]);
      } else {
         self::$metaTags[$name] = (string)$value;
      }
   }

   /**
    * Metoda vrací zadaný meta tag stránky newbo všechny metatagy
    * @return string
    */
   public static function getMetaTag($name = null) {
      if($name != null){
         if(isset (self::$metaTags[$name])){
            return self::$metaTags[$name];
         }
         return null;
      }
      return self::$metaTags;
   }

   /**
    * Metoda nastaví titulní obrázek stránky
    * @param string $path - URL obrázku
    */
   public static function setCoverImage($path)
   {
      self::$coverImagePath = $path;
   }

   /**
    * Metoda vrací adresu titulního obrázku
    * @return string $path
    */
   public static function getCoverImage()
   {
      return self::$coverImagePath;
   }

   /**
    * Metoda vrací objekt detekce prohlížeče
    * @return Browser
    */
   public static function getBrowser()
   {
      return self::$browser;
   }

}
