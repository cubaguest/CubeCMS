<?php

/**
 * Třída JsPluginu JQuery a jeho komponent.
 * Třída slouží pro javascriptů JQuery
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id$ VVE3.3.0 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída JsPluginu pro JQuery
 * @link          http://jquery.com/
 */
class JsPlugin_JQuery extends JsPlugin {
   const BASE_THEME = 'base';
   const JQUERY_VERSION = '2.1.1';
   const JQUERY_UI_VERSION = '1.9.1';

   const FACE_THEME_DIR = 'jqueryui';

   public static $GoogleCDNThemes = array(
      'base', 'black-tie', 'blitzer', 'cupertino', 'dark-hive', 'dot-luv', 'eggplant', 'excite-bike', 'flick', 'hot-sneaks', 'humanity',
      'le-frog', 'mint-choc', 'overcast', 'pepper-grinder', 'redmond', 'smoothness', 'south-street', 'start', 'sunny', 'swanky-purse',
      'trontastic', 'ui-darkness', 'ui-lightness', 'vader'
   );


   private static $globalTheme = self::BASE_THEME;

   /**
    * Pole s konfigurací pluginu
    * @var array
    */
   protected $config = array('theme' => self::BASE_THEME);

   /**
    * Seznam pluginů, které nejsou součástí jqUI, ale v systému jsou
    * @var array 
    */
   protected $plugins = array('ui.timepicker');

   protected function initJsPlugin() {
      if(Face::getParamStatic('jquery_theme') != null){
         self::$globalTheme = Face::getParamStatic('jquery_theme');
         $this->setCfgParam('theme', Face::getParamStatic('jquery_theme'));
      } else if(defined('VVE_JQUERY_THEME')){
         self::$globalTheme = VVE_JQUERY_THEME;
         $this->setCfgParam('theme', VVE_JQUERY_THEME);
      }
   }

   /**
    * Metoda nastaví globální téma pro JQueryUI
    * @param string $theme -- název tématu
    */
   public static function setTheme($theme) {
      self::$globalTheme = $theme;
   }

   /**
    * Metoda vrací adresář k tématu
    * @return string
    */
   public static function getThemeDir($theme){
      // cur face
      if(is_dir(AppCore::getAppWebDir().Template::FACES_DIR.DIRECTORY_SEPARATOR.Template::face().DIRECTORY_SEPARATOR.self::FACE_THEME_DIR.DIRECTORY_SEPARATOR.$theme)){
         return Url_Request::getBaseWebDir().Template::FACES_DIR.'/'.Template::face(true).'/'.self::FACE_THEME_DIR.'/'.$theme.'/';
      }
      // main site face
      else if(VVE_SUB_SITE_DIR != null AND is_dir(AppCore::getAppLibDir().Template::FACES_DIR.DIRECTORY_SEPARATOR.Template::face().DIRECTORY_SEPARATOR.self::FACE_THEME_DIR.DIRECTORY_SEPARATOR.$theme)){
         return Url_Request::getBaseWebDir(true).Template::FACES_DIR.'/'.Template::face(true).'/'.self::FACE_THEME_DIR.'/'.$theme.'/';
      }
      // main css file
      return Url_Request::getBaseWebDir(true).self::JSPLUGINS_BASE_DIR.'/jquery/ui/themes/'.$theme.'/';
   }

   protected function addCss($css) {
      // NOT wok correctly
//      if(defined('VVE_ALLOW_EXTERNAL_JS') AND VVE_ALLOW_EXTERNAL_JS == true AND VVE_DEBUG_LEVEL <= 1 AND in_array($this->getCfgParam('theme'), self::$GoogleCDNThemes)){
//         $this->addFile("http://ajax.googleapis.com/ajax/libs/jqueryui/" . self::JQUERY_UI_VERSION . "/themes/".$this->getCfgParam('theme')."/jquery-ui.css");
//      } else {
//         $this->addFile(new JsPlugin_CssFile("jquery.ui.$css.css", false, self::getThemeDir($this->getCfgParam('theme'))));
//      }
      $this->addFile(new JsPlugin_CssFile("ui/themes/jquery-ui.min.css", false));
      $this->addFile(new JsPlugin_CssFile("theme.css", false, self::getThemeDir($this->getCfgParam('theme'))));
   }

   protected function addJs($name) {
      if (defined('VVE_ALLOW_EXTERNAL_JS') AND VVE_ALLOW_EXTERNAL_JS == true AND VVE_DEBUG_LEVEL <= 1 AND !in_array($name, $this->plugins)) {
//         $this->addFile("http://ajax.googleapis.com/ajax/libs/jqueryui/" . self::JQUERY_UI_VERSION . "/jquery-ui.min.js");
         $this->addFile(Url_Request::getTransferProtocol()."ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js");
      } else {
//         $this->addFile(new JsPlugin_JsFile("jquery.$name.min.js"));
         $this->addFile(new JsPlugin_JsFile("jquery-ui.min.js"));
      }
   }


