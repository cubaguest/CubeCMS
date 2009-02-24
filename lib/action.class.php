<?php
/**
 * Třída pro obsluhu akcí.
 * Třída obsluhuje přenášené akce v URL. Slouží také pro generování vlastních akcí 
 * v modulu, jejich úpravu. Podle zvolené akce se volí také kontroler modulu.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.5.0 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu akcí
 */

class Action {
	/**
	 * Název prvku s parametrem v url
	 * @var string
	 */
	const ACTION_URL_PARAM = 'url';

	/**
	 * Název prvku s typem akce pro kontroler
	 * @var string
	 */
	const ACTION_TYPE_PARAM = 'action';

	/**
	 * Název prvku s názvem akce -> jazykový popis pro url
	 * @var string
	 */
	const ACTION_LABEL_PARAM = 'label';

	/**
	 * Vybraná akce v url
	 * @var string
	 */
	private static $currentAction = null;

	/**
	 * Id Itemu pro vybranou akci
	 * @var integer
	 */
	private static $currentActionIdItem = null;

	/**
	 * Pole s definovanými akcemi
	 * @var array
	 */
	private $actionsArray = array();

	/**
	 * Objekt s parametry modulu
	 * @var Module
	 */
	private $module = null;
	
	/**
	 * výchozí akce
	 * @var string
	 */
	private $defaultAction = null;
	
	/**
	 * Konstruktor
	 *
	 * @param Module -- objekt modulu (nutný pro zjištění id modulu)
	 */
	public final function __construct(){
		$this->module = AppCore::getSelectedModule();
//		Vytvoření uživatelských akcí
		$this->createDefaultActions();
        $this->init();
	}

    /**
     * Metoda pro inicializaci akcí
     */
    protected function init() {}

	/**
	 * Metoda vrací objekt na modul
	 * @return Module -- objekt modulu
	 */
	private function getModule() {
		return $this->module;
	}
	
	/**
	 * Metody vytvoří výchozí akce (add, edit a show)
	 */
	private function createDefaultActions() {
		$this->add();
		$this->edit();
		$this->show();
	}

	/**
	 * Metoda přidá požadovanou akci do seznamu akcí
	 * @param string -- zkratka akce
	 * @param string -- název akce pro kontroler
	 * @param string -- jazykový název akce - je přenášen do url
	 * @param boolean -- jestli má být daná akce výchozí
	 */
	final public function addAction($actionAbbr, $actionName, $actionLabel, $isDefault = false){
		if(!key_exists($actionAbbr, $this->actionsArray)){
			$this->actionsArray[$actionAbbr] = array(self::ACTION_TYPE_PARAM => $actionName,
															  self::ACTION_LABEL_PARAM => $actionLabel);
			if($isDefault){
				$this->defaultAction = $actionAbbr;
			}
		}
	}
	
	/**
	 * Funkce vrazí která akce je definována viz dokumentace
	 *
	 * @return string -- název prováděné akce viz dokumentace
	 */
	public function getSelectedAction() {
		return $this->actionsArray[self::$currentAction][self::ACTION_TYPE_PARAM];
	}

	/**
	 * Funkce vrazí která akce je definována viz dokumentace
	 *
	 * @return integer -- id modulu, který má provést akci
	 */
	public function getSelectedId() {
		return $this->selectedId;
	}

	/**
	 * Metoda vrací výchozí akci pro kontroler při zobrazení článku
	 * @return string -- název akce při článku
	 */
	public function getDefaultArticleAction() {
		return $this->actionsArray[$this->defaultAction][self::ACTION_TYPE_PARAM];
	}

	/**
	 * Funkce zjišťuje, jesli byla akce nastavena
	 *
	 * @return boolean -- true pokud byla akce nastavena
	 */
	public function isAction()
	{
		if(self::$currentAction != null AND self::$currentActionIdItem == $this->getModule()->getId()){
			return true;
		}
		return false;
	}

	/**
	 * Funkce zjišťuje, jesli byla nějáká akce nastavena
	 *
	 * @return boolean -- true pokud byla akce nastavena
	 */
	public function isSomeAction()
	{
		if(self::$currentAction != null){
			return true;
		}
		return false;
	}
	
	/*
	 * metody pro generování do url
	 */
		
	/**
	 * Zakladni metoda pro vygenerování akce edit
	 * @return string -- akce edit pro url 
	 */
	public function edit() {
		$actionAbbr = 'e';
		$this->addAction($actionAbbr, "edit", _('uprava'));
		return $this->createAction($actionAbbr);
	}
	
	/**
	 * Zakladni metoda pro vygenerování akce add
	 * @return string -- akce edit pro url 
	 */
	public function add($idModule = null) {
		$actionAbbr = 'a';
		$this->addAction($actionAbbr, "add", _('pridani'));
		return $this->createAction($actionAbbr);
	}
	
	/**
	 * Zakladni metoda pro vygenerování akce show
	 * @return string -- akce edit pro url 
	 */
	public function show($idModule = null) {
		$actionAbbr = 's';
		$this->addAction($actionAbbr, "show", _('ukaz'), true);
		return $this->createAction($actionAbbr);
	}

	/**
	 * Metoda vytvoří objekt pro danou akci pro vložení do URL
	 * @param string $actionAbbr -- identifikátor akce
	 * @return pole obsahující název, zkratku, id item
	 */
	protected function createAction($actionAbbr) {
		$action = $this->actionsArray[$actionAbbr];

        $return = array();
        $return[0]= $action[self::ACTION_LABEL_PARAM];
        $return[1]= $actionAbbr;
        $return[2]= $this->getModule()->getId();

        return $return;
//		echo $returnString = $action[self::ACTION_LABEL_PARAM].self::ACTION_URL_LABEL_TYPE_SEP
//			.$actionAbbr.self::ACTION_URL_TYPE_ID_SEP.$this->getModule()->getId();
//		return $returnString;
	}

	/**
	 * Metoda vrací výchozí akci pokud není definována
	 * @return string
	 * //TODO není korektní asi lepší použít setAction
	 */
//	public function getDefaultAction()
//	{
//		return $this->defaultAction;
//	}

	/**
	 * Metoda nastavuje akci v url
	 * @param string $action -- řetězec s akcí
	 * @param integer $idItem -- id item pro danou akci
	 */
	public static function setAction($action, $idItem) {
		self::$currentAction = $action;
		self::$currentActionIdItem = $idItem;
	}
}

?>