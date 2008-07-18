<?php
/**
 * Třída pro obsluhu konfiguračního souboru
 */
class Config {
	/**
	 * Název konfiguračního souboru
	 * @var string
	 */
	private $_configFile = null;

	/**
	 * Pole objektů s konfiguračními volbami
	 * @var ArrayObject
	 */
	private $_configArray = null;

	private $_coreErrors = null;

	/**
	 * Konstruktor třídy
	 *
	 * @param string -- soubor s konfigurací
	 */
	function __construct($configFile, &$coreErrors) {
		$this->_configFile = $configFile;
		$this->_coreErrors = new Errors();
		$this->_coreErrors = $coreErrors;
		$this->_configArray = array();
		$this->_initConfigFile();
	}

	/**
	 * Metoda načte konfigurační soubor do pole
	 */
	private function _initConfigFile() {
		if (file_exists($this->_configFile)) {
   			$xml = simplexml_load_file($this->_configFile);

   			$this->_configArray = $this->_objToArray($xml);

//			echo "<pre>";
//			print_r($this->_configArray);
//			echo "</pre>";

		} else {
			throw new CoreException(_('Nepodařilo se otevšít konfigurační soubor ') . $this->_configFile, 101);
		};
	}

	/**
	 * Metoda převede objekt SimpleXMLElement na pole
	 *
	 * @param SimpleXMLElement -- objekt elementů funkce simplexml_load_file
	 * @return array -- pole prvků
	 */
	private function _objToArray($xmlObject) {
		$return = null;

		if(is_array($xmlObject))
		{
			foreach($xmlObject as $key => $value){
				$return[$key] = $this->_objToArray($value);
			}
		}
		else
		{
			$var = get_object_vars($xmlObject);

			if($var)
			{
				foreach($var as $key => $value){
					$return[$key] = $this->_objToArray($value);
				}
			} else {
				return $xmlObject;
			}
		}

		return $return;
	}

	/**
	 * Vrátí požedovanou hodnotu z konfiguračního souboru
	 *
	 * @param string -- volba, která se má vrátit
	 * @param string -- skupina volby, která se má vrátit
	 * @todo dodělat vracení z větší hloubky stromu
	 */
	public function getOptionValue($option, $parentKey = null){
		$return = null;
		if($parentKey == null){
//			try {
				if(!isset($this->_configArray[$option])){
					$error = new CoreException(_("Volba ").$option._(" not defined"), 102);
//					$this->_coreErrors->addError($error->getException());
				} else {
					$return = $this->_configArray[$option];
				}
//				$return = $this->_configArray[$parentKey][$option];
//			} catch (CoreException $err){

//			}

//			return $this->_configArray[$option];
		} else {
//			try {
				if(!isset($this->_configArray[$parentKey][$option])){
					$error = new CoreException(_("Volba ").$option._(" v sekci ").$parentKey._("není definována"), 103);
//					$this->_coreErrors->addError($error->getException());
				} else {
					return $this->_configArray[$parentKey][$option];
				}

//			} catch (CoreException $err){
//				$this->_coreErrors->addError($err->getException());
//				echo "chyba " . $err;

//			}
//			return $this->_configArray[$parentKey][$option];
		}
		return $return;
	}

}



?>