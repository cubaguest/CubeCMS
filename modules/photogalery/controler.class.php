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
	const COLUMN_GALERY_LABEL_LANG_PREFIX 	= 'label_';
//	const COLUMN_GALERY_TEXT_LANG_PREFIX 	= 'text_';
//	const COLUMN_GALERY_URLKEY 				= 'urlkey';
	const COLUMN_GALERY_TIME 				= 'time';
//	const COLUMN_GALERY_ID_USER 				= 'id_user';
	const COLUMN_GALERY_ID 					= 'id_galery';
	const COLUMN_GALERY_ID_SECTION 			= 'id_section';

	/**
	 * Názvy sloupců v databázi pro tabulku s fotkami
	 * @var string
	 */
	const COLUMN_PHOTOS_ID 					= 'id_photo';
//	const COLUMN_PHOTOS_ID_USER 				= 'id_user';
//	const COLUMN_PHOTOS_ID_GALERY 			= 'id_galery';
	const COLUMN_PHOTOS_LABEL_LANG_PREFIX 	= 'label_';
//	const COLUMN_PHOTOS_TEXT_LANG_PREFIX 	= 'text_';
//	const COLUMN_PHOTOS_TIME 				= 'time';
	const COLUMN_PHOTOS_FILE 				= 'file';

	/**
	 * Názvy sloupců v databázi pro tabulku se sekcemi
	 * @var string
	 */
	const COLUMN_SECTION_ID 					= 'id_section';
//	const COLUMN_SECTION_ID_ITEM 			= 'id_item';
//	const COLUMN_SECTION_ID_USER 			= 'id_user';
	const COLUMN_SECTION_LABEL_LANG_PREFIX 	= 'label_';
//	const COLUMN_SECTION_URLKEY 				= 'urlkey';
//	const COLUMN_SECTION_TIME 				= 'time';
	
	/**
	 * Sloupce u tabulky uživatelů
	 * @var string
	 */
//	const COLUMN_USER_NAME = 'username';
	
	
	/**
	 * Speciální imaginární sloupce
	 * @var string
	 */
	const COLUMN_GALERY_LABEL_IMAG 			= 'galerylabel';
	const COLUMN_GALERY_TEXT_IMAG 			= 'galerytext';
//	const COLUMN_GALERY_LANG_IMAG 			= 'lang';
	const COLUMN_GALERY_URLKEY_IMAG 			= 'galeryurlkey';
	const GALERY_SHOW_LINK 					= 'galeryshowlink';
//	const COLUMN_GALERY_EDIT_LINK_IMAG 		= 'editlink';
//	
//	const COLUMN_PHOTOS_TEXT_IMAG 			= 'phototext';
	const COLUMN_PHOTOS_LABEL_IMAG 			= 'photolabel';
	const COLUMN_PHOTOS_SHOW_LINK 			= 'photoshowlink';
	const COLUMN_PHOTOS_EDIT_LINK_IMAG 		= 'editlink';
//	const COLUMN_PHOTOS_FILE_PREFIX_IMAG 	= 'file_';
//	const COLUMN_PHOTOS_LABEL_PREFIX_IMAG 	= 'label_';
//	
	const COLUMN_SECTION_LABEL_IMAG 			= 'sectionlabel';
	const COLUMN_SECTION_URLKEY_IMAG 		= 'sectionurlkey';
	const SECTION_SHOW_LINK 				= 'sectionshowlink';
