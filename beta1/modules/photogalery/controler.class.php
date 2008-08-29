<?php
/**
 * Kontroler pro obsluhu fotogalerie
 * 
 * @version 0.0.1
 * @author Jakub Matas <jakubmatas@gmail.com>
 * @copyright 2008
 * @package module photogalery for VVE v3.0.1
 * 
 * Last number CoreError: 15
 */

class PhotogaleryController extends Controller {
	/**
	 * Konstanta obsahuje, jestli galerie používá sekce nebo ne
	 * @var boolean
	 */
	const GALERY_WITH_SECTIONS = false;
	
	/**
	 * Názvy routes pro zobrazování sekcí
	 * @var string
	 */
	const ROUTE_SHOW_SECTION = 'section';
	
	/**
	 * Názvy sloupců v databázi pro tabulku s galeriemi
	 * @var string
	 */
	const COLUM_GALERY_LABEL_LANG_PREFIX 	= 'label_';
	const COLUM_GALERY_TEXT_LANG_PREFIX 	= 'text_';
	const COLUM_GALERY_URLKEY 				= 'urlkey';
	const COLUM_GALERY_TIME 				= 'time';
	const COLUM_GALERY_ID_USER 				= 'id_user';
	const COLUM_GALERY_ID 					= 'id_galery';
	const COLUM_GALERY_ID_SECTION 			= 'id_section';

	/**
	 * Názvy sloupců v databázi pro tabulku s fotkami
	 * @var string
	 */
	const COLUM_PHOTOS_ID 					= 'id_photo';
	const COLUM_PHOTOS_ID_USER 				= 'id_user';
	const COLUM_PHOTOS_ID_GALERY 			= 'id_galery';
	const COLUM_PHOTOS_LABEL_LANG_PREFIX 	= 'label_';
	const COLUM_PHOTOS_TEXT_LANG_PREFIX 	= 'text_';
	const COLUM_PHOTOS_TIME 				= 'time';
	const COLUM_PHOTOS_FILE 				= 'file';

	/**
	 * Názvy sloupců v databázi pro tabulku se sekcemi
	 * @var string
	 */
	const COLUM_SECTION_ID 					= 'id_section';
	const COLUM_SECTION_ID_ITEM 			= 'id_item';
	const COLUM_SECTION_ID_USER 			= 'id_user';
	const COLUM_SECTION_LABEL_LANG_PREFIX 	= 'label_';
	const COLUM_SECTION_URLKEY 				= 'urlkey';
	const COLUM_SECTION_TIME 				= 'time';
	
	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
	const COLUM_USER_NAME = 'username';
	
	
	/**
	 * Speciální imaginární sloupce
	 * @var string
	 */
	const COLUM_GALERY_LABEL_IMAG 			= 'galerylabel';
	const COLUM_GALERY_TEXT_IMAG 			= 'galerytext';
	const COLUM_GALERY_LANG_IMAG 			= 'lang';
	const COLUM_GALERY_URLKEY_IMAG 			= 'galeryurlkey';
	const COLUM_GALERY_SHOW_LINK 			= 'galeryshowlink';
	const COLUM_GALERY_EDIT_LINK_IMAG 		= 'editlink';
	
	const COLUM_PHOTOS_TEXT_IMAG 			= 'phototext';
	const COLUM_PHOTOS_LABEL_IMAG 			= 'photolabel';
	const COLUM_PHOTOS_SHOW_LINK 			= 'photoshowlink';
	const COLUM_PHOTOS_EDIT_LINK_IMAG 		= 'editlink';
	const COLUM_PHOTOS_FILE_PREFIX_IMAG 	= 'file_';
	const COLUM_PHOTOS_LABEL_PREFIX_IMAG 	= 'label_';
	
	const COLUM_SECTION_LABEL_IMAG 			= 'sectionlabel';
	const COLUM_SECTION_URLKEY_IMAG 		= 'sectionurlkey';
	const COLUM_SECTION_SHOW_LINK 			= 'sectionshowlink';
	const COLUM_SECTION_EDIT_LINK_IMAG 		= 'editlink';
	
	
	/**
	 * Názvy formůlářových prvků
	 * @var string
	 */
	const FORM_SECTION_PREFIX = 'section_';
	const FORM_SECTION_NAME_PREFIX = 'name_';
	const FORM_SECTION_ID = 'id';
	
	const FORM_GALERY_PREFIX = 'galery_';
	const FORM_GALERY_LABEL_PREFIX = 'label_';
	const FORM_GALERY_TEXT_PREFIX = 'text_';
	const FORM_GALERY_ID = 'id';
	
	const FORM_PHOTO_PREFIX = 'photo_';
	const FORM_PHOTO_LABEL_PREFIX = 'label_';
	const FORM_PHOTO_TEXT_PREFIX = 'text_';
	const FORM_PHOTO_FILE = 'file';
	const FORM_PHOTO_ID = 'id';
	
	const FORM_BUTTON_SEND = 'send';
	const FORM_BUTTON_EDIT = 'edit';
	const FORM_BUTTON_DELETE = 'delete';
	
	/**
	 * Velikosti obrázků
	 * @var integer
	 */
	const IMAGE_SMALL_WIDTH = 160;
	const IMAGE_SMALL_HEIGHT = 110;
	const IMAGE_MEDIUM_WIDTH = 600;
	const IMAGE_MEDIUM_HEIGHT = 450;
	const IMAGE_WIDTH = 900;
	const IMAGE_HEIGHT = 675;
	
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
	 * Konstanta s názvem odkládacího adresáře
	 * @var string
	 */
	const IMAGES_TEMP_DIR = 'temp/';
	
	/**
	 * Konstanta s názvem session pro link back
	 * @var string
	 */
	const LINK_BACK_SESSION = 'photogalery_link_back';
	
	/**
	 * Název pramateru pro scrolování fotek
	 * @var string
	 */
	const PHOTOS_SCROLL_URL_PARAM = 'photo';
	
	/**
	 * Proměná obsahuje klíč vybrané sekce
	 * @var string
	 */
	private $selectedSectionKey = null;
	
