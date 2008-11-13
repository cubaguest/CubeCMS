<?php
/**  
 * EPlugin pro přidávání obrázků ke stránce
 * 
 * @category   	VVE VeproveVypeckyEnginy 
 * @package    	DwFiles class
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: dwfiles.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 	Třída Epluginu pro práci s obrázky, přikládanými do stránky
 * 
 * //TODO doělat mazání souborů z celého článku
 */

class UserImages extends Eplugin {
	/**
	 * Název primární šablony s posunovátky
	 * @var string
	 */
	protected $templateFile = 'userimages.tpl';

	/**
	 * Název databázové tabulky se změnama
	 * @var string
	 */
	const DB_TABLE_USER_IMAGES = 'userimages';
	
	/**
	 * Název adresáře kde se ukládají soubory
	 * @var string
	 */
	const USERIMAGES_FILES_DIR = 'userimages';
	
	/**
	 * Název adresáře s miniaturami obrázků
	 */
	const USERIMAGES_SMALL_FILES_DIR = 'small';
	
	/**
	 * Šířka miniatury
	 * @var integer
	 */
	const THUMBNAIL_WIDTH = 110;
	
	/**
	 * Výška miniatury
	 * @var integer
	 */
	const THUMBNAIL_HEIGHT = 80;
	
	/**
	 * Názvy sloupců v db
	 * @var string
	 */
	const COLUM_ID				= 'id_file';
	const COLUM_ID_USER			= 'id_user';
	const COLUM_ID_ITEM			= 'id_item';
	const COLUM_ID_ARTICLE		= 'id_article';
	const COLUM_WIDTH			= 'width';
	const COLUM_HEIGHT			= 'height';
	const COLUM_FILE			= 'file';
	const COLUM_SIZE			= 'size';
	const COLUM_TIME			= 'time';

	const COLUM_LINK_TO_SHOW	= 'link_show';
	const COLUM_LINK_TO_SMALL	= 'link_small';
	
	/**
	 * Názvy formulářových prvků
	 * @var string
	 */
	const FORM_PREFIX = 'userimages_';
	const FORM_NEW_FILE = 'new_file';
	const FORM_BUTTON_SEND = 'send_file';
	const FORM_USERIMAGE_ID = 'id';
	const FORM_BUTTON_DELETE = 'delete';
	
	
//	const COLUM_USER_NAME		= 'name';
//	const COLUM_USER_SURNAME	= 'surname';
//	const COLUM_USER_USERNAME	= 'username';
//	const COLUM_LABEL			= 'label';
//	const COLUM_TIME			= 'time';
//	
	/**
	 * Název volby s názvem tabulky uživatelů
	 * @var string
	 */
	const CONFIG_TABLE_USERS = 'users_table';
	
	/**
	 * Sekce v configu s informacemi o tabulkách
	 * @var string
	 */
	const CONFIG_TABLES_SECTIONS = 'db_tables';
	
	/**
	 * Proměnná s id článku, u kterého se zobrazí změny
	 * @var integer/array
	 */
	private $idArticle = null;
	
	/**
	 * Pole se soubory
	 * @var array
	 */
	private $imagesArray = array();
	private static $otherImagesArray = array();
	
	/**
	 * Pole s popisky
	 * @var array
	 */
	private $labelsArray = array();
	
	/**
	 * Pole s id modulu (items)
	 * @var array
	 */
	private $idItems = null;
	
	/**
	 * ID šablony
	 * @var integer
	 */
	private $idUserImages = '1';
	
	/**
	 * Počet vrácených záznamů
	 * @var integer
	 */
	private $numberOfReturnRows = 0;
	private static $otherNumberOfReturnRows = array();
	
	/**
	 * Metoda inicializace, je spuštěna pří vytvoření objektu  
	 *
	 */
	protected function init(){
		
	}
	
	/**
	 * Metoda nastaví id šablony pro výpis
	 * @param ineger -- id šablony (jakékoliv)
	 */
	public function setIdTpl($id) {
		$this->idDwFiles = $id;
	}
	
