<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class GuestbookController extends Controller {
	/**
	 * Názvy sloupců v db tabulce
	 * @var string
	 */
//	const COLUM_SPONSOR_ID = 'id_sponsor';
//	const COLUM_SPONSOR_ID_ITEM = 'id_item';
//	const COLUM_SPONSOR_NAME_LANG_PREFIX = 'name_';
//	const COLUM_SPONSOR_LABEL_LANG_PREFIX = 'label_';
//	const COLUM_SPONSOR_URL = 'url';
//	const COLUM_SPONSOR_LOGO_IMAGE = 'logo_image';
//	const COLUM_SPONSOR_URLKEY = 'urlkey';
//	const COLUM_SPONSOR_DELETED = 'deleted';
	
	/**
	 * Imaginarní názvy sloupců
	 * @var string
	 */
//	const COLUM_SPONSOR_NAME = 'name';
//	const COLUM_SPONSOR_LABEL = 'label';
//	const COLUM_SPONSOR_EDITLINK = 'editlink';
	
	/**
	 * Názvy formulářových prvků
	 * @var string
	 */
	const FORM_PREFIX				= 'guestbook_';
	const FORM_BUTTON_SEND		= 'send';
//	const FORM_BUTTON_DELETE = 'delete';
	const FORM_TOPIC				= 'topic';
	const FORM_NICK				= 'nick';
	const FORM_EMAIL				= 'email';
	const FORM_TEXT				= 'text';
	const FORM_VERIFY				= 'verify';
//	const FORM_LABEL = 'label';
//	const FORM_LABEL_PREFIX = 'label_';
//	const FORM_URL = 'url';
//	const FORM_LOGO_FILE = 'logo_file';
//	const FORM_ID = 'id';
	
	/**
	 * Velikosti obrázku loga společnosti
	 * @var int
	 */
//	const LOGO_IMAGE_WIDTH = 60;
//	const LOGO_IMAGE_HEIGHT = 40;
//	const LOGO_BIG_IMAGE_WIDTH = 120;
//	const LOGO_BIG_IMAGE_HEIGHT = 80;
	
	/**
	 * Adresář s velkými logy
	 * @var string
	 */
//	const LOGO_BIG_DIR = 'big';
	
	
	public function mainController() {
		$this->checkReadableRights();

//		Vytvoření obejktu formuláře
		$form = new FormValidator();
		$form->setPrefix(self::FORM_PREFIX);

		$form->inputSubmit(self::FORM_BUTTON_SEND)->inputText(self::FORM_TOPIC, true)
			 ->inputText(self::FORM_NICK, true, true)
             ->inputText(self::FORM_EMAIL, true, false, FormValidator::VALIDATION_EMAIL)
             ->textarea(self::FORM_TEXT, true);

//		Ověřovací obrázek
		$verifyImage = new VerifyImageEplugin();
		$this->container()->addData('VERIFY_IMAGE', $verifyImage->getImageName());

//		kontrola formuláře
		if($form->checkForm() AND $verifyImage->verifyImage(self::FORM_PREFIX.self::FORM_VERIFY)){
			echo '<pre>';
			print_r($form->getFormValues(true, true));
			echo '</pre>';
		} else {
			$this->container()->addData('TOPIC_ARRAY', $form->getFormValues(false, true));
		}


        echo '<pre>';
		print_r($form->getFormValues(false, true));
		echo '</pre>';

	}
	
	/**
	 * Kontroler pro obsluhu přidání sponzora
	 */
	public function addController(){
		$this->checkWritebleRights();
		
		$this->createModel("sponsorDetail");

//		Příprava pole pro jazyky
		foreach (Locale::getAppLangs() as $lang) {
			$this->getModel()->sponsorArray[$lang] = array();
		}
		
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_SEND])){
			$logoImageName = null; //TODO dodělat načítání obrázků
//			Načtení loga
			$uploadFile = new UploadFiles($this->errMsg(), true);
			$uploadFile->upload(self::FORM_PREFIX.self::FORM_LOGO_FILE);
			if($uploadFile->isUploaded()){
				$image = new Images($this->errMsg(), $uploadFile->getTmpName());
				$image->setImageName($uploadFile->getOriginalName());
				$image->saveImage($this->getModule()->getDir()->getDataDir(), self::LOGO_IMAGE_WIDTH, self::LOGO_IMAGE_HEIGHT);
				$image->saveImage($this->getModule()->getDir()->getDataDir().self::LOGO_BIG_DIR.DIRECTORY_SEPARATOR, self::LOGO_BIG_IMAGE_WIDTH, self::LOGO_BIG_IMAGE_HEIGHT);
				$logoImageName = $image->getNewImageName();
			}
				
			if($_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.Locale::getDefaultLang()] == null OR $uploadFile->isUploadError() OR !$image->isImage()){
				if($_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.Locale::getDefaultLang()] == null){
					$this->errMsg()->addMessage(_('Nebyly zadány všechny povinné údaje'));
				}

				//		Příprava pole pro jazyky
				foreach (Locale::getAppLangs() as $lang) {
					$this->getModel()->sponsorArray[$lang][self::COLUM_SPONSOR_NAME] = $_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.$lang];
					$this->getModel()->sponsorArray[$lang][self::COLUM_SPONSOR_LABEL] = $_POST[self::FORM_PREFIX.self::FORM_LABEL_PREFIX.$lang];
				}
				
				$this->getModel()->sponsorUrl = $_POST[self::FORM_PREFIX.self::FORM_URL];
			} else {
				
//				načtení všech předchozích url klíču z db
				$sqlSelecturlKey = $this->getDb()->select()->from($this->getModule()->getDbTable(), self::COLUM_SPONSOR_URLKEY);
				$urlkeysArray = $this->getDb()->fetchAssoc($sqlSelecturlKey);
				
				$sFunctions = new SpecialFunctions();
				
//				vygenerování url klíče
				$urlKey = $sFunctions->createDatabaseKey($_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.Locale::getDefaultLang()], $urlkeysArray);
				
				$url=null;
				if($_POST[self::FORM_PREFIX.self::FORM_URL] != null){
					//kontrola zadané url a popřípadě doplnění
					$url = addslashes($_POST[self::FORM_PREFIX.self::FORM_URL]);
					$url = $sFunctions->createUrl($url);
				}
				
				//Vygenerování sloupců do kterých se bude zapisovat
				$columsArray['names'] = array();
				$columsArray['values'] = array();
				array_push($columsArray['names'], self::COLUM_SPONSOR_URLKEY);
				array_push($columsArray['values'], $urlKey);
				array_push($columsArray['names'], self::COLUM_SPONSOR_ID_ITEM);
				array_push($columsArray['values'], $this->getModule()->getId());
				array_push($columsArray['names'], self::COLUM_SPONSOR_LOGO_IMAGE);
				array_push($columsArray['values'], $logoImageName);
				array_push($columsArray['names'], self::COLUM_SPONSOR_URL);
				array_push($columsArray['values'], $url);
				foreach (Locale::getAppLangs() as $lang) {
					array_push($columsArray['names'], self::COLUM_SPONSOR_NAME_LANG_PREFIX.$lang);
					array_push($columsArray['values'], htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.$lang], ENT_QUOTES));