	/**
	 * Kontroler pro zobrazení novinek
	 */
	public function mainController() {
	
//		Kontrola práv
		$this->checkReadableRights();
		
//		if(isset($_POST[self::FORM_SECTION_PREFIX.self::FORM_BUTTON_DELETE])){
//			$this->deleteNews();
//		}
		
		$this->createModel("galeryList");
//		print_r($this->getModel());

		
//		Scrolovátka
		$scroll = $this->eplugin()->scroll();
		
		$tableUsers = $this->getSysConfig()->getOptionValue(Auth::CONFIG_USERS_TABLE_NAME, Config::SECTION_DB_TABLES);
		
		
//		Generování dotazu pro výběr fotek
		$sqlPhotoColums = array();
		$this->getModel()->countOfPhotos = $countOfTopPhotos = $this->getModule()->getSelectParam("photosingalerylist");
		for($i=0; $i < $countOfTopPhotos; $i++){
			$sqlSelectPhotosFile = $this->getDb()->select()->from(array('photo' =>$this->getModule()->getDbTable()), self::COLUM_PHOTOS_FILE) 
											 	 ->where('gal.'.self::COLUM_GALERY_ID.' = photo.'.self::COLUM_PHOTOS_ID_GALERY)
											 	 ->limit($i, 1);
			$sqlPhotoColums[self::COLUM_PHOTOS_FILE_PREFIX_IMAG.$i] = '('.(string)$sqlSelectPhotosFile.')';								 	 
			$sqlSelectPhotosLabel = $this->getDb()->select()->from(array('photo' =>$this->getModule()->getDbTable()), 
						array(self::COLUM_PHOTOS_LABEL_IMAG => "IFNULL(".self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getLang().",
						".self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getDefaultLang().")")) 
											 	 ->where('gal.'.self::COLUM_GALERY_ID.' = photo.'.self::COLUM_PHOTOS_ID_GALERY)
											 	 ->limit($i, 1);
			$sqlPhotoColums[self::COLUM_PHOTOS_LABEL_PREFIX_IMAG.$i] = '('.(string)$sqlSelectPhotosLabel.')';								 	 
		}
		
//		echo "<pre>tady";
//		print_r($sqlPhotoColums);
//		echo "</pre>";
		
//	SQL dotaz

		$sqlGaleryColums = array(self::COLUM_GALERY_ID_USER, self::COLUM_GALERY_TIME, self::COLUM_GALERY_URLKEY_IMAG => self::COLUM_GALERY_URLKEY,
								 self::COLUM_GALERY_LABEL_IMAG => "IFNULL(gal.".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getLang().", gal.".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
								 self::COLUM_GALERY_TEXT_IMAG => "IFNULL(gal.".self::COLUM_GALERY_TEXT_LANG_PREFIX.Locale::getLang().", gal.".self::COLUM_GALERY_TEXT_LANG_PREFIX.Locale::getDefaultLang().")");
	  	
		$sqlColums = array_merge($sqlPhotoColums, $sqlGaleryColums);						 
								 
		$sqlSelect = $this->getDb()->select()->from(array('gal' => $this->getModule()->getDbTable(2)), $sqlColums)
	  										 ->order('gal.'.self::COLUM_GALERY_TIME, 'desc')
	  										 ->join(array('sec' => $this->getModule()->getDbTable(3)), 'sec.'.self::COLUM_SECTION_ID.' = gal.'.self::COLUM_GALERY_ID_SECTION, null,
	  										 array(self::COLUM_SECTION_LABEL_IMAG => "IFNULL(sec.".self::COLUM_SECTION_LABEL_LANG_PREFIX.Locale::getLang().", sec.".self::COLUM_SECTION_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
	  										 self::COLUM_SECTION_URLKEY_IMAG => self::COLUM_SECTION_URLKEY))
	  										 ->where('sec.'.self::COLUM_SECTION_ID_ITEM.' = '.$this->getModule()->getId());
		
	  										 
//	  	Výpočet scrolovátek									 
	  	$scroll->setCountRecordsOnPage($this->getModule()->getRecordsOnPage());
		
	  	$sqlCount = $this->getDb()->select()->from(array('gal' => $this->getModule()->getDbTable(2)), array("count"=>"COUNT(*)"))
											->join(array('sec' => $this->getModule()->getDbTable(3)), 'sec.'.self::COLUM_SECTION_ID.' = gal.'.self::COLUM_GALERY_ID_SECTION, null, null)
											->where('sec.'.self::COLUM_SECTION_ID_ITEM.' = '.$this->getModule()->getId());
		

//	  	Pokud je vybrána sekce je omezen výběr pouze na tuto sekci
		if($this->selectedSectionKey != null){
			$sqlSelect->where('sec.'.self::COLUM_SECTION_URLKEY." = '$this->selectedSectionKey'");
			$sqlCount->where('sec.'.self::COLUM_SECTION_URLKEY." = '$this->selectedSectionKey'");
		}

//		Zjištění počtu záznamů									
		//		echo $sqlCount;
		$count = $this->getDb()->fetchAssoc($sqlCount, true);
		$scroll->setCountAllRecords($count["count"]);				
	  	
	  	
//	  	$scroll->setCountAllRecords($this->getDb()->count($this->getModule()->getDbTable()));									 

//		načtení je potřebných záznamů
	  	$sqlSelect->limit($scroll->getStartRecord(), $scroll->getCountRecords());
	  	
	  	
//	  	echo $sqlSelect;									 
	  	$this->getModel()->allGaleryArray=$this->getDb()->fetchAssoc($sqlSelect);
		
		foreach ($this->getModel()->allGaleryArray as $key => $galery) {
			$this->getModel()->allGaleryArray[$key][self::COLUM_GALERY_SHOW_LINK]=$this->getLink()->article($galery[self::COLUM_GALERY_URLKEY_IMAG]);
			$this->getModel()->allGaleryArray[$key][self::COLUM_SECTION_SHOW_LINK]=$this->getLink()->article(self::ROUTE_SHOW_SECTION.Routes::ROUTE_SEPARATOR.$galery[self::COLUM_SECTION_URLKEY_IMAG])->params();
//			if($news[self::COLUM_NEWS_ID_USER] == $this->getRights()->getAuth()->getUserId() OR $this->getRights()->isControll()){ 
//				$this->getModel()->allNewsArray[$key][self::COLUM_NEWS_EDITABLE] = true;
//				$this->getModel()->allNewsArray[$key][self::COLUM_NEWS_EDIT_LINK] = $this->getLink()->article($news[self::COLUM_NEWS_URLKEY])->action($this->getAction()->actionEdit());
//			} else {
//				$this->getModel()->allNewsArray[$key][self::COLUM_NEWS_EDITABLE] = false;
//			}
//			
//					
		}
		
		
		//Doplnění pole s novinkami do modelu
		$this->getModel()->scroll = $scroll;
		
//		link pro přidání novinky
		$this->getModel()->linkToAddPhotos = $this->getLink()->action($this->getAction()->actionAddphotos());
		$this->getModel()->linkToAddGalery = $this->getLink()->action($this->getAction()->actionAddgalery());
		$this->getModel()->linkToAddSection = $this->getLink()->action($this->getAction()->actionAddsection());
		
		//Adresář s obrázky
		$this->getModel()->dirToImages = $this->getModule()->getDir()->getDataDir();
		$this->getModel()->dirToSmallImages = $this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR;
		
//		Vynulování session s odkazem zpět
		$session = new Sessions();
		$session->remove(self::LINK_BACK_SESSION);
		
	}

	/**
	 * Kontroler pro zobrazení sekcí
	 */
	public function sectionShowController() {
//		Mazání sekce
		if(isset($_POST[self::FORM_SECTION_PREFIX.self::FORM_BUTTON_DELETE])){
			$idSection = (int)htmlspecialchars($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_ID], ENT_QUOTES);
			
			if(!is_numeric($idSection)){
				new CoreException(_('Sekci se nepodařilo smazat, bylo zadáno nekorektní id'),1);
			} else {
				
				if($this->deleteGaleries($idSection, true)){
					$this->infoMsg()->addMessage(_('Sekce byla smazána'));
						
					$this->getLink()->params()->article()->reload();
				} else {
					new CoreException(_('Sekci se nepodařilo smazat. Zřejmně nemáte dostatečná oprávnění pro mazání'),2);
				}
			}
		}
		
		$this->selectedSectionKey = $this->getArticle()->getArticle();
		
//		Spuštění kontroleru s listem galeríí
		$this->mainController();

//		Načttení názvu zvolené sekce
		$sqlSelectSection = $this->getDb()->select()->from($this->getModule()->getDbTable(3), array(self::COLUM_SECTION_LABEL_IMAG => "IFNULL(".self::COLUM_SECTION_LABEL_LANG_PREFIX.Locale::getLang().",
						".self::COLUM_SECTION_LABEL_LANG_PREFIX.Locale::getDefaultLang().")", self::COLUM_SECTION_ID))
						->where(self::COLUM_SECTION_URLKEY." = '".$this->getArticle()."'");
		
		$this->getModel()->sectionName = $this->getDb()->fetchObject($sqlSelectSection);
		$this->getModel()->sectionId = $this->getModel()->sectionName->{self::COLUM_SECTION_ID};
		$this->getModel()->sectionName = $this->getModel()->sectionName->{self::COLUM_SECTION_LABEL_IMAG};
		
		
		
		$this->getModel()->linkToBack = $this->getLink()->article()->action()->params();

		if($this->getRights()->isWritable()){
			$this->getModel()->linkToEdit = $this->getLink()->action($this->getAction()->actionEditsection())->params();
		}
		
//		Odkaz zpět se zobrazené galerie
		$session = new Sessions();
		$session->add(self::LINK_BACK_SESSION, $this->getLink());
		
		$this->getModel()->inSection = true;
		
	}
	
	/**
	 * Kontrole pro přidání sekce galerií
	 */
	public function addsectionController() {
		$this->checkWritebleRights();
		
		$this->createModel('sectionDetail');
		
//		Podle počtu jazyků inicializujeme pole pro přidání novinky
		foreach (Locale::getAppLangs() as $lang) {
			$this->getModel()->sectionArray[$lang] = null;
		}
		$obligatoryLang = Locale::getDefaultLang();
		
		
		if(isset($_POST[self::FORM_SECTION_PREFIX.self::FORM_BUTTON_SEND])){
			if($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.Locale::getDefaultLang()] == null){
				$this->errMsg()->addMessage(_('Nebyl zadán povinný název sekce'));
				
				foreach (Locale::getAppLangs() as $lang) {
					$this->getModel()->sectionArray[$lang] = htmlspecialchars($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.$lang], ENT_QUOTES);
				}
			} else {
				$sqlInseret = $this->saveNewSection();

				//				Vložení do db
				if($this->getDb()->query($sqlInsert)){
					$this->infoMsg()->addMessage(_('Sekce byla uložena'));
					$this->getLink()->article()->action()->reload();
				} else {
					new CoreException(_('Sekci se nepodařilo uložit, chyba při ukládání do db'), 3);
				}
			}
		}
		$this->getModel()->linkToBack = $this->getLink()->action();
	}
	
	/**
	 * Kontroler pro editaci sekce
	 */
	public function sectionEditsectionController() {
		$this->createModel('sectionDetail');
		
//		pokud byla sekce odeslána
		if(isset($_POST[self::FORM_SECTION_PREFIX.self::FORM_BUTTON_SEND])){
			if($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.Locale::getDefaultLang()] == null){
				$this->errMsg()->addMessage(_('Nebyly zadány všechny povinné údaje'));
			} else {
//				Pole pro vložení
				$updateArray = array();
				foreach (Locale::getAppLangs() as $lang) {
					$_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.$lang] != null ? $label = htmlspecialchars($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.$lang], ENT_QUOTES) 
																				   : $label = null;
					
					$updateArray[self::COLUM_SECTION_LABEL_LANG_PREFIX.$lang] = $label;
				}
				
