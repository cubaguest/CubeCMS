<?php
/*
 * Třída modelu s detailem fotky
 */
class GaleryPhotoDetailModel extends DbModel {
	/**
	 * Názvy sloupců v databázi pro tabulku s fotkami
	 * @var string
	 */
	const COLUMN_PHOTOS_ID 					= 'id_photo';
	const COLUMN_PHOTOS_ID_USER 			= 'id_user';
	const COLUMN_PHOTOS_ID_GALERY 		= 'id_galery';
	const COLUMN_PHOTOS_LABEL           = 'label';
	const COLUMN_PHOTOS_TIME_ADD 			= 'time_add';
	const COLUMN_PHOTOS_FILE 				= 'file';

	/**
	 * Metoda načte fotku z požadovaného rozsahu
	 *
	 * @param integer -- id galerie
	 * @param integer -- kolik fotek se má vybrat (výchozí je 300 protože asi více
    * fotek v galerii nebude, omezí to alespoň pukud by bylo zlepší výkon)
	 * @param integer -- od které fotky se má vybírat
	 * @return array -- pole s detailem fotky
	 */
	public function getPhotos($idGalery, $count = 300, $from = 0) {
//			načtení fotky/fotek z db
			$sqlSelect = $this->getDb()->select()->from(array('photo' => $this->getModule()->getDbTable(2)),
				array(self::COLUMN_PHOTOS_LABEL => "IFNULL(photo.".self::COLUMN_PHOTOS_LABEL.'_'.Locale::getLang()
               .", photo.".self::COLUMN_PHOTOS_LABEL.'_'.Locale::getDefaultLang().")",
					self::COLUMN_PHOTOS_ID, self::COLUMN_PHOTOS_FILE))
				->where(self::COLUMN_PHOTOS_ID_GALERY." = '".$idGalery."'")
				->limit((int)$from, (int)$count)
				->order('photo.'.self::COLUMN_PHOTOS_TIME_ADD)
				->order(self::COLUMN_PHOTOS_LABEL);

		return $this->getDb()->fetchAssoc($sqlSelect);
	}
	
	/**
	 * Metoda uloží novou fotografii
	 *
	 * @param array $photoLabel -- pole s popisem fotky
    * @param string  $fileName -- název souboru fotky
	 * @param integer $idGalery -- id galerie
	 * @param integer $time -- časové razítko kdy byla fotka vytvořena
	 * @param integer $idUser -- id uživatele, který fotku přidal
	 */
	public function saveNewPhoto($photoLabel, $fileName, $idGalery, $time = null, $idUser = 0) {
		$photoArr = $this->createValuesArray(self::COLUMN_PHOTOS_LABEL, $photoLabel,
                                          self::COLUMN_PHOTOS_FILE, $fileName,
                                          self::COLUMN_PHOTOS_ID_GALERY, $idGalery,
                                          self::COLUMN_PHOTOS_TIME_ADD, time());

      $sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable(2))
      ->colums(array_keys($photoArr))
      ->values(array_values($photoArr));
      //		Vložení do db
      return $this->getDb()->query($sqlInsert);
	}














   
	/**
	 * Metoda vrátí detail fotky podle zadaného id
	 * @param integer -- id fotky
	 */
	public function getPhotoById($id) {
		//			načtení fotky z db
			$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(2))
				->where(self::COLUMN_PHOTOS_ID." = '".$id."'");
												 
			return $this->getDb()->fetchAssoc($sqlSelect, true);	
	}
	
	
	/**
	 * Metoda vymaže fotku z db
	 * 
	 * @param integer -- id fotky
	 */
	public function deletePhoto($idPhoto) {
		//		Končný výmaz z db
		$sqlDelete = $this->getDb()->delete()->from($this->getModule()->getDbTable(2))
											 ->where(self::COLUMN_PHOTOS_ID.' = '.$idPhoto);
			
		return $this->getDb()->query($sqlDelete);	
			
	}
	
	/**
	 * Metoda vrátí detaily fotek podle zadaných id
	 * @param array/imteger -- id fotky
	 */
	public function getPhotosById($id) {
		//			načtení fotek z db
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable());

		if(is_array($id)){
			foreach ($id as $idSelected) {
				$sqlSelect = $sqlSelect->where(self::COLUMN_PHOTOS_ID." = '".$idSelected."'", 'OR');
			}
		} else {
			$sqlSelect = $sqlSelect->where(self::COLUMN_PHOTOS_ID." = '".$idSelected."'");
		}	

		return $this->getDb()->fetchAssoc($sqlSelect);	
	}
	
	/**
	 * Metoda uloží upravenou fotografii do db
	 *
	 * @param array -- pole s detaily fotografie
	 * @param integer -- id fotky
	 */
	public function saveEditPhoto($photoArray,$idPhoto = null) {
		//TODO dodělat generování nového url klíče

		$sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
											 ->set($photoArray)
											 ->where(self::COLUMN_PHOTOS_ID." = '".$idPhoto."'");

		// vložení do db
		if($this->getDb()->query($sqlInsert)){
			return true;
		} else {
			return false;
		};
	}
	
}

?>