	/**
	 * Metoda nastavuje id článku pro který se budou ukládát soubory
	 * @param integer -- id článku
	 */
	public function setIdArticle($idArticle){
		$this->idArticle = $idArticle;
		
		$this->checkSendImages();
		
		$this->checkDeleteImage();
		
		$this->getImagesFromDb();
	}
	
	/**
	 * Metoda kontroluje, jestli byl odeslán soubor, pokud ano je soubor nahrán a uložen do db
	 */
	private function checkSendImages() {
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_SEND])){
			$uploadFile = new UploadFiles($this->errMsg());
			
			$uploadFile->upload(self::FORM_PREFIX.self::FORM_NEW_FILE);
			
//			podařilo se soubor nahrát
			if($uploadFile->isUploaded()){
				$image = new Images($this->errMsg(), $uploadFile->getTmpName());
				if($image->isImage()){
					//				tak vytvoříme nový název
					$file = new Files();
					$fileName = $file->createNewFileName($uploadFile->getOriginalName(), AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/');
					$copied = $file->copyAs($uploadFile->getTmpName(), AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/', $fileName);

					$fileSize = filesize(AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/'.$fileName);

//					Vytvoříme miniaturu
					$image = new Images($this->errMsg(), $uploadFile->getTmpName());
					$image->setImageName($uploadFile->getOriginalName());
					$image->saveImage(AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/'.self::USERIMAGES_SMALL_FILES_DIR.'/', self::THUMBNAIL_HEIGHT, self::THUMBNAIL_HEIGHT);
					
					$sqlInsert = $this->getDb()->insert()->into(self::DB_TABLE_USER_IMAGES)
														->colums(self::COLUM_ID_ARTICLE,
															self::COLUM_ID_ITEM,
															self::COLUM_ID_USER,
															self::COLUM_FILE,
															self::COLUM_WIDTH,
															self::COLUM_HEIGHT,
															self::COLUM_SIZE,
															self::COLUM_TIME)
														->values($this->idArticle,
															$this->getModule()->getId(),
															$this->getRights()->getAuth()->getUserId(),
															$fileName,
															$image->getOriginalWidth(),
															$image->getOriginalHeight(),
															$fileSize,
															time());
						
					if($copied AND $this->getDb()->query($sqlInsert)){
						$this->infoMsg()->addMessage(_('Obrázek byl uložen'));
						$this->getLinks()->reload();
					} else {
						new CoreException(_('Obrázek se nepodařilo zkopírovat nebo uložit do databáze'));
					}
				}
			}
			
		};
	}
	
	/**
	 * Metoda odstraní zadaný soubor
	 * 
	 * @param integer -- id souboru
	 */
	private function deleteUserImage($id) {
//		načtení informací o souboru 
		$sqlSelect = $this->getDb()->select()->from(array('images'=>self::DB_TABLE_USER_IMAGES), self::COLUM_FILE)
								   ->where(self::COLUM_ID.' = '.$id);
								   
		$file = $this->getDb()->fetchObject($sqlSelect);
		
		if($file != null){
			$deleted = true;
			$files = new Files();
			if($files->exist(AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/'.$file->{self::COLUM_FILE})){
				$deleted = $files->deleteFile(AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/', $file->{self::COLUM_FILE});
			}
			
			if($files->exist(AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/'.self::USERIMAGES_SMALL_FILES_DIR.'/'.$file->{self::COLUM_FILE})){
				$deleted = $files->deleteFile(AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/'.self::USERIMAGES_SMALL_FILES_DIR.'/', $file->{self::COLUM_FILE});
			}

//			vymazání z db
			$sqlDel = $this->getDb()->delete()->from(self::DB_TABLE_USER_IMAGES)
											  ->where(self::COLUM_ID.' = '.$id);
			
			if($deleted AND $this->getDb()->query($sqlDel)){
				$this->infoMsg()->addMessage(_('Obrázek byl smazán'));
				$this->getLinks()->reload();								  
			} else {
				new CoreException(_('Obrázek se nepodařilo vymazaz z adresáře nebo z db'));	
			}
		} else {
			new CoreException(_('Požadovaný obrázek neexistuje'));
		}
	}
	
	/**
	 * Metoda kontroluje, jestli nebyl soubor smazán
	 */
	private function checkDeleteImage() {
		if(isset($_POST[self::FORM_PREFIX.self::FORM_BUTTON_DELETE])){
			if(!is_numeric($_POST[self::FORM_PREFIX.self::FORM_USERIMAGE_ID])){
				new CoreException(_('Nebylo zadáno správné ID obrázku'));
			} else {
				$this->deleteUserImage(htmlspecialchars($_POST[self::FORM_PREFIX.self::FORM_USERIMAGE_ID]));
			}
		}
	}
	
	
	/**
	 * Metoda vrací objekt změny s načtenými změnami u zadaných id článků
	 * @param mixed -- array nebo integer s id článku (popřípadě podpole s id item a id článků)
	 * @return Changes -- vrací objekt Changes (tedy sebe)
	 */
//	public function getChanges($idArticles = null, $idItems = null) {
//		$this->idArticle = $idArticles;
//		$this->idItems = $idItems;
//		
//		$this->getDataFromDb();
//	
////		return $this->changesArray;
//		return $this;
//	}
	
	/**
	 * Metoda uloží změnu do db
	 * @param string -- popis změny
	 * @param integer -- id článku u kterého byla změna provedena
	 * @param integer -- (option) id item u ktré byla změna provedena
	 */
//	public function createChange($label, $idArticle, $idItem = null) {
//		$sqlInser = $this->getDb()->insert()->into(self::DB_TABLE_CHANGES);
//		
//		if($idItem == null){
//			$sqlInser = $sqlInser->colums(self::COLUM_ID_ARTICLE, self::COLUM_ID_ITEM, self::COLUM_ID_USER, self::COLUM_LABEL, self::COLUM_TIME)
//								->values($idArticle, $this->getModule()->getId(), $this->getRights()->getAuth()->getUserId(), $label, time());
//		
//		} else {
//			$sqlInser = $sqlInser->colums(self::COLUM_ID_ARTICLE, self::COLUM_ID_ITEM, self::COLUM_ID_USER, self::COLUM_LABEL, self::COLUM_TIME)
//								->values($idArticle, $idItem, $this->getRights()->getAuth()->getUserId(), $label, time());
//		}
//		
////		vložení záznamu
//		$this->getDb()->query($sqlInser);
//	}
	
	/**
	 * Metoda načte data z db
	 */
	private function getImagesFromDb() {
		$sqlSelect = $this->getDb()->select()->from(array('images'=>self::DB_TABLE_USER_IMAGES), array(self::COLUM_FILE, 
									self::COLUM_SIZE, self::COLUM_TIME, self::COLUM_ID, self::COLUM_WIDTH, self::COLUM_HEIGHT));
		
//		echo "<pre>";
//		print_r($this->idArticle);									 
//		echo "</pre>";									 
											 
		if(is_string($this->idArticle) OR is_numeric($this->idArticle)){
			$sqlSelect = $sqlSelect->where(self::COLUM_ID_ARTICLE." = ".$this->idArticle)
								   ->where(self::COLUM_ID_ITEM." = ".$this->getModule()->getId());
		} else if(is_array($this->idArticle) AND !empty($this->idArticle)){
			foreach ($this->idArticle as $id => $itemId){
				//Pokud je zadáno asociativní pole bez id items
				if(is_string($itemId) OR is_numeric($itemId)){
					$sqlSelect = $sqlSelect->where(self::COLUM_ID_ARTICLE." = ".$itemId." AND ".self::COLUM_ID_ITEM." = ".$this->getModule()->getId(), "OR");
				} else if(is_array($itemId) AND !empty($itemId)){
					$whereString = self::COLUM_ID_ITEM." = ".$id." AND (";
					foreach ($itemId as $idArticle) {
						$whereString.= self::COLUM_ID_ARTICLE." = ".$idArticle." OR ";
					}
					$whereString = substr($whereString, 0, strlen($whereString)-4).")";
					$sqlSelect = $sqlSelect->where($whereString, "OR");
				} else if($itemId == null){
					$sqlSelect = $sqlSelect->where(self::COLUM_ID_ITEM." = ".$id, "OR");
				}
			}
					
		} else if (empty($this->idArticle)){
			$sqlSelect = $sqlSelect->where(self::COLUM_ID_ITEM." = ".$this->getModule()->getId());
		}
											 
		$sqlSelect = $sqlSelect->order(self::COLUM_TIME, "DESC");
		
//		echo $sqlSelect;
		
		$this->imagesArray = $this->getDb()->fetchAssoc($sqlSelect);
		
		$this->getDb()->getNumRows() != null ? $this->numberOfReturnRows = $this->getDb()->getNumRows() : $this->numberOfReturnRows = 0;
		
		if ($this->imagesArray != null) {
//			projití pole a dolnění odkazů
			foreach ($this->imagesArray as $key => $file) {
				$this->imagesArray[$key][self::COLUM_LINK_TO_SHOW] = $this->getLinks()->getMainWebDir().MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/'.$file[self::COLUM_FILE];
//				$this->imagesArray[$key][self::COLUM_LINK_TO_SHOW] = './'.MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/'.$file[self::COLUM_FILE];
				$this->imagesArray[$key][self::COLUM_LINK_TO_SMALL] = './'.MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/'.self::USERIMAGES_SMALL_FILES_DIR.'/'.$file[self::COLUM_FILE];
			}
		}
		
//		echo "<pre>";
//		print_r($this->imagesArray);									 
//		echo "</pre>";	
	}
	
//	/**
//	 * Metoda nastavuje id článku
//	 * @param integer -- id článku
//	 */
//	public function setIdArticle($idArticle) {
//		$this->idArticle = $idArticle;
//	}
	
	
	/**
	 * Metoda obstarává přiřazení proměných do šablony
	 *
	 */
	protected function assignTpl(){
		$this->toTpl("USERMIAGES_LABEL_NAME", _("Nahrané obrázky"));
		$this->toTpl("BUTTON_USERIMAGE_DELETE", _("Smazat"));
		$this->toTpl("BUTTON_USERIMAGE_SEND", _("Přidat"));
		$this->toTpl("IMAGE_NAME", _("Název souboru"));
		$this->toTpl("IMAGE_SIZE_NAME", _("Velikost souboru"));
		$this->toTpl("IMAGE_DIMENSIONS", _("Rozměry"));
		$this->toTpl("IMAGE_DIMENSIONS_WIDTH", _("Šířka"));
		$this->toTpl("IMAGE_DIMENSIONS_HEIGHT", _("Výška"));
		$this->toTpl("IMAGE_LINK_TO_SHOW_NAME", _("Odkaz pro zobrazení"));
		$this->toTpl("CONFIRM_MESAGE_DELETE", _("Opravdu smazat obrázek"));

		self::$otherNumberOfReturnRows[$this->idUserImages] = $this->numberOfReturnRows;
		$this->toTpl("USERIMAGES_NUM_ROWS", self::$otherNumberOfReturnRows);
		$this->toTpl("USERIMAGESFILES_ID", $this->idUserImages);
		
//		if(!empty(self::$otherChanges)){
//			$array = self::$otherChanges;
//		}
		
		$this->toTplJSPlugin(new SubmitForm());
		$this->toTplJSPlugin(new LightBox());
		
		self::$otherImagesArray[$this->idUserImages] = $this->imagesArray;
		
		$this->toTpl("USERIMAGES_ARRAY",self::$otherImagesArray);
		

		
	}

}
?>