//	const COLUMN_SECTION_EDIT_LINK_IMAG 		= 'editlink';
	
	/**
	 * název pole s galeriemi v listu sekcí
	 * @var string
	 */
	const GALERIES_IN_SECTION_LIST_KEY_NAME = 'galeries';
	
	/**
	 * Názvy formůlářových prvků
	 * @var string
	 */
	const FORM_SECTION_PREFIX = 'section_';
	const FORM_SECTION_LABEL = 'label';
	const FORM_SECTION_LABEL_PREFIX = 'label_';
	const FORM_SECTION_ID = 'id';
	
	const FORM_GALERY_PREFIX = 'galery_';
	const FORM_GALERY_LABEL = 'label';
	const FORM_GALERY_LABEL_PREFIX = 'label_';
	const FORM_GALERY_TEXT = 'text';
	const FORM_GALERY_TEXT_PREFIX = 'text_';
	const FORM_GALERY_ID = 'id';
	const FORM_GALERY_ID_SECTION = 'section_id';
	const FORM_GALERY_DATE = 'date';
	
	const FORM_PHOTO_PREFIX = 'photo_';
	const FORM_PHOTO_LABEL = 'label';
	const FORM_PHOTO_LABEL_PREFIX = 'label_';
	const FORM_PHOTO_TEXT = 'text';
	const FORM_PHOTO_TEXT_PREFIX = 'text_';
	const FORM_PHOTO_FILE = 'file';
	const FORM_PHOTO_ID = 'id';
	const FORM_PHOTO_IS_EDIT = 'is_edit';
	const FORM_PHOTO_GALERY_ID = 'galery_id';
	
	const FORM_BUTTON_SEND = 'send';
	const FORM_BUTTON_EDIT = 'edit';
	const FORM_BUTTON_DELETE = 'delete';
	
	/**
	 * Velikosti obrázků
	 * @var integer
	 */
	const IMAGE_SMALL_WIDTH = 125;
	const IMAGE_SMALL_HEIGHT = 95;
	const IMAGE_MEDIUM_WIDTH = 390;
	const IMAGE_MEDIUM_HEIGHT = 410;
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
	 * Počet galerií v sekci při seznamu sekcí
	 * @var integer
	 */
	const NUM_OF_GALERIES_IN_SECTION_LIST = 4;
	
	/**
	 * Proměná obsahuje klíč vybrané sekce
	 * @var string
	 */
	private $selectedSectionKey = null;
	
	/**
	 * Kontroler pro zobrazení fotogalerii
	 */
	public function mainController() {
//		Kontrola práv
		$this->checkReadableRights();
//		Objekt se seznamem sekcí
		$sectionsObj = new SectionListModel();
//		Načtení sekcí
		$sections = $sectionsObj->getSectionList();
//		Objekt se seznamem galerií
		$galeryObj = new GaleriesListModel();
//		procházení sekcí a načítání galerií
		foreach ($sections as $sectionKey => $section) {
			$galeries = $galeryObj->getGaleryList($section[self::COLUMN_SECTION_ID], self::NUM_OF_GALERIES_IN_SECTION_LIST);
			if(!empty($galeries)){
//				Odkazy pro zobrazení galerie
				foreach ($galeries as $galKey => $gal) {
					$galeries[$galKey][self::GALERY_SHOW_LINK] = $this->getLink()->article($gal[self::COLUMN_GALERY_URLKEY_IMAG]);
				}
				$sections[$sectionKey][self::GALERIES_IN_SECTION_LIST_KEY_NAME] = $galeries;
			} else {
				$sections[$sectionKey][self::GALERIES_IN_SECTION_LIST_KEY_NAME] = null;
			}
//			Odkaz pro zobrazení sekce
			$sections[$sectionKey][self::SECTION_SHOW_LINK]=$this->getLink()->article(self::ROUTE_SHOW_SECTION.Routes::ROUTE_SEPARATOR.$section[self::COLUMN_SECTION_URLKEY_IMAG])->params();
		}
		$this->container()->addData('sections', $sections);
//		Adresář s obrázky
		$this->container()->addData('small_images_dir', $this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR);
		if($this->getRights()->isWritable()){
//			vytvoření linků pro přidávání
			$this->container()->addLink('add_photo', $this->getLink()->action($this->getAction()->actionAddphotos()));
			$this->container()->addLink('add_galery', $this->getLink()->action($this->getAction()->actionAddgalery()));
			$this->container()->addLink('add_section', $this->getLink()->action($this->getAction()->actionAddsection()));
		}
//		Vynulování session s odkazem zpět
		$session = new Sessions();
		$session->remove(self::LINK_BACK_SESSION);
	}

	/**
	 * Kontroler pro zobrazení sekcí
	 */
	public function sectionShowController() {
		$this->checkReadableRights();
//		Mazání sekce
		if(isset($_POST[self::FORM_SECTION_PREFIX.self::FORM_BUTTON_DELETE]) AND $this->getRights()->isWritable()){
			$idSection = (int)htmlspecialchars($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_ID], ENT_QUOTES);
			if(!is_numeric($idSection)){
				new CoreException(_('Sekci se nepodařilo smazat, bylo zadáno nekorektní id'),1);
			} else {
				$galeryObj = new GaleriesListModel();
				$galeryDetailObj = new GaleryDetailModel();				
				$photoObj = new PhotoDetailModel();
				$sectionObj = new  SectionDetailModel();
//				načtení galerií
				$galeries = $galeryObj->getGaleryList($idSection);
				$allOk = true;
//				Mazání galeríí
				foreach ($galeries as $gal) {
					$photos = $galeryDetailObj->getPhotosList($gal[self::COLUMN_GALERY_ID]);
//					Vymazání fotek
					foreach ($photos as $photo) {
						if(!$this->deletePhotoFile($photo[self::COLUMN_PHOTOS_FILE])){
							new CoreException(_('Soubor fotky se nepodařilo vymazat'));
							$allOk = false;
						} else if(!$photoObj->deletePhoto($photo[self::COLUMN_PHOTOS_ID])) {
							new CoreException(_('Soubor fotky se nepodařilo vymazat'));
							$allOk = false;
						}
					}
//					Výmaz galerie
					if ($allOk AND !$galeryDetailObj->deleteGalery($gal[self::COLUMN_GALERY_ID])){
						new CoreException(_('Galerii se nepodařilo vymazat'));
						$allOk = false;
					}
				}
//				Mazání sekce
				if($allOk AND !$sectionObj->deleteSection($idSection)){
					new CoreException(_('Sekci se nepodařilo vymazat'));
				} else {
					$this->infoMsg()->addMessage(_('Sekce byla vymazána'));
					$this->getLink()->action()->article()->reload();
				}
			}
		}
//		Načtení vybrané sekce
		$sectionObj = new SectionDetailModel();
		$section = $sectionObj->getSectionByUrlkey($this->getArticle());
//		načtení galerií ze sekcí
		$galeryListObj = new GaleriesListModel();
		$galeries = $galeryListObj->getGaleryList($section[self::COLUMN_SECTION_ID]);
		foreach ($galeries as $galKey => $gal) {
			$galeries[$galKey][self::GALERY_SHOW_LINK] = $this->getLink()->article($gal[self::COLUMN_GALERY_URLKEY_IMAG]);
		}
		$this->container()->addData('section', $section);
		$this->container()->addData('section_label', $section[self::COLUMN_SECTION_LABEL_IMAG]);
		$this->container()->addData('section_id', $section[self::COLUMN_SECTION_ID]);
		$this->container()->addData('galeries', $galeries);
		//		Adresář s obrázky
		$this->container()->addData('small_images_dir', $this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR);
		if($this->getRights()->isWritable()){
//			link pro editaci
			$this->container()->addLink('edit_section', $this->getLink()->action($this->getAction()->actionEditsection())->params());
//			vytvoření linků pro přidávání
			$this->container()->addLink('add_photo', $this->getLink()->action($this->getAction()->actionAddphotos()));
			$this->container()->addLink('add_galery', $this->getLink()->action($this->getAction()->actionAddgalery()));
		}
//		Odkaz zpět se zobrazené galerie
		$this->container()->addLink('link_back', $this->getLink()->article()->action()->params());
		$session = new Sessions();
		$session->add(self::LINK_BACK_SESSION, $this->getLink());
