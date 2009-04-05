<?php
/**  
 * Třída EPluginu pro práci s obrázky ve stránce.
 * Třída slouží pro práci s obrázky v textech, jejich přidávání, mazání, zobrazování
 * a správu. Obsahuje také svou vlastní šablonu pro jednodušší integraci k modulům.
 * Je částečne napojena na TinyMce JsPlugin pro generování listu obrázků.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE3.9.4 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @uses 			používá také třídu TinyMce tinymce.class.php
 * @todo          dodělat mazání všech souborů z článku a mazání souborů z více článků
 */

class UserImagesEplugin extends Eplugin {
   /**
    * Název souboru s listem obrázků
    */
   const IMAGES_LIST_JS_FILE = 'imageslist.js';

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

   /**
    * Formáty pro vrácení seznamu obrázků
    * @var string
    */
   const FILE_IMAGES_FORMAT_TINYMCE = 'tinymce';
   const FILE_IMAGES_FORMAT_ARRAY = 'array';

   /**
    * $_PARAM parametr s id itemu a článku
    * @var string
    */
   const PARAM_URL_ID_ITEM = 'idI';
   const PARAM_URL_ID_ARTICLE = 'idA';
   const PARAM_URL_IMAGES_LIST_TYPE = 'type';

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
    * Název primární šablony s posunovátky
    * @var string
    */
   protected $templateFile = 'userimages.tpl';

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

   /**
    * Pole s ostatními obrázky v jiných modulech
    * @var array
    */
   private static $otherImagesArray = array();

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

   /**
    * Pole s počty ostatních obrázků v jiných modulech
    * @var array
    */
   private static $otherNumberOfReturnRows = array();

   /**
    * Metoda inicializace, je spuštěna pří vytvoření objektu
    */
   protected function init(){}

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
   public function setIdArticle($idArticle = null){
      $this->idArticle = $idArticle;
      $this->checkSendImages();
      $this->checkDeleteImage();
      $this->getImagesFromDb();
      return $this;
   }

   /**
    * Metoda kontroluje, jestli byl odeslán soubor, pokud ano je soubor nahrán a uložen do db
    */
   private function checkSendImages() {
      $form = new Form(self::FORM_PREFIX);
      $form->crSubmit(self::FORM_BUTTON_SEND)
      ->crInputFile(self::FORM_NEW_FILE, true);
      if($form->checkForm()){
         $file = $form->getValue(self::FORM_NEW_FILE);
         $imageFile = new ImageFile($file);
         if($imageFile->isImage()){
            try {
               $dir = AppCore::getAppWebDir().DIRECTORY_SEPARATOR.MAIN_DATA_DIR
               .DIRECTORY_SEPARATOR.self::USERIMAGES_FILES_DIR.DIRECTORY_SEPARATOR;
               // kopírování originálu
               $imageFile->copy($dir);
               // uložení změnšeniny
               if(!$imageFile->saveImage($dir.self::USERIMAGES_SMALL_FILES_DIR.DIRECTORY_SEPARATOR,
                     self::THUMBNAIL_WIDTH, self::THUMBNAIL_HEIGHT)){
                  throw new RuntimeException(_('Obrázek se nepodařilo uložit do adresáře'),1);
               }
               $sqlInsert = $this->getDb()->insert()->table(self::DB_TABLE_USER_IMAGES)
               ->colums(self::COLUM_ID_ARTICLE, self::COLUM_ID_ITEM,
                  self::COLUM_ID_USER, self::COLUM_FILE,	self::COLUM_WIDTH,
                  self::COLUM_HEIGHT, self::COLUM_SIZE, self::COLUM_TIME)
               ->values($this->idArticle,	$this->getModule()->getId(),
                  $this->getRights()->getAuth()->getUserId(),$imageFile->getName(),
                  $imageFile->getOriginalWidth(), $imageFile->getOriginalHeight(),
                  $imageFile->getFileSize(), time());
               if(!$this->getDb()->query($sqlInsert)){
                  throw new Exception(_('Obrázek se nepodařilo uložit'),2);
               }
               $this->infoMsg()->addMessage(_('Obrázek byl uložen'));
               $this->getLinks()->reload();
            } catch (Exception $e) {
               new CoreErrors($e);
            } catch (RuntimeException $e){
               new CoreErrors($e);
            } catch (ImageException $e){
               new CoreErrors($e);
            }
         }
      }
   }

