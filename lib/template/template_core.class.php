<?php
/**
 * Třída pro práci s šablonami modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem). 
 * Umožňuje všechny základní operace při volbě a plnění šablony a jejímu zobrazení.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
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
    * Popisek stránky
    * @var string
    */
   private static $pageDescription = null;

   /**
    * Klíčová slova stránky
    * @var string
    */
   private static $pageKeywords = null;

   /**
    * Konstruktor
    */
   function  __construct() {
      ob_start();
      self::setPageDescription(Category::getSelectedCategory()
         ->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION});
      self::setPageKeywords(Category::getSelectedCategory()
         ->getCatDataObj()->{Model_Category::COLUMN_KEYWORDS});
      parent::__construct(new Url_Link());
   }

   /**
    * Metoda vkládá cestu k css souboru i s názvem souboru
    * @param string $name -- název souboru
    * @return string -- cesta k souboru
    */
   public function style($name) {
      return parent::getFileDir($name, self::STYLESHEETS_DIR, false).$name;
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
      echo(parent::__toString());
      $contents = ob_get_contents();
      // css
      $css = Template::getStylesheets();
      array_walk($css, create_function('&$i,$k','$i = "<link rel=\"stylesheet\" type=\"text/css\" href=\"$i\" />\n";'));
      $cssfiles = implode('', $css);
      unset ($css);
      // js
      $js = Template::getJavascripts();
      array_walk($js, create_function('&$i,$k','$i = "<script type=\"text/javascript\" src=\"$i\"></script>\n";'));
      $jscripts = implode('', $js);
      unset ($js);

      // doplníme titulek stránky
      $arr = self::$pageTitle;
      // pokud je titulek nastavíme jej
      $link = new Url_Link(true);
      if(Url_Request::getCurrentUrl() == (string)$link->category()){
         array_push($arr, VVE_MAIN_PAGE_TITLE);
      } else {
         //@todo možná dávat celou cestu ke kategorii
         array_push($arr, Category::getSelectedCategory()->getName());
         array_push($arr, VVE_WEB_NAME);
      }
      $title = implode(' '.VVE_PAGE_TITLE_SEPARATOR.' ', $arr);

      // dovypsání CoreErrors
      $errCnt = null;
      if(!CoreErrors::isEmpty() AND VVE_DEBUG_LEVEL > 0){
         $tpl = new Template(new Url_Link(true));
         $tpl->addTplFile('coreerrors.phtml');
         ob_start();
         $tpl->renderTemplate();
         $errCnt = ob_get_contents();
         ob_end_clean();
      }

      // desc a keywords pokud nejsou zadány, vyberou se z kategorie
      if(self::$pageDescription == null AND Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION} != null){
         self::$pageDescription = Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION};
      }
      if(self::$pageKeywords == null AND Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_KEYWORDS} != null){
         self::$pageKeywords = Category::getSelectedCategory()->getCatDataObj()->{Model_Category::COLUMN_KEYWORDS};
      }

      // replacements vars
      $contents = preg_replace(array(
         // basic
         '/\{\*-STYLESHEETS-\*\}/',
         '/\{\*-JAVASCRIPTS-\*\}/',
         // basic meta
         '/\{\*-PAGE_TITLE-\*\}/',
         '/\{\*-PAGE_KEYWORDS-\*\}/',
         '/\{\*-PAGE_DESCRIPTION-\*\}/',
         '/\{\*-PAGE_HEADLINE-\*\}/',
         // CORE ERRORS
         '/\{\*-CORE_ERRORS-\*\}/',
         //  remove not used vars
         '/\{\*\-[A-Z0-9_-]+\-\*\}/',
         // remove empty meta tags
         '/[ ]*<meta name="[a-z]+" content="" ?\/>\n/i'
         ), array(
         // basic
         $cssfiles,
         $jscripts,
         // basic meta
         $title,
         htmlspecialchars(strip_tags(self::$pageKeywords)),
         htmlspecialchars(strip_tags(self::$pageDescription)),
         htmlspecialchars(strip_tags(self::$pageHeadline)),
         // CORE ERRORS
         $errCnt,
         //  remove not used vars
         '',
         // remove empty meta tags
         ''
         ), $contents);
      ob_clean();
      return ((string)$contents);
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
   public static function addToPageTitle($title) {
      array_push(self::$pageTitle, $title);
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
      if((string)$keywords != null){
         self::$pageKeywords = (string)$keywords;
      }
   }

   /**
    * Metoda vrací klíčová slova stránky
    * @return string
    */
   public static function getPageKeyword() {
      return self::$pageKeywords;
   }

     /**
    * Metoda nasatvuje popisek stránky
    * @param string $title -- popisek stránky
    */
   public static function setPageDescription($desc) {
      if((string)$desc != null){
         self::$pageDescription = (string)$desc;
      }
   }

   /**
    * Metoda vrací popisek stránky
    * @return string
    */
   public static function getPageDescription() {
      return self::$pageDescription;
   }

}
?>