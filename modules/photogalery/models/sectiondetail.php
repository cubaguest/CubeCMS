<?php
/*
 * Třída modelu s listem galerií
 */
class SectionDetailModel extends DbModel {
	/**
	 * Názvy sloupců v databázi pro tabulku s galeriemi
	 * @var string
	 */
//	const COLUMN_GALERY_LABEL_LANG_PREFIX 	= 'label_';
//	const COLUMN_GALERY_TEXT_LANG_PREFIX 	= 'text_';
//	const COLUMN_GALERY_URLKEY 				= 'urlkey';
//	const COLUMN_GALERY_TIME 				= 'time';
//	const COLUMN_GALERY_ID_USER 			= 'id_user';
//	const COLUMN_GALERY_ID 					= 'id_galery';
	const COLUMN_GALERY_ID_SECTION 			= 'id_section';

	/**
	 * Názvy sloupců v databázi pro tabulku s fotkami
	 * @var string
	 */
//	const COLUMN_PHOTOS_ID 					= 'id_photo';
//	const COLUMN_PHOTOS_ID_USER 				= 'id_user';
//	const COLUMN_PHOTOS_ID_GALERY 			= 'id_galery';
//	const COLUMN_PHOTOS_LABEL_LANG_PREFIX 	= 'label_';
//	const COLUMN_PHOTOS_TEXT_LANG_PREFIX 	= 'text_';
//	const COLUMN_PHOTOS_TIME 				= 'time';
//	const COLUMN_PHOTOS_FILE 				= 'file';

	/**
	 * Názvy sloupců v databázi pro tabulku se sekcemi
	 * @var string
	 */
	const COLUMN_SECTION_ID 				= 'id_section';
	const COLUMN_SECTION_ID_ITEM 			= 'id_item';
	const COLUMN_SECTION_ID_USER 			= 'id_user';
	const COLUMN_SECTION_LABEL_LANG_PREFIX 	= 'label_';
	const COLUMN_SECTION_URLKEY 			= 'urlkey';
	const COLUMN_SECTION_TIME 				= 'time';
	
	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
//	const COLUMN_USER_NAME = 'username';
//	const COLUMN_USER_ID = 'id_user';
	
	
	/**
	 * Speciální imaginární sloupce
	 * @var string
	 */
//	const COLUMN_GALERY_LABEL_IMAG 			= 'galerylabel';
//	const COLUMN_GALERY_TEXT_IMAG 			= 'galerytext';
//	const COLUMN_GALERY_LABEL_IMAG 			= 'galerylabel';
//	const COLUMN_GALERY_TEXT_IMAG 			= 'galerytext';
//	const COLUMN_GALERY_LANG_IMAG 			= 'lang';
//	const COLUMN_GALERY_URLKEY_IMAG 		= 'galeryurlkey';
//	const COLUMN_GALERY_SHOW_LINK 			= 'galeryshowlink';
//	const COLUMN_GALERY_EDIT_LINK_IMAG 		= 'editlink';
//	const COLUMN_GALERY_EDIT_LINK_IMAG 		= 'editlink';
	
//	const COLUMN_PHOTOS_COUNT 				= 'photoscount';
//	const COLUMN_PHOTOS_COUNT 				= 'photoscount';
//	const COLUMN_PHOTOS_TEXT_IMAG 			= 'phototext';
//	const COLUMN_PHOTOS_LABEL_IMAG 			= 'photolabel';
//	const COLUMN_PHOTOS_SHOW_LINK 			= 'photoshowlink';
//	const COLUMN_PHOTOS_EDIT_LINK_IMAG 		= 'editlink';
//	const COLUMN_PHOTOS_FILE_PREFIX_IMAG 	= 'file_';
//	const COLUMN_PHOTOS_LABEL_PREFIX_IMAG 	= 'label_';
	
	const COLUMN_SECTION_LABEL_IMAG 		= 'sectionlabel';
	const COLUMN_SECTION_URLKEY_IMAG 		= 'sectionurlkey';
//	const COLUMN_SECTION_SHOW_LINK 			= 'sectionshowlink';
//	const COLUMN_SECTION_EDIT_LINK_IMAG 		= 'editlink';
	const COLUMN_SECTION_NUM_GALERIES 		= 'num_gals';
	
