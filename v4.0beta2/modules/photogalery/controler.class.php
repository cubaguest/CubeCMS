<?php
/**
 * Kontroler pro obsluhu fotogalerie
 *
 * Jedná se o dvouúrovňovou fotogalerii. Více v docs v modulech
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.5.0 $Revision: $
 * @author 		$Author: $ $Date:$
 *              $LastChangedBy: $ $LastChangedDate: $
 * @todo patří dodělat nahrávání souborů se zip archívu, dodělat zobrazovaní jedné
 * fotky bez pluginu, možná přidat hromadné mazání souborů
 * 
 * Last number CoreError: 6
 */

class PhotogaleryController extends Controller {
    /**
     * Název proměnné s parametrem pro scrollování
     * @var boolean
     */
   const GALERY_SCROLL_NUMBER = 'scroll';

    /**
     * Názvy formůlářových prvků
     * @var string
     */
   const FORM_GALERY_PREFIX = 'galery_';
   const FORM_GALERY_LABEL = 'label';
   const FORM_GALERY_TEXT = 'text';
   const FORM_GALERY_ID = 'id';
   const FORM_GALERY_EXIST_ID = 'exist_id';
   const FORM_GALERY_DATE = 'date';
   const FORM_PHOTO_PREFIX = 'photo_';
   const FORM_PHOTO_LABEL = 'label';
   const FORM_PHOTO_FILE = 'file';
   const FORM_PHOTO_ID = 'id';
   const FORM_BUTTON_SEND = 'send';
   const FORM_BUTTON_EDIT = 'edit';
   const FORM_BUTTON_DELETE = 'delete';

    /**
     * Názvev proměné s velikostí malých obrázků
     * @var integer
     */
   const PARAM_IMAGE_SMALL_WIDTH = 'smallwidth';
   const PARAM_IMAGE_SMALL_HEIGHT = 'smallheight';

   /**
    * Název parametru jestli mají být zpracovány střední fotky
    * @var string
    */
   const PARAM_IMAGE_IS_MEDIUM = 'ismedium';

   /**
     * Názvev proměné s velikostí středních obrázků
     * @var integer
     */
   const PARAM_IMAGE_MEDIUM_WIDTH = 'mediumwidth';
   const PARAM_IMAGE_MEDIUM_HEIGHT = 'mediumheight';

   /**
     * Názvev proměné s velikostí velkých obrázků
     * @var integer
     */
   const PARAM_IMAGE_WIDTH = 'width';
   const PARAM_IMAGE_HEIGHT = 'width';

   /**
    * název parametru jestli má být zobrazena fotka ve stránce nebo v jspluginu
    * @var string
    */
   const PARAM_PHOTO_IN_PAGE = 'photoinpage';

   /**
    * Název parametru s počtem galerií
    * @var string
    */
   const PARAM_NUM_GALERIES_LIST = 'scroll';

   /**
    * Název parametru s počtem fotek v listu galerie
    * @var string
    */
   const PARAM_NUM_PHOTOS_IN_GALERY_LIST = 'numphotosingalerylist';

    /**
     * názvy proměných s adresáři
     * @var string
     */
   const IMAGES_SMAIL_DIR = 'small';

    /**
     * Konstanta obsahuje název adresáře se střednímy
     * @var string
     */
   const IMAGES_MEDIUM_DIR = 'medium';

    /**
     * Konstanta s názvem odkládacího adresáře
     * @var string
     */
   const IMAGES_TEMP_DIR = 'temp';

    /**
     * Název pramateru pro scrolování fotek
     * @var string
     */
   const PHOTOS_SCROLL_URL_PARAM = 'photo';

   /**
    * Název linku pro zobrazení galerie
    * @var string
    */
   const LINK_TO_SHOW_GALERY = 'linkshow';

   /**
    * Název pole s fotkami
    * @var string
    */
   const PHOTOS_ARRAY_NAME = 'photos';

    /**
     * Kontroler pro zobrazení fotogalerii
     */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      //		Vytvoření modelu
      $listGaleriesM = new GaleryListModel();

      //		Scrolovátka
      $scroll = new ScrollEplugin();

      $scroll->setCountRecordsOnPage($this->getModule()->getParam(self::PARAM_NUM_GALERIES_LIST));

      $scroll->setCountAllRecords($listGaleriesM->getCountGaleries());

      // Výběr galerií
      $galeries = $listGaleriesM->getGaleryList($scroll->getStartRecord(), $scroll->getCountRecords());