//		
		$this->container()->addData('in_section',true);
	}
	
	/**
	 * Kontrole pro přidání sekce galerií
	 */
	public function addsectionController() {
		$this->checkWritebleRights();
//		Helpre pro práci s jazykovými poli
		$localeHelper = new LocaleCtrlHelper();
//		Odeslané pole
		$sendArray = array();
		if(isset($_POST[self::FORM_SECTION_PREFIX.self::FORM_BUTTON_SEND])){
			$sendArray = $localeHelper->postsToArray(array(self::FORM_SECTION_LABEL_PREFIX), self::FORM_SECTION_PREFIX);
			if($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_LABEL_PREFIX.Locale::getDefaultLang()] == null){
			   	$this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje'));
			} else {
				$mainLang = Locale::getDefaultLang();
				$mainLabel = $sendArray[self::FORM_SECTION_LABEL_PREFIX.$mainLang];
				$sectionDetail = new SectionDetailModel();
				$saved = $sectionDetail->saveNewSection($sendArray, $mainLabel, $this->getRights()->getAuth()->getUserId());
//				uložení
				if($saved){
					$this->infoMsg()->addMessage(_('Sekce byla uložena'));
					$this->getLink()->article()->action()->params()->reload();
				} else {
					new CoreException(_('Sekci se nepodařilo uložit, chyba při ukládání'), 1);
				}
			}
		}
		$lArray = $localeHelper->generateArray(array(self::FORM_SECTION_LABEL),$sendArray);
//		Sekce do viewru
		$this->container()->addData('section', $lArray);
//		Odkaz zpět
		$this->container()->addLink('link_back', $this->getLink()->action());
	}
	
	/**
	 * Kontroler pro editaci sekce
	 */
	public function sectionEditsectionController() {
		$this->checkWritebleRights();		
		$sectionObj = new SectionDetailModel();
		$section = $sectionObj->getSectionByUrlkeyAllLangs($this->getArticle()->getArticle());
		if(empty($section)){
			new CoreException(_('Požadovaná sekce neexistuje'), 2);
			return false;
		}
//		Zvolení názvu sekce
		if($section[self::COLUMN_SECTION_LABEL_LANG_PREFIX.Locale::getLang()] != null){
			$this->container()->addData('section_label', $section[self::COLUMN_SECTION_LABEL_LANG_PREFIX.Locale::getLang()]);
		} else {
			$this->container()->addData('section_label', $section[self::COLUMN_SECTION_LABEL_LANG_PREFIX.Locale::getDefaultLang()]);
		}
//		Helpre pro práci s jazykovými poli
		$localeHelper = new LocaleCtrlHelper();
		$sendArray = array();
//		pokud byla sekce odeslána
		if(isset($_POST[self::FORM_SECTION_PREFIX.self::FORM_BUTTON_SEND])){
			$sendArray = $localeHelper->postsToArray(array(self::FORM_SECTION_LABEL_PREFIX), self::FORM_SECTION_PREFIX);
			if($_POST[self::FORM_SECTION_PREFIX.self::FORM_SECTION_LABEL_PREFIX.Locale::getDefaultLang()] == null){
				$this->errMsg()->addMessage(_('Nebyly zadány všechny povinné údaje'));
			} else {
				$updated = $sectionObj->saveEditSection($sendArray, $this->getArticle()->getArticle());
				if($updated){
					$this->infoMsg()->addMessage(_("Sekce byla upravena"));
					$this->getLink()->action()->reload();
				} else {
					new CoreException(_("Nepodařilo se upravit sekci"),4);
				}
			}
		}
		$lArray = $localeHelper->generateArray(array(self::FORM_SECTION_LABEL), $sendArray, $section);
//		Sekce do viewru
		$this->container()->addData('section', $lArray);
//		Odkaz zpět			
		$this->container()->addLink('link_back', $this->getLink()->action());
	}
		
	/**
	 * Kontroler pro přidání galerie
	 */
	public function addgaleryController($idSelSection = null) {
		$this->checkWritebleRights();
//		Helpre pro práci s jazykovými poli
		$localeHelper = new LocaleCtrlHelper();
//		Pokud je již vybrána sekce
		if($idSelSection != null){
			$this->container()->addData('section_select', $idSelSection);
		}
//		Odeslané pole
		$sendTextsArray = array();
		if(isset($_POST[self::FORM_GALERY_PREFIX.self::FORM_BUTTON_SEND])){
			//				Vygenerování datumu
			$dateHelp = new DateTimeCtrlHelper();
			$dateStamp = $dateHelp->createStampSmartyPost(self::FORM_GALERY_PREFIX.self::FORM_GALERY_DATE);
			$this->container()->addData('date_select', $dateStamp);
			$sendTextsArray = $localeHelper->postsToArray(array(self::FORM_GALERY_LABEL_PREFIX, self::FORM_GALERY_TEXT_PREFIX), self::FORM_GALERY_PREFIX);
//			Přiřazení vybrané sekce
			$this->container()->addData('section_select', htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_ID_SECTION]));
			if($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.Locale::getDefaultLang()] == null){
			   	$this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje'));
			} else {
				//vytvoření hlavního názvu
				$mainLang = Locale::getDefaultLang();
				$mainLabel = $sendTextsArray[self::FORM_GALERY_LABEL_PREFIX.$mainLang];
				$galeryDetail = new GaleryDetailModel();
//				Vygenerování id sekce
				$idSection = (int)$_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_ID_SECTION];
				$saved = $galeryDetail->saveNewGalery($sendTextsArray, $idSection, $mainLabel, $dateStamp, $this->getRights()->getAuth()->getUserId());
