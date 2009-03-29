<?php
/** 
 * Třída EPluginu pro přidávání souborů ke článku(stránce).
 * Třída umožňuje přidávat, mazat a sparvovat soubory přidané ke článku. Třída
 * teké obstarává jejich výpis pomocí vlastní šablony to článku. 
 * 
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE3.9.4 $Revision: $
 * @author        $Author: $ $Date:$
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída Epluginu pro práci se soubory, přikládanými do stránky
 * 
 * @todo          dodělat mazání všech souborů z článku a mazání souborů z více článků
 */

class UserFilesEplugin extends Eplugin {
	/**
	 * Název primární šablony s posunovátky
	 * @var string
	 */
	protected $templateFile = 'userfiles.tpl';

	/**
	 * Název databázové tabulky se změnama
	 * @var string
	 */
	const DB_TABLE_USER_FILES = 'userfiles';
	
	/**
	 * Název adresáře kde se ukládají soubory
	 * @var string
	 */
	const USER_FILES_DIR = 'userfiles';
	
	/**
	 * Názvy sloupců v db
	 * @var string
	 */
	const COLUM_ID				= 'id_file';
	const COLUM_ID_USER			= 'id_user';
	const COLUM_ID_ITEM			= 'id_item';
	const COLUM_ID_ARTICLE		= 'id_article';
	const COLUM_FILE			= 'file';
	const COLUM_SIZE			= 'size';
	const COLUM_TIME			= 'time';

	const COLUM_LINK_TO_SHOW	= 'link_show';
	const COLUM_LINK_TO_DOWNLOAD= 'link_download';
	
	/**
	 * Názvy formulářových prvků
	 * @var string
	 */
	const FORM_PREFIX = 'userfiles_';
	const FORM_NEW_FILE = 'new_file';
	const FORM_BUTTON_SEND = 'send_file';
	const FORM_USERFILE_ID = 'id';
	const FORM_BUTTON_DELETE = 'delete';
	
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
	private $filesArray = array();
	
	/**
	 * Pole se všemi soubory (obsahuje všechny soubory ze všech instancí pluginu)
	 * @static 
	 * @var array
	 */
	private static $otherFilesArray = array();
	
	/**
	 * ID šablony
	 * @var integer
	 */
	private $idUserFiles = '1';
	
	/**
	 * Počet vrácených záznamů
	 * @var integer
	 */
	private $numberOfReturnRows = 0;
	
	/**
	 * Proměná obsahuje počet všech souborů ze všech ionstancí
	 * @static 
	 * @var integer
	 */
	private static $otherNumberOfReturnRows = array();
	
	/**
	 * Metoda inicializace, je spuštěna pří vytvoření objektu  
	 *
	 */
	protected function init(){}
	
	/**
	 * Metoda je spuštěna při načítání souborů
	 * @param string $fileName -- název souboru
    * @param array $fileparams -- pole s parametry pluginu
	 */
	public function runOnlyEplugin($fileName, $fileParams = null) {}
	
	/**
	 * Metoda nastaví id šablony pro výpis
	 * @param ineger -- id šablony (jakékoliv)
	 */
	public function setIdTpl($id) {
		$this->idUserFiles = $id;
	}
	
	/**
	 * Metoda nastavuje id článku pro který se budou ukládát soubory
	 * @param integer -- id článku
	 */
	public function setIdArticle($idArticle){
		$this->idArticle = $idArticle;
		$this->checkSendFile();
		$this->checkDeleteFile();
		$this->getFilesFromDb();
	}
	
