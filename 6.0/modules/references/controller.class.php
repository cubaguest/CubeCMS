<?php
class References_Controller extends Controller {
   /**
    * Parametry s velikostí malého obrázku
    */
   const PARAM_IMAGE_WIDTH = 'width';
   const PARAM_IMAGE_HEIGHT = 'height';
   const PARAM_SMALL_IMAGE_WIDTH = 'smallwidth';
   const PARAM_SMALL_IMAGE_HEIGHT = 'smallheight';

   /**
    * Adresíř s malými obrázky
    */
   const IMAGES_SMALL_DIR = 'small';
   
  /**
   * Názvy formůlářových prvků
   * @var string
   */
   const FORM_PREFIX = 'reference_';
   const FORM_PREFIX_PHOTO = 'photo_';
   const FORM_BUTTON_SEND = 'send';
   const FORM_BUTTON_EDIT = 'edit';
   const FORM_BUTTON_DELETE = 'delete';
   const FORM_INPUT_ID = 'id';
   const FORM_INPUT_NAME = 'name';
   const FORM_INPUT_TEXT = 'text';
   const FORM_INPUT_LABEL = 'label';
   const FORM_INPUT_FILE = 'file';

  /**
   * Kontroler pro zobrazení novinek
   */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
   }

   public function showController(){
      $this->checkReadableRights();

      $referDetail = new References_Model_Detail($this->sys());
      $this->view()->reference = $referDetail->getReferenceDetailSelLang($this->sys()->article()->getArticle());

      if($this->rights()->isWritable()){
         $form = new Form(self::FORM_PREFIX);
         $form->crInputHidden(self::FORM_INPUT_ID, true, 'is_numeric')
         ->crSubmit(self::FORM_BUTTON_DELETE);

         if($form->checkForm()){
            if(!$referDetail->deleteReference($form->getValue(self::FORM_INPUT_ID))){
               throw new UnexpectedValueException($this->_('Referenci se nepodařilo smazat, zřejmně špatně přenesené id'), 3);
            }
            $this->infoMsg()->addMessage($this->_('Reference byla smazána'));
            $this->link()->article()->action()->rmParam()->reload();
         }
      }
   }

   /**
   * Kontroler pro přidání novinky
   */
   public function addReferenceController(){
      $this->checkWritebleRights();

      $addRefForm = new Form();
      $addRefForm->setPrefix(self::FORM_PREFIX);

      $addRefForm->crInputText(self::FORM_INPUT_NAME, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //        Pokud byl odeslán formulář
      if($addRefForm->checkForm()){
         $refDetail = new References_Model_Detail($this->sys());
         if(!$refDetail->saveNewReference($addRefForm->getValue(self::FORM_INPUT_NAME),
               $addRefForm->getValue(self::FORM_INPUT_TEXT))){
            throw new UnexpectedValueException($this->_('Referenci se nepodařilo uložit, chyba při ukládání.'), 1);
         }
         $this->infoMsg()->addMessage($this->_('Reference byla uložena'));
         $this->link()->article()->action()->rmParam()->reload();
      }

      $this->view()->errorItems = $addRefForm->getErrorItems();
   }

  /**
   * controller pro úpravu novinky
   */
   public function editReferenceTextController() {
      $this->checkWritebleRights();

      $refEditForm = new Form(self::FORM_PREFIX);

      $refEditForm->crInputText(self::FORM_INPUT_NAME, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //        Pokud byl odeslán formulář
      if($refEditForm->checkForm()){
         $articleModel = new References_Model_Detail($this->sys());
         if(!$articleModel->saveEditReference($refEditForm->getValue(self::FORM_INPUT_NAME),
               $refEditForm->getValue(self::FORM_INPUT_TEXT), $this->getArticle())){
            throw new UnexpectedValueException($this->_('Referenci se nepodařilo uložit, chyba při ukládání.'), 2);
         }
         $this->infoMsg()->addMessage($this->_('Reference byla uložena'));
         $this->link()->action()->reload();
      }

      //    Data do šablony
      $this->view()->errorItems = $refEditForm->getErrorItems();
   }

   /**
    * Kontroler pro úpravu fotek
    */
   public function editReferencePhotosController() {
      $this->checkWritebleRights();

      $addPhotoForm = new Form(self::FORM_PREFIX.self::FORM_PREFIX_PHOTO);
      $addPhotoForm->crSubmit(self::FORM_BUTTON_SEND)
      ->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crInputFile(self::FORM_INPUT_FILE, true);

      // přidání fotky
      if($addPhotoForm->checkForm()){
         $image = new File_Image($addPhotoForm->getValue(self::FORM_INPUT_FILE));
         if($image->isImage()) {
            $this->savePhoto($image, $addPhotoForm->getValue(self::FORM_INPUT_LABEL),
               $this->sys()->article());
            $this->infoMsg()->addMessage($this->_('Fotka byla uložena'));
            $this->getLink()->reload();
         } 
      }

   }

   public function addPhotoAjaxController(Ajax $ajaxObj) {
      $this->checkWritebleRights();
      $addPhotoForm = new Form(self::FORM_PREFIX.self::FORM_PREFIX_PHOTO);
      $addPhotoForm->crSubmit(self::FORM_BUTTON_SEND)
      ->crInputFile(self::FORM_INPUT_FILE, true);
      // přidání fotky
      $this->view()->lastId = false;
      $this->view()->result = $this->_('Neuloženo');
      if($addPhotoForm->checkForm()){
         $image = new File_Image($addPhotoForm->getValue(self::FORM_INPUT_FILE));
         if($image->isImage(false)) {
            $this->view()->lastId = $this->savePhoto($image, null,$ajaxObj->getParam('idArticle'));
            $this->view()->result = $this->_('Uloženo');
         } else {
            $this->view()->result = $this->_('Nebyl zadán obrázek');
         }
      }
      sleep(1);
      return true;
   }

   private function savePhoto(File_Image $image, $label, $idArticle) {
      $refPhotoModel = new References_Model_Photos($this->sys());
      // Uložení obrázků
      // malý
      $image->saveImage($this->sys()->module()->getDir()->getDataDir()
          .self::IMAGES_SMALL_DIR.DIRECTORY_SEPARATOR,
          $this->sys()->module()->getParam(self::PARAM_SMALL_IMAGE_WIDTH, 150),
          $this->sys()->module()->getParam(self::PARAM_SMALL_IMAGE_HEIGHT, 75));
      // velký
      $image->saveImage($this->sys()->module()->getDir()->getDataDir(),
          $this->sys()->module()->getParam(self::PARAM_IMAGE_WIDTH, 800),
          $this->sys()->module()->getParam(self::PARAM_IMAGE_HEIGHT, 600));
      // do modelu
      if(!$refPhotoModel->saveNewPhoto($label, $image->getName(), $idArticle)) {
         throw new ModuleException($this->_('Chyba při ukládání fotky'), 1);
      }
      return $refPhotoModel->getLastInsertedId();
   }

   public function deletePhotoAjaxController(Ajax $ajaxObj) {
      $this->checkWritebleRights();
      if ($this->deletePhoto($ajaxObj->getParam('reference_photo_id'))) {
         print (true);
      } else {
         print (false);
      }
      return true;
   }

   private function deletePhoto($id) {
      $model = new References_Model_Photos($this->sys());
      $photo = $model->getPhotoAllLangs($id);

      $file = new File($photo[References_Model_Photos::COLUMN_FILE], $this->sys()->module()
         ->getDir()->getDataDir());
      $file->remove();
      $file = new File($photo[References_Model_Photos::COLUMN_FILE], $this->sys()->module()
         ->getDir()->getDataDir().self::IMAGES_SMALL_DIR);
      $file->remove();

      return $model->deletePhoto($id);
   }

   public function savePhotoLabelAjaxController() {
      $this->checkWritebleRights();
      $editPhotoForm = new Form(self::FORM_PREFIX.self::FORM_PREFIX_PHOTO);

      $editPhotoForm->crSubmit(self::FORM_BUTTON_SEND)
      ->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crInputHidden(self::FORM_INPUT_ID, true);

      if($editPhotoForm->checkForm()){
         $photoModel = new References_Model_Photos($this->sys());
         if($photoModel->saveEditPhoto($editPhotoForm->getValue(self::FORM_INPUT_LABEL),
               $editPhotoForm->getValue(self::FORM_INPUT_ID))){
            print ($this->_('Uloženo'));
         } else {
            throw new Exception($this->_("Popis fotky se nepodařilo uložit"));
         }
      }
      sleep(1);
   }

}
?>