//				uložení
				if($saved){
					$this->infoMsg()->addMessage(_('Galerie byla uložena'));
					$this->getLink()->article()->action()->params()->reload();
				} else {
					new CoreException(_('Galerii se nepodařilo uložit, chyba při ukládání'), 1);
				}
			}
		}
		$lArray = $localeHelper->generateArray(array(self::FORM_GALERY_LABEL, self::FORM_GALERY_TEXT),$sendTextsArray);
//		galerie do viewru
		$this->container()->addData('galery', $lArray);
//		Načtení sekcí
		$sectionsObj = new SectionListModel();
		$sections = $sectionsObj->getSectionList();
		$this->container()->addData('sections', $sections);
//		Odkaz zpět
		$this->container()->addLink('link_back', $this->getLink()->action());
	}
	
	/**
	 * Kontroler pro přidání fotek
	 */
	public function addphotosController() {
		$this->checkWritebleRights();
//		Nastavení zvolené galerie jsme li v galerii
		if($this->getArticle()->isArticle() AND !$this->getArticle()->isRoute()){
			$galObj = new GaleryDetailModel();
			$gal = $galObj->getGaleryDetail($this->getArticle()->getArticle());
			$idSelGalery = $gal[self::COLUMN_GALERY_ID];
			unset($galObj);
			$this->container()->addData('galery_sel', $idSelGalery);
		}
//		Helper pro práci s jazykovými poli
		$localeHelper = new LocaleCtrlHelper();
//		Odeslané pole
		$sendPhotoArray = array();
		$sendGaleryArray = array();
//		Objetk progressBaru
		$progressBar = $this->eplugin()->progressbar();
		$this->container()->addEplugin('progressbar', $progressBar);
		$progressBar->setMessage(_('Inicializace'));
//		Odesílá se ----------------------------------------------------
//		Uložení nových fotek
		if(isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_BUTTON_SEND])){
			$noErrors = true;
//			Pomocí helperu extrahování názvů a popisů
			$sendPhotoArray = $localeHelper->postsToArray(array(self::FORM_PHOTO_LABEL, self::FORM_PHOTO_TEXT), self::FORM_PHOTO_PREFIX);
			$sendGaleryArray = $localeHelper->postsToArray(array(self::FORM_GALERY_LABEL, self::FORM_GALERY_TEXT), self::FORM_GALERY_PREFIX);
//			Načtení id galerie, kde se ukládá fotka
			$idGalery = (int)htmlspecialchars($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_GALERY_ID]);
			$idSection = (int)htmlspecialchars($_POST[self::FORM_PHOTO_PREFIX.self::FORM_GALERY_ID_SECTION]);
//			Projití pole o galerii jestli byla přidána
			$sendNewGalery = false;
			foreach ($sendGaleryArray as $gal) {
				if($gal != null){
					$sendNewGalery = true;
					break;
				}
			}
//			Ukládá se nová galerie
			$reloadWithArticle = true;
			if($sendNewGalery){
//				Kontrola jestli byly zadány všechny potřebné údaje při vytváření galerii
				if($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.Locale::getDefaultLang()] == null){
					$this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje pro vytvoření galerie'));
					$progressBar->close();
					$noErrors=false;
				} else {
					// vytvoření hlavního názvu
					$mainLabel = $sendGaleryArray[self::FORM_GALERY_LABEL_PREFIX.Locale::getDefaultLang()];
					//				Vygenerování datumu
					$dateHelp = new DateTimeCtrlHelper();
					$dateStamp = $dateHelp->createStampSmartyPost(self::FORM_GALERY_PREFIX.self::FORM_GALERY_DATE);
					$this->container()->addData('date_select', $dateStamp);
					unset($dateHelp);
					$galeryObj = new GaleryDetailModel();
					$galerySaved = $galeryObj->saveNewGalery($sendGaleryArray, $idSection, $mainLabel, $dateStamp, $this->getRights()->getAuth()->getUserId());
					$idGalery = $galeryObj->gelLastInsertedGaleryId();
					//				uložení
					if($galerySaved){
						$this->infoMsg()->addMessage(_('Galerie byla uložena'));
						$reloadWithArticle = false;
					} else {
						$noErrors = false;
						$progressBar->close();
						new CoreException(_('Galerii se nepodařilo uložit, chyba při ukládání'), 1);
					}
				}
			}
