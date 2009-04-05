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
   const COLUMN_GALERY_LABEL 	= 'label';
   const COLUMN_GALERY_TEXT 	= 'text';
   const COLUMN_GALERY_TIME_ADD 	= 'time_add';
   const COLUMN_GALERY_TIME_CHANGE	= 'time_edit';
   const COLUMN_GALERY_ID 		= 'id_galery';
   const COLUMN_GALERY_ID_ITEM= 'id_item';

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
    * Proměná obsahuje název galerie
    * @var array
    */
   private $galeryLabel = null;

   /**
    * Proměná obsahuje text galerie
    * @var array
    */
   private $galeryText = null;

   /**
    * Proměná obsahuje počet fotek v galerie
    * @var integer
    */
   private $numPhotos = null;

   /**
    * Proměná obsahuje id galerie
    * @var integer
    */
   private $galeryId = 0;

   /**
    * Proměná obsahuje datum přidání galerie galerie
    * @var integer
    */
   private $galeryAdd = 0;

   /**
    * Id poslední přidané galerie, pokud byla přidána
    * @var integer
    */
   private $idLastInsertedGalery = null;

   /**
    * Metoda načte detail galerie
    *
    * @param string -- url klíč galerie
    */
   public function getGaleryDetail($idGalery) {
      //		if($this->galeryDetail == null){
      $sqlSelectGaleries = $this->getDb()->select()->from(array('gals' => $this->getModule()->getDbTable()),
         array(self::COLUMN_GALERY_LABEL => "IFNULL(gals.".self::COLUMN_GALERY_LABEL.'_'
            .Locale::getLang().", gals.".self::COLUMN_GALERY_LABEL.'_'.Locale::getDefaultLang().")",
            self::COLUMN_GALERY_TEXT => "IFNULL(gals.".self::COLUMN_GALERY_TEXT.'_'.Locale::getLang()
            .", gals.".self::COLUMN_GALERY_TEXT.'_'.Locale::getDefaultLang().")",
            self::COLUMN_GALERY_ID, self::COLUMN_GALERY_TIME_ADD,self::COLUMN_GALERY_TIME_CHANGE))
      ->where('gals.'.self::COLUMN_GALERY_ID.' = \''.$idGalery.'\'');

      return $this->getDb()->fetchAssoc($sqlSelectGaleries, true);
   }


   /**
    * Metoda načte detail galerie se všemi jazykovými mutacemi
    *
    * @param int -- id galerie
    */
   public function getGaleryDetailAllLangs($idGalery) {
      $sqlSelectGaleries = $this->getDb()->select()->from($this->getModule()->getDbTable())
      ->where(self::COLUMN_GALERY_ID.' = \''.$idGalery.'\'');

      $galeryDetail = $this->getDb()->fetchAssoc($sqlSelectGaleries, true);

      //      echo '<pre>';
      //      print_r($galeryDetail);
      //      echo '</pre>';

      $galeryDetail = $this->parseDbValuesToArray($galeryDetail, array(self::COLUMN_GALERY_LABEL,
            self::COLUMN_GALERY_TEXT));

      $this->galeryLabel = $galeryDetail[self::COLUMN_GALERY_LABEL];
      $this->galeryText = $galeryDetail[self::COLUMN_GALERY_TEXT];
      $this->galeryId = $galeryDetail[self::COLUMN_GALERY_ID];
      $this->galeryAdd = $galeryDetail[self::COLUMN_GALERY_TIME_ADD];


      return $galeryDetail;

   }

   public function getLabelsLangs() {
      return $this->galeryLabel;
   }

   public function getTextsLangs() {
      return $this->galeryText;
   }

   public function getDateAdd() {
      return $this->galeryAdd;
   }

   public function getId() {
      return $this->galeryId;
   }


   /**
    * Metoda uloží upravenou galerii do db
    *
    * @param array -- pole s popisky galerie
    * @param array -- pole s texty galerie
    * @param int -- id galerie
    * @param int -- (option) timestamp editace
    */
   public function saveEditGalery($galeryLabels, $galeryTexts, $idGalery, $time = 0) {
      $galeryArr = $this->createValuesArray(self::COLUMN_GALERY_LABEL, $galeryLabels,
         self::COLUMN_GALERY_TEXT, $galeryTexts,
         self::COLUMN_GALERY_TIME_CHANGE, time(),
         self::COLUMN_GALERY_TIME_ADD, $time);

      $sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
      ->set($galeryArr)
      ->where(self::COLUMN_GALERY_ID." = '".$idGalery."'");

      // vložení do db
      if($this->getDb()->query($sqlInsert)){
         return true;
      }
      return false;
   }

   /**
    * Metoda vymaže galerie z db
    *
    * @param integer -- id galerie
    */
   public function deleteGalery($id) {
      //		Končný výmaz z db
      $sqlDelete = $this->getDb()->delete()->from($this->getModule()->getDbTable())
      ->where(self::COLUMN_GALERY_ID.' = '.$id);

      return $this->getDb()->query($sqlDelete);

   }

   /**
    * Metoda vrací počet fotek v galerii
    *
    * @param integer -- id galerie
    * @return integer -- počet fotek
    */
   public function getNumPhotos($idGalery) {
      //		if($this->numPhotos == null){
      //	  		$sqlCount = $this->getDb()->select()->from($this->getModule()->getDbTable(), array("count"=>"COUNT(*)"))
      //												->where(self::COLUMN_GALERY_ID." = '".$idGalery."'");
      //
      //
      ////			Zjištění počtu záznamů
      //			$count = $this->getDb()->fetchObject($sqlCount);
      //			$this->numPhotos = $count->count;
      //		}
      //
      		return 0;
   }

   /**
    * Metoda vrací id vybrané galerie
    *
    * @return integer -- id galerie
    */
   public function getIdGalery() {
      //		return $this->idGalery;
   }

   /**
    * Metoda uloží novou galerii
    *
    * @param array -- pole s názvem galerie
    * @param array -- pole s popisem galerie
    * @param integer -- časové razítko kdy byla galerie vytvořena
    * @param integer -- id uživatele, který galerii přidal
    *
    * @return bool/int -- false nebo id vytvořené galerie
    */
   public function saveNewGalery($galeryLabel, $galeryText, $time = null, $idUser = 0) {
      if($time == null) $time=time();

      $galeryArr = $this->createValuesArray(self::COLUMN_GALERY_LABEL, $galeryLabel,
         self::COLUMN_GALERY_TEXT, $galeryText,
         self::COLUMN_GALERY_ID_ITEM, $this->getModule()->getId(),
         self::COLUMN_GALERY_TIME_ADD, $time);

      $sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable())
      ->colums(array_keys($galeryArr))
      ->values(array_values($galeryArr));
      //      //		Vložení do db
      if($this->getDb()->query($sqlInsert)){
         return $this->getDb()->getLastInsertedId();
      } else {
         return false;
      }

   }
}

?>