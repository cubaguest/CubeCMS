<?php
/**
 * Třída pro práci s moduly v kategorii
 *
 * @version 0.3.9
 * @author Jakub Matas <jakubmatas@gmail.com>
 * @package module class
 * //TODO
 */
class Module {
	/**
	 * Odělovač parametrů v pramaterech modulu
	 * @var string
	 */
	const MODULE_PARAMS_SEPARATOR = ';';
	
	/**
	 * název modulu
	 * @var string
	 */
	private $moduleName;

	/**
	 * id modulu (item prvku) v db
	 * @var integer
	 */
	private $id;

	/**
	 * id modulu v db
	 * @var integer
	 */
	private $idModule;

	/**
	 * popis modulu (label)
	 * @var string
	 */
	private $moduleLabel;

	/**
	 * alt modulu (alt)
	 * @var string
	 */
	private $moduleAlt;

	/**
	 * pole s parametry modulu
	 * ["název parametru"] => "hodnota"
	 * @var array
	 */
	private $params = array();

	/**
	 * Pole databázových tabulek modulu
	 * @var array
	 */
	private $dbTables = array();

	/**
	 * Proměná s datovým adresářem modulu
	 * @var string
	 */
	private $dataDir = null;
	
	/**
	 * Proměná obsahuje počet záznamů na stránce pro scrollery
	 * @var integer
	 */
	private $recordsOnPage = 0;
	
	/**
	 * Konstruktor třídy pro práci s modulem
	 *
	 * @param integer -- id modulu
	 * @param string -- nazev modulu
	 * @param string -- popis (label) modulu
	 * @param string -- popisný alt modulu
	 * @param array -- pole názvů db tabulek modulu
	 * @param string -- parametry modulu
	 */
	function __construct(stdClass $moduleObject, $dbTables){
		$this->setId($moduleObject->id_item);
		$this->setIdModule($moduleObject->id_module);
		$this->setName($moduleObject->name);
		$this->setDbTables($dbTables);
		$this->setDataDir($moduleObject->datadir);
		$this->setParams($moduleObject->params);
		$this->setLabel($moduleObject->label);
		$this->setAlt($moduleObject->alt);
		$this->setRecordsOnPage($moduleObject->scroll);

		//objekt pro práci s adresáři modulu
//		$this->moduleDir = new ModuleDirs($this->getName());
	}

	/**
	 * Metoda vrací objekt s adresáři modulu
	 * @return ModuleDirs -- objek s adresáři modulu
	 */
	public function getDir() {
		return new ModuleDirs($this->getName(), $this->dataDir);
	}

	/**
	 * Funkce nastavi id modulu (item)
	 *
	 * @param integer -- id modulu (item)
	 */
	function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Funkce vraci id modulu (item)
	 *
	 * @return integer -- id modulu (item)
	 */
	function getId()
	{
		return $this->id;
	}

	/**
	 * Funkce nastavi id modulu
	 *
	 * @param integer -- id modulu
	 */
	function setIdModule($idModule)
	{
		$this->idModule = $idModule;
	}

	/**
	 * Funkce vraci id modulu
	 *
	 * @return integer -- id modulu
	 */
	function getIdModule()
	{
		return $this->idModule;
	}

	/**
	 * Funkce nastavi jmeno module
	 *
	 * @param String -- jmeno modulu
	 */
	function setName($moduleName)
	{
		$this->moduleName = $moduleName;
	}

	/**
	 * Funkce vraci jmeno modulu
	 *
	 * @return String -- jmeno modulu
	 */
	function getName()
	{
		return $this->moduleName;
	}

	/**
	 * Funkce nastavi nazev modulu
	 *
	 * @param String -- nazev modulu
	 */
	function setLabel($moduleLable)
	{
		$this->label = $moduleLable;
	}

	/**
	 * Funkce vraci nazev modulu
	 *
	 * @return String -- nazev modulu
	 */
	function getLabel()
	{
		return $this->label;
	}

	/**
	 * Funkce nastavi popis (alt) modulu
	 *
	 * @param String -- popis modulu
	 */
	function setAlt($moduleAlt)
	{
		$this->alt = $moduleAlt;
	}

	/**
	 * Funkce vraci popis (alt) modulu
	 *
	 * @return String -- popis modulu
	 */
	function getAlt()
	{
		return $this->alt;
	}

	/**
	 * Funkce nastavi počet záznamů na stránce
	 *
	 * @param integer -- počet záznamů
	 */
	function setRecordsOnPage($countRecords)
	{
		$this->recordsOnPage = $countRecords;
	}

	/**
	 * Funkce vraci počet záznamů na stránce
	 *
	 * @return integer -- počet záznamů
	 */
	function getRecordsOnPage()
	{
		return $this->recordsOnPage;
	}

	/**
	 * Funkce nastavi cestu k adresari s daty modulu
	 *
	 * @param String -- jmeno adresare
	 */
	function setDataDir($dir)
	{
		$this->dataDir = $dir;
	}

	/**
	 * Funkce vraci jmeno adresare s daty modulu
	 *
	 * @return String -- jmeno adresare
	 * @deprecated -- přístup k adrsářům má být přes objekt ModuleDirs
	 */
//	function getDataDir()
//	{
//		return $this->dataDir;
//	}

	/**
	 * Funkce vraci jmeno databázové tabulky s daty modulu
	 *
	 * @param integer -- cislo tabulky
	 * @return String -- jmeno adresare
	 */
	function getDbTable($tableNum = 1)
	{
		if(isset($this->dbTables[$tableNum])){
			return $this->dbTables[$tableNum];
		} else {
			return false;
		}
	}

	/**
	 * Funkce nastavi jména databázových tabulek s daty modulu
	 *
	 * @param String -- jmeno tabulky1
	 * @param String -- jmeno tabulky2 (option)
	 * @param String -- jmeno tabulky3 (option)
	 */
//	function setDbTable($table1, $table2 = null, $table3 = null)
//	{		
//		$this->dbTable[1] = $table1;
//		if($table2){
//			$this->dbTable[2] = $table2;
//		}
//		if($table3){
//			$this->dbTable[3] = $table3;
//		}
//	}

	/**
	 * Funkce nastavi parametry modulu
	 *
	 * @param String -- parametry
	 */
	function setParams($catParams)
	{
		if ($catParams != null){
			$arrayValues = array();
			$arrayValues = explode(self::MODULE_PARAMS_SEPARATOR, $catParams);
//			print_r($arrayValues);

			foreach ($arrayValues as $value) {
				$tmpArrayValue = explode("=", $value);
				$this->params[$tmpArrayValue[0]]=$tmpArrayValue[1];
			}
		}
//		print_r($this->categoryParams);
	}

	/**
	 * Metoda vraci parametry modulu
	 *
	 * @return Array -- parametry
	 */
	function getParams()
	{
		return $this->params;
	}

	/**
	 * Metoda vraci hodnotu zvoleneho parametru modulu
	 *
	 * @return string -- parametr
	 */
	function getSelectParam($param)
	{
		return $this->params[$param];
	}


	/**
	 * Metoda nastaví tabulky modulu
	 * @param array -- pole s tabulkama
	 */
	public function setDbTables($dbTables) {
		//TODO není iplementována optimálně
		$this->dbTables = $dbTables;
	}

}

?>