//			Nastavení progressbaru
			$progressBar->setSteps(2);
			$progressBar->setMessage(_('Nahrání souboru'));			
			$uploadFile = new UploadFiles($this->errMsg());
			$uploadFile->upload(self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_FILE);
			if($uploadFile->isUploadError()){
				$progressBar->close();
			}
			if($uploadFile->isUploaded() AND $noErrors){
				//			Projití pole o galerii jestli byla přidána
				$sendPhotosLabel = false;
				foreach ($sendPhotoArray as $photo) {
					if($photo != null){
						$sendPhotosLabel = true;
						break;
					}
				}
//				Objekt pro práci s fotkama
				$photoObj = new PhotoDetailModel();
//				Pokud byl vlože zip soubor
				if($uploadFile->isZipFile()){
					$files = new Files();
//============================= ZMĚNA DOBY PROVÁDĚNÍ SCRIPTU ===========================
//900 sekund
					set_time_limit(3600);
//======================================================================================
//					Rozbalení archívu
					$progressBar->setMessage(_('Rozbalení archivu'));
					$files->unZip($uploadFile->getTmpName(), $this->getModule()->getDir()->getDataDir().self::IMAGES_TEMP_DIR, false);
//					Objekt pro ukládání fotek
					$photoObj = new PhotoDetailModel();
					$countFiles=0;
					$handle=opendir($this->getModule()->getDir()->getDataDir().self::IMAGES_TEMP_DIR);
					while (false!==($file = readdir($handle))) {
						if ($file == "." OR $file == ".." OR is_dir($file)) continue;
						$countFiles++;
					}
//					Nastavení progressbaru
					$progressBar->setSteps(($countFiles*4));
//					Otevření adresáře s rozbaleným archívem
					$handle=opendir($this->getModule()->getDir()->getDataDir().self::IMAGES_TEMP_DIR);
					while (false!==($file = readdir($handle))) {
    					if ($file == "." OR $file == ".." OR is_dir($file)) continue;
    					$imageFromZip = new Images($this->errMsg(), $this->getModule()->getDir()->getDataDir().self::IMAGES_TEMP_DIR.$file, false);
//    					Pokud je obrázek tak vytvoříme miniatury a uložíme
    					if($imageFromZip->isImage()){
    						$insertDbValue = array();
    						$saved = true;
    						//TODO optimalizovat, tak aby byl nejlépe if jenom jeden
    						if($saved){
    							$progressBar->setMessage(_('Vytváření velkého obrázku'));
    							$imageFromZip->setImageName($file);
    							$imageFromZip->saveImage($this->getModule()->getDir()->getDataDir(), self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
    						}
    						$imageName = $imageFromZip->getNewImageName();
    						if($saved){
    							$progressBar->setMessage(_('Vytváření malého obrázku'));
    							$imageFromZip->setCrop(true);
    							$imageFromZip->saveImage($this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR, self::IMAGE_SMALL_WIDTH, self::IMAGE_SMALL_HEIGHT);
    						}
    						if($saved){
    							$progressBar->setMessage(_('Vytváření středního obrázku'));
    							$imageFromZip->setCrop(false);
    							$imageFromZip->saveImage($this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_THUMBNAILS_DIR, self::IMAGE_MEDIUM_WIDTH, self::IMAGE_MEDIUM_HEIGHT);
    						}
//							Pokd není název tak jej doplníme z názvu souboru
    						if(!$sendPhotosLabel){
    							$sendPhotoArray[self::COLUMN_PHOTOS_LABEL_LANG_PREFIX.Locale::getDefaultLang()] = $imageName;
    						}
//    						uložení fotky
    						if($saved){
    							$progressBar->setMessage(_('Ukládání do databáze'));
    							$saved = $photoObj->saveNewPhoto($sendPhotoArray, $idGalery, $imageName);
    							sleep(1);
    						}
    					}
					}
//					smažeme dočasný adresář i s bordelem
    				$progressBar->setMessage(_('Odstranění dočasných souborů'));
					$files->rmDir($this->getModule()->getDir()->getDataDir().self::IMAGES_TEMP_DIR);
					$progressBar->close();
				} 
//				Jakýkoliv jiný soubor s obrázkem
				else {
					$progressBar->setSteps(4);
					$image = new Images($this->errMsg(), $uploadFile->getTmpName());
					if($image->isImage()){
						$saved = true;
						if($saved){
							$progressBar->setMessage(_('Vytváření velkého obrázku'));
							$image->setImageName($uploadFile->getOriginalName());
							$saved = $image->saveImage($this->getModule()->getDir()->getDataDir(), self::IMAGE_WIDTH, self::IMAGE_HEIGHT);
						}
						if($saved){
							$progressBar->setMessage(_('Vytváření malého obrázku'));
    						$image->setCrop(true);
							$saved = $image->saveImage($this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR, self::IMAGE_SMALL_WIDTH, self::IMAGE_SMALL_HEIGHT);
						}
						if($saved){
							$image->setCrop(false);
							$saved = $image->saveImage($this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_THUMBNAILS_DIR, self::IMAGE_MEDIUM_WIDTH, self::IMAGE_MEDIUM_HEIGHT);
						}
						$imageName = $image->getNewImageName();
//						Pokd není název tak jej doplníme z názvu souboru
						if(!$sendPhotosLabel){
							$progressBar->setMessage(_('Vytváření středního obrázku'));
							$sendPhotoArray[self::COLUMN_PHOTOS_LABEL_LANG_PREFIX.Locale::getDefaultLang()] = $imageName;
						}
//    					uložení fotky
						if($saved){
							$progressBar->setMessage(_('Ukládání do databáze'));
							$saved = $photoObj->saveNewPhoto($sendPhotoArray, $idGalery, $imageName);
						}
					}
				}
//				zjištění zda vše proběhlo v pořádku
				if($saved){
					$this->infoMsg()->addMessage(_('Fotografie byly uloženy'));
					$link =	$this->getLink()->action()->params();
					
//					Při přidávání galerie přnést na sezna galerií
					if(!$reloadWithArticle AND !$this->getArticle()->isRoute()){
						$link = $link->article();
					}
					$link->reload();
				} else {
					$noErrors = false;
					new CoreException(_('Fotografie se nepodařilo uložit, chyba při ukládání'), 1);
				}
			}
			unset($progressBar);
		}
//		Generování výchozího obsahu -----------------------------------
		$pArray = $localeHelper->generateArray(array(self::FORM_PHOTO_LABEL_PREFIX, self::FORM_PHOTO_TEXT_PREFIX),$sendPhotoArray);
		$gArray = $localeHelper->generateArray(array(self::FORM_PHOTO_LABEL_PREFIX, self::FORM_PHOTO_TEXT_PREFIX),$sendGaleryArray);
//		fotka do viewru
		$this->container()->addData('photo', $pArray);
//		galerie do viewru
		$this->container()->addData('galery', $gArray);
//		Načtení sekcí
		$sectionsObj = new SectionListModel();
		$sections = $sectionsObj->getSectionList();
		$this->container()->addData('sections', $sections);
		unset($sectionsObj);
