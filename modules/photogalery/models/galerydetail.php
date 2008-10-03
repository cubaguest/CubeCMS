<?php
/**
 * Model pro detail galerie
 *
 */
class GaleryDetailModel extends DbModel {
	/**
	 * Názvy sloupců v databázi pro tabulku s galeriemi
	 * @var string
	 */
	const COLUMN_GALERY_LABEL_LANG_PREFIX 	= 'label_';
	const COLUMN_GALERY_TEXT_LANG_PREFIX 	= 'text_';
	const COLUMN_GALERY_URLKEY 				= 'urlkey';
	const COLUMN_GALERY_TIME 				= 'time';
	const COLUMN_GALERY_ID_USER 			= 'id_user';
	const COLUMN_GALERY_ID 					= 'id_galery';
	const COLUMN_GALERY_ID_SECTION 			= 'id_section';

	/**
	 * Názvy sloupců v databázi pro tabulku s fotkami
	 * @var string
	 */
	const COLUMN_PHOTOS_ID 					= 'id_photo';
//	const COLUMN_PHOTOS_ID_USER 				= 'id_user';
	const COLUMN_PHOTOS_ID_GALERY 			= 'id_galery';
	const COLUMN_PHOTOS_LABEL_LANG_PREFIX 	= 'label_';
//	const COLUMN_PHOTOS_TEXT_LANG_PREFIX 	= 'text_';
	const COLUMN_PHOTOS_TIME 				= 'time';
	const COLUMN_PHOTOS_FILE 				= 'file';

	/**
	 * Názvy sloupců v databázi pro tabulku se sekcemi
	 * @var string
	 */
//	const COLUMN_SECTION_ID 				= 'id_section';
//	const COLUMN_SECTION_ID_ITEM 			= 'id_item';
//	const COLUMN_SECTION_ID_USER 			= 'id_user';
//	const COLUMN_SECTION_LABEL_LANG_PREFIX 	= 'label_';
//	const COLUMN_SECTION_URLKEY 			= 'urlkey';
//	const COLUMN_SECTION_TIME 				= 'time';
	
	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
	const COLUMN_USER_NAME = 'username';
	const COLUMN_USER_ID = 'id_user';
	
	
	/**
	 * Speciální imaginární sloupce
	 * @var string
	 */
	const COLUMN_GALERY_LABEL_IMAG 			= 'galerylabel';
	const COLUMN_GALERY_TEXT_IMAG 			= 'galerytext';
//	const COLUMN_GALERY_LABEL_IMAG 			= 'galerylabel';
//	const COLUMN_GALERY_TEXT_IMAG 			= 'galerytext';
//	const COLUMN_GALERY_LANG_IMAG 			= 'lang';
	const COLUMN_GALERY_URLKEY_IMAG 		= 'galeryurlkey';
//	const COLUMN_GALERY_SHOW_LINK 			= 'galeryshowlink';
//	const COLUMN_GALERY_EDIT_LINK_IMAG 		= 'editlink';
	
	const COLUMN_PHOTOS_COUNT 				= 'num_photos';
//	const COLUMN_PHOTOS_TEXT_IMAG 			= 'phototext';
	const COLUMN_PHOTOS_LABEL_IMAG 			= 'photolabel';
//	const COLUMN_PHOTOS_SHOW_LINK 			= 'photoshowlink';
//	const COLUMN_PHOTOS_EDIT_LINK_IMAG 		= 'editlink';
//	const COLUMN_PHOTOS_FILE_PREFIX_IMAG 	= 'file_';
//	const COLUMN_PHOTOS_LABEL_PREFIX_IMAG 	= 'label_';
	
//	const COLUMN_SECTION_LABEL_IMAG 		= 'sectionlabel';
//	const COLUMN_SECTION_URLKEY_IMAG 		= 'sectionurlkey';
//	const COLUMN_SECTION_SHOW_LINK 			= 'sectionshowlink';
//	const COLUMN_SECTION_EDIT_LINK_IMAG 		= 'editlink';

	/**
	 * Proměná obsahuje detail galerie
	 * @var array
	 */
	private $galeryDetail = null;
	
	/**
	 * Proměná obsahuje počet fotek v galerie
	 * @var integer
	 */
	private $numPhotos = null;
	
	/**
	 * Proměná obsahuje id galerie
	 * @var integer
	 */
	private $idGalery = 0;
	