//				Vložení do db
				$sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable(3))
													 ->set($updateArray)
													 ->where(self::COLUM_SECTION_URLKEY." = '".$this->getArticle()->getArticle()."'");
				
				if($this->getDb()->query($sqlInsert)){
					$this->infoMsg()->addMessage(_("Sekce byla upravena"));
					$this->getLink()->action()->reload();
				} else {
					new CoreException(_("Nepodařilo se upravit sekci v db"),4);
				}
				
				
			}
			
			
		}
		
		$sqlSelectSection = $this->getDb()->select()->from($this->getModule()->getDbTable(3))
						->where(self::COLUM_SECTION_URLKEY." = '".$this->getArticle()."'");

		$section = $this->getDb()->fetchObject($sqlSelectSection);				
						
//		Podle počtu jazyků inicializujeme pole pro přidání novinky
		foreach (Locale::getAppLangs() as $lang) {
			$this->getModel()->sectionArray[$lang] = $section->{self::COLUM_SECTION_LABEL_LANG_PREFIX.$lang};
		}				
	
//		$this->getModel()->idSection = $section->{self::COLUM_SECTION_ID};
		$this->getModel()->defaultName = $section->{self::COLUM_SECTION_LABEL_LANG_PREFIX.Locale::getDefaultLang()};

		$this->getModel()->linkToBack = $this->getLink()->action();
		
	}
		
	/**
	 * Metoda ukládá sekci do db
	 * 
	 * @return Db -- objekt sql dotazu pro vložení sekce
	 */
	private function saveNewSection() {
		$newSectionName =  htmlspecialchars($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.Locale::getDefaultLang()], ENT_QUOTES);

		//				načtení všech klíču sekcí z db
		$sqlSectionSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(3), self::COLUM_SECTION_URLKEY);

		$sFunction = new SpecialFunctions();
		$newUrlkKey = $sFunction->createDatabaseKey($newSectionName, $this->getDb()->fetchAssoc($sqlSectionSelect));

		unset($sFunction);

		//Vygenerování sloupců do kterých se bude zapisovat
		$columsArrayNames = array();
		$columsArrayValues = array();
		array_push($columsArrayNames, self::COLUM_SECTION_URLKEY);
		array_push($columsArrayValues, $newUrlkKey);
		array_push($columsArrayNames, self::COLUM_SECTION_ID_ITEM);
		array_push($columsArrayValues, $this->getModule()->getId());
		array_push($columsArrayNames, self::COLUM_SECTION_ID_USER);
		array_push($columsArrayValues, $this->getRights()->getAuth()->getUserId());
		array_push($columsArrayNames, self::COLUM_SECTION_TIME);
		array_push($columsArrayValues, time());
		foreach (Locale::getAppLangs() as $lang) {
			array_push($columsArrayNames, self::COLUM_SECTION_LABEL_LANG_PREFIX.$lang);
			array_push($columsArrayValues, htmlspecialchars($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.$lang], ENT_QUOTES));
		}

		return $sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable(3))
										  ->colums($columsArrayNames)
										  ->values($columsArrayValues);


	}
	
	/**
	 * Metoda uloží novou galerii
	 * 
	 * @param integer -- id sekce 
	 * @return db -- objekt pro vložení do databáze
	 */
	private function saveNewGalery($idSection) {
		//				Uložení galerie do db
				$newGaleryName =  htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.Locale::getDefaultLang()], ENT_QUOTES);

				//				načtení všech klíču sekcí z db
				$sqlGalerySelect = $this->getDb()->select()->from($this->getModule()->getDbTable(2), self::COLUM_GALERY_URLKEY);

				$sFunction = new SpecialFunctions();
				$newUrlkKey = $sFunction->createDatabaseKey($newGaleryName, $this->getDb()->fetchAssoc($sqlGalerySelect));

				unset($sFunction);

				//Vygenerování sloupců do kterých se bude zapisovat
				$columsArrayNames = array();
				$columsArrayValues = array();
				array_push($columsArrayNames, self::COLUM_GALERY_URLKEY);
				array_push($columsArrayValues, $newUrlkKey);
				array_push($columsArrayNames, self::COLUM_GALERY_ID_SECTION);
				array_push($columsArrayValues, $idSection);
				array_push($columsArrayNames, self::COLUM_GALERY_ID_USER);
				array_push($columsArrayValues, $this->getRights()->getAuth()->getUserId());
				array_push($columsArrayNames, self::COLUM_GALERY_TIME);
				array_push($columsArrayValues, time());
				foreach (Locale::getAppLangs() as $lang) {
					array_push($columsArrayNames, self::COLUM_GALERY_LABEL_LANG_PREFIX.$lang);
					array_push($columsArrayValues, htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.$lang], ENT_QUOTES));
					array_push($columsArrayNames, self::COLUM_GALERY_TEXT_LANG_PREFIX.$lang);
					array_push($columsArrayValues, htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_TEXT_PREFIX.$lang], ENT_QUOTES));
				}

				return $sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable(2))
							->colums($columsArrayNames)
							->values($columsArrayValues);;
	}
		
	/**
	 * Kontroler pro přidání galerie
	 */
	public function addgaleryController() {
		$this->checkWritebleRights();
		
		$this->createModel('addGalery');


//		Podle počtu jazyků inicializujeme pole pro přidání novinky
		foreach (Locale::getAppLangs() as $lang) {
			$this->getModel()->newSectionArray[$lang] = null;
			$this->getModel()->newGaleryArray[$lang] = array();			
		}

		if(isset($_POST[self::FORM_GALERY_PREFIX.self::FORM_BUTTON_SEND])){
			if($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.Locale::getDefaultLang()] == null){
				$this->errMsg()->addMessage(_('Nebyl zadán povinný název galerie'));
				
				foreach (Locale::getAppLangs() as $lang) {
					$this->getModel()->newGaleryArray[$lang][self::COLUM_GALERY_LABEL_IMAG] = htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.$lang], ENT_QUOTES);
					$this->getModel()->newGaleryArray[$lang][self::COLUM_GALERY_TEXT_IMAG] = htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_TEXT_PREFIX.$lang], ENT_QUOTES);
					$this->getModel()->newSectionArray[$lang] = htmlspecialchars($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.$lang], ENT_QUOTES);
				}
			} else {
				$sectionId = null;
				
//				Je zadána nová sekce
				if($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.Locale::getDefaultLang()] != null){
					$sqlInsert = $this->saveNewSection();

				//				Vložení do db
					if($this->getDb()->query($sqlInsert)){
						$sectionId = $this->getDb()->getLastInsertedId();
					} else {
						new CoreException(_('Sekci se nepodařilo uložit, chyba při ukládání do db'), 5);
					}
				} else {
					$sectionId = (int)htmlspecialchars($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_ID], ENT_QUOTES);
				}
				
				$sqlGaleryInsert = $this->saveNewGalery($sectionId);
				
							
				//				Vložení do db
				if($this->getDb()->query($sqlGaleryInsert) AND $sectionId != null){
					$this->infoMsg()->addMessage(_('Galerie byla uložena'));
					$this->getLink()->article()->action()->reload();
				} else {
					new CoreException(_('Galerii nebo novou sekci se nepodařilo uložit, chyba při ukládání do db'), 6);
				}			
			}
			
		}
		
		//		načtení sekcí
		$sqlSelectSection = $this->getDb()->select()->from($this->getModule()->getDbTable(3), 
						array(self::COLUM_SECTION_LABEL_IMAG => "IFNULL(".self::COLUM_SECTION_LABEL_LANG_PREFIX.Locale::getLang().",
						".self::COLUM_SECTION_LABEL_LANG_PREFIX.Locale::getDefaultLang().")", self::COLUM_SECTION_ID))
						->where(self::COLUM_SECTION_ID_ITEM.' = '.$this->getModule()->getId());
													
		$this->getModel()->sectionArray = $this->getDb()->fetchAssoc($sqlSelectSection);
		
		$this->getModel()->linkToBack = $this->getLink()->action();
	}
	
	/**
	 * Kontroler pro přidání fotek
	 */
	public function addphotosController() {
		$this->checkWritebleRights();
		
		$this->createModel('addPhoto');
		
//		Podle počtu jazyků inicializujeme pole pro přidání novinky
		foreach (Locale::getAppLangs() as $lang) {
			$this->getModel()->newSectionArray[$lang] = null;
			$this->getModel()->newGaleryArray[$lang] = array();			
			$this->getModel()->photoArray[$lang] = array();			
		}
		
//		Uložení nových fotek
		if(isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_BUTTON_SEND])){
			$noErrors = true;
			
//			Kontrola jestli byly zadány všechny potřebné údaje při vytváření sekce nabo galerii
			if($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.Locale::getDefaultLang()] != null AND
			   $_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.Locale::getDefaultLang()] == null){
			   $this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje pro vytvoření sekce a galerie'));
			   $noErrors=false;	
			}
			
			
			$saveToDb = false;
			
			$uploadFile = new UploadFiles($this->errMsg());
			
			$uploadFile->upload(self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_FILE);
			
			if($uploadFile->isUploaded() AND $noErrors){
				//				Pole názvů sloupců a hodnot obrázků
				$insertDbColums = array();
				$insertDbValues = array();

				//					Příprava sloupců s hodnotami
				foreach (Locale::getAppLangs() as $lang) {
					array_push($insertDbColums, self::COLUM_PHOTOS_LABEL_LANG_PREFIX.$lang);
					array_push($insertDbColums, self::COLUM_PHOTOS_TEXT_LANG_PREFIX.$lang);
				}
				array_push($insertDbColums, self::COLUM_PHOTOS_FILE);

//				Pokud byl vlože zip soubor
				if($uploadFile->isZipFile()){
					$files = new Files();

//					Rozbalení archívu
					$files->unZip($uploadFile->getTmpName(), $this->getModule()->getDir()->getDataDir().self::IMAGES_TEMP_DIR, false);
				
//============================= ZMĚNA DOBY PROVÁDĚNÍ SCRIPTU ===========================
//600 sekund
					set_time_limit(900);
//======================================================================================
					
//					Otevření adresáře s rozbaleným archívem
					$handle=opendir($this->getModule()->getDir()->getDataDir().self::IMAGES_TEMP_DIR);
					while (false!==($file = readdir($handle))) {
    					if ($file == "." OR $file == ".." OR is_dir($file)) continue;
    					
    					$imageFromZip = new Images($this->errMsg(), $this->getModule()->getDir()->getDataDir().self::IMAGES_TEMP_DIR.$file, false);

//    					Pokud je obrázek tak vytvoříme miniatury a uložíme
    					if($imageFromZip->isImage()){
    						$insertDbValue = array();
    						
    						$imageFromZip->setImageName($file);
    						$imageFromZip->saveImage($this->getModule()->getDir()->getDataDir(), self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
    						$imageFromZip->saveImage($this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR, self::IMAGE_SMALL_WIDTH, self::IMAGE_SMALL_HEIGHT);
    						$imageFromZip->saveImage($this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_THUMBNAILS_DIR, self::IMAGE_MEDIUM_WIDTH, self::IMAGE_MEDIUM_HEIGHT);
    					
//    						Vytvoření pole pro zápis do db
    						foreach (Locale::getAppLangs() as $lang) {
    							
    							$_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_LABEL_PREFIX.$lang] != null ?
									array_push($insertDbValue, htmlspecialchars($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_LABEL_PREFIX.$lang], ENT_QUOTES)):
									array_push($insertDbValue, $imageFromZip->getNewImageName());
//    							array_push($insertDbColums, self::COLUM_PHOTOS_TEXT_LANG_PREFIX.Locale::getDefaultLang());
    							$_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_TEXT_PREFIX.$lang] != null ?
    								array_push($insertDbValue, htmlspecialchars($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_TEXT_PREFIX.$lang], ENT_QUOTES)):
    								array_push($insertDbValue, null);
    						}
    						array_push($insertDbValue, $imageFromZip->getNewImageName());
    						//Kvůli možnosti vkládání více řádků (obrázků)
							array_push($insertDbValues, $insertDbValue);
							
    					}

					}
