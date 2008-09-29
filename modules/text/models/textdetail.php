<?php
/*
 * Třída modelu s detailem textu
 * 
 */
class TextDetailModel extends DbModel {
//	function _init() {

//	}
	/**
	 * Názvy sloupců v db
	 * @var string
	 */
	const COLUM_ID_ITEM = 'id_item';
	const COLUM_CHANGED_TIME = 'changed_time';
	const COLUM_TEXT_LANG_PRFIX = 'text_';

	/**
	 * Názvy imaginárních sloupců
	 * @var string
	 */
	const COLUM_TEXT_IMAG = 'text';
	
	private $text = null;
	private $langTexts = null;
	
	/**
	 * Jestli je text uložen v db
	 * @var boolean
	 */
	private $inDb = false;
	
	/**
	 * Metoda provede načtení textu z db
	 *
	 * @return string -- načtený text
	 */
	public function getText() {
		if($this->text == null){
			$this->getTextFromDb();
		}
		
		return $this->text;
	}
	
	/**
	 * Metoda načte jeden jazykový text z databáze
	 *
	 */
	private function getTextFromDb() {
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(), array(self::COLUM_TEXT_IMAG =>"IFNULL(".self::COLUM_TEXT_LANG_PRFIX.Locale::getLang().",".self::COLUM_TEXT_LANG_PRFIX.Locale::getDefaultLang().")"))
									->where(self::COLUM_ID_ITEM." = ".$this->getModule()->getId());

		$this->text=$this->getDb()->fetchObject($sqlSelect);
		if($this->text != null){
			$this->text=$this->text->{self::COLUM_TEXT_IMAG};
		};
	}
	
	/**
	 * Metoda načte všechny jazkové variace textu z db 
	 *
	 * @param array -- pole s odeslanými texty
	 * @return array -- pole s textama
	 */
	public function getAllLangText($valuesArray) {
		
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
						->where(self::COLUM_ID_ITEM." = ".$this->getModule()->getId());
				
		$text = $this->getDb()->fetchAssoc($sqlSelect,true);

		if(!empty($text)){
			$this->inDb = true;
		}
		
		$localeHelper = new LocaleCtrlHelper();
				
		$lArray = $localeHelper->generateArray(array('text'), $valuesArray, $text);
		
//		echo "<pre>";
//		print_r($lArray);
//		echo "</pre>";

		return $lArray;
	}
	/*EOF getAllLangText*/

	/**
	 * Metoda vrací jesli je text uložen v databázi
	 *
	 * @return boolean -- true pokud je text v db
	 */
	public function inDb() {
		return  $this->inDb;
	}
}

?>