   /**
    * Metoda odstraní zadaný soubor
    *
    * @param integer -- id souboru
    */
   private function deleteUserImage($id) {
      //		načtení informací o souboru
      $sqlSelect = $this->getDb()->select()->table(self::DB_TABLE_USER_IMAGES,'images')
      ->colums(self::COLUM_FILE)
      ->where(self::COLUM_ID, $id);
      $file = $this->getDb()->fetchObject($sqlSelect);
      try {
         if($file == null){
            throw new RuntimeException(_('Požadovaný obrázek neexistuje'),3);
         }
         $dir = AppCore::getAppWebDir().DIRECTORY_SEPARATOR.MAIN_DATA_DIR.DIRECTORY_SEPARATOR
         .self::USERIMAGES_FILES_DIR.DIRECTORY_SEPARATOR;
         $dirSmall = AppCore::getAppWebDir().DIRECTORY_SEPARATOR.MAIN_DATA_DIR.DIRECTORY_SEPARATOR
         .self::USERIMAGES_FILES_DIR.DIRECTORY_SEPARATOR.self::USERIMAGES_SMALL_FILES_DIR.DIRECTORY_SEPARATOR;
         $imageFile = new File($file->{self::COLUM_FILE}, $dir);
         $imageFileSmall = new File($file->{self::COLUM_FILE}, $dirSmall);
         if(!$imageFile->remove()){
            throw new RuntimeException(_('Soubor se nepodařilo smazat z filesystému'),4);
         }
         if(!$imageFileSmall->remove()){
            throw new RuntimeException(_('Soubor se nepodařilo smazat z filesystému'),4);
         }
         //            vymazání z db
         $sqlDel = $this->getDb()->delete()->table(self::DB_TABLE_USER_IMAGES)
         ->where(self::COLUM_ID,$id);
         $this->getDb()->query($sqlDel);
         $this->infoMsg()->addMessage(_('Obrázek byl smazán'));
         if(!UrlRequest::isAjaxRequest()){
            $this->getLinks()->reload();
         }
      } catch (Exception $e) {
         new CoreErrors($e);
      }
   }

   /**
    * Metoda vymaže všechny uživatelské obrázky
    * @param integer $idArticle -- id článku u kterého se mají obrázky vymazat
    */
   public function deleteAllImages($idArticle = null) {
      $this->idArticle = $idArticle;

      $this->getImagesFromDb();
      //
      $dir = AppCore::getAppWebDir().DIRECTORY_SEPARATOR.MAIN_DATA_DIR.DIRECTORY_SEPARATOR
      .self::USERIMAGES_FILES_DIR.DIRECTORY_SEPARATOR;
      $dirSmall = AppCore::getAppWebDir().DIRECTORY_SEPARATOR.MAIN_DATA_DIR.DIRECTORY_SEPARATOR
      .self::USERIMAGES_FILES_DIR.DIRECTORY_SEPARATOR.self::USERIMAGES_SMALL_FILES_DIR.DIRECTORY_SEPARATOR;

      try {
         foreach ($this->imagesArray as $uImage) {
            $file = new File($uImage[self::COLUM_FILE], $dir);
            $file->remove();
            $file = new File($uImage[self::COLUM_FILE], $dirSmall);
            $file->remove();
         }
         // vymaz z db
         $sqlDel = $this->getDb()->delete()->table(self::DB_TABLE_USER_IMAGES)
         ->where(self::COLUM_ID_ITEM,$this->getModule()->getId())
         ->where(self::COLUM_ID_ARTICLE,$this->idArticle);
         if(!$this->getDb()->query($sqlDel)){
            throw new UnexpectedValueException(_('Chyba při mazání uživatelských obrázků'), 4);
         }
      } catch (Exception $e) {
         new CoreErrors($e);
      }
   }

   /**
    * Metoda kontroluje, jestli nebyl soubor smazán
    */
   private function checkDeleteImage() {
      $form = new Form(self::FORM_PREFIX);
      $form->crInputHidden(self::FORM_USERIMAGE_ID, true)
      ->crSubmit(self::FORM_BUTTON_DELETE);
      if($form->checkForm()){
         $this->deleteUserImage($form->getValue(self::FORM_USERIMAGE_ID));
      }
   }