//		Načtení galerií
		$galeryObj = new GaleriesListModel();
		$galeries = $galeryObj->getGaleriesListWithSections();
		$this->container()->addData('galeries', $galeries);
		unset($galeryObj);
		$this->container()->addLink('link_back', $this->getLink()->action());
	}
	
	/**
	 * Metoda pro přidávání galerií v sekci
	 */
	public function sectionAddgaleryController() {
		$this->checkWritebleRights();
		$sectionObj = new SectionDetailModel();
		$section = $sectionObj->getSectionByUrlkey($this->getArticle()->getArticle());
		$isSelSection = $section[self::COLUMN_SECTION_ID];
		$this->addgaleryController($isSelSection);
	}

	/**
	 * Metoda pro přidávání galerií v sekci
	 */
	public function sectionAddphotosController() {
		$this->checkWritebleRights();
		$this->addphotosController();
	}
	
	/**
	 * Kontroler pro zobrazení fotogalerie
	 */
	public function showController()
	{
//		Mazání zvolené fotky
		if(isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_BUTTON_DELETE]) AND $this->getRights()->isWritable()){
			if(!is_numeric($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID])){
				new CoreException(_('Fotku se nepodařilo smazat, bylo zadáno nekorektní id'),10);
			} else {
				$idPhoto = (int)$_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID];
				$photoObj = new PhotoDetailModel();
				$photo = $photoObj->getPhotoById($idPhoto);
				if(!empty($photo) AND $this->deletePhotoFile($photo[self::COLUMN_PHOTOS_FILE])){
//					Vymazání z db
					if($photoObj->deletePhoto($idPhoto)){
						$this->infoMsg()->addMessage(_('Fotka byla smazána'));
						$this->getLink()->params()->reload();
					} else {
						new CoreException(_('Fotku se nepodařilo smazat. Chyba při mazání'),11);
					}
				}
				unset($photoObj);
			}
		}
		
//		Mazání zvolené galerie
		if(isset($_POST[self::FORM_GALERY_PREFIX.self::FORM_BUTTON_DELETE]) AND $this->getRights()->isWritable()){
			$idGalery = (int)htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_ID], ENT_QUOTES);
			$galeryObj = new GaleryDetailModel();
			$photoObj = new PhotoDetailModel();
			$photos = $galeryObj->getPhotosList($idGalery);
			$deleted = true;
//			Vymazání všech fotek
			foreach ($photos as $pkey => $photo) {
				if($this->deletePhotoFile($photo[self::COLUMN_PHOTOS_FILE])){
					if(!$photoObj->deletePhoto($photo[self::COLUMN_PHOTOS_ID])){
						$deleted = false;
					}
				} else {
					$deleted = false;
				}
			}
			if($deleted){
				if($galeryObj->deleteGalery($idGalery)){
					$this->infoMsg()->addMessage(_('Galeire byla smazána'));
					$this->getLink()->action()->article()->params()->reload();
				} else {
					new CoreException(_('Galerii se nepodařilo smazat'), 12);
				}
			} else {
				new CoreException(_('Nepodařilo se smazat fotky z galerie.'), 12);
			}
		}
		
//		Pokud se zobrazuje celá galerie
		if(!isset($_GET[self::PHOTOS_SCROLL_URL_PARAM])){
			$galeryObj = new GaleryDetailModel();
			$galery = $galeryObj->getGaleryDetail($this->getArticle()->getArticle());
			$this->container()->addData('galery', $galery);
			$this->container()->addLink('edit_galery', $this->getLink()->action($this->getAction()->actionEditgalery()));
			$photos = $galeryObj->getPhotosList($galery[self::COLUMN_GALERY_ID]);
			$photoNumber = 1;
			foreach ($photos as $photoKey => $photo) {
				$photos[$photoKey][self::COLUMN_PHOTOS_SHOW_LINK]=$this->getLink()->param(self::PHOTOS_SCROLL_URL_PARAM, $photoNumber);
				$photos[$photoKey][self::COLUMN_PHOTOS_EDIT_LINK_IMAG]=$this->getLink()->action($this->getAction()->actionEditphoto());
				$photoNumber++;
			}
			$this->container()->addData('photos', $photos);
			$this->container()->addData('small_images_dir', $this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR);
			if($this->getRights()->isWritable()){
				$this->container()->addLink('edit_photos', $this->getLink()->action($this->getAction()->actionEditphotos()));
			}
//			//Odkaz zpět
			$session = new Sessions();
			if(!$session->isEmpty(self::LINK_BACK_SESSION)){
				$this->container()->addLink('link_back', $session->get(self::LINK_BACK_SESSION));
			} else {
				$this->container()->addLink('link_back', $this->getLink()->action()->article());
			}
//			Odkaz pro přidání fotky
			$this->container()->addLink('add_photo', $this->getLink()->action($this->getAction()->actionAddphotos()));
		}
