<?php
/**
 * Třída pro práci s locale (místním nastavením)
 *
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	Locale class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: locale.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída pro obsluhu jazykového nastavení
 */
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
	 * Název adresáře s locales
	 * @var string
	 */
	const LOCALES_DIR = 'locale';
	
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
	public static function factory() {
		
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
		
		self::_setLangTranslations();
	}
	
	/**
	 * Metoda vrací pole s názvy jazyků
	 * 
	 * @return array -- pole s názvy jazyků
	 */
	public static function getAppLangsNames() {
		$returnArray = array();
		
//		echo "<pre>";
//		print_r(self::getAppLangs());
//		echo "</pre>";
		
		foreach (self::getAppLangs() as $langKey => $lang) {
//			array_push($returnArray, self::$localesNames[$lang]);
			$returnArray[$lang] = self::$localesNames[$lang];
		}
		
		return $returnArray;
	}
	
		/**
	 * Metoda nastaví názvy jazyků jayzyky
	 * 
	 * //TODO dořešit při více jazyků
	 */
	private static function _setLangTranslations(){
		self::$localesNames = array("cs" => _('Česky'),
									"en" => _('Anglicky'),
									"de" => _('Německy'));
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
	
	/**
	 * Metoda přenastaví lokalizační texty na engine
	 */
	public static function switchToEngineTexts() {
		textdomain(AppCore::GETTEXT_DEFAULT_DOMAIN);
	}

	/**
	 * Metoda přenastaví lokalizační texty na engine
	 * 
	 * @param string -- název modulu, na který se mají texty přnastavit (option)\ 
	 * 					 pokud je prázdná použije se zvolený modul enginu
	 */
	public static function switchToModuleTexts($moduleName = null){
		if($moduleName == null AND AppCore::getSelectedModule() != null){		
			textdomain(AppCore::getSelectedModule()->getName());
		} else if($moduleName != null){
			textdomain($moduleName);
		}
	}
	
	/**
	 * Metoda přidá textovou doménu pro překlad
	 * 
	 * @param string -- název modulu pro kterou se má překlad přidat (Option)
	 */
	public static function bindTextDomain($moduleName = null) {
		if($moduleName == null AND AppCore::getSelectedModule() != null){
			bindtextdomain(AppCore::getSelectedModule()->getName(), '.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR 
			. DIRECTORY_SEPARATOR . AppCore::getSelectedModule()->getName() . DIRECTORY_SEPARATOR. self::LOCALES_DIR);
		} else if($moduleName != null){
			bindtextdomain($moduleName, '.' . DIRECTORY_SEPARATOR . AppCore::MODULES_DIR 
			. DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR. self::LOCALES_DIR);
		}
	}
	
	
}

?>