	/**
	 * Metoda kontroluje, jestli byl odeslán soubor, pokud ano je soubor nahrán a uložen do db
	 */
	private function checkSendFile() {
      $sendForm = new Form(self::FORM_PREFIX);
      $sendForm->crSubmit(self::FORM_BUTTON_SEND)
         ->crInputFile(self::FORM_NEW_FILE, true);
      if($sendForm->checkForm()){
         $file = $sendForm->getValue(self::FORM_NEW_FILE);
         try {
            $file->copy(AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'.self::USER_FILES_DIR.'/');
            $sqlInsert = $this->getDb()->insert()->table(self::DB_TABLE_USER_FILES)
               ->colums(self::COLUM_ID_ARTICLE, self::COLUM_ID_ITEM,
                  self::COLUM_ID_USER, self::COLUM_FILE,
                  self::COLUM_SIZE, self::COLUM_TIME)
               ->values($this->idArticle, $this->getModule()->getId(),
                  $this->getRights()->getAuth()->getUserId(), $file->getName(),
                  $file->getFileSize(), time());
            $this->getDb()->query($sqlInsert);
            $this->infoMsg()->addMessage(_('Soubor byl uložen'));
            $this->getLinks()->reload();
         } catch (Exception $e) {
            $this->errMsg()->addMessage(_('Soubor se nepodařilo uložit'));
            new CoreErrors ($e);
         }
      }
	}
	
	/**
	 * Metoda odstraní zadaný soubor
	 * 
	 * @param integer -- id souboru
	 */
	private function deleteUserFile($id) {
//		načtení informací o souboru 
		$sqlSelect = $this->getDb()->select()->table(self::DB_TABLE_USER_FILES, 'files')
         ->colums(self::COLUM_FILE)
			->where(self::COLUM_ID, $id);
								   
		$file = $this->getDb()->fetchObject($sqlSelect);
      try {
         if($file == null){
            throw new RuntimeException(_('zadaný soubor již neexistuje'), 1);
         }
         $dir = AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'.self::USER_FILES_DIR.'/';
         $file = new File($file->{self::COLUM_FILE}, $dir);
         //	vymazání z db
         $sqlDel = $this->getDb()->delete()->table(self::DB_TABLE_USER_FILES)
            ->where(self::COLUM_ID, $id);

         if(!$file->remove()){
            throw new RuntimeException(_('Soubor se nepodařilo vymazat z filesystému'),2);
         }
         if(!$this->getDb()->query($sqlDel)){
            throw new DBException(_('Soubor se nepodařilo smazat z databáze'), 3);
         }
         $this->infoMsg()->addMessage(_('Soubor byl smazán'));
         $this->getLinks()->reload();
      } catch (RuntimeException $e) {
         new CoreErrors($e);
      } catch (DBException $e){
         new CoreErrors($e);
      }
	}

   /**
    * Metoda vymaže všechny uživatelské obrázky
    */
   public function deleteAllFiles() {
//      $images = $this->getFilesFromDb($this->getModule()->getId(), $this->idArticle);
//
//      $dir = AppCore::getAppWebDir().DIRECTORY_SEPARATOR.MAIN_DATA_DIR.DIRECTORY_SEPARATOR
//            .self::USERIMAGES_FILES_DIR.DIRECTORY_SEPARATOR;
//         $dirSmall = AppCore::getAppWebDir().DIRECTORY_SEPARATOR.MAIN_DATA_DIR.DIRECTORY_SEPARATOR
//            .self::USERIMAGES_FILES_DIR.DIRECTORY_SEPARATOR.self::USERIMAGES_SMALL_FILES_DIR.DIRECTORY_SEPARATOR;
//
//      try {
//         foreach ($images as $key => $img) {
//            $file = new File($key, $dir);
//            $file->remove();
//            $file = new File($key, $dirSmall);
//            $file->remove();
//         }
//         // vymaz z db
//         $sqlDel = $this->getDb()->delete()->from(self::DB_TABLE_USER_IMAGES)
//         ->where(self::COLUM_ID_ITEM.' = '.$this->getModule()->getId())
//         ->where(self::COLUM_ID_ARTICLE.' = '.$this->idArticle);
//         $this->getDb()->query($sqlDel);
//      } catch (Exception $e) {
//         new CoreException(_('Nepodařilo se smazat uživatelské obrázky chyba ').$e->getMessage(),3);
//      }

   }

	/**
	 * Metoda kontroluje, jestli nebyl soubor smazán
	 */
	private function checkDeleteFile() {
      $form = new Form(self::FORM_PREFIX);
      $form->crSubmit(self::FORM_BUTTON_DELETE)
      ->crInputHidden(self::FORM_USERFILE_ID, true);
      if($form->checkForm()){
         $this->deleteUserFile($form->getValue(self::FORM_USERFILE_ID));
      }
	}
	
	/**
	 * Metoda načte data z db
	 */
	private function getFilesFromDb() {
		$sqlSelect = $this->getDb()->select()
      ->table(self::DB_TABLE_USER_FILES, 'files')
      ->colums(array(self::COLUM_FILE, self::COLUM_SIZE, self::COLUM_TIME, self::COLUM_ID));
		
		if(is_string($this->idArticle) OR is_numeric($this->idArticle)){
			$sqlSelect = $sqlSelect->where(self::COLUM_ID_ARTICLE, $this->idArticle)
								   ->where(self::COLUM_ID_ITEM, $this->getModule()->getId());
		} else if(is_array($this->idArticle) AND !empty($this->idArticle)){
			foreach ($this->idArticle as $id => $itemId){
				//Pokud je zadáno asociativní pole bez id items
				if(is_string($itemId) OR is_numeric($itemId)){
					$sqlSelect->where(self::COLUM_ID_ARTICLE,$itemId)
               ->where(self::COLUM_ID_ITEM, $this->getModule()->getId(), Db::COND_OPERATOR_OR);
				} else if(is_array($itemId) AND !empty($itemId)){
               // REFACTORING
//					$whereString = self::COLUM_ID_ITEM." = ".$id." AND (";
//					foreach ($itemId as $idArticle) {
//						$whereString.= self::COLUM_ID_ARTICLE, $idArticle, " OR ";
//					}
//					$whereString = substr($whereString, 0, strlen($whereString)-4).")";
//               $sqlSelect = $sqlSelect->where($whereString, Db::COND_OPERATOR_OR);
				} else if($itemId == null){
					$sqlSelect = $sqlSelect->where(self::COLUM_ID_ITEM, $id, Db::COND_OPERATOR_OR);
				}
			}
		} else if (empty($this->idArticle)){
			$sqlSelect = $sqlSelect->where(self::COLUM_ID_ITEM, $this->getModule()->getId());
		}
      $sqlSelect = $sqlSelect->order(self::COLUM_TIME, Db::ORDER_DESC);
		$this->filesArray = $this->getDb()->fetchAll($sqlSelect);
		$this->getDb()->getNumRows() != null ? $this->numberOfReturnRows = $this->getDb()->getNumRows() : $this->numberOfReturnRows = 0;

      if ($this->filesArray != null) {
      //	pprojití pole a dolnění odkazů
			foreach ($this->filesArray as $key => $file) {
            $this->filesArray[$key][self::COLUM_LINK_TO_SHOW] = Links::getMainWebDir().MAIN_DATA_DIR.'/'.self::USER_FILES_DIR.'/'.$file[self::COLUM_FILE];
				$this->filesArray[$key][self::COLUM_LINK_TO_DOWNLOAD] = $this->getLinks()->getLinkToDownloadFile('./'.MAIN_DATA_DIR.'/'.self::USER_FILES_DIR.'/', $file[self::COLUM_FILE]);
			}
		}
	}
	
	/**
	 * Metoda obstarává přiřazení proměných do šablony
	 *
	 */
	protected function assignTpl(){
		$this->toTpl("USERFILES_LABEL_NAME", _("Nahrané soubory"));
		$this->toTpl("BUTTON_USERFILE_DELETE", _("Smazat"));
		$this->toTpl("BUTTON_USERFILE_SEND", _("Přidat"));
		$this->toTpl("FILE_NAME", _("Název souboru"));
		$this->toTpl("FILE_SIZE_NAME", _("Velikost souboru"));
		$this->toTpl("FILE_LINK_TO_SHOW_NAME", _("Odkaz pro zobrazení"));
		$this->toTpl("FILE_LINK_TO_DOWNLOAD_NAME", _("Odkaz pro stažení"));
		$this->toTpl("CONFIRM_MESAGE_DELETE", _("Opravdu smazat soubor"));

		self::$otherNumberOfReturnRows[$this->idUserFiles] = $this->numberOfReturnRows;
		$this->toTpl("USERFILES_NUM_ROWS", self::$otherNumberOfReturnRows);
		$this->toTpl("USERFILES_ID", $this->idUserFiles);
		
//		if(!empty(self::$otherChanges)){
//			$array = self::$otherChanges;
//		}
		
		$this->toTplJSPlugin(new SubmitForm());
		self::$otherFilesArray[$this->idUserFiles] = $this->filesArray;
		$this->toTpl("USERFILES_ARRAY",self::$otherFilesArray);
	}
}
?>