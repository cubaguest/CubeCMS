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
	
	public function getText() {
		if($this->text == null){
			$this->getTextFromDb();
		}
		
		return $this->text;
	}
	
	private function getTextFromDb() {
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(), array(self::COLUM_TEXT_IMAG =>"IFNULL(".self::COLUM_TEXT_LANG_PRFIX.Locale::getLang().",".self::COLUM_TEXT_LANG_PRFIX.Locale::getDefaultLang().")"))
									->where(self::COLUM_ID_ITEM." = ".$this->getModule()->getId());

		$this->text=$this->getDb()->fetchObject($sqlSelect);
		if($this->text != null){
			$this->text=$this->text->{self::COLUM_TEXT_IMAG};
		};
	}
	
	
}

?>