//					array_push($columsArray['values'], $_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.$lang]);
					array_push($columsArray['names'], self::COLUM_SPONSOR_LABEL_LANG_PREFIX.$lang);
//					array_push($columsArray['values'], htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_LABEL_PREFIX.$lang], ENT_QUOTES));
					array_push($columsArray['values'], htmlspecialchars_decode($_POST[self::FORM_PREFIX.self::FORM_LABEL_PREFIX.$lang]));
				}
				
				$sqlInsert = $this->getDb()->insert()->into($this->getModule()->getDbTable())
													 ->colums($columsArray['names'])
													 ->values($columsArray['values']);
													 
				//				Vložení do db
				if($this->getDb()->query($sqlInsert)){
					$this->infoMsg()->addMessage(_('Sponzor byl uložen'));
					$this->getLink()->article()->action()->reload();
				} else {
					new CoreException(_('Sponzora se nepodařilo uložit, chyba při ukládání do db'), 1);
				}
			}
		}
	}
	
	/**
	 * Controler pro úpravu spozora
	 */
public function editController() {
		$this->checkWritebleRights();
		
		$this->createModel("sponsorDetail");
		
//		adresář s obrázky
		$this->getModel()->dirToImages = $this->getModule()->getDir()->getDataDir();
		
		$idSponsor = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_ID], ENT_QUOTES);
		
		//		načtení novinky z db
		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
											 ->where(self::COLUM_SPONSOR_ID_ITEM." = ".$this->getModule()->getId())
											 ->where(self::COLUM_SPONSOR_ID." = ".$idSponsor)
											 ->where(self::COLUM_SPONSOR_URLKEY." = '".$this->getArticle()->getArticle()."'");
		
								 
		$sponsors = $this->getDb()->fetchAssoc($sqlSelect, true);
		
		
