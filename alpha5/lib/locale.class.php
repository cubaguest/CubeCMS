<?php
class Locale {
	/**
	 * Oddělovač jazyků v konfiguračním souboru
	 * @var string
	 */
	const LANG_SEPARATOR = ';';
	
	/**
	 * Název $_GET s jazykem
	 * @var string
	 */
	const URL_PARAM_WITH_LANG = 'lang';
	
	/**
	 * Objekt se systémovou konfigurací
	 * @var Config
	 */
	private static $config = null;
	
	/**
	 * Pole se všemi locales
	 * @var array
	 */
	private static $locales = array("cs" => "cs_CZ.UTF-8",
									"en" => "en_US",
									"de" => "de_DE");
	
	/**
	 * počet jazyků v aplikaci
	 * @var integer
	 */
	private static $countOfLangs = 1;
	
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
	 * Metoda pro vytvoření prostředí třídy locales
	 *
	 * @param Config -- objekt systémové konfigurace
	 */
	public static function factory(Config $config) {
		self::$config = $config;
		
		self::parseLangs();
		
		if (!isset($_GET[self::URL_PARAM_WITH_LANG])){
			self::$selectLang = self::$defaultLang;
		} else {
			
			if(self::langExist($_GET[self::URL_PARAM_WITH_LANG])){
				self::$selectLang = $_GET[self::URL_PARAM_WITH_LANG];
			} else {
				self::$selectLang = self::$defaultLang;
			}
		}
	}
	
	/**
	 * Metoda vrací zvolené locales pro zadaný jazyk
	 * @param string -- jazyk (cs, en, de, ...)
	 */
	public static function getLocale($lang = null) {
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
		$langs = self::$config->getOptionValue("langs");
		
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
		} else {
			return false;
		}
	}
	
	/**
	 * Metoda nastaví vybraný jazyk
	 * @param string -- název jazyku
	 */
	private static function setLang($lang) {
		if(self::langExist($lang)){
			self::$selectLang = $lang;
		} else {
			self::$selectLang = self::$defaultLang;
		}
	}
	
	/**
	 * Metoda vrací vybraný jazyk aplikace
	 * @return string -- vybraný jazyk aplikace
	 */
	public static function getLang() {
		return self::$selectLang;
	}
	
	
}

?>