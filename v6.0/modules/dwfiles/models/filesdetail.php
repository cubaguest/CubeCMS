<?php
/*
 * Třída modelu s detailem textu
 * 
 */
class FilesDetailModel extends DbModel {
//	function _init() {

//	}
	/**
	 * Názvy sloupců v db
	 * @var string
	 */
	const COLUMN_ID_ITEM = 'id_item';
	const COLUMN_ID_FILE = 'id_file';
	const COLUMN_LABEL = 'label';
	const COLUMN_FILE_NAME  = 'file';



	/**
	 * Metoda provede uložení souboru do db
	 * @param string -- název souboru
    * @param array -- pole s popisy
	 * 
	 */
   public function saveNewFile($fileName, $fileLabels) {
      $fileArr = $this->createValuesArray(self::COLUMN_FILE_NAME, $fileName,
                                          self::COLUMN_LABEL, $fileLabels,
                                          self::COLUMN_ID_ITEM, $this->getModule()->getId());

      $sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable())
      ->colums(array_keys($fileArr))
      ->values(array_values($fileArr));
//      //		Vložení do db
      if($this->getDb()->query($sqlInsert)){
         return true;
      } else {
         return false;
      }
   }

   /**
    * Metoda vrací seznam souborů
    */
   public function getDwFiles() {
      $sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(),
         array(self::COLUMN_LABEL => "IFNULL(".self::COLUMN_LABEL.'_'.Locale::getLang().", "
            .self::COLUMN_LABEL.'_'.Locale::getDefaultLang().")", self::COLUMN_ID_FILE,
            self::COLUMN_FILE_NAME))
		->where(self::COLUMN_ID_ITEM." = ".$this->getModule()->getId())
		->order(self::COLUMN_LABEL, 'asc');

		$returArray = $this->getDb()->fetchAssoc($sqlSelect);

		return $returArray;
   }

   /**
    * Metoda vrací soubor podle ID
    */
   public function getDwFile($id) {
      $sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(),
         self::COLUMN_FILE_NAME)
		->where(self::COLUMN_ID_FILE." = ".$id);

		$returArray = $this->getDb()->fetchAssoc($sqlSelect, true);

      if(!empty ($returArray)){
         return $returArray[self::COLUMN_FILE_NAME];
      }
      return false;
		
   }

   /**
    * Metoda smaže soubor podle ID
    */
   public function deleteDwFile($id) {
      $sqlDelete = $this->getDb()->delete()->from($this->getModule()->getDbTable())
      ->where(self::COLUMN_ID_FILE." = ".$id);

      return $this->getDb()->query($sqlDelete);

   }


//	public function getText() {
//		if($this->text == null){
//			$this->getTextFromDb();
//		}
//
//		return $this->text;
//	}
	
	/**
	 * Metoda načte jeden jazykový text z databáze
	 *
	 */
//	private function getTextFromDb() {
//		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(), array(self::COLUMN_TEXT =>"IFNULL(".self::COLUMN_TEXT_LANG_PRFIX.Locale::getLang().",".self::COLUMN_TEXT_LANG_PRFIX.Locale::getDefaultLang().")"))
//									->where(self::COLUMN_ID_ITEM." = ".$this->getModule()->getId());
//
//		$this->text=$this->getDb()->fetchObject($sqlSelect);
//		if($this->text != null){
//			$this->text=$this->text->{self::COLUMN_TEXT};
//		};
//	}
	
	/**
	 * Metoda načte všechny jazkové variace textu z db 
	 *
	 * @return array -- pole s textama
	 */
//	public function getAllLangText() {
//		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
//						->where(self::COLUMN_ID_ITEM." = ".$this->getModule()->getId());
//
//		$text = $this->getDb()->fetchAssoc($sqlSelect,true);
//
//
//      if(!empty ($text)){
//         $text = $this->parseDbValuesToArray($text, self::COLUMN_TEXT);
//      }
//
//		return $text[self::COLUMN_TEXT];
//	}
	/*EOF getAllLangText*/

//   public function saveEditText($texts) {
//      if($this->isSaved()){
//         $textArr = $this->createValuesArray(self::COLUMN_TEXT, $texts,
//            self::COLUMN_CHANGED_TIME, time());
//         //
//         $sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
//               ->set($textArr)
//               ->where(self::COLUMN_ID_ITEM." = '".$this->getModule()->getId()."'");
//         //
//         //      // vložení do db
//         if($this->getDb()->query($sqlInsert)){
//            return true;
//         } else {
//            return false;
//         };
//      } else {
//         $textArr = $this->createValuesArray(self::COLUMN_TEXT, $texts,
//            self::COLUMN_CHANGED_TIME, time(),
//            self::COLUMN_ID_ITEM, $this->getModule()->getId());
//         //
//         $sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable())
//               ->colums(array_keys($textArr))
//               ->values(array_values($textArr));
//         //      //		Vložení do db
//         if($this->getDb()->query($sqlInsert)){
//            return true;
//         } else {
//            return false;
//         }
//      }
//   }
}

?>