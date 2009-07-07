<?php
/**
 * Třída pro práci s moduly v kategorii.
 * Třída poskytuj ezákladní přístup k parametrům modulu. Pomocí ní lze zjišťovat 
 * např. použité databázové tabulky modulu, adresáře, a některé ostatní parametry, 
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu modulů v kategorii
 */

class Module {
	/**
	 * Odělovač parametrů v pramaterech modulu
	 * @var string
	 */
	const MODULE_PARAMS_SEPARATOR = ';';

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
	 * Objekt s adresáři modulu
	 * @var Module_Dirs
	 */
	private $moduleDirs = null;

   /**
    * Objekt s názvy modulu
    * @var Module_Labels 
    */
   private $moduleLabels = null;

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
		$this->setId($moduleObject->{Model_Module::COLUMN_ITEM_ID});
		$this->setIdModule($moduleObject->{Model_Module::COLUMN_ID_MODULE});
		$this->setDbTables($dbTables);
      $this->setParams($moduleObject->{Model_Module::COLUMN_ITEM_PARAMS});
      $this->moduleLabels = new Module_Labels($moduleObject);
      $this->moduleDirs = new Module_Dirs($this->getLabel()->name(),
         $moduleObject->{Model_Module::COLUMN_DATADIR});
	}

   /**
    * Destruktor odstraní právě prováděný modul
    */
   public function  __destruct() {
   }

   /**
    * Metoda vrací objekt s názvy modulu (alias pro label())
    * @return Module_Labels
    * @deprecated -- lepší použít label(pro přístup k objektu s názvy)
    */
   public function getLabel() {
      return $this->label();
   }

   /**
    * Metoda vrací objekt s názvy modulu
    * @return Module_Labels
    */
   public function label() {
      return $this->moduleLabels;
   }

	/**
	 * Metoda vrací objekt s adresáři modulu
	 * @return Module_Dirs -- objek s adresáři modulu
	 */
	public function getDir() {
		return $this->moduleDirs;
	}

	/**
	 * Funkce nastavi id modulu (item)
	 *
	 * @param integer -- id modulu (item)
	 */
	private function setId($id){
		$this->id = $id;
	}

	/**
	 * Funkce vraci id modulu (item)
	 *
	 * @return integer -- id modulu (item)
	 */
	public function getId(){
		return $this->id;
	}

	/**
	 * Funkce nastavi id modulu
	 *
	 * @param integer -- id modulu
	 */
	private function setIdModule($idModule){
		$this->idModule = $idModule;
	}

	/**
	 * Funkce vraci id modulu
	 *
	 * @return integer -- id modulu
	 */
	public function getIdModule(){
		return $this->idModule;
	}

	/**
	 * Funkce vraci jmeno modulu
	 *
	 * @return String -- jmeno modulu
	 */
	public function getName(){
		return $this->getLabel()->name();
	}

	/**
	 * Funkce vraci popis (alt) modulu
	 *
	 * @return String -- popis modulu
	 */
	public function getAlt(){
		return $this->getLabel()->alt();
	}

	/**
	 * Funkce vraci jmeno databázové tabulky s daty modulu
	 *
	 * @param integer -- cislo tabulky
	 * @return String -- jmeno adresare
	 */
	public function getDbTable($tableNum = 1){
		if(isset($this->dbTables[$tableNum])){
			return $this->dbTables[$tableNum];
		} else {
			return false;
		}
	}

	/**
	 * Funkce nastavi parametry modulu
	 *
	 * @param String -- parametry
	 */
   private function setParams($catParams){
      if ($catParams != null){
         $arrayValues = array();
         $arrayValues = explode(self::MODULE_PARAMS_SEPARATOR, $catParams);

         foreach ($arrayValues as $value) {
            $tmpArrayValue = explode("=", $value);
            if(isset($tmpArrayValue[1]) AND $tmpArrayValue[1] != null){
               if($tmpArrayValue[1] == 'true'){
                  $this->params[$tmpArrayValue[0]]=true;
               } else if($tmpArrayValue[1] == 'false'){
                  $this->params[$tmpArrayValue[0]]=false;
               } else if(ereg('^[0-9]*$', $tmpArrayValue[1])){
                  $this->params[$tmpArrayValue[0]]=(int)$tmpArrayValue[1];
               } else {
                  $this->params[$tmpArrayValue[0]]=$tmpArrayValue[1];
               }
            } else {
               $this->params[$tmpArrayValue[0]]=null;
            }
         }
      }
   }

	/**
	 * Metoda vraci parametry modulu
	 *
	 * @return Array -- parametry
	 */
	public function getParams(){
		return $this->params;
	}

	/**
	 * Metoda vraci hodnotu zvoleneho parametru modulu
	 * @param string $param -- název parametru
    * @param mixed $defaultValue -- výchozí parametr
	 * @return string -- hodnota parametr
	 */
	public function getParam($param, $defaultValue = null){
		if(isset($this->params[$param])){
			return $this->params[$param];
		} else {
			return $defaultValue;
		}
	}

	/**
	 * Metoda nastaví tabulky modulu
	 * @param array -- pole s tabulkama
    * @todo není iplementována optimálně
	 */
	private function setDbTables($dbTables) {
		$this->dbTables = $dbTables;
	}
}

?>