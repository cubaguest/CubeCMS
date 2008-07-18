<?php
/**
 * Třída obsluhuje proměnou action, použitou při předávání parametrů akcí u modulů
 *
 * @author Jakub Matas <jakubmatas@gmail.com>
 *
 * @version 0.0.1
 * @package vypeckyengine action.class
 */
abstract class Action {
	/**
	 * Název $_GET s akcí
	 * @var string
	 */
	const GET_ACTION = Links::GET_ACTION;
	
	/**
	 * Oddělovač mezi akcí a id modulu
	 * @var string
	 */
	const ACTION_SEPARATOR = '-';
	
	/**
	 * Maximální počet znaků v akci
	 * @var integer
	 */
	const ACTION_MAX_CHARS = 3;
	
	/**
	 * Prefix uživatelských metod pro obsluhu akcí
	 * @var string
	 */
	const ACTION_USERS_METHODS_PREFIX = 'action';
	
	/**
	 * Vybraná akce v url
	 * @var string
	 */
	private $selectedAction = null;
	
	/**
	 * Vybrané id v url
	 * @var integer
	 */
	private $selectedId = null;

	/**
	 * Je-li akce nastavena
	 * @var boolean
	 */
	private $isSet = false;

	/**
	 * Objekt s parametry modulu
	 * @var Module
	 */
	private $module = null;
	
	/**
	 * Pole s definovanými výchozími akcemi slouží pro překlad do akce v url
	 * je nutné zachovat znění pole
	 * @var array
	 */
	private $actionsTranslateArray = array("edit"	=> 	"e",
										   "add"	=>	"a",
										   "show"	=>	"s");
	
	/**
	 * výchozí akce
	 * @var string
	 */
	private $defaultAction = "show";
	
	/**
	 * Konstruktor
	 *
	 * @param Module -- objekt modulu (nutný pro zjištění id modulu)
	 */
	public final function __construct(Module $module){
//		Vytvoření uživatelských akcí
		$this->createModuleActions();

//		Parsování zvolené akce
		if(isset($_GET[self::GET_ACTION]) AND $_GET[self::GET_ACTION] != ""){
			$this->isSet = $this->parseAction($_GET[self::GET_ACTION]);
		} else {
			$this->isSet = false;
		}
		$this->module = $module;
	}

	/**
	 * Metoda vrací objekt na modul
	 * @return Module -- objekt modulu
	 */
	private function getModule() {
		return $this->module;
	}
	
	/**
	 * Metoda spojí pole s výchozími a uživatelskými akcemi
	 */
//	private function splitActionsArray() {
//		;
//	}

	/**
	 * Metoda nastavuje akci
	 * //TODO možná nastavit jinak a zabránit použití ve funkcích
	 */
	public function setAction()
	{
		;
	}
	
	
	/**
	 * Metoda přidá požadovanou akci do seznamu akcí
	 * @param string -- název akce
	 * @param string -- zkratka akce (maximálně 3 znaky)
	 */
	final public function addAction($actionName, $actionAbbr = null){
		
		$actionAbbr == null ? $actionAbbr = substr($actionName, 0, self::ACTION_MAX_CHARS) : $actionAbbr = substr($actionAbbr, 0, self::ACTION_MAX_CHARS);
	
		$this->actionsTranslateArray[$actionName] = $actionAbbr;
		
	}
	
	/**
	 * Metoda s uživatelskými akcemi
	 * Je nutné ji definovat v souboru modulu
	 */
	abstract function actions();
	
	/**
	 * Metoda provede přiřazení a vytvoření všech uživatelských akcí všech
	 */
	final private function createModuleActions() {
//		Přiřazení uživatelských akcí
		$this->actions();
		
//		foreach ($this->actionsTranslateArray as $actions) {
//			
//		}
				
	}
	
	/**
	 * Magická metoda pro vytváření funkcí uživatelských akcí 
	 *
	 * @param string -- název nové metody
	 * @return unknown
	 */
	function __call($methodName, $params) {
		if (substr($methodName,0,strlen(self::ACTION_USERS_METHODS_PREFIX)) == self::ACTION_USERS_METHODS_PREFIX){
			
//			Zjištění názvu metody bez action
			$actionMetod = strtolower(substr($methodName, strlen(self::ACTION_USERS_METHODS_PREFIX), strlen($methodName)));
			if(!isset($this->actionsTranslateArray[$actionMetod])){
				new CoreException(_("Akce ").$actionMetod._(" není definována"), 20);
			} else {
				return $this->creatAction($this->actionsTranslateArray[$actionMetod]);
			}

		};
	}
	
	
	
	/**
	 * Funkce rozparsuke akci na části akce-id
	 *
	 * @param string -- řetězec přenášený akcí
	 * @return boolena -- vrací true pokud akce je zařazena v seznamu povolených akcí viz dokumentace
	 * //TODO špatné parsování
	 */
	private function parseAction($get_action)
	{
		echo "action ". $get_action;
		list($action, $id) = explode(self::ACTION_SEPARATOR, (string)$get_action);
		$return = false;
		
		foreach ($this->actionsTranslateArray as $key => $value) {
			if($value == $action){
				$this->selectedAction = $key;
				$this->selectedId = $id;
				$return = true;
			}
		}

		return $return;
	}

	/**
	 * Funkce vrazí která akce je definována viz dokumentace
	 *
	 * @return string -- název prováděné akce viz dokumentace
	 */
	public function getAction() {
		return $this->selectedAction;
	}

	/**
	 * Funkce vrazí která akce je definována viz dokumentace
	 *
	 * @return integer -- id modulu, který má provést akci
	 */
	public function getId() {
		return $this->selectedId;
	}

	/**
	 * Funkce zjišťuje, jesli byla akce nastavena
	 *
	 * @return boolean -- true pokud byla akce nastavena
	 */
	public function isAction()
	{
		return $this->isSet;
	}
	
	/*
	 * metody pro generování do url
	 */
	
		
	/**
	 * Funkce vytvoří řetězec s akcí
	 *
	 * @param string -- a jakou akci se jedná viz dokumentace
	 * @param integer -- id modulu v kategorii (iditem)
//	 */
	function creatAction($action)
	{
		$return = $action.self::ACTION_SEPARATOR.$this->getModule()->getId();
		return $return;
	}
		
	/**
	 * Zakladni metoda pro vygenerování akce edit
	 * @return string -- akce edit pro url 
	 */
	public function actionEdit() {
		return $this->creatAction($this->actionsTranslateArray["edit"]);
	}
	
	/**
	 * Zakladni metoda pro vygenerování akce add
	 * @return string -- akce edit pro url 
	 */
	public function actionAdd() {
		return $this->creatAction($this->actionsTranslateArray["add"]);
	}
	
	/**
	 * Zakladni metoda pro vygenerování akce show
	 * @return string -- akce edit pro url 
	 */
	public function actionShow() {
		return $this->creatAction($this->actionsTranslateArray["show"]);
	}
	
	/**
	 * Metoda vrací true pokud je u zvoleného modulu akce
	 * @return boolean -- true pokud modulu patří akce
	 */
	public function haveAction() {
		if($this->getId() == $this->getModule()->getId()){
			return true;
		} else {
			//TODO chtělo by upravit při zobrazeném článku
			return false;
//			if($this->)
		}
	}
	
	/**
	 * Metoda vrací výchozí akci pokud není definována
	 * @return string
	 * //TODO není korektní asi lepší použít setAction
	 */
	public function getDefaultAction()
	{
		return $this->defaultAction;
	}
	
	
}

?>