//		Kontrole jestli se neukládá
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_SEND])){
			$logoImageName = null; //TODO dodělat načítání obrázků
//			Načtení loga
			$isImage = true;
			$uploadFile = new UploadFiles($this->errMsg(), true);
			$uploadFile->upload(self::FORM_PREFIX.self::FORM_LOGO_FILE);
			if($uploadFile->isUploaded()){
				$image = new Images($this->errMsg(), $uploadFile->getTmpName());
				$image->setImageName($uploadFile->getOriginalName());
				$image->saveImage($this->getModule()->getDir()->getDataDir(), self::LOGO_IMAGE_WIDTH, self::LOGO_IMAGE_HEIGHT);
				$image->saveImage($this->getModule()->getDir()->getDataDir().self::LOGO_BIG_DIR, self::LOGO_BIG_IMAGE_WIDTH, self::LOGO_BIG_IMAGE_HEIGHT);
				$logoImageName = $image->getNewImageName();
				$isImage = $image->isImage();
			}
			
			if($_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.Locale::getDefaultLang()] == null OR $uploadFile->isUploadError() OR !$isImage){
			   	if($_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.Locale::getDefaultLang()] == null){
					$this->errMsg()->addMessage(_('Nebyly zadány všechny potřebné údaje'));
			   	}
			   	
			   	foreach (Locale::getAppLangs() as $lang) {
			   		$sponsors[self::COLUM_SPONSOR_NAME_LANG_PREFIX.$lang] = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.$lang], ENT_QUOTES);
//			   		$sponsors[self::COLUM_SPONSOR_NAME_LANG_PREFIX.$lang] = $_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.$lang];
			   		$sponsors[self::COLUM_SPONSOR_LABEL_LANG_PREFIX.$lang] = htmlspecialchars_decode($_POST[self::FORM_PREFIX.self::FORM_LABEL_PREFIX.$lang], ENT_QUOTES);
			   		$sponsors[self::COLUM_SPONSOR_LABEL_LANG_PREFIX.$lang] = $_POST[self::FORM_PREFIX.self::FORM_LABEL_PREFIX.$lang];
			   	}
			} else {
//				Pole s upravenými hodnotami
				$updateArray = array();			
				
				//				načtení všech předchozích url klíču z db
				$sqlSelecturlKey = $this->getDb()->select()->from($this->getModule()->getDbTable(), self::COLUM_SPONSOR_URLKEY);
				$urlkeysArray = $this->getDb()->fetchAssoc($sqlSelecturlKey);
				
//				objekt se speciálními funkcemi
				$sFunctions = new SpecialFunctions();
				
//				vygenerování url klíče
				$updateArray[self::COLUM_SPONSOR_URLKEY] = $sFunctions->createDatabaseKey($_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.Locale::getDefaultLang()], $urlkeysArray);
				
				$updateArray[self::COLUM_SPONSOR_URL] = null;
				if($_POST[self::FORM_PREFIX.self::FORM_URL] != null){
					//kontrola zadané url a popřípadě doplnění
					$url = addslashes($_POST[self::FORM_PREFIX.self::FORM_URL]);
					$updateArray[self::COLUM_SPONSOR_URL] = $sFunctions->createUrl($url);
				}

//				Pokud je ukládán nějaký obrázek
				if($isImage AND $uploadFile->isUploaded()){
					//				uložení názvu obrázku
					$updateArray[self::COLUM_SPONSOR_LOGO_IMAGE] = $logoImageName;
					//				smazání starého obrázku
					if($logoImageName != null){
						$sqlImageSelect = $this->getDb()->select()->from($this->getModule()->getDbTable(), self::COLUM_SPONSOR_LOGO_IMAGE)
						->where(self::COLUM_SPONSOR_ID.' = '.$idSponsor);
							
						$oldImage = $this->getDb()->fetchObject($sqlImageSelect);
						if($oldImage->{self::COLUM_SPONSOR_LOGO_IMAGE} != null){
							$file = new Files();
							$file->deleteFile($this->getModule()->getDir()->getDataDir(), $oldImage->{self::COLUM_SPONSOR_LOGO_IMAGE});
							unset($file);
						}

					}
				}
				