      // projití galerií - doplnění odkazů pro zobrazení galerie
      // vytažení fotek pro každou galerii
      $photoDetailM = new GaleryPhotoDetailModel();
      foreach ($galeries as $key => $galery) {
         $galeries[$key][self::PHOTOS_ARRAY_NAME] = $photoDetailM->getPhotos(
            $galery[GaleryListModel::COLUMN_GALERY_ID],
            $this->getModule()->getParam(self::PARAM_NUM_PHOTOS_IN_GALERY_LIST));

         $galeries[$key][self::LINK_TO_SHOW_GALERY] = $this->getLink()->article(
            $galery[GaleryListModel::COLUMN_GALERY_LABEL],
            $galery[GaleryListModel::COLUMN_GALERY_ID]);
      }

      //      Předání dat do šablony
      $this->container()->addData('GALERIES_LIST', $galeries);
      $this->container()->addData('PHOTOS_SMALL_DIR', $this->getModule()->getDir()->getDataDir(true)
         .self::IMAGES_SMAIL_DIR.URL_SEPARATOR);

      $this->container()->addEplugin('scroll', $scroll);

      if($this->getRights()->isWritable()){
         //	vytvoření linků pro přidávání
         $this->container()->addLink('link_add_galery', $this->getLink()->action($this->getAction()->addGalery()));
      }
   }

    /**
     * Kontroler pro přidání galerie
     */
   public function addgaleryController() {
      $this->checkWritebleRights();

      $sendGaleryForm = new Form(self::FORM_GALERY_PREFIX);

      $sendGaleryForm->crSubmit(self::FORM_BUTTON_SEND)
      ->crInputText(self::FORM_GALERY_LABEL, false, true)
      ->crInputText(self::FORM_GALERY_TEXT, false, true)
      ->crInputText(self::FORM_GALERY_EXIST_ID)
      ->crInputDate(self::FORM_GALERY_DATE)
      ->crInputText(self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_LABEL, false, true)
      ->crInputFile(self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_FILE, true);

      // načtení uložených galerií
      $galeryListM = new GaleryListModel();
      $galsList = array();
      $galeries = $galeryListM->getGaleryList(0,0,true);
      foreach ($galeries as $gal) {
         $galsList[$gal[GaleryListModel::COLUMN_GALERY_ID]] = $gal[GaleryListModel::COLUMN_GALERY_LABEL];
      }
      $this->container()->addData('GALERIES_LIST', $galsList);

      if($sendGaleryForm->checkForm()){
         $galLabel = $sendGaleryForm->getValue(self::FORM_GALERY_LABEL);
         // pokud se ukládá nová galeri
         if($galLabel[Locale::getDefaultLang()] != ''){
            // uložení nové galerie
            $galeryDetailM = new GaleryDetailModel();
            $galeryId = $galeryDetailM->saveNewGalery($sendGaleryForm->getValue(self::FORM_GALERY_LABEL),
               $sendGaleryForm->getValue(self::FORM_GALERY_TEXT),
               $sendGaleryForm->getValue(self::FORM_GALERY_DATE),
               $this->getRights()->getAuth()->getUserId());
         }
         // použita existující galerie
         else {
            $galeryId = $sendGaleryForm->getValue(self::FORM_GALERY_EXIST_ID);
         }

         if($galeryId == false OR $galeryId == 0){
            new CoreException(_('Galerii se nepodařilo uložit, chyba při ukládání galerie'), 1);
         } else {
            // seznam nezpracovaných souborů
            $errorFiles = null;
            $someFile = false;

            $photoModel = new GaleryPhotoDetailModel();
            $photoLabels = $sendGaleryForm->getValue(self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_LABEL);

            // Procházení fotek
            foreach ($sendGaleryForm->getValue(self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_FILE) as $key => $file) {
               $imageFile = new ImageFile($file);
               $zipFile = new ZipFile($file);

               if($imageFile->isImage(false)){
                  // Uložení plné velikosti
                  $imageFile->saveImage($this->getModule()->getDir()->getDataDir(),
                     $this->getModule()->getParam(self::PARAM_IMAGE_WIDTH),
                     $this->getModule()->getParam(self::PARAM_IMAGE_HEIGHT));

                  // uložení miniatory
                  $imageFile->setCrop(true);
                  $imageFile->saveImage($this->getModule()->getDir()->getDataDir().self::IMAGES_SMAIL_DIR.DIRECTORY_SEPARATOR,
                     $this->getModule()->getParam(self::PARAM_IMAGE_SMALL_WIDTH),
                     $this->getModule()->getParam(self::PARAM_IMAGE_SMALL_HEIGHT));
                  $imageFile->setCrop(false);
                  // Pokud je nastaveno vytváření středních fotek
                  if($this->getModule()->getParam(self::PARAM_IMAGE_IS_MEDIUM)){
                     $imageFile->saveImage($this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_DIR.DIRECTORY_SEPARATOR,
                        $this->getModule()->getParam(self::PARAM_IMAGE_MEDIUM_WIDTH),
                        $this->getModule()->getParam(self::PARAM_IMAGE_MEDIUM_HEIGHT));
                  }

                  // Uloženi do db
                  if($photoModel->saveNewPhoto($photoLabels[$key]
                        , $imageFile->getName(), $galeryId)){
                     $someFile = true;
                  } else {
                     new CoreException(_('Chyba při ukládání fotky'),2);
                     break;
                  }
               } else if($zipFile->isZipFile()){
                  /**
                   * @todo dodělat
                   */
               } else {
                  $errorFiles .= '"'.$file->getName().'", ';
               }
            }

            if($someFile){
               $this->infoMsg()->addMessage(_('Fotky byly uloženy'));
               if($errorFiles != null){
                  $errorFiles = substr($errorFiles, 0, strlen($errorFiles)-2);
                  $this->errMsg()->addMessage(_('Soubory ').$errorFiles.
                     _(' nebyly zpracovány, protože systém zvolený typ souborů nepodporuje.'), true);
                  $this->getLink()->reload();
               } else {
                  $this->getLink()->action()->reload();
               }
            } else {
               $galeryDetail->deleteGalery($galeryId); // protože nebyla uložena ani jedna fotka
               $this->errMsg()->addMessage(_('Nebyla odeslána ani jediná fotka. Byly zadány korektní soubory?'));
            }
         }
      }

      $this->container()->addData('GALERY_DATA', $sendGaleryForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $sendGaleryForm->getErrorItems());

      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->action());
   }

   /**
     * Kontroler pro zobrazení fotogalerie
     */
   public function showController()
   {
      $this->checkReadableRights();

      // model fotek
      $photoDetailM = new GaleryPhotoDetailModel();

      // načtení všech fotek v galerii
      $photos = $photoDetailM->getPhotos($this->getArticle()->getArticle());

      // pokud je právo zápisu doplníme odkazy pro editaci ????????????
      // při editaci odkaz na přidání fotky
      if($this->getRights()->isWritable()){
         //	vytvoření linků pro přidávání
         $this->container()->addLink('link_add_photo', $this->getLink()->action($this->getAction()->addPhoto()));
         $this->container()->addLink('link_edit_galery', $this->getLink()->action($this->getAction()->editGalery()));
      }

      // vložení fotek do šablony
      $this->container()->addData('PHOTOS_LIST', $photos);

      // adresáře s fotkami
      $this->container()->addData('SMALL_DIR', $this->getModule()->getDir()->getDataDir(true)
         .self::IMAGES_SMAIL_DIR.URL_SEPARATOR);
      $this->container()->addData('MEDIUM_DIR', $this->getModule()->getDir()->getDataDir(true)
         .self::IMAGES_MEDIUM_DIR);
      $this->container()->addData('FULL_DIR', $this->getModule()->getDir()->getDataDir(true));

      //		Mazání zvolené fotky
      $deleteForm = new Form(self::FORM_PHOTO_PREFIX);
      $deleteForm->crSubmit(self::FORM_BUTTON_DELETE)
      ->crInputHidden(self::FORM_PHOTO_ID, 'is_numeric');
      if($deleteForm->checkForm()){
         if($this->deletePhotoById($deleteForm->getValue(self::FORM_PHOTO_ID))){
            $this->infoMsg()->addMessage(_('Fotka byla smazána'));
            $this->getLink()->reload();
         } else {
            new CoreException(_('Fotku se nepodařilo smazat'),2);
         }
      }

      // mazání galerie
      $deleteForm = new Form(self::FORM_GALERY_PREFIX);
      $deleteForm->crSubmit(self::FORM_BUTTON_DELETE)
      ->crInputHidden(self::FORM_GALERY_ID, true, 'is_numeric');

      //model galerie
      $galeryM = new GaleryDetailModel();

      if($deleteForm->checkForm()){
         // načtení všech fotek
         $photos = $photoDetailM->getPhotos($deleteForm->getValue(self::FORM_GALERY_ID));
         $error = false;
         // výmaz souborů
         foreach ($photos as $photo) {
            if($this->deletePhotoById($photo[GaleryPhotoDetailModel::COLUMN_PHOTOS_ID])){
            } else {
               $error = true;
               new CoreException(_('Fotku').' '.$photo[GaleryPhotoDetailModel::COLUMN_PHOTOS_FILE].' '
                  ._('se nepodařilo smazat'),3);
               break;
            }
         }

         if ($error != true AND $galeryM->deleteGalery($deleteForm->getValue(self::FORM_GALERY_ID))){
            $this->infoMsg()->addMessage(_('Fotka byla smazána'));
            $this->getLink()->action()->article()->reload();
         } else {
            new CoreException(_('Galerii se nepodařilo smazat'),4);
         }
      }

      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article());

      //Název a id galerie
      $this->container()->addData('GALERY_DATA', $galeryM->getGaleryDetail($this->getArticle()->getArticle()));
   }

   /**
    * Kontroler pro přidání fotky v galerii
    */
   public function addphotoController() {
      $this->checkWritebleRights();

      $sendPhotoForm = new Form(self::FORM_PHOTO_PREFIX);

      $sendPhotoForm->crSubmit(self::FORM_BUTTON_SEND)
      ->crInputText(self::FORM_PHOTO_LABEL, false, true)
      ->crInputFile(self::FORM_PHOTO_FILE, true);

      if($sendPhotoForm->checkForm()){
         // seznam nezpracovaných souborů
         $errorFiles = null;
         $someFile = false;

         $photoModel = new GaleryPhotoDetailModel();
         $photoLabels = $sendPhotoForm->getValue(self::FORM_PHOTO_LABEL);

         // Procházení fotek
         foreach ($sendPhotoForm->getValue(self::FORM_PHOTO_FILE) as $key => $file) {
            $imageFile = new ImageFile($file);
            $zipFile = new ZipFile($file);

            if($imageFile->isImage(false)){
               // Uložení plné velikosti
               $imageFile->saveImage($this->getModule()->getDir()->getDataDir(),
                  $this->getModule()->getParam(self::PARAM_IMAGE_WIDTH),
                  $this->getModule()->getParam(self::PARAM_IMAGE_HEIGHT));

               // uložení miniatory
               $imageFile->setCrop(true);
               $imageFile->saveImage($this->getModule()->getDir()->getDataDir().self::IMAGES_SMAIL_DIR.DIRECTORY_SEPARATOR,
                  $this->getModule()->getParam(self::PARAM_IMAGE_SMALL_WIDTH),
                  $this->getModule()->getParam(self::PARAM_IMAGE_SMALL_HEIGHT));
               $imageFile->setCrop(false);
               // Pokud je nastaveno vytváření středních fotek
               if($this->getModule()->getParam(self::PARAM_IMAGE_IS_MEDIUM)){
                  $imageFile->saveImage($this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_DIR.DIRECTORY_SEPARATOR,
                     $this->getModule()->getParam(self::PARAM_IMAGE_MEDIUM_WIDTH),
                     $this->getModule()->getParam(self::PARAM_IMAGE_MEDIUM_HEIGHT));
               }

               // Uloženi do db
               if($photoModel->saveNewPhoto($photoLabels[$key]
                     , $imageFile->getName(), $this->getArticle()->getArticle())){
                  $someFile = true;
               } else {
                  new CoreException(_('Chyba při ukládání fotky'),5);
                  break;
               }
            } else if($zipFile->isZipFile()){
                  /**
                   * @todo dodělat
                   */
            } else {
               $errorFiles .= '"'.$file->getName().'", ';
            }
         }

         if($someFile){
            $this->infoMsg()->addMessage(_('Fotky byly uloženy'));
            if($errorFiles != null){
               $errorFiles = substr($errorFiles, 0, strlen($errorFiles)-2);
               $this->errMsg()->addMessage(_('Soubory ').$errorFiles.
                  _(' nebyly zpracovány, protože systém zvolený typ souborů nepodporuje.'), true);
               $this->getLink()->reload();
            } else {
               $this->getLink()->action()->reload();
            }
         } else {
            $this->errMsg()->addMessage(_('Nebyla odeslána ani jediná fotka. Byly zadány korektní soubory?'));
         }
      }

      $this->container()->addData('PHOTO_DATA', $sendPhotoForm->getValues()); // ???????
      $this->container()->addData('ERROR_ITEMS', $sendPhotoForm->getErrorItems());

      // detail galerie
      $galeryDetail = new GaleryDetailModel();
      $galery = $galeryDetail->getGaleryDetail($this->getArticle()->getArticle());
      $this->container()->addData('GALERY_LABEL', $galery[GaleryDetailModel::COLUMN_GALERY_LABEL]);

      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->action());
   }

   /**
    * Metoda vymaže zadanou fotku
    * @param int $idPhoto -- id fotky
    */
   private function deletePhotoById($idPhoto){
      // načtení informací o fotce
      $photoM = new GaleryPhotoDetailModel();

      $photoInfo = $photoM->getPhotoById($idPhoto);

      $deleted = $deletedM = $deletedS = false;

      //výmaz souborů
      $photoFile = new File($photoInfo[GaleryPhotoDetailModel::COLUMN_PHOTOS_FILE],
         $this->getModule()->getDir()->getDataDir());
      $deleted = $photoFile->remove();

      if($this->getModule()->getParam(self::PARAM_IMAGE_IS_MEDIUM)){
         $photoFile = new File($photoInfo[GaleryPhotoDetailModel::COLUMN_PHOTOS_FILE],
            $this->getModule()->getDir()->getDataDir().self::IMAGES_MEDIUM_DIR.DIRECTORY_SEPARATOR);
         $deletedM = $photoFile->remove();
      } else {
         $deletedM = true;
      }

      $photoFile = new File($photoInfo[GaleryPhotoDetailModel::COLUMN_PHOTOS_FILE],
         $this->getModule()->getDir()->getDataDir().self::IMAGES_SMAIL_DIR.DIRECTORY_SEPARATOR);
      $deletedS = $photoFile->remove();

      if ($deleted AND $deletedM AND $deletedS AND $photoM->deletePhoto($idPhoto)){
         return true;
      }
      return false;
   }

   /**
     * Kontroler pro editaci galerie
     */
   public function editgaleryController() {
      $this->checkWritebleRights();

      $sendGaleryForm = new Form(self::FORM_GALERY_PREFIX);

      $sendGaleryForm->crSubmit(self::FORM_BUTTON_SEND)
      ->crInputText(self::FORM_GALERY_LABEL, true, true)
      ->crInputText(self::FORM_GALERY_TEXT, false, true)
      ->crInputDate(self::FORM_GALERY_DATE);

      //načtení předchozích částí galerie
      $galeryDetail = new GaleryDetailModel();

      $galeryDetail->getGaleryDetailAllLangs($this->getArticle()->getArticle());
      //      Nastavení hodnot prvků
      $sendGaleryForm->setValue(self::FORM_GALERY_LABEL, $galeryDetail->getLabelsLangs());
      $sendGaleryForm->setValue(self::FORM_GALERY_TEXT, $galeryDetail->getTextsLangs());
      $sendGaleryForm->setValue(self::FORM_GALERY_DATE, $galeryDetail->getDateAdd());

      //echo "<pre>vystup";
      //print_r($sendGaleryForm->getValues());
      //echo "</pre>";

      if($sendGaleryForm->checkForm()){
         // uložení nové galerie
         if($galeryDetail->saveEditGalery($sendGaleryForm->getValue(self::FORM_GALERY_LABEL),
               $sendGaleryForm->getValue(self::FORM_GALERY_TEXT), $this->getArticle(),
               $sendGaleryForm->getValue(self::FORM_GALERY_DATE))){
            $this->infoMsg()->addMessage(_('Galerie byla uložena'));
            $this->getLink()->action()->reload();
         } else {
            new CoreException(_('Galerii se nepodařilo uložit, chyba při ukládání.'), 6);
         }

      }

      $this->container()->addData('GALERY_DATA', $sendGaleryForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $sendGaleryForm->getErrorItems());

      // detail galerie
      $galery = $galeryDetail->getGaleryDetail($this->getArticle()->getArticle());
      $this->container()->addData('GALERY_LABEL', $galery[GaleryDetailModel::COLUMN_GALERY_LABEL]);

      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->action());
   }


   /**
    * Patří použít při zapnutí zobrazení středního sloupce
    */
   private function showPhotoControllerPrivate() {
      //			Změna viewru na zobrazení fotky
      $this->changeActionView('showPhoto');
      $galeryObj = new GaleryDetailModel();
      $galery = $galeryObj->getGaleryDetail($this->getArticle()->getArticle());
      $photoObj = new PhotoDetailModel();
      $scroll = new ScrollEplugin();
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
     * Kontroler pro úpravu fotky
     * @todo patří doiplmenetovat
     */
   public function editphotoController() {
      $this->checkWritebleRights();
      if(!isset($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID])){
         new CoreException(_('Žádné id fotografie nebylo přeneseno'), 77);
         return false;
      }
      if(!is_numeric($_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID])){
         new CoreException(_('Špatně zadané id fotografie'), 78);
         return false;
      }
      $idPhoto = (int)$_POST[self::FORM_PHOTO_PREFIX.self::FORM_PHOTO_ID];
      $photoObj = new PhotoDetailModel();
      $photo = $photoObj->getPhotoById($idPhoto);
      if(empty($photo)){
         new CoreException(_('Požadovaná fotka neexistuje'), 79);
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
}
?>