   protected function setFiles() {
//		Přidání js soubrů pluginu
      if (defined('VVE_ALLOW_EXTERNAL_JS') AND VVE_ALLOW_EXTERNAL_JS == true AND VVE_DEBUG_LEVEL <= 1) {
         $this->addFile(Url_Request::getTransferProtocol()."ajax.googleapis.com/ajax/libs/jquery/" . self::JQUERY_VERSION . "/jquery.min.js");
      } else {
         $this->addFile(new JsPlugin_JsFile("jquery-" . self::JQUERY_VERSION . ".min.js"));
      }
      $this->addFile(new JsPlugin_JsFile("jquery-migrate-1.2.1.min.js"));
   }

   /**
    * Metda vytvoří výchozí konfigurační soubor
    */
   protected function generateFile(JsPlugin_JsFile $file) {

   }

   /**
    * Metody pro přidávání částí jQuery UI, effects
    */

   /**
    * Metoda přidá jádro pro efekty UI
    * @return JsPlugin_JQuery
    */
   public function addUICore() {
      $this->addJs('ui.core');
      $this->addCss('core');
      $this->addCss('theme');
      return $this;
   }

   /**
    * Metoda přidá widgent UI - widget (základní pro dialog a ostatní věci)
    * @return JsPlugin_JQuery
    */
   public function addUIWidget() {
      //deps
      $this->addUICore();
      $this->addJs("ui.widget");
      return $this;
   }

   /**
    * Metoda přidá část UI - mouse (pro práci s myší)
    * @return JsPlugin_JQuery
    */
   public function addUIMouse() {
      //deps
      $this->addUICore();
      $this->addUIWidget();
      $this->addJs("ui.mouse");
      return $this;
   }

   /**
    * Metoda přidá část UI - position (pro práci s pozicemi boxů)
    * @return JsPlugin_JQuery
    */
   public function addUIPosition() {
      $this->addJs("ui.position");
      return $this;
   }

   /**
    * Metoda přidá efekty UI - draggable (přesunování)
    * @return JsPlugin_JQuery
    */
   public function addUIDraggable() {
      $this->addUICore();
      $this->addUIMouse();
      $this->addUIWidget();
      $this->addJs("ui.draggable");
      return $this;
   }

   /**
    * Metoda přidá efekty UI - droppable (odstraňování)
    * @return JsPlugin_JQuery
    */
   public function addUIDroppable() {
      $this->addUICore();
      $this->addUIWidget();
      $this->addUIMouse();
      $this->addUIDraggable();
      $this->addJs("ui.droppable");
      return $this;
   }

   /**
    * Metoda přidá efekty UI - resizable (zěna velikosti)
    * @return JsPlugin_JQuery
    */
   public function addUIResizable() {
      $this->addUICore();
      $this->addUIMouse();
      $this->addUIWidget();
      $this->addJs("ui.resizable");
      $this->addCss('resizable');
      return $this;
   }

   /**
    * Metoda přidá efekty UI - selectable (označování)
    * @return JsPlugin_JQuery
    */
   public function addUISelectable() {
      $this->addUICore();
      $this->addUIMouse();
      $this->addUIWidget();
      $this->addJs("ui.selectable");
      return $this;
   }

   /**
    * Metoda přidá efekty UI - sortable (řazení)
    * @return JsPlugin_JQuery
    */
   public function addUISortable() {
      $this->addUICore();
      $this->addUIMouse();
      $this->addUIWidget();
      $this->addJs("ui.sortable");
      return $this;
   }

   /*
    * Widgets
    */

   /**
    * Metoda přidá widgent UI - accordion (roztahování boxů)
    * @return JsPlugin_JQuery
    */
   public function addUIAccordion() {
      $this->addUICore();
      $this->addUIWidget();
      $this->addJs("ui.accordion");
      $this->addCss('accordion');
      return $this;
   }

   /**
    * Metoda přidá widgent UI - button
    * @return JsPlugin_JQuery
    */
   public function addUIButton() {
      $this->addUICore();
      $this->addUIWidget();
      $this->addCss("button");
      $this->addJs("ui.button");
      return $this;
   }

   /**
    * Metoda přidá widgent UI - menu
    * @return JsPlugin_JQuery
    */
   public function addUIMenu() {
      $this->addUICore();
      $this->addUIWidget();
      $this->addUIPosition();
      $this->addCss("menu");
      $this->addJs("ui.menu");
      return $this;
   }

   /**
    * Metoda přidá widgent UI - autocomplete
    * @return JsPlugin_JQuery
    */
   public function addUIAutoComplete() {
      $this->addUICore();
      $this->addUIWidget();
      $this->addUIPosition();
      $this->addUIMenu();
      $this->addJs("ui.autocomplete");
      $this->addCss("autocomplete");
      return $this;
   }

   /**
    * Metoda přidá widget UI - dialog (box dialogu)
    * @return JsPlugin_JQuery
    */
   public function addUIDialog() {
      $this->addUICore();
      $this->addUIWidget();
      $this->addUIButton();
      $this->addUIDraggable();
      $this->addUIMouse();
      $this->addUIPosition();
      $this->addUIResizable();
      $this->addJs("ui.dialog");
      $this->addCss('dialog');
      return $this;
   }

