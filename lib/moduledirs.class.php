<?php
/**
 * Třída pro obsluhu adresářů modulu.
 * Třída slouží pro přístup k jednotlivým adresářům modulu. Pracuje jak s datovým, 
 * tak s hlavním adresářem modulu, ale i s adresáři stylesheetu a šablon modulu.
 * 
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE3.9.4 $Revision: $
 * @author        $Author: $ $Date:$
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu adresářů modulu
 */

class ModuleDirs {
	/**
	 * Adresář se složkami modulu
	 * @var string
	 */
	const MODULES_MAIN_DIR = 'modules';

	/**
	 * Adresář s modely
	 * @var string
	 */
	const MODELS_DIR = 'models';

	/**
	 * Adresář s šablonami
	 * @var string
	 */
	const TEMPLATES_DIR = 'templates';

	/**
	 * Zařazení z pohledu index.tpl
	 * @var string
	 */
	const TEMPLATES_INDEX_DIR = '.';
	
	/**
	 * Adresář se styly
	 * @var string
	 */
	const STYLESHEETS_DIR = 'stylesheets';

	/**
	 * Adresář s javascripty
	 * @var string
	 */
	const JAVASCRIPTS_DIR = 'javascripts';
	
	/**
	 * oddělovač adresářů
	 * @var string
	 */
	const DIR_SEPARATOR = '/';
	
	/**
	 * Adresář webu (static)
	 * @var string
	 */
	private static $mainWebDir;

	/**
	 * Adresář se všemi daty
	 */
	private static $mainDataDir;
	
	/**
	 * Adresář modulu ve filesystému (jeho název)
	 * @var string
	 */
	private $moduleDir = null;
	
	/**
	 * Proměná s datovým adresářem modulu
	 * @var string
	 */
	private $moduleDataDir = null;

	/*
	 * ============ METODY
	 */
	
	
	/**
	 * Konstruktor vytvoří základní adresářovou strukturu
	 *
	 * @param string -- adresář modulu (název)
	 * @param string -- datový adresář modulu
	 */
	function __construct($moduleDir, $moduleDataDir) {
		$this->moduleDir = $moduleDir;
		$this->moduleDataDir = $moduleDataDir;
	}

	/**
	 * Statická metoda nastaví hlavní adresář webu
	 *
	 * @param string -- adresář webu
	 */
	public static function setWebDir($webDir) {
		ModuleDirs::$mainWebDir = $webDir;
	}

	/**
	 * Statická metoda vrací hlavní adresář webu
	 *
	 * @return string -- adresář webu
	 */
	private static function getWebDir() {
		return ModuleDirs::$mainWebDir;
	}

	/**
	 * Statická metoda nastaví hlavní datový adresář webu
	 *
	 * @param string -- datový adresář webu
	 */
	public static function setWebDataDir($dataDir) {
		ModuleDirs::$mainDataDir = $dataDir;
	}
	
	/**
	 * Statická metoda vrací hlavní datový adresář webu
	 *
	 * @return string -- datový adresář webu
	 */
	private static function getWebDataDir() {
		return ModuleDirs::$mainDataDir;
	}

	/**
	 * Metoda vrací adresář k modulu
	 * @param boolean -- jestli se má vráti i cesta s prefixe (nutné pro relativní přístup)
	 * @return string -- cesta modulu
	 */
	public function getMainDir($withRelativePrefix = true) {
		if($withRelativePrefix){
			return ModuleDirs::getWebDir().self::MODULES_MAIN_DIR.self::DIR_SEPARATOR.$this->moduleDir.self::DIR_SEPARATOR;
		} else {
			return self::MODULES_MAIN_DIR.self::DIR_SEPARATOR.$this->moduleDir.self::DIR_SEPARATOR;
		}
	}

	/**
	 * Metoda vrací adresář s modely
	 */
	public function getModelsDir() {
		return $this->getMainDir().self::MODELS_DIR.self::DIR_SEPARATOR;
	}

	/**
	 * Metoda vrací cestu k datovému adresáři modulu
     * @param boolean[option] -- jestli má být vrácen s označením relativní cesty
	 * @return string -- cesta k adresáři 
	 */
	public function getDataDir($withRelPrefix = true) {
		if($this->moduleDataDir == null){
         throw new ModuleException(sprintf(_('Modul "%s" nemá definovaný datový adresář'),
               AppCore::getSelectedModule()->getName()));
		} else {
            if($withRelPrefix){
                return self::getWebDir().self::getWebDataDir().self::DIR_SEPARATOR.$this->moduleDataDir.self::DIR_SEPARATOR;
            } else {
                return self::getWebDataDir().self::DIR_SEPARATOR.$this->moduleDataDir.self::DIR_SEPARATOR;
            }
		}
	}
	
	/**
	 * Metody vrací cestu k adresáři css souborů modulu
	 * @return string -- cesta k css souborům
	 */
	public function getStylesheetsDir($withPrefix = false) {
		if($withPrefix){
			return $this->getMainDir().self::STYLESHEETS_DIR.self::DIR_SEPARATOR;
		} else {
			return $this->getMainDir(false).self::STYLESHEETS_DIR.self::DIR_SEPARATOR;
		}
		
	}
	
	/**
	 * Metoda vrací adresář k javascript souborům modulu
	 * @return string -- cesta k adresáři s javascripty
	 */
	public function getJavaScriptsDir($withPrefix = true) {
		if($withPrefix){
            return $this->getMainDir().self::JAVASCRIPTS_DIR.self::DIR_SEPARATOR;
        } else {
            return $this->getMainDir(false).self::JAVASCRIPTS_DIR.self::DIR_SEPARATOR;
        }
	}
	
	/**
	 * Metoda vrací adresář k šablonám modulu
	 * 
	 * @param boolean -- jestli se má vráti i cesta s prefixe (nutné pro relativní přístup)
	 * @return string -- adresář k šablonám modulu
	 */
	public function getTemplatesDir($withPrefix = true) {
		if($withPrefix){
			return self::TEMPLATES_INDEX_DIR.$this->getMainDir().self::TEMPLATES_DIR.self::DIR_SEPARATOR;
		} else {
			return $this->getMainDir(false).self::TEMPLATES_DIR.self::DIR_SEPARATOR;
		}
	}
	
}

?>
