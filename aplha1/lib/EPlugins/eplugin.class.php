<?php
/**
 * Abstraktní třída pro Engine Pluginy
 * (např. scroll, comments, atd.)
 *
 */
class Eplugin {
	/**
	 * Výchozí cesta s šablonama
	 * @var string
	 */
	const EPLUGINS_DEFAULT_TEMPALTES_DIR = AppCore::TEMPLATES_DIR;

	/**
	 * Proměná s názvem šablony
	 * @var string 
	 */
	protected $templateFile = null;
	
	/**
	 * Proměná s polem hodnot předávaných do šablony
	 * @var array
	 */
	private $_tplVarsArray = array();
	
	/**
	 * Použití výchozí šablony epluginu
	 * @var boolean //TODO zbytečná
	 */
//	private $useDefaultTemplate = true;
	
	/**
	 * Objekt s linky
	 * @var Links
	 */
	private $links = null;
	
	/**
	 * Objekt s db konektorem
	 * @var Db
	 */
	private $dbConnector = null;
	
	/**
	 * Objekt s informacemi o modulu
	 * @var Module
	 */
	private $module = null;

	/**
	 * Objekt s informacemi o právach uživatele
	 * @var Rights
	 */
	private $rights = null;
	
	/**
	 * Konstruktor třídy, spouští metodu init();
	 *
	 */
	function __construct(Db $dbConnector, Module $module, Rights $rights)
	{
		$this->module = $module;
		$this->dbConnector = $dbConnector;
		$this->rights = $rights;

		$this->init();
	}
	
	protected function init(){}
	
	/**
	 * Metoda vrací objekt k tvorbě odkazů
	 *
	 * @return Links -- objekt odkazů
	 */
	protected function getLinks()
	{
		return new Links();
	}
	
	/**
	 * Metoda vrací objekt modulu
	 *
	 * @return Module -- objekt modulu
	 */
	protected function getModule()
	{
		return $this->module;
	}
	
	
	/**
	 * Metoda vrací objekt k připojení k db
	 *
	 * @return Db -- objekt Db
	 */
	protected function getDb()
	{
		return $this->dbConnector;
	}
	
	
	/**
	 * Abstraktní metoda pro inicializaci epluginu
	 *
	 */
//	abstract function init();
	
	/**
	 * Metoda vrací název šablony
	 * @return string -- název šablony
	 */
	final public function getTpl()
	{
//		echo $this->templateFile;
		return $this->templateFile;
	}
	
	/**
	 * Metoda vrací objekt pluginu scroll
	 * @return Scroll -- objekt scrolleru
	 */
	public function scroll()
	{
		return new Scroll($this->getDb(), $this->getModule(), $this->rights);
	}
	
	/**
	 * Metoda zařadí proměné epluginu do šablony
	 * Je volána z viewru
	 *
	 * @param Template -- objekt šablony
	 */
	public function assignToTpl(Template $template)	{
//		Přiřazení proměnných do pole
		$this->assignTpl();

//		Vložení proměných do šablony
		foreach ($this->_tplVarsArray as $var => $value) {
			$template->addVar($var, $value);
		}
	}
	
	/**
	 * Metoda obstarává přiřazení proměných do šablony
	 *
	 */
	protected function assignTpl(){}
	
	/**
	 * Metoda pro asociování proměných do šablony
	 *
	 * @param string -- název proměnné
	 * @param mixed -- hodnota proměnné
	 */
	final protected function toTpl($tplValueName, $value)
	{
		$this->_tplVarsArray[$tplValueName] = $value;
	}
	
	
}
?>