   /**
    * Metoda načte seznam obrázků
    * @param integer -- id item
    * @param integer -- (option)id článku u kterého byla změna provedena
    */
   public function getImagesList($idItem, $idArticle = null) {
      $sqlSelect = $this->getDb()->select()->table(self::DB_TABLE_USER_IMAGES,'img')
      ->colums(self::COLUM_FILE)
      ->where(self::COLUM_ID_ITEM, $idItem);
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
               .MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/'.$image[self::COLUM_FILE];
         }
      }
      return $returnArray;
   }

   /**
    * Metoda načte data z db
    */
   private function getImagesFromDb($idItem = null) {
      if($idItem == null){
         $idItem = $this->getModule()->getId();
      }
      $sqlSelect = $this->getDb()->select()
      ->table(self::DB_TABLE_USER_IMAGES, 'images')
      ->colums(array(self::COLUM_FILE, self::COLUM_SIZE, self::COLUM_TIME, self::COLUM_ID,
            self::COLUM_WIDTH, self::COLUM_HEIGHT));
      $sqlSelect->where(self::COLUM_ID_ITEM, $idItem);
      if(is_numeric($this->idArticle)){
         $sqlSelect->where(self::COLUM_ID_ARTICLE, $this->idArticle);
      } else if(is_array($this->idArticle) AND !empty($this->idArticle)){
         foreach ($this->idArticle as $id => $itemId){
            //Pokud je zadáno asociativní pole bez id items
            if(is_string($itemId) OR is_numeric($itemId)){
               $sqlSelect->where(self::COLUM_ID_ARTICLE, $itemId)
               ->where(self::COLUM_ID_ITEM, $idItem, Db::COND_OPERATOR_OR);
            } else if(is_array($itemId) AND !empty($itemId)){
               // REFACTORING
               //					$whereString = self::COLUM_ID_ITEM." = ".$id." AND (";
               //					foreach ($itemId as $idArticle) {
               //						$whereString.= self::COLUM_ID_ARTICLE." = ".$idArticle." OR ";
               //					}
               //					$whereString = substr($whereString, 0, strlen($whereString)-4).")";
               //					$sqlSelect->where($whereString, Db::COND_OPERATOR_OR);
            } else if($itemId == null){
               $sqlSelect->where(self::COLUM_ID_ITEM, $id, Db::COND_OPERATOR_OR);
            }
         }
      }
      $sqlSelect->order(self::COLUM_TIME, Db::ORDER_DESC);
      $this->imagesArray = $this->getDb()->fetchAll($sqlSelect);
      $this->getDb()->getNumRows() != null ? $this->numberOfReturnRows = $this->getDb()->getNumRows() : $this->numberOfReturnRows = 0;

      if ($this->imagesArray != null) {
         //			projití pole a dolnění odkazů
         foreach ($this->imagesArray as $key => $file) {
            $this->imagesArray[$key][self::COLUM_LINK_TO_SHOW] = Links::getMainWebDir().MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/'.$file[self::COLUM_FILE];
            $this->imagesArray[$key][self::COLUM_LINK_TO_SMALL] = MAIN_DATA_DIR.'/'.self::USERIMAGES_FILES_DIR.'/'.self::USERIMAGES_SMALL_FILES_DIR.'/'.$file[self::COLUM_FILE];
         }
      }
   }