//					smažeme dočasný adresář i s bordelem
					$files->rmDir($this->getModule()->getDir()->getDataDir().self::IMAGES_TEMP_DIR);
					
					$saveToDb = true;
				} 
//				Jakýkoliv jiný soubor
				else {
					$image = new Images($this->errMsg(), $uploadFile->getTmpName());
					if($image->isImage()){
						$insertDbValue = array();
						$image->setImageName($uploadFile->getOriginalName());
						$image->saveImage($this->getModule()->getDir()->getDataDir(), self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
						$image->saveImage($this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR, self::IMAGE_SMALL_WIDTH, self::IMAGE_SMALL_HEIGHT);
						$image->saveImage($this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_THUMBNAILS_DIR, self::IMAGE_MEDIUM_WIDTH, self::IMAGE_MEDIUM_HEIGHT);
						$imageName = $image->getNewImageName();
							
						foreach (Locale::getAppLangs() as $lang) {
//							array_push($insertDbColums, self::COLUM_PHOTOS_LABEL_LANG_PREFIX.$lang);
							$_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_LABEL_PREFIX.$lang] != null ?
							array_push($insertDbValue, htmlspecialchars($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_LABEL_PREFIX.$lang], ENT_QUOTES)):
							array_push($insertDbValue, $imageName);

//							array_push($insertDbColums, self::COLUM_PHOTOS_TEXT_LANG_PREFIX.$lang);
							$_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_TEXT_PREFIX.$lang] != null ?
							array_push($insertDbValue, htmlspecialchars($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_TEXT_PREFIX.$lang], ENT_QUOTES)):
							array_push($insertDbValue, null);
						}
						
						array_push($insertDbValue, $imageName);
							
						$saveToDb = true;
							
						//Kvůli možnosti vkládání více řádků (obrázků)
						array_push($insertDbValues, $insertDbValue);
					}
				}
			}
			
