<?php
/*
 * Třída modelu s listem galerií
 */
class SectionListModel extends DbModel {
	/**
	 * Názvy sloupců v databázi pro tabulku s galeriemi
	 * @var string
	 */
//	const COLUMN_GALERY_LABEL_LANG_PREFIX 	= 'label_';
//	const COLUMN_GALERY_TEXT_LANG_PREFIX 	= 'text_';
//	const COLUMN_GALERY_URLKEY 				= 'urlkey';
//	const COLUMN_GALERY_TIME 				= 'time';
//	const COLUMN_GALERY_ID_USER 			= 'id_user';
	const COLUMN_GALERY_ID 					= 'id_galery';
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
//	const COLUMN_SECTION_ID_USER 			= 'id_user';
	const COLUMN_SECTION_LABEL_LANG_PREFIX 	= 'label_';
	const COLUMN_SECTION_URLKEY 			= 'urlkey';
//	const COLUMN_SECTION_TIME 				= 'time';
	
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
	 * Proměná s listem sekcí
	 * @var array
	 */
	private $sectionList = null;
	
	
	/**
	 * Metoda vrací seznam sekcí
	 *
	 * @return array -- list sekcí
	 */
	public function getSectionList() {
		if($this->sectionList == null){
			$sqlSelectSections = $this->getDb()->select()->from(array('secs' =>$this->getModule()->getDbTable(3)), array(self::COLUMN_SECTION_LABEL_IMAG => "IFNULL(secs.".self::COLUMN_SECTION_LABEL_LANG_PREFIX.Locale::getLang().", secs.".self::COLUMN_SECTION_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
													 self::COLUMN_SECTION_ID, self::COLUMN_SECTION_URLKEY_IMAG=>self::COLUMN_SECTION_URLKEY))
													 ->join(array('gals' => $this->getModule()->getDbTable(2)), 'gals.'.self::COLUMN_GALERY_ID_SECTION.' = secs.'.self::COLUMN_SECTION_ID, 'LEFT', array(self::COLUMN_SECTION_NUM_GALERIES => 'COUNT(gals.'.self::COLUMN_GALERY_ID.')'))
													 ->where('secs.'.self::COLUMN_SECTION_ID_ITEM.' = '.$this->getModule()->getId())
													 ->group('secs.'.self::COLUMN_SECTION_ID)
													 ->order(self::COLUMN_SECTION_LABEL_IMAG);
			
			$this->sectionList = $this->getDb()->fetchAssoc($sqlSelectSections);
		}
			
		return $this->sectionList;			
	}
}

?>