//		Zobrazuje se fotografie
		else {
//			Kontroler pro zobrazení fotografie
			$this->showPhotoControllerPrivate();
		}
	}
	
	private function showPhotoControllerPrivate() {
//			Změna viewru na zobrazení fotky
			$this->changeActionView('showPhoto');
			$galeryObj = new GaleryDetailModel();
			$galery = $galeryObj->getGaleryDetail($this->getArticle()->getArticle());
			$photoObj = new PhotoDetailModel();
			$scroll = $this->eplugin()->scroll();
			$scroll->setUrlParam(self::PHOTOS_SCROLL_URL_PARAM);
//		  	Výpočet scrolovátek									 
	  		$scroll->setCountRecordsOnPage(1);
	  		$scroll->setCountAllRecords($galeryObj->getNumPhotos($galeryObj->getIdGalery()));
//	  		Přiřazení scrolovátek
	  		$this->container()->addEplugin('scroll', $scroll);
//	  		Načtení fotky
	  		$photo = $photoObj->getPhoto($galeryObj->getIdGalery(), $scroll->getStartRecord(), $scroll->getCountRecords());
			$this->container()->addData('photo', $photo);
			$this->container()->addData('photo_label', $photo[self::COLUMN_PHOTOS_LABEL_IMAG]);
			$this->container()->addData('galery_label', $galery[self::COLUMN_GALERY_LABEL_IMAG]);
			if($this->getRights()->isWritable()){
				$this->container()->addLink('link_edit', $this->getLink()->action($this->getAction()->actionEditphoto()));
			}
//			Adresář s obrázky
			$this->container()->addData('images_dir', $this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_THUMBNAILS_DIR);
			$this->container()->addData('images_big_dir', $this->getModule()->getDir()->getDataDir());
//			Odkaz zpět
			$this->container()->addLink('link_back', $this->getLink()->withoutParam(self::PHOTOS_SCROLL_URL_PARAM));
	}

	/**
	 * Kontroler pro editaci galerie
	 */
	public function editgaleryController() {
		$this->checkWritebleRights();
		$galeryObj = new GaleryDetailModel();
		$galery = $galeryObj->getGaleryDetailAllLangs($this->getArticle()->getArticle());
		if(empty($galery)){
			new CoreException(_('Požadovaná galerie neexistuje'), 2);
			return false;
		}
//		Čas vytvoření galerie
		$this->container()->addData('date_select', $galery[self::COLUMN_GALERY_TIME]);
		
//		Zvolení názvu sekce
		if($galery[self::COLUMN_GALERY_LABEL_LANG_PREFIX.Locale::getLang()] != null){
			$this->container()->addData('galery_label', $galery[self::COLUMN_GALERY_LABEL_LANG_PREFIX.Locale::getLang()]);
		} else {
			$this->container()->addData('galery_label', $galery[self::COLUMN_GALERY_LABEL_LANG_PREFIX.Locale::getDefaultLang()]);
		}
//		Načtení sekcí
		$sectionsObj = new SectionListModel();
		$sections = $sectionsObj->getSectionList();
		$this->container()->addData('sections', $sections);
		$this->container()->addData('section_select', $galery[self::COLUMN_GALERY_ID_SECTION]);
//		Helpre pro práci s jazykovými poli
		$localeHelper = new LocaleCtrlHelper();
		$sendArray = array();
//		pokud byla galerie odeslána
		if(isset($_POST[self::FORM_GALERY_PREFIX.self::FORM_BUTTON_SEND])){
//			Vygenerování datumu
			$dateHelp = new DateTimeCtrlHelper();
			$dateStamp = $dateHelp->createStampSmartyPost(self::FORM_GALERY_PREFIX.self::FORM_GALERY_DATE);
			$this->container()->addData('date_select', $dateStamp);
//			Vybraná sekce
			$idSelSection = (int)htmlspecialchars($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_ID_SECTION]);
			$this->container()->addData('section_select', $idSelSection);
			$sendArray = $localeHelper->postsToArray(array(self::FORM_GALERY_LABEL_PREFIX, self::FORM_GALERY_TEXT_PREFIX), self::FORM_GALERY_PREFIX);
			if($_POST[self::FORM_GALERY_PREFIX.self::FORM_GALERY_LABEL_PREFIX.Locale::getDefaultLang()] == null){
				$this->errMsg()->addMessage(_('Nebyly zadány všechny povinné údaje'));
			} else {
				$sendArray[self::COLUMN_GALERY_ID_SECTION] = $idSelSection;
				$sendArray[self::COLUMN_GALERY_TIME] = $dateStamp;
				$updated = $galeryObj->saveEditGalery($sendArray, $this->getArticle()->getArticle());
				if($updated){
					$this->infoMsg()->addMessage(_("Galerie byla upravena"));
					$this->getLink()->action()->reload();
				} else {
					new CoreException(_("Nepodařilo se upravit galerii"),4);
				}
			}
		}
		$lArray = $localeHelper->generateArray(array(self::FORM_GALERY_LABEL, self::FORM_GALERY_TEXT), $sendArray,$galery);
//		Sekce do viewru
		$this->container()->addData('galery', $lArray);
//		Odkaz zpět			
		$this->container()->addLink('link_back', $this->getLink()->action());
	}
		
	/**
	 * Kontroler pro úpravu fotky
	 */
	public function editphotoController() {
		$this->checkWritebleRights();
		if(!isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID])){
			new CoreException(_('Žádné id fotografie nebylo přeneseno'), 2);
			return false;
		}
		if(!is_numeric($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID])){
			new CoreException(_('Špatně zadané id fotografie'), 2);
			return false;
		}
		$idPhoto = (int)$_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID];
		$photoObj = new PhotoDetailModel();
		$photo = $photoObj->getPhotoById($idPhoto);
		if(empty($photo)){
			new CoreException(_('Požadovaná fotka neexistuje'), 2);
			return false;
		}
		
//		Zvolení názvu fotky
		if($photo[self::COLUMN_PHOTOS_LABEL_LANG_PREFIX.Locale::getLang()] != null){
			$this->container()->addData('photo_label', $photo[self::COLUMN_PHOTOS_LABEL_LANG_PREFIX.Locale::getLang()]);
		} else {
			$this->container()->addData('photo_label', $photo[self::COLUMN_PHOTOS_LABEL_LANG_PREFIX.Locale::getDefaultLang()]);
		}
		$this->container()->addData('photo_id', $photo[self::COLUMN_PHOTOS_ID]);
		
//		Helpre pro práci s jazykovými poli
		$localeHelper = new LocaleCtrlHelper();
		$sendArray = array();
		
//		pokud byla galerie odeslána
		if(isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_BUTTON_SEND])){
			$sendArray = $localeHelper->postsToArray(array(self::FORM_PHOTO_LABEL_PREFIX, self::FORM_PHOTO_TEXT_PREFIX), self::FORM_PHOTO_PREFIX);
			if($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_LABEL_PREFIX.Locale::getDefaultLang()] == null){
				$this->errMsg()->addMessage(_('Nebyly zadány všechny povinné údaje'));
			} else {
				
				$updated = $photoObj->saveEditPhoto($sendArray, $idPhoto);
				if($updated){
					$this->infoMsg()->addMessage(_("Fotka byla upravena"));
					$this->getLink()->action()->reload();
				} else {
					new CoreException(_("Nepodařilo se upravit fotku"),4);
				}
			}
		}
		$lArray = $localeHelper->generateArray(array(self::FORM_PHOTO_LABEL_PREFIX, self::FORM_PHOTO_TEXT_PREFIX), $sendArray,$photo);