//			echo "<pre>";
//			print_r($insertDbValues);
//			print_r($insertDbColums);
//			echo "</pre>";
			
			if($saveToDb AND $noErrors){
				$sectionId = null;
//				Pokud je vytvářena nová sekce
				if($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_NAME_PREFIX.Locale::getDefaultLang()] != null){
					$sqlInsSection = $this->saveNewSection();

				//				Vložení do db
					if($this->getDb()->query($sqlInsSection)){
						$sectionId = $this->getDb()->getLastInsertedId();
					} else {
						new CoreException(_('Sekci se nepodařilo uložit, chyba při ukládání do db'), 7);
					}
					
				} else {
					$sectionId = htmlspecialchars($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_ID], ENT_QUOTES);
				}

//				Je ukládána nová galerie
				$idInsertedGalery = null;
				if($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.Locale::getDefaultLang()] != null){

					$sqlGaleryInsert = $this->saveNewGalery($sectionId);
						
					//				Vložení do db
					if($this->getDb()->query($sqlGaleryInsert) AND $sectionId != null){
						$idInsertedGalery = $this->getDb()->getLastInsertedId();	
					} else {
						new CoreException(_('Galerii nebo novou sekci se nepodařilo uložit, chyba při ukládání do db'), 8);
					}
				} else {
					$idInsertedGalery = $_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_ID];
				}
				
								
//				Vložení fotek do db
				if($idInsertedGalery != null){
					
//					Doplnění ostatních sloupců s id uživatele a id galerie
					array_push($insertDbColums, self::COLUM_PHOTOS_ID_GALERY);
					array_push($insertDbColums, self::COLUM_PHOTOS_ID_USER);
					array_push($insertDbColums, self::COLUM_PHOTOS_TIME);		
					
					$sqlInsertPhoto = $this->getDb()->insert()->into($this->getModule()->getDbTable())
													->colums($insertDbColums);
													
//					doplnění hodnot
					foreach ($insertDbValues as $value) {
//						doplnění hodnot ostatních sloupců
						array_push($value, $idInsertedGalery);
						array_push($value, $this->getRights()->getAuth()->getUserId());
						array_push($value, time());	
						
						$sqlInsertPhoto->values($value);
					}
													
					
					if($this->getDb()->query($sqlInsertPhoto)){
						$this->infoMsg()->addMessage(_('Fotky byla/byly uloženy'));
						$this->getLink()->action()->reload();	
					} else {
						new CoreException(_('Fotku se nepodařilo uložit, chyba při ukládání do db'), 9);
					}
				}
			}
		}
		
		
		//		načtení sekcí
		$sqlSelectSection = $this->getDb()->select()->from($this->getModule()->getDbTable(3), 
						array(self::COLUM_SECTION_LABEL_IMAG => "IFNULL(".self::COLUM_SECTION_LABEL_LANG_PREFIX.Locale::getLang().",
						".self::COLUM_SECTION_LABEL_LANG_PREFIX.Locale::getDefaultLang().")", self::COLUM_SECTION_ID))
						->where(self::COLUM_SECTION_ID_ITEM.' = '.$this->getModule()->getId());
													
		$this->getModel()->sectionArray = $this->getDb()->fetchAssoc($sqlSelectSection);
		
		//		načtení galerií
		$sqlSelectSection = $this->getDb()->select()->from(array('gal' =>$this->getModule()->getDbTable(2)), 
						array(self::COLUM_GALERY_LABEL_IMAG => "IFNULL(gal.".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getLang().",
						gal.".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getDefaultLang().")", self::COLUM_GALERY_ID, self::COLUM_GALERY_URLKEY))
						->join(array('sec' => $this->getModule()->getDbTable(3)), 'sec.'.self::COLUM_SECTION_ID.' = gal.'.self::COLUM_GALERY_ID_SECTION, null, 
						array(self::COLUM_SECTION_LABEL_IMAG => "IFNULL(sec.".self::COLUM_SECTION_LABEL_LANG_PREFIX.Locale::getLang().",
						sec.".self::COLUM_SECTION_LABEL_LANG_PREFIX.Locale::getDefaultLang().")"))
						->where('sec.'.self::COLUM_SECTION_ID_ITEM.' = '.$this->getModule()->getId())
						->order('sec.'.self::COLUM_SECTION_ID);
													
		$this->getModel()->galeryArray = $this->getDb()->fetchAssoc($sqlSelectSection);
		
		if($this->getArticle()->isArticle() AND !$this->getArticle()->withRoute()){
			reset($this->getModel()->galeryArray);
	
			while($this->getModel()->idSelectedGalery == null){
				$currentGalery = current($this->getModel()->galeryArray);
				if($currentGalery[self::COLUM_GALERY_URLKEY] == $this->getArticle()->getArticle()){
					$this->getModel()->idSelectedGalery = $currentGalery[self::COLUM_GALERY_ID];
				}
				next($this->getModel()->galeryArray);
			}
			reset($this->getModel()->galeryArray);
		}
		
		//				Doplnění nastavených hodnot
		if(isset($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_ID])){
			$this->getModel()->idSelectedGalery = $_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_ID];
		}
		if(isset($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_ID])){
			$this->getModel()->idSelectedSection = $_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_ID];
		}
		
		$this->getModel()->linkToBack = $this->getLink()->action();
	}
	
	/**
	 * Metoda pro přidávání galerií v sekci
	 */
	public function sectionAddgaleryController() {
		$this->checkWritebleRights();
		
		$this->addgaleryController();
		
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(3))
											 ->where(self::COLUM_SECTION_URLKEY." = '".$this->getArticle()->getArticle()."'");	
		
		$this->getModel()->idSelectedSection = $this->getDb()->fetchObject($sqlSelect);
		$this->getModel()->idSelectedSection = $this->getModel()->idSelectedSection->{self::COLUM_SECTION_ID};
	}

	/**
	 * Metoda pro přidávání galerií v sekci
	 */
	public function sectionAddphotosController() {
		$this->checkWritebleRights();
		
		$this->addphotosController();
		
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(3))
											 ->where(self::COLUM_SECTION_URLKEY." = '".$this->getArticle()->getArticle()."'");	
		
		$this->getModel()->idSelectedSection = $this->getDb()->fetchObject($sqlSelect);
		$this->getModel()->idSelectedSection = $this->getModel()->idSelectedSection->{self::COLUM_SECTION_ID};
	}
	
	/**
	 * Kontroler pro zobrazení fotogalerie
	 */
	public function showController()
	{
//		Mazání zvolené fotky
		if(isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_BUTTON_DELETE])){
			if(!is_numeric($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID])){
				new CoreException(_('Fotku se nepodařilo smazat, bylo zadáno nekorektní id'),10);
			} else {
				if($this->deletePhotos((int)$_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID])){
					$this->infoMsg()->addMessage(_('Fotka byla smazána'));
					
					$this->getLink()->params()->reload();
				} else {
					new CoreException(_('Fotku se nepodařilo smazat. Zřejmně nemáte dostatečná oprávnění pro mazání'),11);
				}
			}
			
		}
		
