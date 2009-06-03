<?php
/**
 * Třída pro práci s locale (místním nastavením).
 * Třída slouží pro práci s jazykovým nastavením aplikace. Je určena k volbě
 * výchozího a zvoleného jazyka aplikace. Lze s ní ískat i kompletní výpis všech
 * jazyků, a všech použiých jazyků v aplikaci.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.4 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu jazykového nastavení
 * @internal      Last ErrorCode 2
 */

class Locale {
   /**
    * Oddělovač jazyků v konfiguračním souboru
    * @var string
    */
   const LANG_SEPARATOR = ';';

   /**
    * Název Session s jazykem
    * @var string
    */
   const SESSION_LANG = 'lang';

   /**
    * Název adresáře s locales
    * @var string
    */
   const LOCALES_DIR = 'locale';

   /**
    * výchozí doména pro gettext
    * @var string
    */
   const GETTEXT_DEFAULT_DOMAIN = 'messages';

   /**
    * Gettext engine locales dir
    * @var string
    */
   const GETTEXT_DEFAULT_LOCALES_DIR = './locale';

   /**
    * Doména pro gettext a moduly
    * @var string
    */
   const GETTEXT_MDOMAIN = 'module_messages';

   /**
    * Pole se všemi locales
    * @var array
    */
   private static $locales = array("cs" => "cs_CZ.UTF-8",
                                   "en" => "en_US",
                                   "de" => "de_DE");

   /**
    * Pole se všemi názvy jazyků
    *
    * @var array
    */
   private static $localesNames = array();

   /**
    * Výchozí jazyk aplikace
    * @var string
    */
   private static $defaultLang = null;

   /**
    * Vybraný jazyk aplikace
    * @var string
    */
   private static $selectLang = null;

   /**
    * Jazyky aplikace
    * @var array
    */
   private static $appLangs = array();

   /**
    * Doména modulu
    * @var string
    */
   private $moduleDomain = null;

   /**
    * Metoda pro vytvoření prostředí třídy locales
    */
   public static function factory() {
      // vybere jazyky aplikace
      self::parseLangs();
      // nastaví výchozí jazyk
      self::$defaultLang = self::$appLangs[0];
   }

   /**
    * Metoda nastaví aplikaci pro zvolený jazyk
    */
   public static function selectLang() {
      $session = new Sessions();
      if(self::$selectLang == null){
         // pokud nebyl jazyk nastaven při prohlížení
         if($session->isEmpty(self::SESSION_LANG)){
            // načteme jazyk klienta a zjistíme, jestli existuje mutace aplikace
            $lang = self::getLangsByClient();
            if($lang !== false){
               self::$selectLang = $lang;
            } else {
               self::$selectLang = self::$defaultLang;
            }
            $session->add(self::SESSION_LANG, self::$selectLang);
            if(self::$selectLang != self::$defaultLang){
               $link = new Links();
               $link->lang(self::$selectLang)->reload();
            }
         }
         // jazyk klienta byl zjištěn a nastaven
         else {
            self::$selectLang = self::$defaultLang;
            $session->add(self::SESSION_LANG, self::$selectLang);
         }
      } else {
         if(!self::isAppLang(self::$selectLang)){
            self::$selectLang = self::$defaultLang;
            new CoreErrors(new UnexpectedValueException(
                  _('Zvolený jazyk není v aplikaci implementován'),1));
         }
         
         if(self::$selectLang != $session->get(self::SESSION_LANG)){
            $session->add(self::SESSION_LANG, self::$selectLang);
         } else {
            self::$selectLang = $session->get(self::SESSION_LANG);
         }
      }
      // Doplnění jazyků
      self::_setLangTranslations();
      // nastaví Locales
      self::setLocalesEnv();
   }

   /**
    * Metoda zjistí jestli se jedná o jazyk aplikace
    * @param string $lang -- jazyk (cs, en, de)
    * @return boolean -- true pokud se jedná o jazyk aplikace
    */
   private static function isAppLang($lang){
      if(in_array($lang, self::$appLangs)){
         return true;
      }
      return false;
   }

   /**
    * Metoda vrací pole s názvy jazyků
    *
    * @return array -- pole s názvy jazyků
    */
   public static function getAppLangsNames() {
      $returnArray = array();
      foreach (self::getAppLangs() as $langKey => $lang) {
         $returnArray[$lang] = self::$localesNames[$lang];
      }
      return $returnArray;
   }

   /**
    * Metoda načte a vrátí podporované jazyky klienta
    * @return array -- pole jazyků klienta
    * @todo -- optimalizovat
    */
   public static function getLangsByClient() {
      $clientString = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
      // odstraníme mezery KHTML, webkit
      $clientString = str_replace(" ", "", $clientString);
      // rozdělit na jazyky
      $clientLangs = Explode(",", $clientString);
      $langs = array();
      $match = array();
      foreach ($clientLangs as $lang) {
         preg_match('/([a-z]{2,3})/', $lang, $match);
         if (in_array($match[1], self::getAppLangs())){
               return $match[1];
         }
//         $langs[] = preg_replace('/^!([a-z]{2,3})(.*)$/', 'd', $lang);
      }
      return false;
   }