	/**
	 * Proměná obsahuje vybranou sekci
	 * @var array -- vybraná sekce
	 */
	private $selectSection = null;
	
	public function getSection($idSection) {
		;
	}
	
	public function getSectionByUrlkey($urlkey) {
		if($this->selectSection == null){
			$sqlSelect = $this->getDb()->select()->from(array('secs' => $this->getModule()->getDbTable(3)), array(self::COLUMN_SECTION_LABEL_IMAG => "IFNULL(secs.".self::COLUMN_SECTION_LABEL_LANG_PREFIX.Locale::getLang().", secs.".self::COLUMN_SECTION_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
													self::COLUMN_SECTION_ID, self::COLUMN_SECTION_URLKEY, self::COLUMN_SECTION_URLKEY_IMAG=>self::COLUMN_SECTION_TIME))
											->join(array('gals'=>$this->getModule()->getDbTable(2)), 'secs.'.self::COLUMN_SECTION_ID.'=gals.'.self::COLUMN_GALERY_ID_SECTION, 'LEFT', array(self::COLUMN_SECTION_NUM_GALERIES =>'COUNT(gals.'.self::COLUMN_GALERY_ID_SECTION.')'))
											->group('secs.'.self::COLUMN_SECTION_ID)
											->order('secs.'.self::COLUMN_SECTION_TIME, 'DESC')
											->where('secs.'.self::COLUMN_SECTION_URLKEY.' = \''.$urlkey.'\'');
											
			$this->selectSection = $this->getDb()->fetchAssoc($sqlSelect, true);
		}									
		return $this->selectSection;
	}

	/**
	 * Metoda vraci detail sekce se všemi jazyky
	 *
	 * @param string -- url klíč
	 * @return array -- pole s detaily sekce
	 */
	public function getSectionByUrlkeyAllLangs($urlkey) {
			$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(3))
											->where(self::COLUMN_SECTION_URLKEY.' = \''.$urlkey.'\'');
											
			$section = $this->getDb()->fetchAssoc($sqlSelect, true);
		return $section;
	}
	
	/**
	 * Metoda uloží novinku do db
	 *
	 * @param array -- pole s texty novinky
	 */
	public function saveNewSection($sectionArray, $mainLabel, $idUser = 0) {
//			vygenerování url klíče	
			$dbHelper = new DbCtrlHelper();
			$urlKey = $dbHelper->generateDatabaseUrlKey($mainLabel, $this->getModule()->getDbTable(3), self::COLUMN_SECTION_URLKEY);
			
			//Vygenerování sloupců do kterých se bude zapisovat
			$columsArray = array_keys($sectionArray);
			$valuesArray = array_values($sectionArray);
			array_push($columsArray, self::COLUMN_SECTION_URLKEY);
			array_push($valuesArray, $urlKey);
			array_push($columsArray, self::COLUMN_SECTION_ID_ITEM);
			array_push($valuesArray, $this->getModule()->getId());
			array_push($columsArray, self::COLUMN_SECTION_TIME);
			array_push($valuesArray, time());
			array_push($columsArray, self::COLUMN_SECTION_ID_USER);
			array_push($valuesArray, $idUser);

			$sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable(3))
					->colums($columsArray)
					->values($valuesArray);
				
		//		Vložení do db
		if($this->getDb()->query($sqlInsert)){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Metoda uloží upravenou sekci do db
	 *
	 * @param array -- pole s detaily sekce
	 */
	public function saveEditSection($sectionArray, $urlkey, $idSection = null) {
		//TODO dodělat generování nového url klíče

		$sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable(3))
											 ->set($sectionArray)
											 ->where(self::COLUMN_SECTION_URLKEY." = '".$urlkey."'");

		if($idSection != null){
			$sqlInsert = $sqlInsert->where(self::COLUMN_SECTION_ID." = ".$idSection);
		}

		// vložení do db
		if($this->getDb()->query($sqlInsert)){
			return true;
		} else {
			return false;
		};
	}
	
	/**
	 * Metoda vymaže sekci z db
	 * 
	 * @param integer -- id sekce
	 */
	public function deleteSection($id) {
		//		Končný výmaz z db
		$sqlDelete = $this->getDb()->delete()->from($this->getModule()->getDbTable(3))
											 ->where(self::COLUMN_SECTION_ID.' = '.$id);
			
		return $this->getDb()->query($sqlDelete);	
	}
}

?>