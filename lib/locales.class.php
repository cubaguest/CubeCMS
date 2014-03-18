<?php
/**
 * Třída pro práci s locale (místním nastavením).
 * Třída slouží pro práci s jazykovým nastavením aplikace. Je určena k volbě
 * výchozího a zvoleného jazyka aplikace. Lze s ní ískat i kompletní výpis všech
 * jazyků, a všech použiých jazyků v aplikaci.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro obsluhu jazykového nastavení
 * @internal      Last ErrorCode 2
 */

class Locales extends TrObject {
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
   const GETTEXT_DEFAULT_LOCALES_DIR = 'locale';

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
                                   "en" => "en_US.UTF-8",
                                   "de" => "de_DE.UTF-8",
                                   "ru" => "ru_RU.UTF-8",
                                   "sk" => "sk_SK.UTF-8",
                                   "au" => "en_AU.UTF-8",
                                   "us" => "en_US.UTF-8",
                                   "da" => "da_DK.UTF-8",
                                   "es" => "es_ES.UTF-8",
                                   "pl" => "pl_PL.UTF-8",
                                   "lv" => "lat.UTF-8",
                                   "is" => "is_IS.UTF-8",
                                   "sl" => "sl_SL.UTF-8",
                                   "et" => "et_EE.UTF-8",
                                   "lt" => "lt_LT.UTF-8",
                                   "hu" => "hu.UTF-8",
                                   "sv" => "sv_SE.UTF-8",
       );

   /**
    * Pole s podobnými jazyky (je použito při výchozím nasatvení jazyku)
    * @var <type>
    */
   private static $similaryLangs = array('cs' => 'sk', 'sk' => 'cs');

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
    * Jestli je aplikace vícejazyčná
    * @var boolean
    */
   private static $isMultilang = false;

   /**
    * Doména modulu
    * @var string
    * @deprecated
    */
   private $moduleDomain = null;

   /**
    * Pole s importovanými doménami překladu
    * @var array
    * @deprecated
    */
   private static $bindedDomains = array();

   /**
    * Metoda pro vytvoření prostředí třídy locales
    */
   public static function factory() {
      // vybere jazyky aplikace
      self::parseLangs();
      if(count(self::$appLangs) > 1){
         self::$isMultilang = true;
      }
      // nastaví výchozí jazyk
      self::$defaultLang = VVE_DEFAULT_APP_LANG;
   }

   /**
    * Metoda nastaví aplikaci pro zvolený jazyk
    */
   public static function selectLang() {
      if(self::$selectLang == null){
         // pokud nebyl jazyk nastaven při prohlížení
         if(!isset ($_SESSION[self::SESSION_LANG])){
            // načteme jazyk klienta a zjistíme, jestli existuje mutace aplikace
            self::$selectLang = self::getLangsByClient();
            $_SESSION[self::SESSION_LANG] = self::$selectLang;
            if(self::$selectLang != self::$defaultLang){
               $link = new Url_Link();
               $link->lang(self::$selectLang)->reload();
            }
         }
         // jazyk klienta byl zjištěn a nastaven
         else {
            self::$selectLang = self::$defaultLang;
            $_SESSION[self::SESSION_LANG] = self::$selectLang;
         }
      } else {
         if(!self::isAppLang(self::$selectLang)){
            self::$selectLang = self::$defaultLang;
            $tr = new Translator();
            new CoreErrors(new UnexpectedValueException( $tr->tr('Zvolený jazyk není v aplikaci implementován'),1));
         }

         if(!isset ($_SESSION[self::SESSION_LANG]) OR self::$selectLang != $_SESSION[self::SESSION_LANG]){
            $_SESSION[self::SESSION_LANG]= self::$selectLang;
         } else {
            self::$selectLang = $_SESSION[self::SESSION_LANG];
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
      $retLang = self::getDefaultLang();
      if(VVE_ENABLE_LANG_AUTODETECTION && isset ($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
         $clientString = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
         // odstraníme mezery KHTML, webkit
         $clientString = str_replace(" ", "", $clientString);
         // rozdělit na jazyky
         $clientLangs = Explode(",", $clientString);
         // zkrácení jazyků
         function langs_strings_repair(&$lang, $key){
            $match = array();
            preg_match('/([a-z]{2,3})/', $lang, $match);
            $lang = $match[1];
         }
         array_walk($clientLangs, 'langs_strings_repair');
         // test existence primárního jazyka
         if($clientLangs[0] == self::getDefaultLang()) return self::getDefaultLang();
         // test podobnoti
         if(isset (self::$similaryLangs[$clientLangs[0]])
                 AND in_array(self::$similaryLangs[$clientLangs[0]], self::getAppLangs())){
            return self::$similaryLangs[$clientLangs[0]];
         }
         // volba podle klienta
         $match = array();
         foreach ($clientLangs as $lang) {
            if (in_array($lang, self::getAppLangs())) return $lang;
         }
      }
      return $retLang;
   }

   /**
    * Metoda nastaví názvy jazyků jayzyky
    * //TODO dořešit přidávání více jazyků
    */
   private static function _setLangTranslations(){
      $tr = new Translator();
      self::$localesNames = array("cs" => $tr->tr('Česky'),
                                  "en" => $tr->tr('English'),
                                  "au" => $tr->tr('English (AUS)'),
                                  "us" => $tr->tr('English (USA)'),
                                  "de" => $tr->tr('Deutsch'),
                                  "ru" => $tr->tr('Pусский'),
                                  "sk" => $tr->tr('Slovensky'),
                                  "da" => $tr->tr('Danish'),
                                  "es" => $tr->tr('Spanish'),
                                  "pl" => $tr->tr('Polski'),
                                  "lv" => $tr->tr('Latvian'),
                                  "is" => $tr->tr('Icelandic'),
                                  "sl" => $tr->tr('Slovenian'),
                                  "et" => $tr->tr('Estonian'),
                                  "lt" => $tr->tr('Lithuanian'),
                                  "hu" => $tr->tr('Hungarian'),
                                  "sv" => $tr->tr('Swedish'),
          );
   }

   /**
    * Metoda nastaví locales na daný jazyk
    */
   private static function setLocalesEnv() {
      //	nastavení gettext a locales
      $locale = self::getLocale(self::getLang());
      if(SERVER_PLATFORM == 'WIN'){
         $locale = 'czech'; // Windows potřebují jiný druh
      }
      if(setlocale(LC_ALL, $locale) == false){
         $tr = new Translator();
//         throw new DomainException(sprintf($tr->tr('Nepodporované Locales %s.'), self::getLocale(self::getLang())));
         trigger_error(sprintf($tr->tr('Nepodporované Locales %s.'), self::getLocale(self::getLang())));
      }
      /* DEPRECATED */
      bindtextdomain(self::GETTEXT_DEFAULT_DOMAIN, AppCore::getAppLibDir().self::GETTEXT_DEFAULT_LOCALES_DIR);
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
    * Metoda vrací zvolené locales pro zadaný jazyk
    * @param string -- jazyk (cs, en, de, ...)
    * @todo doladit aby tam nebylo UTF-8
    */
   public static function getLangLocale($lang = null) {
      $l = self::getDefaultLang();
      if($lang != null){
         $l = $lang;
      } else if(self::$selectLang != null){
         $l = self::$selectLang;
      }
      if(isset(self::$locales[$l.'.UTF-8'])){
         $locale = self::$locales[$l.'.UTF-8'];
      } else {
         $locale = self::$locales[$l];
      }
      // odstranění za tečkou
      $locale = preg_replace("/\.[\w-]+/i", '', $locale);
      return $locale;
   }

   /**
    * Metoda vrací výchozí jazyk aplikace (první, uvedený v configu)
    * @return string/array -- výchozí jazyk (cs, en, ..)
    */
   public static function getDefaultLang($returnArray = false){
      if($returnArray){
         return self::getLangLabel(self::$defaultLang);
      } else {
         return self::$defaultLang;
      }
   }

   /**
    * Metoda rozparsuje hodnoty jazyku uvedených v configu
    */
   private static function parseLangs() {
      $langs = VVE_APP_LANGS;
      if ($langs != null){
         self::$appLangs = explode(self::LANG_SEPARATOR, $langs);
         self::$defaultLang = self::$appLangs[0];
      }
   }

   /**
    * Metoda vrací pole jazyků aplikace
    * @return array -- pole jazyků aplikace např. array(0 => 'cs', 1 => 'en', ...)
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
         self::selectLang();
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
    * Metoda vrací pole s vypraným jazykem
    * @param string $langShor -- zkratka jazyka
    */
   public static function getLangLabel($langShor) {
      if(in_array($langShor, self::$appLangs)){
         $lang = Locales::getAppLangsNames();
         return array($langShor => $lang[$langShor]);
      } else {
         return false;
      }
   }

   /**
    * Metoda vrací jazyk uživatele
    * @return string (cs, en, de, ...)
    */
   public static function getUserLang()
   {
      return Model_UsersSettings::getSettings('userlang', Locales::getLang());
   }
   
   /**
    * Konstruktor vytvoří objekt pro přístup k locales
    * @param string $moduleDomain
    */
   public function  __construct($moduleDomain) {
      $this->moduleDomain = $moduleDomain;
      $this->bindTextDomain($moduleDomain);
   }

   /**
    * 
    * @param unknown_type $domain
    * @deprecated - používat Translator
    */
   public function setDomain($domain){
      $this->bindTextDomain($domain);
      $this->moduleDomain = $domain;
   }

   /**
    *
    * @param unknown_type $domain
    * @deprecated - používat Translator
    */
   public function _($message, $domain = null) {
      return $this->gettext($message, $domain);
   }

   /**
    *
    * @param unknown_type $domain
    * @deprecated - používat Translator
    */
   public function gettext($message, $domain = null) {
      if($domain === null){
         return dgettext($this->moduleDomain, $message);
      } else {
         return dgettext($domain, $message);
      }
   }

   /**
    *
    * @param unknown_type $domain
    * @deprecated - používat Translator
    */
   public function ngettext($message1, $message2, $int, $domain = null) {
      if($domain === null){
         return dngettext($this->moduleDomain, $message1, $message2, $int);
      } else {
         return dngettext($domain, $message1, $message2, $int);
      }
   }

   /**
    *
    * @param unknown_type $domain
    * @deprecated - používat Translator
    */
   private function bindTextDomain($moduleDomain) {
      if($moduleDomain != null && !in_array($moduleDomain, self::$bindedDomains)){
         bindtextdomain($moduleDomain, AppCore::getAppLibDir() . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR
         . DIRECTORY_SEPARATOR . $moduleDomain . DIRECTORY_SEPARATOR. self::LOCALES_DIR);
         array_push(self::$bindedDomains, $moduleDomain);
      }
   }

   /**
    * Metoda vrací true pokud se jedná o vícejazyčnou aplikaci
    * @return boolean
    */
   public static function isMultilang() {
      return self::$isMultilang;
   }

   /**
    * Metoda zkišťuje jestli je zadaný jazyk jazykem aplikace
    * @param string $lang -- jazyková zkratka (cs, en, ...)
    * @return bool
    */
   public static function isLang($lang) {
      return in_array($lang, self::$appLangs);
   }
}
?>