   /**
    * Metoda přidá widget UI - slider (posunovač)
    * @return JsPlugin_JQuery
    */
   public function addUISlider() {
      $this->addUICore();
      $this->addUIMouse();
      $this->addUIWidget();
      $this->addJs("ui.slider");
      $this->addCss('slider');
      return $this;
   }

   /**
    * Metoda přidá widget UI - spinner input se šipkami pro čísla
    * @return JsPlugin_JQuery
    */
   public function addUISpinner() {
      $this->addUICore();
      $this->addUIWidget();
      $this->addUIButton();
      $this->addJs("ui.spinner");
      $this->addCss('spinner');
      return $this;
   }

   /**
    * Metoda přidá widgent UI - tabs (tabulka boxů - záložky)
    * @return JsPlugin_JQuery
    */
   public function addUITabs() {
      $this->addUICore();
      $this->addUIWidget();
      $this->addJs("ui.tabs");
      $this->addCss('tabs');
      return $this;
   }

   /**
    * Metoda přidá widgent UI - tooltip (nápověda)
    * @return JsPlugin_JQuery
    */
   public function addUITooltip() {
      $this->addUICore();
      $this->addUIWidget();
      $this->addUIPosition();
      $this->addJs("ui.tooltip");
      $this->addCss('tooltip');
      return $this;
   }

   /**
    * Metoda přidá widgent UI - datepicker (box s výběrem data)
    * @return JsPlugin_JQuery
    */
   public function addUIDatepicker() {
      $this->addUICore();
      $this->addUIWidget();
      /**
       * @todo zkontrolovat závislos s obrázky umístěnými ve složce pluginu
       */
      $this->addJs("ui.datepicker");
      if(Locales::getLang() != "en"){
         $this->addFile(new JsPlugin_JsFile("datepicker-" . Locales::getLang() . ".js", false, 'ui/i18n/'));
      }
      $this->addCss('datepicker');
      return $this;
   }
   
   /**
    * Metoda přidá widgent UI - timepicker (box s výběrem času)
    * @return JsPlugin_JQuery
    */
   public function addUITimepicker() {
      $this->addUICore();
      $this->addUISlider();
      $this->addUIDatepicker();
      $this->addJQPlugin("timepicker");
      if(Locales::getLang() != "en"){
         $this->addFile(new JsPlugin_JsFile("jquery.ui.timepicker-" . Locales::getLang() . ".js", false, 'ui/i18n/'));
      }
      $this->addFile(new JsPlugin_CssFile("jquery.ui.timepicker.css", false, Url_Request::getBaseWebDir(true).self::JSPLUGINS_BASE_DIR.'/jquery/ui/themes/'));
      return $this;
   }

   /**
    * Metoda přidá widgent UI - progressbar (pregress bar)
    * @return JsPlugin_JQuery
    */
   public function addUIProgressBar() {
      $this->addUICore();
      $this->addUIWidget();
      $this->addJs("ui.progressbar");
      $this->addCss('progressbar');
      return $this;
   }

   /*
    * Efekty
    */

   /**
    * Metoda přidá efekt UI - core (jádro efektů)
    * @return JsPlugin_JQuery
    */
   public function addUIEffectCore() {
      $this->addJs("effects.core");
      return $this;
   }

   /**
    * Metoda přidá efekty UI - vybraný efekt
    * @param string $efectName -- název efektu
    * @return JsPlugin_JQuery
    */
   public function addUIEffect($efectName) {
      $this->addUIEffectCore();
      $this->addJs("effects." . $efectName);
      return $this;
   }

   /*
    * PLUGINS
    */

   /**
    * Metoda přidá plugin pro práci s cookie
    * @return JsPlugin_JQuery
    */
   public function addPluginCookie() {
      $this->addJQPlugin("cookie");
      return $this;
   }

   /**
    * Metoda přidá plugin pro práci s metadaty
    * @return JsPlugin_JQuery
    */
   public function addPluginMetadata() {
      $this->addFile(new JsPlugin_JsFile("jquery.metadata.min.js"));
      return $this;
   }

   /**
    * Metoda přidá plugin pro ajax upload souborů
    * @return JsPlugin_JQuery
    */
   public function addPluginAjaxUploadFile() {
      $this->addFile(new JsPlugin_JsFile("ajaxupload.2.8.js"));
      return $this;
   }

   /**
    * Metoda přidá plugin OpacityRollover
    * @return JsPlugin_JQuery
    */
   public function addPluginOpacityRollOver() {
      $this->addFile(new JsPlugin_JsFile("jquery.opacityrollover.js"));
      return $this;
   }

   /**
    * Metoda přidá plugin ()
    * @param string -- název pluginu
    * @param string -- cesta pluginu
    * @return JsPlugin_JQuery
    * @todo -- dodělat načítání podle cesty
    */
   public function addJQPlugin($name, $path = null) {
      $this->addFile(new JsPlugin_JsFile("plugins/jquery.".$name.".min.js"));
      return $this;
   }

}