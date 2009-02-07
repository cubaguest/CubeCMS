<?php
class PhotogaleryPanel extends Panel {
		/**
	 * Názvy sloupců v databázi pro tabulku s galeriemi
	 * @var string
	 */
	const COLUM_GALERY_LABEL_LANG_PREFIX 	= 'label_';
//	const COLUM_GALERY_TEXT_LANG_PREFIX 	= 'text_';
	const COLUM_GALERY_URLKEY 				= 'urlkey';
	const COLUM_GALERY_TIME 				= 'time';
//	const COLUM_GALERY_ID_USER 				= 'id_user';
	const COLUM_GALERY_ID 					= 'id_galery';
	const COLUM_GALERY_ID_SECTION 			= 'id_section';

	/**
	 * Názvy sloupců v databázi pro tabulku s fotkami
	 * @var string
	 */
//	const COLUM_PHOTOS_ID 					= 'id_photo';
//	const COLUM_PHOTOS_ID_USER 				= 'id_user';
	const COLUM_PHOTOS_ID_GALERY 			= 'id_galery';
	const COLUM_PHOTOS_LABEL_LANG_PREFIX 	= 'label_';
//	const COLUM_PHOTOS_TEXT_LANG_PREFIX 	= 'text_';
	const COLUM_PHOTOS_TIME 				= 'time';
	const COLUM_PHOTOS_FILE 				= 'file';

	/**
	 * Názvy sloupců v databázi pro tabulku se sekcemi
	 * @var string
	 */
	const COLUM_SECTION_ID 					= 'id_section';
	const COLUM_SECTION_ID_ITEM 			= 'id_item';
//	const COLUM_SECTION_ID_USER 			= 'id_user';
//	const COLUM_SECTION_LABEL_LANG_PREFIX 	= 'label_';
	const COLUM_SECTION_URLKEY 				= 'urlkey';
//	const COLUM_SECTION_TIME 				= 'time';
	
	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
//	const COLUM_USER_NAME = 'username';
	
	
	/**
	 * Speciální imaginární sloupce
	 * @var string
	 */
	const COLUM_GALERY_LABEL_IMAG 			= 'galerylabel';
//	const COLUM_GALERY_TEXT_IMAG 			= 'galerytext';
//	const COLUM_GALERY_LANG_IMAG 			= 'lang';
//	const COLUM_GALERY_URLKEY_IMAG 			= 'galeryurlkey';
	const COLUM_GALERY_SHOW_LINK 			= 'galeryshowlink';
//	const COLUM_GALERY_EDIT_LINK_IMAG 		= 'editlink';
//	
//	const COLUM_PHOTOS_TEXT_IMAG 			= 'phototext';
	const COLUM_PHOTOS_LABEL_IMAG 			= 'photolabel';
	const COLUM_PHOTOS_SHOW_LINK 			= 'photoshowlink';
//	const COLUM_PHOTOS_EDIT_LINK_IMAG 		= 'editlink';
//	const COLUM_PHOTOS_FILE_PREFIX_IMAG 	= 'file_';
	const COLUM_PHOTOS_LABEL_PREFIX_IMAG 	= 'label_';
	const COLUM_PHOTOS_COUNT_IMAG 			= 'num_photos';
//	
//	const COLUM_SECTION_LABEL_IMAG 			= 'sectionlabel';
//	const COLUM_SECTION_URLKEY_IMAG 		= 'sectionurlkey';
//	const COLUM_SECTION_SHOW_LINK 			= 'sectionshowlink';
//	const COLUM_SECTION_EDIT_LINK_IMAG 		= 'editlink';
	
	/**
	 * Počet nových galeríí v panelu
	 * @var integer
	 */
	const NUMBER_OF_NEW_GALERIES = 5;

	/**
	 * Počet náhodných fotek v panelu
	 * @var integer
	 */
	const NUMBER_OF_RANDOM_PHOTOS = 3;

		/**
	 * Konstanta obsahuje název adresáře s miniaturami
	 * @var string
	 */
	const IMAGES_THUMBNAILS_DIR = 'small/';

	/**
	 * Konstanta obsahuje název adresáře se střednímy
	 * @var string
	 */
	const IMAGES_MEDIUM_THUMBNAILS_DIR = 'medium/';
	
	/**
	 * Název pramateru pro scrolování fotek
	 * @var string
	 */
	const PHOTOS_SCROLL_URL_PARAM = 'photo';
	
	/**
	 * Pole s náhodnými fotkami
	 * @var array
	 */
	private $randomPhotosArray = array();
	
	/**
	 * Link do novinek
	 * @var string
	 */
	private $galeriesLink = null;
	
	/**
	 * Pole s posledními přidanými galeriemi
	 * @var array
	 */
	private $lastAddGaleries = array();
	
	/**
	 * Cestak malým fotkám
	 * @var string
	 */
	private $linkToPhotogalery = null;
	