//		Mazání zvolené galerie
		if(isset($_POST[self::FORM_GALERY_PREFIX.self::FORM_BUTTON_DELETE])){
			$idGalery = (int)htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_ID], ENT_QUOTES);
			
			if($this->deleteGaleries($idGalery)){
				$this->infoMsg()->addMessage(_('Galeire byla smazána'));
				$this->getLink()->action()->article()->params()->reload();
			} else {
				new CoreException(_('Galerii se nepodařilo smazat'), 12);
			}
		}
		
		
//		Pokud se neprohlíží jednotlivé fotky
		if(!isset($_GET[self::PHOTOS_SCROLL_URL_PARAM])){
			$this->createModel('galeryDetail');


			$sqlSelect = $this->getDb()->select()->from(array('photos'=>$this->getModule()->getDbTable(1)), array(self::COLUM_PHOTOS_LABEL_IMAG => "IFNULL(photos.".self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getLang().",
						photos.".self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getDefaultLang().")", self::COLUM_PHOTOS_TEXT_IMAG => "IFNULL(photos.".self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getLang().",
						photos.".self::COLUM_PHOTOS_TEXT_LANG_PREFIX.Locale::getDefaultLang().")", self::COLUM_PHOTOS_ID, self::COLUM_PHOTOS_FILE))
						->join(array('gal'=>$this->getModule()->getDbTable(2)), 'photos.'.self::COLUM_PHOTOS_ID_GALERY.' = gal.'.self::COLUM_GALERY_ID, null, null)
						->where('gal.'.self::COLUM_GALERY_URLKEY." = '".$this->getArticle()."'");
			$this->getModel()->galeryArray = $this->getDb()->fetchAssoc($sqlSelect);


			//		Načtení názvu galerie a textu ke galerii
			$sqlSelectGalery = $this->getDb()->select()->from($this->getModule()->getDbTable(2), array(self::COLUM_GALERY_LABEL_IMAG => "IFNULL(".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getLang().",
						".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getDefaultLang().")", self::COLUM_GALERY_TEXT_IMAG => "IFNULL(".self::COLUM_GALERY_TEXT_LANG_PREFIX.Locale::getLang().",
						".self::COLUM_GALERY_TEXT_LANG_PREFIX.Locale::getDefaultLang().")", self::COLUM_GALERY_ID))
						->where(self::COLUM_GALERY_URLKEY." = '".$this->getArticle()."'");
				
			$galeryInfo = $this->getDb()->fetchObject($sqlSelectGalery);

			$this->getModel()->galeryInfo[self::COLUM_GALERY_LABEL_IMAG] = $galeryInfo->{self::COLUM_GALERY_LABEL_IMAG};
			$this->getModel()->galeryInfo[self::COLUM_GALERY_TEXT_IMAG] = $galeryInfo->{self::COLUM_GALERY_TEXT_IMAG};
			$this->getModel()->galeryInfo[self::COLUM_GALERY_ID] = $galeryInfo->{self::COLUM_GALERY_ID};

			//Odkaz zpět
			$session = new Sessions();
			if(!$session->isEmpty(self::LINK_BACK_SESSION)){
				$this->getModel()->linkToBack = $session->get(self::LINK_BACK_SESSION);
			} else {
				$this->getModel()->linkToBack = $this->getLink()->action()->article();
			}

			//		Vytvoření linku pro zobrazení fotky
//			číslo fotky
			$photoNumber = 1;
			foreach ($this->getModel()->galeryArray as $photoKey => $photo) {
//				dsad
				$this->getModel()->galeryArray[$photoKey][self::COLUM_PHOTOS_SHOW_LINK]=$this->getLink()->param(self::PHOTOS_SCROLL_URL_PARAM, $photoNumber);
//				$this->getModel()->galeryArray[$photoKey][self::COLUM_PHOTOS_EDIT_LINK_IMAG]=$this->getLink()->action($this->getAction()->actionEditphoto())->param(self::PHOTOS_SCROLL_URL_PARAM, $photoNumber);
				$this->getModel()->galeryArray[$photoKey][self::COLUM_PHOTOS_EDIT_LINK_IMAG]=$this->getLink()->action($this->getAction()->actionEditphoto());
				$photoNumber++;
			}
			
//			adresář s obrázky
			$this->getModel()->dirToSmallImages = $this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR;

//			odkazy
			if($this->getRights()->isWritable()){
				$this->getModel()->linkToAddPhotos = $this->getLink()->action($this->getAction()->actionAddphotos());
				$this->getModel()->linkToEditGalery = $this->getLink()->action($this->getAction()->actionEditgalery());
			}
			
		}
//		Zobrazuje se fotografie
		else {
//			Změna viewru na zobrazení fotky
			$this->changeActionView('showPhoto');
			
			
			$this->createModel('photoDetail');
			
			
			$scroll = $this->eplugin()->scroll();
			$scroll->setUrlParam(self::PHOTOS_SCROLL_URL_PARAM);
			
			//	  	Výpočet scrolovátek									 
	  		$scroll->setCountRecordsOnPage(1);
		
	  		$sqlCount = $this->getDb()->select()->from(array('photo' => $this->getModule()->getDbTable()), array("count"=>"COUNT(*)"))
												->join(array('galery' => $this->getModule()->getDbTable(2)), 'photo.'.self::COLUM_PHOTOS_ID_GALERY.' = galery.'.self::COLUM_GALERY_ID, null, null)
												->where('galery.'.self::COLUM_GALERY_URLKEY." = '".$this->getArticle()."'");
		

//			Zjištění počtu záznamů									
//			echo $sqlCount;
			$count = $this->getDb()->fetchAssoc($sqlCount, true);
			$scroll->setCountAllRecords($count["count"]);

			$this->getModel()->scroll = $scroll;
			
//			načtení fotky z db
			$sqlSelect = $this->getDb()->select()->from(array('photo' => $this->getModule()->getDbTable()),
				array(self::COLUM_PHOTOS_LABEL_IMAG => "IFNULL(photo.".self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getLang().", photo.".self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getDefaultLang().")",
					  self::COLUM_PHOTOS_TEXT_IMAG => "IFNULL(photo.".self::COLUM_PHOTOS_TEXT_LANG_PREFIX.Locale::getLang().", photo.".self::COLUM_PHOTOS_TEXT_LANG_PREFIX.Locale::getDefaultLang().")",
					  self::COLUM_PHOTOS_ID, self::COLUM_PHOTOS_FILE))
				->join(array('galery' => $this->getModule()->getDbTable(2)), 'photo.'.self::COLUM_PHOTOS_ID_GALERY.' = galery.'.self::COLUM_GALERY_ID, null, array(self::COLUM_GALERY_LABEL_IMAG => "IFNULL(galery.".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getLang().", galery.".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getDefaultLang().")"))
				->where('galery.'.self::COLUM_GALERY_URLKEY." = '".$this->getArticle()."'")
				->limit($scroll->getStartRecord(), $scroll->getCountRecords());
												 
			$this->getModel()->photoDetailArray = $this->getDb()->fetchAssoc($sqlSelect, true);									 
			
//			Link pro editaci
			$this->getModel()->photoDetailArray[self::COLUM_PHOTOS_EDIT_LINK_IMAG]=$this->getLink()->action($this->getAction()->actionEditphoto());
			
//			Adresář s obrázky
			$this->getModel()->dirToMediumImages = $this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_THUMBNAILS_DIR;
			
			$this->getModel()->linkToBack = $this->getLink()->withoutParam(self::PHOTOS_SCROLL_URL_PARAM);
		}
		
		//Adresář s obrázky
		$this->getModel()->dirToImages = $this->getModule()->getDir()->getDataDir();

//		echo "<pre>";
//		print_r($this->getModel()->photoDetailArray);
//		echo "</pre>";
	}

	/**
	 * Kontroler pro editaci galerie
	 */
	public function editgaleryController() {
		$this->checkWritebleRights();
		
		$this->createModel('editGalery');

		$idGalery = (int)htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_ID], ENT_QUOTES);
		$this->getModel()->idGalery = $idGalery;
		
