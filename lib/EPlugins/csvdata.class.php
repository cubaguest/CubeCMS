<?php
/** 
 * EPlugin práci s daty ve formátu cv
 * 
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	CsvData class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: csvdata.class.php 3.0.0 beta1 2.11.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída Epluginu pro práci s daty ve formátu csv
 * 
 */

class CsvData extends Eplugin {
	/**
	 * Název primární šablony s posunovátky
	 * @var string
	 */
	protected $templateFile = 'csvdata.tpl';

	/**
	 * Názvy formulářových prvků
	 * @var string
	 */
	const FORM_PREFIX 		= 'csv_';
	const FORM_SAVE 		= 'save';
	const FORM_SEPARATOR 	= 'separator';
	
	/**
	 * Oddělova pro řetězce s oddělovačem
	 * @var string
	 */
	const CSV_ENCLOSED = '"';
	
	/**
	 * Výchozí název přípony csv souboru
	 * @var string
	 */
	const DEFAULT_FILE_EXTENSION = '.csv';
	
	/**
	 * Pole s daty
	 * @var array
	 */
	private $dataArray = array();
	
	/**
	 * Pole s popisky dat
	 * @var array
	 */
	private $labelsArray = array();
	
	/**
	 * Řetězec s csv daty
	 * @var string
	 */
	private $csvData = null;
	
	/**
	 * ID šablony
	 * @var integer
	 */
	private $idcsv = '1';
	
	/**
	 * Pole s možnými oddělovači
	 * @var array
	 */
	private $csvDataSeparators = array(1 => ',', ';', '.');
	
	/**
	 * Nastavený separator
	 * @var string
	 */
	private $selectedSeparator = ',';
	
	/**
	 * Metoda inicializace, je spuštěna pří vytvoření objektu  
	 *
	 */
	protected function init(){
		
	}
	
	/**
	 * Metoda nastaví id šablony pro výpis
	 * @param ineger -- id šablony (jakékoliv)
	 */
	public function setIdTpl($id) {
		$this->idUserFiles = $id;
	}
	
	/**
	 * Metoda kontroluje, jestli se odesílají data v csv
	 * 
	 * @return boolean -- true pokud jsou datat odesílána
	 */
	public function checkSendCsvData() {
		if(isset($_POST[self::FORM_PREFIX.self::FORM_SAVE])){
			$this->selectedSeparator = $this->csvDataSeparators[$_POST[self::FORM_PREFIX.self::FORM_SEPARATOR]];
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Metoda nastaví data, která se budou převádět na csv řetězec
	 * 
	 * @param array -- pole s daty (dvourozměrné pole)
	 */
	public function setData($data) {
		$this->dataArray = $data;
	}
	
	/**
	 * Metoda nastaví popisky k datům (nutný stejný počet popisku jako dat)
	 * @param array -- pole s popisky
	 */
	public function setDataLabels($labels) {
		$this->labelsArray = $labels;
	}
	
	
	/**
	 * Metoda odešle data v csv formátu jako soubor pomocí hlaviček
	 *
	 */
	public function sendData($fileName = 'data') {
		
		$csvData = $this->getCsvData();
				
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Length: " . strlen($csvData));
    	// Output to browser with appropriate mime type, you choose ;)
		header("Content-type: text/x-csv");
		//header("Content-type: text/csv");
		//header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=".$fileName.self::DEFAULT_FILE_EXTENSION);
		echo $csvData;
		exit;
	}
	
	/**
	 * Metoda vrací data csv jako řetězec, např, pro uložení do db
	 * 
	 * @return string -- řetězec s daty
	 */
	public function getCsvData() {
		$csvString = null;
		
		if(!empty($this->dataArray) AND is_array($this->dataArray)){
			foreach ($this->dataArray as $row) {
				$rowString = null;
				
				foreach ($row as $cell) {
					$rowString .= $this->conversionItem($cell).$this->selectedSeparator;
				}
//				Odstraní poslední oddělovač
				$rowString = substr($rowString, 0, strlen($rowString)-1);
				$csvString .= $rowString."\n";
			}
		}
				
		return $csvString;
	}
	
	/**
	 * Metoda kontroluje a popřípadě opravuje prvek pro použití v csv
	 *
	 * @param string -- prvek
	 * @return string -- upravený prvek
	 */
	private function conversionItem($item) {
//		Test na uvozovky
		if(strpos($item, self::CSV_ENCLOSED) !== false){
			$item = str_replace(self::CSV_ENCLOSED, self::CSV_ENCLOSED.self::CSV_ENCLOSED, $item);
		}

//		Test na mezeru nebo separator
		if(strpos($item, ' ') !== false OR strpos($item, $this->selectedSeparator) !== false){
			$item = self::CSV_ENCLOSED.$item.self::CSV_ENCLOSED;
		}
		
		return $item;
	}
	
	/**
	 * Metoda uloží csv data do zadaného souboru
	 * 
	 * @param string -- název souboru
	 * //TODO není implementována
	 */
	public function saveCsvData($file) {
		;
	}
	
	/**
	 * Metoda obstarává přiřazení proměných do šablony
	 *
	 */
	protected function assignTpl(){
		$this->toTpl("CSV_SAVE_DATA_LABEL", _("Uložení dat v CSV formátu (Např. MS Excel)"));
		$this->toTpl("CSV_DATA_SEPARATOR", _("Oddělovač"));
		$this->toTpl("BUTTON_CSV_SAVE", _("Uložit"));
		
		$this->toTpl("SEPARATORS", $this->csvDataSeparators);
	}

}
?>