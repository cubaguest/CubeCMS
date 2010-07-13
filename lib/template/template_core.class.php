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
   // zastavení výpisu buferu
      ob_start();
      print(parent::__toString());
      $contents = ob_get_contents();

      //vytvoříme pole css souborů
      $cssfiles = null;
      foreach (Template::getStylesheets() as $css) {
         $elem = new Html_Element('link');
         $elem->setAttrib('rel', "stylesheet");
         $elem->setAttrib('type', "text/css");
         $elem->setAttrib('href', $css);
         $cssfiles .= $elem;
      }
      $contents = str_replace('{*-STYLESHEETS-*}', $cssfiles, $contents);

      //vytvoříme pole javascriptů
      $jscripts = null;
      foreach (Template::getJavascripts() as $js) {
         $elem = new Html_Element('script');
         $elem->setAttrib('src', $js);
         $elem->setAttrib('type', "text/javascript");
         $jscripts .= $elem;
      }
      $contents = str_replace('{*-JAVASCRIPTS-*}', $jscripts, $contents);

      // doplníme titulek stránky
      $arr = self::$pageTitle;
      // pokud je titulek nastavíme jej
      $link = new Url_Link(true);
      if(Url_Request::getCurrentUrl() == (string)$link->category()){
         array_push($arr, VVE_MAIN_PAGE_TITLE);
      } else {
         array_push($arr, Category::getSelectedCategory()->getName());
         array_push($arr, VVE_WEB_NAME);
      }
      $title = implode(' '.VVE_PAGE_TITLE_SEPARATOR.' ', $arr);
      
      $title = substr($title, 0, strlen($title)-strlen(VVE_PAGE_TITLE_SEPARATOR)-2);
      $contents = str_replace('{*-PAGE_TITLE-*}', htmlspecialchars($title), $contents);
      // keywords
      if(self::$pageKeywords != null){
         $contents = str_replace('{*-PAGE_KEYWORDS-*}', htmlspecialchars(self::$pageKeywords), $contents);
      }
      // description
      if(self::$pageDescription != null){
         $contents = str_replace('{*-PAGE_DESCRIPTION-*}', htmlspecialchars(self::$pageDescription), $contents);
      }
      // doplníme hlavní nadpis stránky
      $headline = self::$pageHeadline;
      $contents = str_replace('{*-PAGE_HEADLINE-*}', $headline, $contents);

      // dovypsání CoreErrors
      if(!CoreErrors::isEmpty()){
         $tpl = new Template(new Url_Link(true));
         $tpl->addTplFile('coreerrors.phtml');
         ob_start();
         $tpl->renderTemplate();
         $errContents = ob_get_contents();
         ob_end_clean();
         $contents = str_replace('{*-CORE_ERRORS-*}', $errContents, $contents);
      }

      // odstranění všech proměnných a prázdných meta tagů
      $contents = preg_replace(array('/\{\*\-[A-Z0-9_-]+\-\*\}/e', '/<meta(.+?)content="" ?\/>/e'), array(null, null), $contents);

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
      if($keywords != null|''){
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
      if($desc != null|''){
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