//				Pole pro vložení
				foreach (Locale::getAppLangs() as $lang) {
					$_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.$lang] != null ? $name = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_NAME_PREFIX.$lang], ENT_QUOTES) 
																				   : $name = null;
					$_POST[self::FORM_PREFIX.self::FORM_LABEL_PREFIX.$lang] != null ? $label = htmlspecialchars_decode($_POST[self::FORM_PREFIX.self::FORM_LABEL_PREFIX.$lang]) 
																				   : $label = null;
					
					$updateArray[self::COLUM_SPONSOR_NAME_LANG_PREFIX.$lang] = $name;
					$updateArray[self::COLUM_SPONSOR_LABEL_LANG_PREFIX.$lang] = $label;
				}
				
//				Vložení do db
				$sqlInsert = $this->getDb()->update()->table($this->getModule()->getDbTable())
													 ->set($updateArray)
													 ->where(self::COLUM_SPONSOR_ID." = ".$idSponsor)
													 ->where(self::COLUM_SPONSOR_URLKEY." = '".$this->getArticle()->getArticle()."'");
				
				if($this->getDb()->query($sqlInsert)){
					$this->infoMsg()->addMessage(_("Sponzor byl upraven"));
					$this->getLink()->article()->action()->reload();
				} else {
					new CoreException(_("Nepodařilo se upravit sponzora v db"),2);
				}
			}
		}
		
//		echo "<pre>";
//		print_r($news);
//		echo "</pre>";
		
		if(!empty($sponsors)){
			//		Podle počtu jazyků inicializujeme pole pro přidání sponzora
			foreach (Locale::getAppLangs() as $lang) {
				$this->getModel()->sponsorArray[$lang] = array();
				$this->getModel()->sponsorArray[$lang][self::COLUM_SPONSOR_NAME] = $sponsors[self::COLUM_SPONSOR_NAME_LANG_PREFIX.$lang];
				$this->getModel()->sponsorArray[$lang][self::COLUM_SPONSOR_LABEL] = $sponsors[self::COLUM_SPONSOR_LABEL_LANG_PREFIX.$lang];
			}
			$this->getModel()->idSponsor = $sponsors[self::COLUM_SPONSOR_ID];
			$this->getModel()->sponsorDefaultName = htmlspecialchars_decode($sponsors[self::COLUM_SPONSOR_NAME_LANG_PREFIX.Locale::getDefaultLang()]);
			$this->getModel()->sponsorImageFile = $sponsors[self::COLUM_SPONSOR_LOGO_IMAGE];
			$this->getModel()->sponsorUrl = $sponsors[self::COLUM_SPONSOR_URL];
			
		} else {
			$this->errMsg()->addMessage(_('Nepodařilo se načíst spozora z db. zřejmně nemáte práva pro editaci sponzora'));
		}
		
//		echo "<pre>";
//		print_r($this->getModel()->newsArray);
//		echo "</pre>";
	}
	
	/**
	 * Metoda vymaže zadaného sponzora
	 */
	private function deleteSponsor() {
		$idSponsor = htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_ID], ENT_QUOTES);
		
		if(!is_numeric($idSponsor)){
			new CoreException(_('Bylo přeneseno nekorektní id sponzora'), 3);
		} else {
			$sqlDelete = $this->getDb()->update()->table($this->getModule()->getDbTable())
												 ->set(array(self::COLUM_SPONSOR_DELETED => (int)true))
												 ->where(self::COLUM_SPONSOR_ID.' = '.$idSponsor)
												 ->where(self::COLUM_SPONSOR_ID_ITEM.' = '.$this->getModule()->getId());
			
			if($this->getDb()->query($sqlDelete)){
				$this->infoMsg()->addMessage(_('Sponzor byl smazán'));
				$this->getLink()->action()->article()->reload();
			} else {
				new CoreException(_('Nepodařilo se smazat sponzora z db. Zřejmně špatné id'));
			}
		}
	}
}

?>