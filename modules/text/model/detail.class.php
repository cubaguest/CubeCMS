<?php
/*
 * Třída modelu s detailem textu
 * 
 */
class Text_Model_Detail extends Model_Db {
   /**
    * Tabulka s detaily
    */
    const DB_TABLE = 'texts';

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
//		if($this->text == null){
//			$this->getTextFromDb();
//		}
//		return $this->text;
      return "pokusný text";
	}
	
	/**
	 * Metoda načte jeden jazykový text z databáze
	 *
	 */
	private function getTextFromDb() {
		$sqlSelect = $this->getDb()->select()
      ->table(Db::table(self::DB_TABLE))
      ->colums(array(self::COLUMN_TEXT =>"IFNULL(".self::COLUMN_TEXT_LANG_PRFIX.Locale::getLang()
            .",".self::COLUMN_TEXT_LANG_PRFIX.Locale::getDefaultLang().")"))
		->where(self::COLUMN_ID_ITEM, $this->module()->getId());

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
		$sqlSelect = $this->getDb()->select()
         ->table(Db::table(self::DB_TABLE))
			->where(self::COLUMN_ID_ITEM, $this->module()->getId());
				
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
         $sqlInsert = $this->getDb()->update()->table(Db::table(self::DB_TABLE))
               ->set($textArr)
               ->where(self::COLUMN_ID_ITEM, $this->module()->getId());
         //      // vložení do db
         return $this->getDb()->query($sqlInsert);
      } else {
         $textArr = $this->createValuesArray(self::COLUMN_TEXT, $texts,
            self::COLUMN_CHANGED_TIME, time(),
            self::COLUMN_ID_ITEM, $this->module()->getId());
         $sqlInsert = $this->getDb()->insert()->table(Db::table(self::DB_TABLE))
               ->colums(array_keys($textArr))
               ->values(array_values($textArr));
         //	Vložení do db
         return $this->getDb()->query($sqlInsert);
      }
   }

   private function isSaved() {
      $sql = $this->getDb()->select()->table(Db::table(self::DB_TABLE))
      ->colums(array('count' => 'COUNT(*)'))
      ->where(self::COLUMN_ID_ITEM, $this->module()->getId());

      $count = $this->getDb()->fetchObject($sql);

      if($count->count > 0){
         return true;
      }
      return false;
   }

   public function getLastChange() {
      $sqlSelect = $this->getDb()->select()
      ->table(Db::table(self::DB_TABLE))
      ->colums(self::COLUMN_CHANGED_TIME)
		->where(self::COLUMN_ID_ITEM, $this->module()->getId());

		$time=$this->getDb()->fetchObject($sqlSelect);
      if($time != null AND $time instanceof stdClass){
         return $time->{self::COLUMN_CHANGED_TIME};
		};
   }
}

?>