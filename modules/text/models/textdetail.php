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
	const COLUMN_ID_ITEM = 'id_item';
	const COLUMN_CHANGED_TIME = 'changed_time';
	const COLUMN_TEXT_LANG_PRFIX = 'text_';
	const COLUMN_TEXT = 'text';

	private $text = null;
	
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
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(), array(self::COLUMN_TEXT =>"IFNULL(".self::COLUMN_TEXT_LANG_PRFIX.Locale::getLang().",".self::COLUMN_TEXT_LANG_PRFIX.Locale::getDefaultLang().")"))
									->where(self::COLUMN_ID_ITEM." = ".$this->getModule()->getId());

		$this->text=$this->getDb()->fetchObject($sqlSelect);
		if($this->text != null){
			$this->text=$this->text->{self::COLUMN_TEXT};
		};
	}
	
	/**
	 * Metoda načte všechny jazkové variace textu z db 
	 *
	 * @return array -- pole s textama
	 */
	public function getAllLangText() {
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
						->where(self::COLUMN_ID_ITEM." = ".$this->getModule()->getId());
				
		$text = $this->getDb()->fetchAssoc($sqlSelect,true);


      if(!empty ($text)){
         $text = $this->parseDbValuesToArray($text, self::COLUMN_TEXT);
      }

		return $text[self::COLUMN_TEXT];
	}
	/*EOF getAllLangText*/

   public function saveEditText($texts) {
      if($this->isSaved()){
         $textArr = $this->createValuesArray(self::COLUMN_TEXT, $texts,
            self::COLUMN_CHANGED_TIME, time());
         //
         $sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
               ->set($textArr)
               ->where(self::COLUMN_ID_ITEM." = '".$this->getModule()->getId()."'");
         //
         //      // vložení do db
         if($this->getDb()->query($sqlInsert)){
            return true;
         } else {
            return false;
         };
      } else {
         $textArr = $this->createValuesArray(self::COLUMN_TEXT, $texts,
            self::COLUMN_CHANGED_TIME, time(),
            self::COLUMN_ID_ITEM, $this->getModule()->getId());
         //
         $sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable())
               ->colums(array_keys($textArr))
               ->values(array_values($textArr));
         //      //		Vložení do db
         if($this->getDb()->query($sqlInsert)){
            return true;
         } else {
            return false;
         }
      }
   }

   private function isSaved() {
      $sql = $this->getDb()->select()->from($this->getModule()->getDbTable(), array('count' => 'COUNT(*)'));

      $count = $this->getDb()->fetchObject($sql);

      if($count->count > 0){
         return true;
      }
      return false;

   }
}

?>