/**
    * Metoda volaná přes ajax pro přidání souboru
    * @param Ajax $ajaxObj -- objekt ajax, poskytuje základní parametry předané
    * požadavkem
    */
   public function addImageAjax($ajaxObj) {
      $form = new Form(self::FORM_PREFIX);
      $form->crSubmit(self::FORM_BUTTON_SEND)
      ->crInputFile(self::FORM_NEW_FILE, true)
      ->crInputHidden('idItem', true, 'is_numeric')
      ->crInputHidden('idArticle', false, 'is_numeric');
      if($form->checkForm()){
         $file = $form->getValue(self::FORM_NEW_FILE);
         $imageFile = new ImageFile($file);
         if($imageFile->isImage()){
            try {
               $dir = AppCore::getAppWebDir().DIRECTORY_SEPARATOR.MAIN_DATA_DIR
               .DIRECTORY_SEPARATOR.self::USERIMAGES_FILES_DIR.DIRECTORY_SEPARATOR;
               // kopírování originálu
               $imageFile->copy($dir);
               // uložení změnšeniny
               if(!$imageFile->saveImage($dir.self::USERIMAGES_SMALL_FILES_DIR.DIRECTORY_SEPARATOR,
                     self::THUMBNAIL_WIDTH, self::THUMBNAIL_HEIGHT)){
                  throw new RuntimeException(_('Obrázek se nepodařilo uložit do adresáře'),1);
               }
               $sqlInsert = $this->getDb()->insert()->table(self::DB_TABLE_USER_IMAGES)
               ->colums(self::COLUM_ID_ARTICLE, self::COLUM_ID_ITEM,
                  self::COLUM_ID_USER, self::COLUM_FILE,	self::COLUM_WIDTH,
                  self::COLUM_HEIGHT, self::COLUM_SIZE, self::COLUM_TIME)
               ->values($form->getValue('idArticle'),	$form->getValue('idItem'),
                  AppCore::getAuth()->getUserId(),$imageFile->getName(),
                  $imageFile->getOriginalWidth(), $imageFile->getOriginalHeight(),
                  $imageFile->getFileSize(), time());
               if(!$this->getDb()->query($sqlInsert)){
                  throw new Exception(_('Obrázek se nepodařilo uložit'),2);
               }
               $this->infoMsg()->addMessage(_('Obrázek byl uložen'));
            } catch (Exception $e) {
               new CoreErrors($e);
            }
         }
      }
   }

   /**
    * Metoda vrací seznam obrázků volaním přes ajax a vypisuje JSON
    * @param Ajax $ajaxOb -- objekt vvolaného ajaxu
    */
   public function getImagesAjax($ajaxOb) {
      if($ajaxOb->getAjaxParam('idArticle')){
         $this->idArticle = $ajaxOb->getAjaxParam('idArticle');
      }
      $this->getImagesFromDb($ajaxOb->getAjaxParam('idItem'));

      header('Cache-Control: no-cache, must-revalidate');
      //header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
      header('Content-type: application/json');

      echo '{"images":[';
      foreach ($this->imagesArray as $file) {
         echo json_encode($file).',';
      }
      echo ']}';
   }

   /**
    * Metoda pro mazání souboru ajaxem
    * @param Ajax $ajaxObj -- objekt ajaxu
    */
   public function deleteImageAjax($ajaxObj){
      $this->deleteUserImage($ajaxObj->getAjaxParam('idImage'));
   }

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
      $this->toTpl("CONFIRM_MESAGE_DELETE_IMAGE", _("Opravdu smazat obrázek"));
      // tady je to kvůli více šablonám na stránce
      self::$otherNumberOfReturnRows[$this->idUserImages] = $this->numberOfReturnRows;
      $this->toTpl("USERIMAGES_NUM_ROWS", self::$otherNumberOfReturnRows);
      $this->toTpl("USERIMAGES_ID", $this->idUserImages);
      self::$otherImagesArray[$this->idUserImages] = $this->imagesArray;
      $this->toTpl("USERIMAGES_ARRAY",self::$otherImagesArray);

      $jQueryPlugin = new JQuery();
      $jQueryPlugin->addPluginAjaxUploadFile();
      $this->toTplJSPlugin($jQueryPlugin);
      // AJAX
      $ajaxLink = new AjaxLink($this);
      $this->toTpl("AJAX_USERIMAGE_FILE",$ajaxLink->getFile());
      $this->toTpl("ID_ITEM", $this->getModule()->getId());
      if($this->idArticle != null){
         $this->toTpl("ID_ARTICLE", $this->idArticle);
      }
      $this->toTpl("UPLOAD_IMAGE",_('Nahrát obrázek'));
   }

   /**
    * Metoda je spuštěna pokud se generuje soubor pro výstup (list obrázků)
    * @todo dodělat generování také do jiných typů souborů
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
         switch ($file->getParam(self::PARAM_URL_IMAGES_LIST_TYPE)) {
            case self::FILE_IMAGES_FORMAT_TINYMCE:
               $data = TinyMce::generateListImages($array);
               header("Content-Length: " . strlen($data));
               header("Content-type: application/x-javascript");
               echo $data;
               exit();
               break;
            default:
               break;
         }
      }
      return false;
   }

   /**
    * Metoda vrací odkaz na soubor se seznamem obrázků
    * @param string -- typ v jakém se mají obrázky formátu vrátit
    * @return mixed -- seznam obrázků
    * @todo dodělat tak by se daly předávat i celá pole v url parametrech, a jiné druhy souborů
    */
   public function getImagesListLink($type) {
      switch ($type) {
         case self::FILE_IMAGES_FORMAT_TINYMCE:
            $file = new JsFile(self::IMAGES_LIST_JS_FILE, true);
            $file->setParam(self::PARAM_URL_ID_ITEM, $this->getModule()->getId());
            $file->setParam(self::PARAM_URL_IMAGES_LIST_TYPE, self::FILE_IMAGES_FORMAT_TINYMCE);
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
    * Metoda přejmenuje id článku na nové (při vytváření článků u kterých není id)
    * @param integer $oldId -- staré id článku (nejčastěji id uživatele)
    * @param integer $newId -- nové id (nejčastěji id nového článku)
    * @return boolean -- pokudakce proběhla
    */
   public function renameIdArticle($oldId,$newId) {
      return $this->getDb()->query($this->getDb()->update()
         ->table(UserImagesEplugin::DB_TABLE_USER_IMAGES, 'images')
         ->set(array(self::COLUM_ID_ARTICLE => $newId))
         ->where(self::COLUM_ID_ARTICLE, $oldId));
   }
}
?>