	public function panelController() {
//		načtení zadaného počtu galerií
		$sqlSelectGaleries = $this->getDb()->select()->from(array('gal' => $this->getModule()->getDbTable(2)), 
			array(self::COLUM_GALERY_LABEL_IMAG => "IFNULL(gal.".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getLang().",
			gal.".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getDefaultLang().")", self::COLUM_GALERY_URLKEY, self::COLUM_GALERY_ID, self::COLUM_PHOTOS_COUNT_IMAG => 'COUNT(photo.'.self::COLUM_PHOTOS_ID_GALERY.')'))
				->join(array('sec'=>$this->getModule()->getDbTable(3)), 'gal.'.self::COLUM_GALERY_ID_SECTION.' = sec.'.self::COLUM_SECTION_ID, null, null)
				->join(array('photo'=>$this->getModule()->getDbTable()), 'photo.'.self::COLUM_PHOTOS_ID_GALERY.' = gal.'.self::COLUM_GALERY_ID, null, null)
				->group('photo.'.self::COLUM_PHOTOS_ID_GALERY)
				->order('RAND()')
//				->order('photo.'.self::COLUM_PHOTOS_TIME)
				->limit(0, self::NUMBER_OF_RANDOM_PHOTOS)
				->where('sec.'.self::COLUM_SECTION_ID_ITEM." = '".$this->getModule()->getId()."'");
										   
		$randomGaleries = $this->getDb()->fetchObjectArray($sqlSelectGaleries);

//		Adresář s miniaturami
		$smallDir = $this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR;
		
		for ($photo=0; $photo < count($randomGaleries); $photo++){
			$numPhotos = $randomGaleries[$photo]->{self::COLUM_PHOTOS_COUNT_IMAG};
						
			$selectPhoto = rand(0, $numPhotos-1);
			
//			načtení náhodné fotky ze zadané galerie
			$sqlSelecPhoto = $this->getDb()->select()->from($this->getModule()->getDbTable(), 
				array(self::COLUM_PHOTOS_LABEL_IMAG => "IFNULL(".self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getLang().",
				".self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getDefaultLang().")", self::COLUM_PHOTOS_FILE))
													 ->order(self::COLUM_PHOTOS_TIME)
													 ->limit($selectPhoto,1)
													 ->order(self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getDefaultLang())
													 ->order(self::COLUM_PHOTOS_TIME)
													 ->where(self::COLUM_PHOTOS_ID_GALERY.' = '.$randomGaleries[$photo]->{self::COLUM_GALERY_ID});

			$selectedRandPhoto = $this->getDb()->fetchObject($sqlSelecPhoto);

			$this->randomPhotosArray[$photo] = array();
			$this->randomPhotosArray[$photo][self::COLUM_PHOTOS_FILE] = $smallDir.$selectedRandPhoto->{self::COLUM_PHOTOS_FILE};
			$this->randomPhotosArray[$photo][self::COLUM_PHOTOS_SHOW_LINK] = $this->getLink()->param(self::PHOTOS_SCROLL_URL_PARAM, $selectPhoto+1)->article($randomGaleries[$photo]->{self::COLUM_GALERY_URLKEY});
			$this->randomPhotosArray[$photo][self::COLUM_PHOTOS_LABEL_IMAG] = $selectedRandPhoto->{self::COLUM_PHOTOS_LABEL_IMAG};

			$this->randomPhotosArray[$photo][self::COLUM_GALERY_SHOW_LINK] = $this->getLink()->article($randomGaleries[$photo]->{self::COLUM_GALERY_URLKEY});
			$this->randomPhotosArray[$photo][self::COLUM_GALERY_LABEL_IMAG] = $randomGaleries[$photo]->{self::COLUM_GALERY_LABEL_IMAG};



		}
		
//		Načtení posledně přidaných galerií
		$sqlSelecLastGaleries = $this->getDb()->select()->from(array('gal' =>$this->getModule()->getDbTable(2)), 
			array(self::COLUM_GALERY_LABEL_IMAG => "IFNULL(gal.".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getLang().",
			gal.".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getDefaultLang().")", self::COLUM_GALERY_URLKEY))
			->join(array('sec'=> $this->getModule()->getDbTable(3)), 'sec.'.self::COLUM_SECTION_ID.'=gal.'.self::COLUM_GALERY_ID_SECTION, null, null)
			->where('sec.'.self::COLUM_SECTION_ID_ITEM.' = '.$this->getModule()->getId())
			->limit(0, self::NUMBER_OF_NEW_GALERIES)
			->order('gal.'.self::COLUM_GALERY_TIME, 'DESC');
														
		$this->lastAddGaleries = $this->getDb()->fetchAssoc($sqlSelecLastGaleries);
		
//		Přidání odkazu na galerie
		foreach ($this->lastAddGaleries as $key => $galery){
			$this->lastAddGaleries[$key][self::COLUM_GALERY_SHOW_LINK] = $this->getLink()->article($galery[self::COLUM_GALERY_URLKEY]);
		}
		
			
			
//		Link do kategorie
		$this->linkToPhotogalery = $this->getLink();
	}
	
	public function panelView() {
		$this->template()->addTpl("panel.tpl");
		$this->template()->addCss('style.css');
		
		$this->template()->addVar("RANDOM_PHOTOS_ARRAY", $this->randomPhotosArray);
		$this->template()->addVar("GALERY_NAME", _("Galerie"));
		$this->template()->addVar("GALERY_MORE", _("více"));

		$this->template()->addVar("PHOTOGALERY_LINK", $this->linkToPhotogalery);
		
		$this->template()->addVar("NEW_PHOTOGALERIES", _('Nové galerie'));
		$this->template()->addVar("NEW_PHOTOGALERIES_ARRAY", $this->lastAddGaleries);

		
		$this->template()->addVar("PHOTOGALERY_LINK_NAME", _('Další fotogalerie'));
		
		
		
	}
	
	
}
?>