//		Podle počtu jazyků inicializujeme pole pro přidání novinky
		foreach (Locale::getAppLangs() as $lang) {
			$this->getModel()->galeryArray[$lang] = array();			
		}

		if(isset($_POST[self::FORM_GALERY_PREFIX.self::FORM_BUTTON_SEND])){
			if($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.Locale::getDefaultLang()] == null){
				$this->errMsg()->addMessage(_('Nebyl zadán povinný název galerie'));
				
				foreach (Locale::getAppLangs() as $lang) {
					$this->getModel()->galeryArray[$lang][self::COLUM_GALERY_LABEL_IMAG] = htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.$lang], ENT_QUOTES);
					$this->getModel()->galeryArray[$lang][self::COLUM_GALERY_TEXT_IMAG] = htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_TEXT_PREFIX.$lang], ENT_QUOTES);
				}
			} else {
//				Pole pro úpravu
				$updateArray = array();
				foreach (Locale::getAppLangs() as $lang) {
					$_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.$lang] != null ? $label = htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.$lang], ENT_QUOTES) 
																				   : $label = null;
					$_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_TEXT_PREFIX.$lang] != null ? $text = htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_TEXT_PREFIX.$lang], ENT_QUOTES) 
																				   : $text = null;
					
					$updateArray[self::COLUM_GALERY_LABEL_LANG_PREFIX.$lang] = $label;
					$updateArray[self::COLUM_PHOTOS_TEXT_LANG_PREFIX.$lang] = $text;
				}
				
//				Vložení do db
				$sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable(2))
													 ->set($updateArray)
													 ->where(self::COLUM_GALERY_URLKEY." = '".$this->getArticle()->getArticle()."'")
													 ->where(self::COLUM_GALERY_ID." = ".$idGalery);
				
				//				Vložení do db
				if($this->getDb()->query($sqlInsert)){
					$this->infoMsg()->addMessage(_('Galerie byla upravena'));
					$this->getLink()->action()->reload();
				} else {
					new CoreException(_('Galerii se nepodařilo uložit, chyba při ukládání do db'), 13);
				}			
			}
			
		}
	
//		načtení galerie
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(2), array(self::COLUM_GALERY_LABEL_IMAG => "IFNULL(".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getLang().", ".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getDefaultLang().")", '*'))
					->where(self::COLUM_GALERY_URLKEY." = '".$this->getArticle()->getArticle()."'")
					->where(self::COLUM_GALERY_ID.' = '.$idGalery);
			
		$galery = $this->getDb()->fetchObject($sqlSelect);
		$this->getModel()->idGalery = $galery->{self::COLUM_GALERY_ID};
		$this->getModel()->nameGalery = $galery->{self::COLUM_GALERY_LABEL_IMAG};
		
		
		if(empty($this->getModel()->galeryArray[Locale::getDefaultLang()])){
			foreach (Locale::getAppLangs() as $lang) {
				$this->getModel()->galeryArray[$lang][self::COLUM_GALERY_LABEL_IMAG] = $galery->{self::COLUM_GALERY_LABEL_LANG_PREFIX.$lang};
				$this->getModel()->galeryArray[$lang][self::COLUM_GALERY_TEXT_IMAG] = $galery->{self::COLUM_GALERY_TEXT_LANG_PREFIX.$lang};
			}
		}
		
		$this->getModel()->linkToBack = $this->getLink()->action();
	}
		
	/**
	 * Kontroler pro úpravu fotky
	 */
	public function editphotoController() {
		$this->checkWritebleRights();
		
		$this->createModel('photoEditDetail');
			
		$idPhoto = (int)$_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID];

		if(isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_BUTTON_SEND])){
			if($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_LABEL_PREFIX.Locale::getDefaultLang()] == null){
				$this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje'));
				
				foreach (Locale::getAppLangs() as $lang) {
					$this->getModel()->photoArray[$lang][self::COLUM_PHOTOS_LABEL_IMAG] = htmlspecialchars($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_LABEL_PREFIX.$lang], ENT_QUOTES);
					$this->getModel()->photoArray[$lang][self::COLUM_PHOTOS_TEXT_LANG_PREFIX] = htmlspecialchars($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_TEXT_PREFIX .$lang], ENT_QUOTES);
			   	}
			} else {
//				Pole pro vložení
				$updateArray = array();
				foreach (Locale::getAppLangs() as $lang) {
					$_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_LABEL_PREFIX.$lang] != null ? $label = htmlspecialchars($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_LABEL_PREFIX.$lang], ENT_QUOTES) 
																				   : $label = null;
					$_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_TEXT_PREFIX.$lang] != null ? $text = htmlspecialchars($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_TEXT_PREFIX.$lang], ENT_QUOTES) 
																				   : $text = null;
					
					$updateArray[self::COLUM_PHOTOS_LABEL_LANG_PREFIX.$lang] = $label;
					$updateArray[self::COLUM_PHOTOS_TEXT_LANG_PREFIX.$lang] = $text;
				}
				
//				Vložení do db
				$sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
													 ->set($updateArray)
													 ->where(self::COLUM_PHOTOS_ID." = ".$idPhoto);
				
													 
				if($this->getDb()->query($sqlInsert)){
					$this->infoMsg()->addMessage(_("Fotka byla upravena"));
					
					$this->getLink()->action()->reload();
				} else {
					new CoreException(_("Nepodařilo se upravit fotku v db"),14);
				}
				
				
			}
			
		}
		
//			načtení fotky z d
		$sqlSelect = $this->getDb()->select()->from(array('photo' => $this->getModule()->getDbTable()))
				->join(array('galery' => $this->getModule()->getDbTable(2)), 'photo.'.self::COLUM_PHOTOS_ID_GALERY.' = galery.'.self::COLUM_GALERY_ID, null, array(self::COLUM_GALERY_LABEL_IMAG => "IFNULL(galery.".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getLang().", galery.".self::COLUM_GALERY_LABEL_LANG_PREFIX.Locale::getDefaultLang().")"))
				->where('galery.'.self::COLUM_GALERY_URLKEY." = '".$this->getArticle()."'")
				->where('photo.'.self::COLUM_PHOTOS_ID." = ".$idPhoto);