   /**
    * Metoda nastaví názvy jazyků jayzyky
    * //TODO dořešit přidávání více jazyků
    */
   private static function _setLangTranslations(){
      self::$localesNames = array("cs" => _('Česky'),
                                  "en" => _('Anglicky'),
                                  "de" => _('Německy'));
   }

   /**
    * Metoda nastaví locales na daný jazyk
    */
   private static function setLocalesEnv() {
      //	nastavení gettext a locales
      putenv("LANG=".self::getLocale(self::getLang()));
      setlocale(LC_ALL, self::getLocale(self::getLang()));
      bindtextdomain(self::GETTEXT_DEFAULT_DOMAIN, self::GETTEXT_DEFAULT_LOCALES_DIR);
      textdomain(self::GETTEXT_DEFAULT_DOMAIN);
   }

   /**
    * Metoda vrací zvolené locales pro zadaný jazyk
    * @param string -- jazyk (cs, en, de, ...)
    */
   private static function getLocale($lang = null) {
      if($lang == null){
         return self::$locales[self::$selectLang];
      } else {
         if(key_exists($lang, self::$locales)){
            return self::$locales[$lang];
         } else {
            reset(self::$locales);
            return current(self::$locales);
         }
      }
   }

   /**
    * Metoda vrací výchozí jazyk aplikace (první, uvedený v configu)
    * @return string -- výchozí jazyk (cs, en, ..)
    */
   public static function getDefaultLang(){
      return self::$defaultLang;
   }

   /**
    * Metoda rozparsuje hodnoty jazyku uvedených v configu
    */
   private static function parseLangs() {
      $langs = AppCore::sysConfig()->getOptionValue("langs");
      if ($langs != null){
         self::$appLangs = explode(self::LANG_SEPARATOR, $langs);
         self::$defaultLang = self::$appLangs[0];
      }
   }

   /**
    * Metoda vrací pole jazyků aplikace
    * @return array -- pole jazyků aplikace
    */
   public static function getAppLangs() {
      return self::$appLangs;
   }

   /**
    * Metoda vrací true pokud jazyk existuje //TODO private funkce
    *
    * @param string -- název jazyku (cs, en, ...)
    * @return boolean -- true pro existenci jazyku
    */
   public static function langExist($lang) {
      if(in_array($lang, self::$appLangs)){
         return true;
      }
      return false;
   }

   /**
    * Metoda nastaví vybraný jazyk
    * @param string -- název jazyku (cs, en, de, ...)
    */
   public static function setLang($lang) {
      if(self::langExist($lang)){
         self::$selectLang = $lang;
      } else {
         self::$selectLang = self::$defaultLang;
      }
      //self::setLocalesEnv(); // kvůli změně
   }

   /**
    * Metoda vrací vybraný jazyk aplikace
    * @return string -- vybraný jazyk aplikace
    */
   public static function getLang() {
      return self::$selectLang;
   }

   /**
    * Metoda přidá textovou doménu pro překlad
    *
    * @param string -- název modulu pro kterou se má překlad přidat (Option)
    */
//   public static function bindTextDomain($moduleName = null) {
//      if($moduleName == null AND AppCore::getSelectedModule() != null){
//         bindtextdomain(AppCore::getSelectedModule()->getName(), '.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR
//            . DIRECTORY_SEPARATOR . AppCore::getSelectedModule()->getName() . DIRECTORY_SEPARATOR. self::LOCALES_DIR);
//      } else if($moduleName != null){
//         bindtextdomain($moduleName, '.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR
//            . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR. self::LOCALES_DIR);
//      }
//   }

   /**
    * Metoda inicializuje doplňkové funkce pro práci s překlady
    */
//   public static function initTranslationsFunctions() {
      /**
       * Funkce pro lokalizaci textů v modulech, pokud modul neexistuje je použit
       * překlad pro engine
       * 
       * @param string $message -- zpráva pro překlad
       * @return striing -- přeložený text
       */
//      function _m($message) {
//         if(AppCore::getSelectedModule() != null){
//            return dgettext(AppCore::getSelectedModule()->getName(), $message);
//         }
//         return _($message);
//      }
//   }

   /**
    * Konstruktor vytvoří objekt pro přístup k locales
    * @param string $moduleDomain
    */
   public function  __construct($moduleDomain) {
      $this->moduleDomain = $moduleDomain;
      $this->bindTextDomain();
   }

   public function _m($message) {
      return dgettext($this->moduleDomain, $message);
   }

   private function bindTextDomain() {
      bindtextdomain($this->moduleDomain, '.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR
         . DIRECTORY_SEPARATOR . $this->moduleDomain . DIRECTORY_SEPARATOR. self::LOCALES_DIR);
   }
}
?>