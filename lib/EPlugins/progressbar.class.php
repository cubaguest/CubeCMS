<?php
/**
 * Třída Epluginu pro práci s progrssbarem.
 * Třída je určena pro práci s progressbarem, který zobrazuje stav provádění
 * ve vlastním otevřeném okně. Je využit například při nahrávání více fotek.
 * 
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: scroll.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída EPluginu pro práci se scrolovátky
 * @todo 			dodělat možnost vytvářet vlastní styl okna
 */

class ProgressBarEplugin extends Eplugin {
	/**
	 * Název primární šablony s posunovátky
	 * @var string
	 */
	protected $templateFile = null;

	/**
	 * název adresáře s progressbarem
	 * @var string
	 */
	const PROGRESS_DIR = 'progressbar';
	
	/**
	 * název souboru s progressbarem
	 * @var string
	 */
	const PROGRESS_FILE = 'progressbar.php';
	
	/**
	 * Celkový počet procent
	 * @var integer
	 */
	const ALL_PERCENTS = 100;
	
	/**
	 * Aktuální počet procent
	 * @var integer
	 */
	private $actulaPercents = 0;
	
	/**
	 * Proměnná obsahuje objekt session
	 * @var Sessions
	 */
	private $sessions = null;
	
	/**
	 * Pole s stavy baru
	 * @var array
	 */
	private $progressArray = array('text' => null,
								   'percent' => 0,
								   'message' => null,
								   'title' => null,
								   'close_text' => null);
	
	/**
	 * Celkový počet prvků
	 * @var integer
	 */
	private $countSteps = 50;
	
	/**
	 * Aktuální krok
	 */
	private $actualStep = 1;
	
	/**
	 * Odkaz pro zobrazení progres baru
	 * @var string
	 */
	private $progresLink = null;
	
	/**
	 * Metoda inicializace, je spuštěna pří vytvoření objektu  
	 *
	 */
	protected function init()
	{
//		Načtení stránky z url
		$this->sessions = new Sessions();
		$this->progresLink = (string)$this->getLinks(true)->lang().AppCore::SPECIALITEMS_DIR.Links::COOL_URL_SEPARATOR.self::PROGRESS_DIR.Links::COOL_URL_SEPARATOR.self::PROGRESS_FILE;
		$this->setPageText();
		$this->reloadSession();
	}
	
	/**
	 * Metoda nastaví zobrazovanou správu v progressbaru
	 *
	 * @param string -- zpráva
	 */
	public function setMessage($message)
	{
		$this->progressArray['message']	 = $message;
		$this->countPercents();
		$this->actualStep++;
		$this->reloadSession();
		sleep(1);
	}
	
	/**
	 * Metoda nastavuje počet kroků v progressbaru
	 * @param integer -- počet kroků
	 */
	public function setSteps($steps) {
		$this->countSteps = $steps;
		$this->actualStep = 1;
	}
	
	/**
	 * Metoda nastavuje text zprávy
	 *
	 */
	public function setPageText($text = null) {
		if($text == null){
			$this->progressArray['text'] = _('Hotovo');
		}
	}
	
	/**
	 * Metoda nastavuje titulek okna
	 *
	 * @param string -- titulek okna
	 * @todo implementovat nastavení titulku okna
	 */
	public function setWindowTitle($title)
	{
//		Metoda nastavuje titulek okna
	}
	
	/**
	 * Metoda ektualizuje session
	 */
	private function reloadSession() {
		$this->sessions->add('progress', $this->progressArray);
		$this->sessions->commit();
	}
	
	/**
	 * Metoda spočítá nová procenta
	 */
	private function countPercents(){
		$perOnStep = self::ALL_PERCENTS/$this->countSteps;
		
		$perOnStep = round($perOnStep);
		
		$this->progressArray['percent']	 = $this->actualStep*$perOnStep;
		
		if($this->actualStep == $this->countSteps){
			$this->progressArray['percent']	 = -1;
		}
	}
	
	/**
	 * Uzavření okna s progressbarem
	 */
	public function close() {
		$this->progressArray['percent']	 = -1;
		$this->reloadSession();
	}
	
	/**
	 * Metoda vynuluje progressbar
	 */
	public function clear() {
		$this->progressArray = array('text' => null,
								   'percent' => 0,
								   'message' => null,
								   'title' => null,
								   'close_text' => null);
	}
	
	/**
	 * Metoda vynuluje progresbar
	 */
	public function __destruct() {
//		$this->clear();
		$this->reloadSession();
	}
	
	/**
	 * Metoda obstarává přiřazení proměných do šablony
	 *
	 */
	protected function assignTpl(){
//		Zapnutí tlačítek
		$this->toTpl("PROGRESSBAR_LINK", $this->progresLink);
		
		$jsPlugin = new ProgressBarJs();
		$this->toTplJSPlugin($jsPlugin);
	}
}
?>