//				->limit($scroll->getStartRecord(), $scroll->getCountRecords());

		$photoDetail = $this->getDb()->fetchObject($sqlSelect);

		if($photoDetail == null){
			new CoreException(_('Fotku se nepodařilo načíst, zřejmně šaptně zadané id'), 15);
		} else {

			if(empty($this->getModel()->photoArray)){
				//		Podle počtu jazyků inicializujeme pole pro editaci fotky
				foreach (Locale::getAppLangs() as $lang) {
					$this->getModel()->photoArray[$lang][self::COLUM_PHOTOS_LABEL_IMAG] = $photoDetail->{self::COLUM_PHOTOS_LABEL_LANG_PREFIX.$lang};
					$this->getModel()->photoArray[$lang][self::COLUM_PHOTOS_TEXT_IMAG] = $photoDetail->{self::COLUM_PHOTOS_TEXT_LANG_PREFIX.$lang};
				}
			}
			//			Doplnění ostatních záznamů
			$this->getModel()->idPhoto = $photoDetail->{self::COLUM_PHOTOS_ID};
			$this->getModel()->photoFile = $photoDetail->{self::COLUM_PHOTOS_FILE};
			
			if($photoDetail->{self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getLang()} != null){
				$this->getModel()->photoName = $photoDetail->{self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getLang()};
			} else {
				$this->getModel()->photoName = $photoDetail->{self::COLUM_PHOTOS_LABEL_LANG_PREFIX.Locale::getDefaultLang()};
			}
		}
			
		//			Adresář s obrázky
		$this->getModel()->dirToSmallImages = $this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_THUMBNAILS_DIR;

		$session = new Sessions();
		if($session->isEmpty(self::LINK_BACK_SESSION)){
			$this->getModel()->linkToBack = $this->getLink()->withoutParam(self::PHOTOS_SCROLL_URL_PARAM);
		} else {
			$this->getModel()->linkToBack = $session->get(self::LINK_BACK_SESSION);
		}
	}
	
	/**
	 * Metoda pro mazání fotky/fotek
	 * 
	 * @param integer -- id fotky nebo id galerie
	 * @param boolean -- jestli se má mazat podle id galerie
	 * 
	 * @return boolean -- true pokud byla fotka/fotky smazány
	 */
	private function deletePhotos($id, $allPhotosFromGalery = false ) {
		if(!$allPhotosFromGalery){

			//		načtení fotky z d
			$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
			->where(self::COLUM_PHOTOS_ID.' = '.$id);

			$photoDetail = $this->getDb()->fetchObject($sqlSelect);

			//		Vymazání souborů fotek
			$files = new Files();
			$allFilesDeleted = true;

			if(!$files->deleteFile($this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR, $photoDetail->{self::COLUM_PHOTOS_FILE})){
				$allFilesDeleted = false;
			}
			if(!$files->deleteFile($this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_THUMBNAILS_DIR, $photoDetail->{self::COLUM_PHOTOS_FILE})){
				$allFilesDeleted = false;
			}
			if(!$files->deleteFile($this->getModule()->getDir()->getDataDir(), $photoDetail->{self::COLUM_PHOTOS_FILE})){
				$allFilesDeleted = false;
			}
			//		if(!$files->deleteFile($this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR, $photoDetail->{self::COLUM_PHOTOS_FILE})
			//		   OR !$files->deleteFile($this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_THUMBNAILS_DIR, $photoDetail->{self::COLUM_PHOTOS_FILE})
			//		   OR !$files->deleteFile($this->getModule()->getDir()->getDataDir(), $photoDetail->{self::COLUM_PHOTOS_FILE})){
			//			$allFilesDeleted = false;
			//
			//		}

			//		Končný výmaz z db
			$sqlDelete = $this->getDb()->delete()->from($this->getModule()->getDbTable())
			->where(self::COLUM_PHOTOS_ID.' = '.$id);

			if($allFilesDeleted AND $this->getDb()->query($sqlDelete)){
				return true;
			} else {
				return false;
			}
		}
//		Maže se celá galerie
		else {
		//		načtení fotek z db
			$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
										->where(self::COLUM_PHOTOS_ID_GALERY.' = '.$id);

			$photosDetail = $this->getDb()->fetchObjectArray($sqlSelect);

			
			
			//		Vymazání souborů fotek
			$files = new Files();
			foreach ($photosDetail as $photoDetail) {
				$allFilesDeleted = true;

				if(!$files->deleteFile($this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR, $photoDetail->{self::COLUM_PHOTOS_FILE})){
					$allFilesDeleted = false;
				}
				if(!$files->deleteFile($this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_THUMBNAILS_DIR, $photoDetail->{self::COLUM_PHOTOS_FILE})){
					$allFilesDeleted = false;
				}
				if(!$files->deleteFile($this->getModule()->getDir()->getDataDir(), $photoDetail->{self::COLUM_PHOTOS_FILE})){
					$allFilesDeleted = false;
				}

				//		Končný výmaz z db
				if(!$allFilesDeleted){
					break;
				}
			}
			
			$sqlDelete = $this->getDb()->delete()->from($this->getModule()->getDbTable())
								->where(self::COLUM_PHOTOS_ID_GALERY.' = '.$id);

			if($allFilesDeleted AND $this->getDb()->query($sqlDelete)){
				return true;
			} else {
				return false;
			}
		}
		
	}
	
	/**
	 * Metoda vymeže zadanou galerii/galerie
	 * 
	 * @param integer -- id galerie nebo id sekce
	 * @param boolean -- jestli se maže sekce nebo galerie
	 * 
	 * @return boolean -- true pokud se galerie podařilo smazat
	 */
	private function deleteGaleries($id, $deleteAllFromSection = false) {
		if(!$deleteAllFromSection){
			if($this->deletePhotos($id,true)){
//				Samotné vymazání galerie
				$sqlDelete = $this->getDb()->delete()->from($this->getModule()->getDbTable(2))
													 ->where(self::COLUM_GALERY_ID.' = '.$id);

				if($this->getDb()->query($sqlDelete)){
					return true;
				} else {
					return false;
				}
			} else {
				return false;				
			}
		}
//		jsou mazány všechny galerie ze sekce
		else {
//			Načtení všech galerií v sekci
			$sqlSectionSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(2))
														->where(self::COLUM_GALERY_ID_SECTION.' = '.$id);
														
			$galeries = $this->getDb()->fetchObjectArray($sqlSectionSelect);

			$deletedAll = true;
			foreach ($galeries as $galery) {
				if(!$this->deletePhotos($galery->{self::COLUM_GALERY_ID},true)){
					$deletedAll = false;
					break;
				}
			}

//			Smazání galerií
			$sqlDeleteGaleries = $this->getDb()->delete()->from($this->getModule()->getDbTable(2))
														 ->where(self::COLUM_GALERY_ID_SECTION.' = '.$id);
														 
			if($deletedAll AND $this->getDb()->query($sqlDeleteGaleries)){
				$sqlDeleteSection = $this->getDb()->delete()->from($this->getModule()->getDbTable(3))
															->where(self::COLUM_SECTION_ID.' = '.$id);
															
				if($this->getDb()->query($sqlDeleteGaleries)){
					return true;	
				} else {
					return false;
				}
			} else {
				return false;
			}
			
		}
	}
	
}

?>