	/**
	 * Metoda načte detail galerie
	 *
	 * @param string -- url klíč galerie
	 */
	public function getGaleryDetail($urlKey) {
		if($this->galeryDetail == null){
			$sqlSelectGaleries = $this->getDb()->select()->from(array('gals' => $this->getModule()->getDbTable(2)), array(self::COLUMN_GALERY_LABEL_IMAG => "IFNULL(gals.".self::COLUMN_GALERY_LABEL_LANG_PREFIX.Locale::getLang().", gals.".self::COLUMN_GALERY_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
											self::COLUMN_GALERY_TEXT_IMAG => "IFNULL(gals.".self::COLUMN_GALERY_TEXT_LANG_PREFIX.Locale::getLang().", gals.".self::COLUMN_GALERY_TEXT_LANG_PREFIX.Locale::getDefaultLang().")",										
											self::COLUMN_GALERY_ID, self::COLUMN_GALERY_ID_USER, self::COLUMN_GALERY_TIME, self::COLUMN_GALERY_URLKEY_IMAG => self::COLUMN_GALERY_URLKEY))			
											->join(array('users' => $this->getUserTable()), 'users.'.self::COLUMN_USER_ID.'=gals.'.self::COLUMN_GALERY_ID_USER, null, self::COLUMN_USER_NAME)
											->join(array('photos'=>$this->getModule()->getDbTable()), 'photos.'.self::COLUMN_PHOTOS_ID_GALERY.'=gals.'.self::COLUMN_GALERY_ID, 'LEFT', array(self::COLUMN_PHOTOS_COUNT => 'COUNT(photos.'.self::COLUMN_PHOTOS_ID_GALERY.')'))
											->group('photos.'.self::COLUMN_PHOTOS_ID_GALERY)
											->where('gals.'.self::COLUMN_GALERY_URLKEY.' = \''.$urlKey.'\'')
											->order('gals.'.self::COLUMN_GALERY_TIME, 'DESC')
											->order('gals.'.self::COLUMN_GALERY_LABEL_LANG_PREFIX.Locale::getDefaultLang());
		
			$this->galeryDetail = $this->getDb()->fetchAssoc($sqlSelectGaleries, true);
			
			$this->idGalery = $this->galeryDetail[self::COLUMN_GALERY_ID];
		}

		
		return $this->galeryDetail;
		
	}
	
	/**
	 * Metoda vrátí tabulku uživatelů
	 *
	 * @return string -- název tabulky s uživateli
	 */
	private function getUserTable() {
		$tableUsers = AppCore::sysConfig()->getOptionValue(Auth::CONFIG_USERS_TABLE_NAME, Config::SECTION_DB_TABLES);
		
		return $tableUsers;
	}
	
	
	/**
	 * Metoda načte seznam fotek v galerii
	 *
	 * @param integer -- id galerie
	 */
	public function getPhotosList($idGalery) {
//		Načtení fotek galerie
		$sqlSelect = $this->getDb()->select()->from(array('photos'=>$this->getModule()->getDbTable(1)), array(self::COLUMN_PHOTOS_LABEL_IMAG => "IFNULL(photos.".self::COLUMN_PHOTOS_LABEL_LANG_PREFIX.Locale::getLang().",
						photos.".self::COLUMN_PHOTOS_LABEL_LANG_PREFIX.Locale::getDefaultLang().")", self::COLUMN_PHOTOS_ID, self::COLUMN_PHOTOS_FILE, self::COLUMN_PHOTOS_TIME))
						->where('photos.'.self::COLUMN_PHOTOS_ID_GALERY." = '".$idGalery."'")
						->order('photos.'.self::COLUMN_PHOTOS_TIME)
						->order('photos.'.self::COLUMN_PHOTOS_LABEL_LANG_PREFIX.Locale::getDefaultLang());
			
		$photos = $this->getDb()->fetchAssoc($sqlSelect);
		
		return $photos;
	}
	
	/**
	 * Metoda vrací počet fotek v galerii
	 *
	 * @param integer -- id galerie
	 * @return integer -- počet fotek
	 */
	public function getNumPhotos($idGalery) {
		if($this->numPhotos == null){
	  		$sqlCount = $this->getDb()->select()->from($this->getModule()->getDbTable(), array("count"=>"COUNT(*)"))
												->where(self::COLUMN_GALERY_ID." = '".$idGalery."'");
		

//			Zjištění počtu záznamů									
			$count = $this->getDb()->fetchObject($sqlCount);
			$this->numPhotos = $count->count;
		}
		
		return $this->numPhotos;
	}
	
	/**
	 * Metoda vrací id vybrané galerie
	 *
	 * @return integer -- id galerie
	 */
	public function getIdGalery() {
		return $this->idGalery;
	}
	
	
}

?>