<?php
/*
 * Třída modelu  pro uložení detailů textů
 * 
 */
class SaveTextsDetailModel extends DbModel {
	/**
	 * Názvy sloupců v db
	 * @var string
	 */
	const COLUM_ID_ITEM = 'id_item';
	const COLUM_CHANGED_TIME = 'changed_time';

	/**
	 * Metoda uloží text do db
	 *
	 * @param array -- pole s texty v jazykových mutacích
	 * @param boolean -- jestli se má ukládat nový text, nebo aktualizovat
	 */
	public function saveTextToDb($textArray, $update = false) {
		if($update){
			$textArray[self::COLUM_CHANGED_TIME] = time();
			
			$sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
												->set($textArray)
												->where(self::COLUM_ID_ITEM." = '".$this->getModule()->getId()."'");
		} else {
			//Vygenerování sloupců do kterých se bude zapisovat
					$columsArray = array_keys($textArray);
					$valuesArray = array_values($textArray);
					array_push($columsArray, self::COLUM_ID_ITEM);
					array_push($valuesArray, $this->getModule()->getId());
					array_push($columsArray, self::COLUM_CHANGED_TIME);
					array_push($valuesArray, time());

					$sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable())
											   ->colums($columsArray)
											   ->values($valuesArray);
		}
		
//		Vložení do db
		if($this->getDb()->query($sqlInsert)){
			return true;
		} else {
			return false;
		}
	}
}

?>