//		Sekce do viewru
		$this->container()->addData('photo', $lArray);
//		Odkaz zpět			
		$this->container()->addLink('link_back', $this->getLink()->action());
	}
	
	/**
	 * Kontroler pro úpravu více fotografií
	 */
	public function editphotosController() {
		$this->checkWritebleRights();
		//		Odkaz zpět			
		$this->container()->addLink('link_back', $this->getLink()->action());
		if(!isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID]) AND !isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_BUTTON_SEND])){
			$this->errMsg()->addMessage(_('Nebyla vybrána žádná fotografie'), true);
			$this->getLink()->action()->reload();
		}
		$idPhotos = array();
		if(isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID])){
			$idPhotos = $_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID];
		} else {
			$this->errMsg()->addMessage(_('Nekorektně odeslaná id fotek'), true);
			$this->getLink()->action()->reload();
		}
		$sendArray = array();
//		Co se s fotkama provádí
//		Je-li mazáno
		if(isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_IS_EDIT]) 
			AND $_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_IS_EDIT] == 0 
			AND isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_BUTTON_DELETE])){
			$photoObj = new PhotoDetailModel();
			$photos = $photoObj->getPhotosById($idPhotos);
			foreach ($photos as $photo) {
				if(!$this->deletePhotoFile($photo[self::COLUMN_PHOTOS_FILE])){
					new CoreException(_('Chyba při mazání souborů fotek'), 2);
				} else if(!$photoObj->deletePhoto($photo[self::COLUMN_PHOTOS_ID])){
					new CoreException(_('Chyba při mazání fotek z databáze'), 2);
				}
			}
			$this->infoMsg()->addMessage(_('Fotky byly vymazány'));
			$this->getLink()->action()->reload();	
			return false;	
		}
//		Jsou editovány
		else if(isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_IS_EDIT]) 
			AND $_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_IS_EDIT] == 1 
			AND isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_BUTTON_EDIT])) {
		}		
		//		Helpre pro práci s jazykovými poli
		$localeHelper = new LocaleCtrlHelper();
		$photosObj = new PhotoDetailModel();
//		pokud byly fotky odeslány
		if(isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_BUTTON_SEND])){
//			Převedení entit na html entity
			function htmlEncode(&$item, $key) {
				$item = htmlspecialchars($item, ENT_QUOTES);
			}
			$saveArray = array();
			$allOk = true;
			foreach ($_POST[self::FORM_PHOTO_PREFIX] as $idPhoto => $photo) {
				array_push($idPhotos, $idPhoto);
//				Převedení entit na html entity
				array_walk($photo, 'htmlEncode');
				array_push($sendArray, $photo);
				$saveArray[$idPhoto] = $photo;
//				Kontrola jastli byli zadány všechny údaje
				if($photo[self::FORM_PHOTO_LABEL_PREFIX.Locale::getDefaultLang()] == null){
					$allOk = false;
					$this->errMsg()->addMessage(_('Nebyly zadány všechny povinné údaje'));
				}
			}
//			Uložení do db		
			if($allOk){
				$allSaved = true;
				foreach ($saveArray as $idPhoto => $photo) {
					if(!$photosObj->saveEditPhoto($photo, $idPhoto)){
						$allSaved = false;
					}
				}
				if($allSaved){
					$this->infoMsg()->addMessage(_("Fotografie byly upraveny"));
					$this->getLink()->action()->reload();
				} else {
					new CoreException(_("Nepodařilo se upravit fotografii"),4);
				}
			}
		}
		$photos = $photosObj->getPhotosById($idPhotos);
		$allPhotosArray = array();
		$tmpArray = array();
		foreach ($photos as $key => $photo) {
			if(!isset($sendArray[$key])){
				$sendArray[$key]=null;
			}
			$lArray = $localeHelper->generateArray(array(self::FORM_PHOTO_LABEL_PREFIX, self::FORM_PHOTO_TEXT_PREFIX), $sendArray[$key],$photo);
			$tmpArray[self::COLUMN_PHOTOS_LABEL_IMAG] = $lArray;
			$tmpArray[self::COLUMN_PHOTOS_FILE] = $photo[self::COLUMN_PHOTOS_FILE];
			$tmpArray[self::COLUMN_PHOTOS_ID] = $photo[self::COLUMN_PHOTOS_ID];
			array_push($allPhotosArray, $tmpArray);
		}
//		Fotky do viewru
		$this->container()->addData('photos', $allPhotosArray);
		$this->container()->addData('photos_dir', $this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR);
	}

	/**
	 * Metoda vymaže soubory zadané fotky
	 * 
	 * @param string -- název souboru
	 */
	private function deletePhotoFile($file) {
//		Vymazání souborů fotek
		$files = new Files();
		$allFilesDeleted = true;
		if($files->exist($this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR.$file) AND
			!$files->deleteFile($this->getModule()->getDir()->getDataDir().self::IMAGES_THUMBNAILS_DIR, $file)){
			$allFilesDeleted = false;
		}
		if($files->exist($this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_THUMBNAILS_DIR.$file) AND
			!$files->deleteFile($this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_THUMBNAILS_DIR, $file)){
			$allFilesDeleted = false;
		}
		if($files->exist($this->getModule()->getDir()->getDataDir().$file) AND
			!$files->deleteFile($this->getModule()->getDir()->getDataDir(), $file)){
			$allFilesDeleted = false;
		};
		unset($files);
		return $allFilesDeleted;
	}
}
?>