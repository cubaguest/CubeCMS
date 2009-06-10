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
    * Název šablony s listem souborů
    */
   const TPL_FILE = 'userfiles.phtml';

   /**
    * Název souboru s listem obrázků
    */
   const IMAGES_LIST_JS_FILE = 'imageslist.js';

   /**
    * Název souboru s listem odkazů
    */
   const LINKS_LIST_JS_FILE = 'linkslist.js';

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
    * Název adresáře s miniaturami obrázků
    */
   const USER_FILES_SMALL_IMAGES_DIR = 'small';

   /**
    * Šířka miniatury
    * @var integer
    */
   const IMAGE_THUMBNAIL_WIDTH = 150;

   /**
    * Výška miniatury
    * @var integer
    */
   const IMAGE_THUMBNAIL_HEIGHT = 150;

   /**
    * Názvy sloupců v db
    * @var string
    */
   const COLUM_ID				= 'id_file';
   const COLUM_ID_USER		= 'id_user';
   const COLUM_ID_ITEM		= 'id_item';
   const COLUM_ID_ARTICLE	= 'id_article';
   const COLUM_FILE			= 'file';
   const COLUM_SIZE			= 'size';
   const COLUM_TIME			= 'time';
   const COLUM_TYPE			= 'type';
   const COLUM_WIDTH			= 'width';
   const COLUM_HEIGHT		= 'height';

   const COLUM_LINK_TO_SHOW	= 'link_show';
   const COLUM_LINK_TO_DOWNLOAD= 'link_download';
   const COLUM_LINK_TO_SMALL	= 'link_small';
   /**
    * Typy souborů které rozeznává
    */
   const FILE_TYPE_OTHER = 'file';
   const FILE_TYPE_IMAGE = 'image';
   const FILE_TYPE_FLASH = 'flash';

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
    * Formáty pro vrácení seznamu obrázků
    * @var string
    */
   const FILE_LIST_FORMAT_TINYMCE = 'tinymce';
   const FILE_LIST_FORMAT_ARRAY = 'array';

   /**
    * $_PARAM parametr s id itemu a článku
    * @var string
    */
   const PARAM_URL_ID_ITEM = 'idI';
   const PARAM_URL_ID_ARTICLE = 'idA';
   const PARAM_URL_LIST_TYPE = 'type';

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
    * Název parametru s id Item
    */
   const AJAX_PARAM_IDITEM = 'idItem';


   /**
    * Id článku u kterého se zpracovávají soubory
    * @var integer
    */
   private $idArticle = null;

   /**
    * Pole se soubory
    * @var array
    */
   private $filesArray = array();

   /**
    * Počet vrácených záznamů
    * @var integer
    */
   private $numberOfReturnRows = 0;

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    *
    */
   protected function init($params = null){
      if($this->sys()->article() != null){
         $this->idArticle = $this->sys()->article()->getArticle();
      }
   }

   /**
    * Metoda spustí eplugin
    * @param mixed $params -- parametry epluginu (pokud je třeba)
    */
   protected function run($params = null){
      $this->checkSendFile();
      $this->checkDeleteFile();
      $this->getFilesFromDb();
   }

   /**
    * Metoda je spuštěna při načítání souborů
    * @param string $file -- název souboru
    * @param array $params -- pole s parametry pluginu
    */
   public function runOnlyEplugin($file, $params = null) {
      if($file == self::IMAGES_LIST_JS_FILE) {
         $file = new JsFile($file, true);
         $file->setParams($params);
         $idArticle = null;
         if($file->getParam(self::PARAM_URL_ID_ARTICLE)){
            $idArticle = rawurldecode($file->getParam(self::PARAM_URL_ID_ARTICLE));
         }
         $array = $this->getImagesList($file->getParam(self::PARAM_URL_ID_ITEM),$idArticle);
         switch ($file->getParam(self::PARAM_URL_LIST_TYPE)) {
            case self::FILE_LIST_FORMAT_TINYMCE:
               TinyMce::sendListImages($array);
               break;
            default:
               break;
         }
      } else if($file == self::LINKS_LIST_JS_FILE){
         $file = new JsFile($file, true);
         $file->setParams($params);
         $idArticle = null;
         if($file->getParam(self::PARAM_URL_ID_ARTICLE)){
            $idArticle = rawurldecode($file->getParam(self::PARAM_URL_ID_ARTICLE));
         }
         $array = $this->getLinksList($file->getParam(self::PARAM_URL_ID_ITEM),$idArticle);
         switch ($file->getParam(self::PARAM_URL_LIST_TYPE)) {
            case self::FILE_LIST_FORMAT_TINYMCE:
               TinyMce::sendListLinks($array);
               break;
            default:
               break;
         }
      }
      return false;
   }

   /**
    * Metoda načte seznam obrázků
    * @param integer -- id item
    * @param integer -- (option)id článku u kterého byla změna provedena
    */
   public function getImagesList($idItem, $idArticle = null) {
      $sqlSelect = $this->getDb()->select()->table(self::DB_TABLE_USER_FILES,'files')
      ->colums(self::COLUM_FILE)
      ->where(self::COLUM_ID_ITEM, $idItem)
      ->where(self::COLUM_TYPE, self::FILE_TYPE_IMAGE);
      if($idArticle != null){
         $sqlSelect->where(self::COLUM_ID_ARTICLE, $idArticle);
      }
      //	vložení záznamu
      $images = $this->getDb()->fetchAll($sqlSelect);
      $returnArray = array();
      //	Převedení na normální pole kde klíč je název souboru a hodota je cesta
      if(!empty($images)){
         foreach ($images as $image) {
            $returnArray[$image[self::COLUM_FILE]] = $this->getLinks()->getMainWebDir()
               .MAIN_DATA_DIR.'/'.self::USER_FILES_DIR.'/'.$image[self::COLUM_FILE];
         }
      }
      return $returnArray;
   }

   /**
    * Metoda načte seznam odkazů k souborům
    * @param integer -- id item
    * @param integer -- (option)id článku u kterého byla změna provedena
    */
   public function getLinksList($idItem, $idArticle = null) {
      $sqlSelect = $this->getDb()->select()->table(self::DB_TABLE_USER_FILES,'files')
      ->colums(self::COLUM_FILE)
      ->where(self::COLUM_ID_ITEM, $idItem);
      if($idArticle != null){
         $sqlSelect->where(self::COLUM_ID_ARTICLE, $idArticle);
      }
      //	vložení záznamu
      $links = $this->getDb()->fetchAll($sqlSelect);
      $returnArray = array();
      //	Převedení na normální pole kde klíč je název souboru a hodota je cesta
      if(!empty($links)){
         foreach ($links as $file) {
            $returnArray[$file[self::COLUM_FILE]] = Links::getMainWebDir()
            .MAIN_DATA_DIR.'/'.self::USER_FILES_DIR.'/'.$file[self::COLUM_FILE];
            $returnArray[$file[self::COLUM_FILE]._('-Stažení')] = $this->getLinks()
            ->getLinkToDownloadFile('./'.MAIN_DATA_DIR.'/'.self::USER_FILES_DIR.'/', $file[self::COLUM_FILE]);
         }
      }
      return $returnArray;
   }

   /**
    * Metoda nastaví id šablony pro výpis
    * @param ineger -- id šablony (jakékoliv)
    */
   public function setIdTpl($id) {
      $this->idUserFiles = $id;
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

            //zjištění typu souboru (obrázek, soubor, flash)
            $image = new ImageFile($file);
            $flash = new FlashFile($file);

            $width = 0;
            $height = 0;
            
            if($image->isImage(false)){
               // uložení malého obrázku pro náhledy
               $image->saveImage(AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'
                  .self::USER_FILES_DIR.'/'.self::USER_FILES_SMALL_IMAGES_DIR.'/',
                  self::IMAGE_THUMBNAIL_WIDTH, self::IMAGE_THUMBNAIL_HEIGHT);

               $fileType = self::FILE_TYPE_IMAGE;
               $width = $image->getOriginalWidth();
               $height = $image->getOriginalHeight();
            } else if($flash->isFlash(false)){
               $fileType = self::FILE_TYPE_FLASH;
               $width = $flash->getWidth();
               $height = $flash->getHeight();
            } else {
               $fileType = self::FILE_TYPE_OTHER;
            }

            $sqlInsert = $this->getDb()->insert()->table(self::DB_TABLE_USER_FILES)
            ->colums(self::COLUM_ID_ARTICLE, self::COLUM_ID_ITEM,
               self::COLUM_ID_USER, self::COLUM_FILE,
               self::COLUM_TYPE, self::COLUM_WIDTH, self::COLUM_HEIGHT,
               self::COLUM_SIZE, self::COLUM_TIME)
            ->values($this->idArticle, $this->module()->getId(),
               $this->sys()->rights()->getAuth()->getUserId(), $file->getName(),
               $fileType, $width, $height,
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
      ->colums(array(self::COLUM_FILE, self::COLUM_TYPE))
      ->where(self::COLUM_ID, $id);

      $dbFile = $this->getDb()->fetchObject($sqlSelect);
      try {
         if($dbFile == null){
            throw new RuntimeException(_('Zadaný soubor již neexistuje'), 1);
         }
         $dir = AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'.self::USER_FILES_DIR.'/';
         $file = new File($dbFile->{self::COLUM_FILE}, $dir);

         // pokud je obrázek smažeme i miniaturu
         if($dbFile->{self::COLUM_TYPE} == self::FILE_TYPE_IMAGE){
            $smallFile = new File($dbFile->{self::COLUM_FILE}, $dir.self::USER_FILES_SMALL_IMAGES_DIR.'/');
            if(!$smallFile->remove()){
               throw new RuntimeException(_('Soubor se nepodařilo vymazat z filesystému'),2);
            }
         }

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
         if(!UrlRequest::isAjaxRequest()){
            $this->getLinks()->reload();
         }
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

      $this->getFilesFromDb();
      //
      $dir = AppCore::getAppWebDir().DIRECTORY_SEPARATOR.MAIN_DATA_DIR.DIRECTORY_SEPARATOR
      .self::USER_FILES_DIR.DIRECTORY_SEPARATOR;

      try {
         foreach ($this->filesArray as $ufile) {
            $file = new File($ufile[self::COLUM_FILE], $dir);
            $file->remove();
            // pokud je obrázek maže se i miniatura
            if($ufile[self::COLUM_TYPE] == self::FILE_TYPE_IMAGE){
               $file = new File($ufile[self::COLUM_FILE], $dir.self::USER_FILES_SMALL_IMAGES_DIR.'/');
               $file->remove();
            }
         }
         // vymaz z db
         $sqlDel = $this->getDb()->delete()->table(self::DB_TABLE_USER_FILES)
         ->where(self::COLUM_ID_ITEM,$this->module()->getId())
         ->where(self::COLUM_ID_ARTICLE,$this->idArticle);
         if(!$this->getDb()->query($sqlDel)){
            throw new UnexpectedValueException(_('Chyba při mazání uživatelských souborů'), 4);
         }
      } catch (Exception $e) {
         new CoreErrors($e);
      }
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
    * @param int $idItem -- (option) id item pokud není je použito id modulu
    */
   private function getFilesFromDb($idItem = null) {
      if($idItem == null){
         $idItem = $this->module()->getId();
      }

      $sqlSelect = $this->getDb()->select()
      ->table(self::DB_TABLE_USER_FILES, 'files')
//      ->colums(array(self::COLUM_FILE, self::COLUM_SIZE, self::COLUM_TIME, self::COLUM_ID));
      ->colums(Db::COLUMN_ALL);

      if(is_string($this->idArticle) OR is_numeric($this->idArticle)){
         $sqlSelect = $sqlSelect->where(self::COLUM_ID_ARTICLE, $this->idArticle)
         ->where(self::COLUM_ID_ITEM, $idItem);
      } else if(is_array($this->idArticle) AND !empty($this->idArticle)){
         foreach ($this->idArticle as $id => $itemId){
            //Pokud je zadáno asociativní pole bez id items
            if(is_string($itemId) OR is_numeric($itemId)){
               $sqlSelect->where(self::COLUM_ID_ARTICLE,$itemId)
               ->where(self::COLUM_ID_ITEM, $this->module()->getId(), Db::COND_OPERATOR_OR);
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
         $sqlSelect = $sqlSelect->where(self::COLUM_ID_ITEM, $idItem);
      }
      $sqlSelect = $sqlSelect->order(self::COLUM_TIME, Db::ORDER_DESC);
      $this->filesArray = $this->getDb()->fetchAll($sqlSelect);
      $this->getDb()->getNumRows() != null ? $this->numberOfReturnRows = $this->getDb()->getNumRows() : $this->numberOfReturnRows = 0;

      if ($this->filesArray != null) {
         //	pprojití pole a dolnění odkazů
         foreach ($this->filesArray as $key => $file) {
            $this->filesArray[$key][self::COLUM_LINK_TO_SHOW] = Links::getMainWebDir()
               .MAIN_DATA_DIR.'/'.self::USER_FILES_DIR.'/'.$file[self::COLUM_FILE];
            $this->filesArray[$key][self::COLUM_LINK_TO_DOWNLOAD] = $this->getLinks()
               ->getLinkToDownloadFile('./'.MAIN_DATA_DIR.'/'.self::USER_FILES_DIR.'/', $file[self::COLUM_FILE]);
            if($file[self::COLUM_TYPE] == self::FILE_TYPE_IMAGE){
               $this->filesArray[$key][self::COLUM_LINK_TO_SMALL] = MAIN_DATA_DIR
                  .'/'.self::USER_FILES_DIR.'/'.self::USER_FILES_SMALL_IMAGES_DIR.'/'.$file[self::COLUM_FILE];
            }
         }
      }
   }

   /**
    * Metoda volaná přes ajax pro přidání souboru
    * @param Ajax $ajaxObj -- objekt ajax, poskytuje základní parametry předané
    * požadavkem
    */
   public function addFileAjax($ajaxObj) {
      $sendForm = new Form(self::FORM_PREFIX);
      $sendForm->crSubmit(self::FORM_BUTTON_SEND)
      ->crInputFile(self::FORM_NEW_FILE, true)
      ->crInputHidden('idItem', true, 'is_numeric')
      ->crInputHidden('idArticle', false, 'is_numeric');
      if($sendForm->checkForm()){
         $file = $sendForm->getValue(self::FORM_NEW_FILE);
         try {
            $file->copy(AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'.self::USER_FILES_DIR.'/');

            //zjištění typu souboru (obrázek, soubor, flash)
            $image = new ImageFile($file);
            $flash = new FlashFile($file);

            $width = 0;
            $height = 0;

            if($image->isImage(false)){
               // uložení malého obrázku pro náhledy
               $image->saveImage(AppCore::getAppWebDir().'/'.MAIN_DATA_DIR.'/'
                  .self::USER_FILES_DIR.'/'.self::USER_FILES_SMALL_IMAGES_DIR.'/',
                  self::IMAGE_THUMBNAIL_WIDTH, self::IMAGE_THUMBNAIL_HEIGHT);
               
               $fileType = self::FILE_TYPE_IMAGE;
               $width = $image->getOriginalWidth();
               $height = $image->getOriginalHeight();
            } else if($flash->isFlash(false)){
               $fileType = self::FILE_TYPE_FLASH;
               $width = $flash->getWidth();
               $height = $flash->getHeight();
            } else {
               $fileType = self::FILE_TYPE_OTHER;
            }

            $sqlInsert = $this->getDb()->insert()->table(self::DB_TABLE_USER_FILES)
            ->colums(self::COLUM_ID_ARTICLE, self::COLUM_ID_ITEM,
               self::COLUM_ID_USER,self::COLUM_FILE,
               self::COLUM_TYPE, self::COLUM_WIDTH, self::COLUM_HEIGHT,
               self::COLUM_SIZE, self::COLUM_TIME)
            ->values($sendForm->getValue('idArticle'), $sendForm->getValue('idItem'),
               AppCore::getAuth()->getUserId(),$file->getName(),
               $fileType, $width, $height,
               $file->getFileSize(), time());
            $this->getDb()->query($sqlInsert);
            echo _('Soubor byl uložen');
//            $this->getLinks()->reload();
         } catch (Exception $e) {
            echo _('Soubor se nepodařilo uložit');
            new CoreErrors ($e);
         }
      }
   }

   /**
    * Metoda vrací seznam souborů volaním přes ajax
    * @param Ajax $ajaxOb -- objekt vvolaného ajaxu
    */
   public function getFilesAjax($ajaxOb) {
      if($ajaxOb->getAjaxParam('idArticle')){
         $this->idArticle = $ajaxOb->getAjaxParam('idArticle');
      }
      $this->getFilesFromDb($ajaxOb->getAjaxParam('idItem'));

      header('Cache-Control: no-cache, must-revalidate');
      //header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      header('Content-type: application/json');

      echo '{"files":[';
      foreach ($this->filesArray as $file) {
         echo json_encode($file).',';
      }
      echo ']}';
   }

   /**
    * Metoda pro mazání souboru ajaxem
    * @param Ajax $ajaxObj -- objekt ajaxu
    */
   public function deleteFileAjax($ajaxObj){
      $this->deleteUserFile($ajaxObj->getAjaxParam('idFile'));
   }

   /**
    * Metoda obstarává přiřazení proměných do šablony
    *
    */
   protected function view(){
      $this->template()->addTplFile(self::TPL_FILE, true);
      $jQueryPlugin = new JQuery();
      $jQueryPlugin->addPluginAjaxUploadFile();      
      $this->template()->addJsPlugin($jQueryPlugin);

      $this->template()->numRows = $this->numberOfReturnRows;

      
      $this->template()->filesArray = $this->filesArray;

      $ajaxLink = new AjaxLink($this);

      $this->template()->ajaxUserfileFile = $ajaxLink->getFile();
      $this->template()->ajaxAddFileParams = $ajaxLink->getParams();
      $this->template()->idItem = $this->module()->getId();
      if($this->idArticle != null){
         $this->template()->idArticle = $this->idArticle;
      } else {
         $this->template()->idArticle = $this->module()->getId();
      }
   }

   /**
    * Metoda přejmenuje id článku na nové (při vytváření článků u kterých není id)
    * @param integer $oldId -- staré id článku (nejčastěji id uživatele)
    * @param integer $newId -- nové id (nejčastěji id nového článku)
    * @return boolean -- pokudakce proběhla
    */
   public function renameIdArticle($oldId,$newId) {
      return $this->getDb()->query($this->getDb()->update()
         ->table(self::DB_TABLE_USER_FILES, 'files')
         ->set(array(self::COLUM_ID_ARTICLE => $newId))
         ->where(self::COLUM_ID_ARTICLE, $oldId));
   }

   /**
    * Metoda vrací odkaz na soubor se seznamem obrázků
    * @param string -- (option) typ v jakém se mají obrázky formátu vrátit (default: self::FILE_IMAGES_FORMAT_TINYMCE)
    * @return mixed -- seznam obrázků
    * @todo dodělat tak by se daly předávat i celá pole v url parametrech, a jiné druhy souborů
    */
   public function getImagesListLink($type = self::FILE_LIST_FORMAT_TINYMCE) {
      switch ($type) {
         case self::FILE_LIST_FORMAT_TINYMCE:
            $file = new JsFile(self::IMAGES_LIST_JS_FILE, true);
            $file->setParam(self::PARAM_URL_ID_ITEM, $this->module()->getId());
            $file->setParam(self::PARAM_URL_LIST_TYPE, self::FILE_LIST_FORMAT_TINYMCE);
            if(is_numeric($this->idArticle)){
               $file->setParam(self::PARAM_URL_ID_ARTICLE, $this->idArticle);
            }
            return $this->getFileLink($file);
            break;
         default:
            $link = Links::getMainWebDir().'eplugin'.strtolower($this->getEpluginName()).'.js';
            break;
      }
      return false;
   }

   /**
    * Metoda vrací odkaz na soubor se seznamem linků k souborům a obrázkům
    * @param string -- (option)typ v jakém se mají obrázky formátu vrátit (default: self::FILE_IMAGES_FORMAT_TINYMCE)
    * @return mixed -- seznam obrázků
    * @todo dodělat tak by se daly předávat i celá pole v url parametrech, a jiné druhy souborů
    */
   public function getLinksListLink($type = self::FILE_LIST_FORMAT_TINYMCE) {
      switch ($type) {
         case self::FILE_LIST_FORMAT_TINYMCE:
            $file = new JsFile(self::LINKS_LIST_JS_FILE, true);
            $file->setParam(self::PARAM_URL_ID_ITEM, $this->module()->getId());
            $file->setParam(self::PARAM_URL_LIST_TYPE, self::FILE_LIST_FORMAT_TINYMCE);
            if(is_numeric($this->idArticle)){
               $file->setParam(self::PARAM_URL_ID_ARTICLE, $this->idArticle);
            }
            return $this->getFileLink($file);
            break;
         default:
            $link = Links::getMainWebDir().'eplugin'.strtolower($this->getEpluginName()).'.js';
            break;
      }
      return false;
   }
}
?>