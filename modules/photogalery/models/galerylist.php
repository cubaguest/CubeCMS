<?php
/*
 * Třída modelu s listem galerií
 */
class GaleryListModel extends DbModel {
   /**
    * Názvy sloupců v databázi pro tabulku s galeriemi
    * @var string
    */
   const COLUMN_GALERY_LABEL     = 'label';
   const COLUMN_GALERY_TEXT      = 'text';
   const COLUMN_GALERY_TIME_ADD  = 'time_add';
   const COLUMN_GALERY_ID_USER 	= 'id_user';
   const COLUMN_GALERY_ID 			= 'id_galery';
   const COLUMN_GALERY_ID_ITEM 	= 'id_item';

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

   //	const COLUMN_PHOTOS_COUNT 				= 'num_photos';
   //	const COLUMN_PHOTOS_COUNT 				= 'photoscount';
   //	const COLUMN_PHOTOS_TEXT_IMAG 			= 'phototext';
   //	const COLUMN_PHOTOS_LABEL_IMAG 			= 'photolabel';
   //	const COLUMN_PHOTOS_SHOW_LINK 			= 'photoshowlink';
   //	const COLUMN_PHOTOS_EDIT_LINK_IMAG 		= 'editlink';
   //	const COLUMN_PHOTOS_FILE_PREFIX_IMAG 	= 'file_';
   //	const COLUMN_PHOTOS_LABEL_PREFIX_IMAG 	= 'label_';

   //	const COLUMN_SECTION_LABEL_IMAG 		= 'sectionlabel';
   //	const COLUMN_SECTION_URLKEY_IMAG 		= 'sectionurlkey';
   //	const COLUMN_SECTION_SHOW_LINK 			= 'sectionshowlink';
   //	const COLUMN_SECTION_EDIT_LINK_IMAG 		= 'editlink';


   /**
    * Metoda vrací zadaný počet galerií
    * @param int $start -- od které galerie se začíná
    * @param int $cout -- počet galerií
    */
   public function getGaleryList($start = 0, $count = 0, $orderName = false) {
      $sqlSelect = $this->getDb()->select()->from(array("gal" => $this->getModule()->getDbTable()),
         array(self::COLUMN_GALERY_LABEL => "IFNULL(".self::COLUMN_GALERY_LABEL.'_'.Locale::getLang()
            .", ".self::COLUMN_GALERY_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_GALERY_TEXT => "IFNULL(".self::COLUMN_GALERY_TEXT.'_'.Locale::getLang()
            .", ".self::COLUMN_GALERY_TEXT.'_'.Locale::getDefaultLang().")",
            self::COLUMN_GALERY_TIME_ADD, self::COLUMN_GALERY_ID))
      ->where("gal.".self::COLUMN_GALERY_ID_ITEM." = ".$this->getModule()->getId());

      if($count != 0){
         $sqlSelect->limit($start, $count);
      }

      if($orderName){
         $sqlSelect->order(self::COLUMN_GALERY_LABEL, 'asc');
      } else {
         $sqlSelect->order("gal.".self::COLUMN_GALERY_TIME_ADD, 'desc');
      }

      return $this->getDb()->fetchAssoc($sqlSelect);
   }

   /**
    * Metoda vrací celkový počet galeríí
    *
    * @return int -- počet galerií
    */
   public function getCountGaleries() {
      $sqlCount = $this->getDb()->select()->from($this->getModule()->getDbTable(), array("count"=>"COUNT(*)"))
      ->where(self::COLUMN_GALERY_ID_ITEM. ' = '.$this->getModule()->getId());
      $count = $this->getDb()->fetchObject($sqlCount);
      return $count->count;
   }












   private function getUserTable() {
      $tableUsers = AppCore::sysConfig()->getOptionValue(Auth::CONFIG_USERS_TABLE_NAME, Config::SECTION_DB_TABLES);

      return $tableUsers;
   }

   public function getGaleriesListWithSections($idSection = null) {
      $sqlSelectGaleries = $this->getDb()->select()->from(array('gals' => $this->getModule()->getDbTable(2)), array(self::COLUMN_GALERY_LABEL_IMAG => "IFNULL(gals.".self::COLUMN_GALERY_LABEL_LANG_PREFIX.Locale::getLang().", gals.".self::COLUMN_GALERY_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
            self::COLUMN_GALERY_ID, self::COLUMN_GALERY_ID_USER, self::COLUMN_GALERY_TIME, self::COLUMN_GALERY_URLKEY_IMAG => self::COLUMN_GALERY_URLKEY))
      ->join(array('sections'=>$this->getModule()->getDbTable(3)), 'sections.'.self::COLUMN_SECTION_ID.'=gals.'.self::COLUMN_GALERY_ID_SECTION, null, array(self::COLUMN_SECTION_LABEL_IMAG => "IFNULL(sections.".self::COLUMN_SECTION_LABEL_LANG_PREFIX.Locale::getLang().", sections.".self::COLUMN_SECTION_LABEL_LANG_PREFIX.Locale::getDefaultLang().")"))
      ->order(self::COLUMN_SECTION_LABEL_IMAG)
      ->order('gals.'.self::COLUMN_GALERY_TIME, 'DESC');

      if($idSection != null){
         $sqlSelectGaleries = $sqlSelectGaleries->where('gals.'.self::COLUMN_GALERY_ID_SECTION.' = '.$idSection);
      }

      $galsArray = $this->getDb()->fetchAssoc($sqlSelectGaleries);

      return $galsArray;
   }

}

?>