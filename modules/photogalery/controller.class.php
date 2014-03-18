<?php
/**
 * Kontroler pro obsluhu fotogalerie
 *
 * Jedná se o jednoúrovňovou fotogalerii s textem
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author 		$Author: $ $Date:$
 *              $LastChangedBy: $ $LastChangedDate: $
 */

class Photogalery_Controller extends Controller {
   const DIR_SMALL = 'small';
   const DIR_MEDIUM = 'medium';
   const DIR_ORIGINAL = 'original';

   const PARAM_TPL_MAIN = 'tplmain';
   const PARAM_EDITOR_TYPE = 'editor';
   const PARAM_SMALL_W = 'small_width';
   const PARAM_SMALL_H = 'small_height';
   const PARAM_SMALL_C = 'small_crop';
   const PARAM_MEDIUM_W = 'medium_width';
   const PARAM_MEDIUM_H = 'medium_height';
   const PARAM_MEDIUM_C = 'medium_crop';

   const SMALL_WIDTH = 140;
   const SMALL_HEIGHT = 140;

   const MEDIUM_WIDTH = VVE_DEFAULT_PHOTO_W;
   const MEDIUM_HEIGHT = VVE_DEFAULT_PHOTO_H;

   /**
    * Zpětný odkaz
    * @var Url_Link
    */
   public $linkBack = null;

   /**
    * jestli se má načíst text
    * @var bool
    * @deprecated
    */
   public $loadText = true;

   /**
    * id sub itemu
    * @var int
    */
   public $idItem = 0;

   /**
    * podadresář obrázků
    * @var string
    */
   public $subDir = null;

   public function init()
   {
      $this->idItem = $this->category()->getId();
      $this->actionsLabels = array(
          'main' => $this->tr('Hlavní stránka')
      );
   }

   /**
    * Kontroler pro zobrazení fotogalerii
    */
   public function mainController()
   {
      //		Kontrola práv
      $this->checkReadableRights();

      $this->view()->images = PhotoGalery_Model_Images::getImages($this->category()->getId(),
         $this->idItem);

      $this->view()->subdir = $this->subDir;
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $this->subDir);

      if($this->loadText == true){
         $this->view()->text = Text_Model::getText($this->category()->getId(), Text_Controller::TEXT_MAIN_KEY);
      }
   }

   public function edittextController()
   {
      $this->checkWritebleRights();

      $form = new Form("text_", true);
      $textarea = new Form_Element_TextArea('text', $this->tr("Text"));
      $textarea->setLangs();
      $form->addElement($textarea);

      $model = new Text_Model();
      $text = $model->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :subkey',
         array('idc' => $this->category()->getId(), 'subkey' => Text_Controller::TEXT_MAIN_KEY))
         ->record();
      if($text != false) {
         $form->text->setValues($text->{Text_Model::COLUMN_TEXT});
      }

      $submit = new Form_Element_SaveCancel('send');
      $form->addElement($submit);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->link()->route()->reload();
      }

      if($form->isValid()) {
         try {
            if($text == false){
               $text = $model->newRecord();
               $text->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
               $text->{Text_Model::COLUMN_SUBKEY} = Text_Controller::TEXT_MAIN_KEY;
            }
            $text->{Text_Model::COLUMN_TEXT} = $form->text->getValues();
            $text->{Text_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($form->text->getValues());
            $model->save($text);
            $this->infoMsg()->addMessage($this->tr('Text byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }

      // view
      $this->view()->template()->form = $form;
   }

   public function editphotosController(Url_Link $backLink = null)
   {
      $this->checkWritebleRights();

      $imagesM = new PhotoGalery_Model_Images();

      $addForm = $this->savePhotoForm();
      if($addForm->isValid()) {
         $this->infoMsg()->addMessage($this->tr('Obrázek byl uložen'));
         $this->link()->reload();
      }

      $editForm = new Form('editimage_');
      $imgName = new Form_Element_Text('name', $this->tr('Název'));
      $imgName->setLangs();
      $editForm->addElement($imgName);
      $imgOrd = new Form_Element_Text('ord', $this->tr('Pořadí'));
      $editForm->addElement($imgOrd);
      $imgRotation = new Form_Element_Select('rotate', $this->tr('Otočit'));
      $imgRotation->setOptions(array('0°' => 0, '90°' => 90, '180°' => 180, '270°' => 270));
      $imgRotation->setValues(0);
      $editForm->addElement($imgRotation);

      $imgDesc = new Form_Element_TextArea('desc', $this->tr('Popis'));
      $imgDesc->setLangs();
      $editForm->addElement($imgDesc);
      $imgDel = new Form_Element_Checkbox('delete', $this->tr('Smazat'));
      $editForm->addElement($imgDel);
      $imgId = new Form_Element_Hidden('id');
      $editForm->addElement($imgId);

      $eGoBack = new Form_Element_Checkbox('goBack', $this->tr('Zavřít po uložení'));
      $eGoBack->setValues(false);
      $editForm->addElement($eGoBack);

      $submit = new Form_Element_SaveCancel('save', array($this->tr('Uložit'), $this->tr('Zavřít')));
      $submit->setCancelConfirm(false);
      $editForm->addElement($submit);

      if($editForm->isSend() AND $editForm->save->getValues() == false){
         if($this->linkBack instanceof Url_Link){
            $this->linkBack->reload();
         } else if($backLink === null){
            $this->link()->route()->reload();
         } else {
            $backLink->reload();
         }
      }

      if($editForm->isValid()) {
         $names = $editForm->name->getValues();
         $descs = $editForm->desc->getValues();
         $orders = $editForm->ord->getValues();
         $ids = $editForm->id->getValues();

         foreach ($ids as $id) {
            try {
               if ($editForm->delete->getValues($id) === true) {
                  $this->deleteImage($id);
               } else {
                  // ukládají změny
                  $imagesM->saveImage($this->category()->getId(), $this->idItem, null,
                     $names[$id], $descs[$id], $orders[$id], $id);
                  // rotace pokud je
                  if ($editForm->rotate->getValues($id) != 0) {
                     $file = $imagesM->getImage($id)->{PhotoGalery_Model_Images::COLUMN_FILE};
                     /**
                      * Otočí se original a znovu se vytvoří miniatury
                   */
                     $image = new Filesystem_File_Image($file, $this->module()->getDataDir(false)
                           . $this->subDir . Photogalery_Controller::DIR_ORIGINAL);
                     $image->rotateImage($editForm->rotate->getValues($id));
                     $image->save();

                     $image = new Filesystem_File_Image($file, $this->module()->getDataDir() .
                           $this->subDir . self::DIR_ORIGINAL);
                     $image->saveAs($this->module()->getDataDir() . $this->subDir . self::DIR_SMALL,
                        $this->category()->getParam('small_width', VVE_IMAGE_THUMB_W),
                        $this->category()->getParam('small_height', VVE_IMAGE_THUMB_H),
                        $this->category()->getParam('small_crop', true));
                     $image->saveAs($this->module()->getDataDir() . $this->subDir . self::DIR_MEDIUM,
                        $this->category()->getParam('medium_width', self::MEDIUM_WIDTH),
                        $this->category()->getParam('medium_height', self::MEDIUM_HEIGHT),
                        $this->category()->getParam('medium_crop', false));

                     unset($image);
                  }
               }
            } catch (Exception $exc) {
               new CoreErrors($exc);
            }
         }

         $this->infoMsg()->addMessage($this->tr('Obrázky byly uloženy'));
         if($editForm->goBack->getValues() == true){
            if($this->linkBack instanceof Url_Link){
               $this->linkBack->reload();
            } else if($backLink === null){
               $this->link()->route()->reload();
            } else {
               $backLink->reload();
            }
         } else {
            $this->link()->reload();
         }
      }

      // odkaz na editaci obrázku
      $this->view()->template()->linkImageCrop = $this->link()->route('editphoto',
              array('id' => '%s'));

      $this->view()->template()->images = PhotoGalery_Model_Images::getImages($this->category()->getId(), $this->idItem);
      $this->view()->template()->addForm = $addForm;
      $this->view()->template()->editForm = $editForm;
      $this->view()->template()->idItem = $this->idItem;
      // adresáře k fotkám
      $this->view()->subdir = $this->subDir;
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $this->subDir);
   }

   public function sortphotosController(Url_Link $backLink = null)
   {
      $this->checkWritebleRights();

      $imagesM = new PhotoGalery_Model_Images();

      $editForm = new Form('sort_image_');
      $imgId = new Form_Element_Hidden('id');
      $imgId->setDimensional();
      $editForm->addElement($imgId);

      $submit = new Form_Element_SaveCancel('save', array($this->tr('Uložit'), $this->tr('Zavřít')));
      $submit->setCancelConfirm(false);
      $editForm->addElement($submit);

      if($editForm->isSend() AND $editForm->save->getValues() == false){
         if($this->linkBack instanceof Url_Link){
            $this->linkBack->rmParam('b')->reload();
         } else if($backLink === null){
            $this->link()->rmParam('b')->route()->reload();
         } else {
            $backLink->rmParam('b')->reload();
         }
      }

      if($editForm->isValid()) {
         $ids = $editForm->id->getValues();
         $allOk = true;
         foreach ($ids as $key => $id) {
            try {
               // ukládají změny
               $imagesM->setPosition($id, $key+1);
            } catch (PDOException $exc) {
               CoreErrors::addException($exc);
               $allOk = false;
            }
         }
         $this->infoMsg()->addMessage($this->tr('Obrázky byly uloženy'));
         if($allOk){
            if($this->linkBack instanceof Url_Link){
               $this->linkBack->rmParam('b')->reload();
            } else if($backLink === null){
               $this->link()->route()->rmParam('b')->reload();
            } else {
               $backLink->rmParam('b')->reload();
            }
         }
      }

      $this->view()->template()->images = PhotoGalery_Model_Images::getImages($this->category()->getId(), $this->idItem);
      $this->view()->template()->form = $editForm;
      $this->view()->template()->idArt = $this->idItem;
      // adresáře k fotkám
      $this->view()->subdir = $this->subDir;
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $this->subDir);
   }

   public function deletephotoController()
   {
      $this->checkWritebleRights();
      if(isset ($_POST['id'])){
         $this->deleteImage((int)$_POST['id']);
         $this->infoMsg()->addMessage($this->tr('Obrázek byl smazán'));
      }
   }

   /**
    * Metoda vymaže obrázek
    */
   private function deleteImage($idImage)
   {
      $imagesM = new PhotoGalery_Model_Images();
      $img = $imagesM->record($idImage);
      if(!$img){
         throw new BadRequestException($this->tr('Tento obrázek neexistuje'));
      }

      // smazání souborů
      $file = new Filesystem_File($img->{PhotoGalery_Model_Images::COLUMN_FILE},
              $this->module()->getDataDir().$this->subDir.self::DIR_SMALL.DIRECTORY_SEPARATOR);
      $file->delete();
      $file = new Filesystem_File($img->{PhotoGalery_Model_Images::COLUMN_FILE},
              $this->module()->getDataDir().$this->subDir.self::DIR_MEDIUM.DIRECTORY_SEPARATOR);
      $file->delete();
      $file = new Filesystem_File($img->{PhotoGalery_Model_Images::COLUMN_FILE},
              $this->module()->getDataDir().$this->subDir.self::DIR_ORIGINAL.DIRECTORY_SEPARATOR);
      $file->delete();
      // remove z db
      $imagesM->delete($img);
   }

   /**
    * Metoda vymaže obrázky od článku
    */
   public function deleteImages($idArt)
   {
      $imagesM = new PhotoGalery_Model_Images();
      $images = $imagesM->where(PhotoGalery_Model_Images::COLUMN_ID_CAT." = :idc AND ".PhotoGalery_Model_Images::COLUMN_ID_ART." = :ida",
         array('idc' => $this->category()->getId(), 'ida' => $idArt))->records();
      foreach ($images as $image) {
         try {
            $this->deleteImage($image->{PhotoGalery_Model_Images::COLUMN_ID});
         } catch (Exception $e) {
            $this->errMsg()->addMessage($e->getMessage(), true);
         }
      }
   }

   private function savePhotoForm()
   {
      $addForm = new Form('addimage_');
      $addFile = new Form_Element_File('image', $this->tr('Obrázek'));
      $addFile->addValidation(new Form_Validator_FileExtension(array('jpg', 'jpeg', 'png', 'gif')));
      $addFile->setUploadDir($this->module()->getDataDir()
              .$this->subDir.self::DIR_ORIGINAL.DIRECTORY_SEPARATOR);
      $addFile->setOverWrite(false);
      $addForm->addElement($addFile);

      $idItem = new Form_Element_Hidden('idItem');
      $idItem->setValues($this->idItem);
      $addForm->addElement($idItem);

      $addSubmit = new Form_Element_Submit('send',$this->tr('Odeslat'));
      $addForm->addElement($addSubmit);

      if($addForm->isValid()) {
         $image = new File_Image($addFile);
         $imgSmall = $image->copy($this->module()->getDataDir().$this->subDir.self::DIR_SMALL, true);
         $crop = $this->category()->getParam('small_crop', VVE_IMAGE_THUMB_CROP) == true ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO;
         $imgSmall->getData()
            ->resize($this->category()->getParam('small_width', VVE_IMAGE_THUMB_W),
                     $this->category()->getParam('small_height', VVE_IMAGE_THUMB_H), $crop)
            ->save();

         $crop = $this->category()->getParam('medium_crop', false) == true ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO;
         $imgMedium = $image->copy($this->module()->getDataDir().$this->subDir.self::DIR_MEDIUM, true);
         $imgMedium->getData()
            ->resize($this->category()->getParam('medium_width', VVE_DEFAULT_PHOTO_W),
                     $this->category()->getParam('medium_height', VVE_DEFAULT_PHOTO_H), $crop)
            ->save();
         // zjistíme pořadí
         $imagesM = new PhotoGalery_Model_Images();

         $count = (int)$imagesM->getCountImages($this->category()->getId(), $addForm->idItem->getValues());

         // uloženhí do db
         $imagesM->saveImage($this->category()->getId(), $addForm->idItem->getValues(),
                 $image->getName(), $image->getName(), null, $count+1);

      }
      return $addForm;
   }

   /**
    * Metoda pro upload fotek pomocí Ajax requestu
    */
   public function uploadFileController()
   {
      $this->checkWritebleRights();
      $this->view()->allOk = false;
      if($this->savePhotoForm()->isValid()) {
         $this->infoMsg()->addMessage($this->tr('Obrázek byl uložen'));
      } else {
         $this->errMsg()->addMessage($this->tr('Chyba při nahrávání, asy nebyl vybrán obrázek, nebo byl poškozen.'));
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }

   public function checkFileController()
   {
      $fileArray = array();
      foreach ($_POST as $key => $value) {
         if ($key != 'folder') {
            if (file_exists($this->module()->getDataDir() . $this->subDir.self::DIR_ORIGINAL.DIRECTORY_SEPARATOR . $value)) {
               $fileArray[$key] = $value;
            }
         }
      }
      echo json_encode($fileArray);
   }

   public function editphotoController()
   {
      $this->checkWritebleRights();

      $m = new PhotoGalery_Model_Images();
      $image = $m->getImage($this->getRequest('id'));

      $editForm = new Form('image_');

      $elemX = new Form_Element_Hidden('start_x');
      $editForm->addElement($elemX);
      $elemY = new Form_Element_Hidden('start_y');
      $editForm->addElement($elemY);

      $elemW = new Form_Element_Hidden('width');
      $editForm->addElement($elemW);

      $elemH = new Form_Element_Hidden('height');
      $editForm->addElement($elemH);

      $elemGoBack = new Form_Element_Checkbox('goBack', $this->tr('Přejít po uložení zpět'));
      $elemGoBack->setValues(true);
      $editForm->addElement($elemGoBack);

      $elemSubmit = new Form_Element_SaveCancel('save');
      $editForm->addElement($elemSubmit);

      if($editForm->isSend() AND $elemSubmit->getValues() == false){
         $this->link()->route('editphotos')->reload();
      }

      if($editForm->isValid()) {
         $imageF = new Filesystem_File_Image($image->{PhotoGalery_Model_Images::COLUMN_FILE},
                 $this->module()->getDataDir().$this->subDir.self::DIR_MEDIUM.DIRECTORY_SEPARATOR);
         $imageF->cropAndSave($this->module()->getDataDir().$this->subDir.self::DIR_SMALL.DIRECTORY_SEPARATOR,
                 $this->category()->getParam('small_width',VVE_IMAGE_THUMB_W),
                 $this->category()->getParam('small_height',VVE_IMAGE_THUMB_H),
                 $editForm->start_x->getValues(), $editForm->start_y->getValues(),
                 $editForm->width->getValues(), $editForm->height->getValues());
         $this->infoMsg()->addMessage($this->tr('Miniatura byla upravena'));
         if($editForm->goBack->getValues() == true) {
            $this->link()->route('editphotos')->reload();
         } else {
            $this->link()->reload();
         }
      }

      $this->view()->template()->form = $editForm;
      $this->view()->template()->image = $image;
       // adresáře k fotkám
      $this->view()->subdir = $this->subDir;
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $this->subDir);
   }

   public function cropThumbController()
   {
      $this->checkWritebleRights();

      if(!isset($_POST['id']) || !isset($_POST['x']) || !isset($_POST['y']) || !isset($_POST['w']) || !isset($_POST['h']) ){
         throw new BadRequestException($this->tr('Nebyly předány všechny potřebné parametry'));
      }

      $id = (int)$this->getRequestParam('id');
      $x = (int)$this->getRequestParam('x');
      $y = (int)$this->getRequestParam('y');
      $w = (int)$this->getRequestParam('w');
      $h = (int)$this->getRequestParam('h');

      $m = new PhotoGalery_Model_Images();
      $imageRec = $m->record( $id );

      if(!$imageRec){
         throw new BadRequestException($this->tr('Zadaný obrázek neexistuje'));
      }

      $image = new File_Image( $imageRec->{PhotoGalery_Model_Images::COLUMN_FILE},
         $this->module()->getDataDir().$this->subDir.self::DIR_MEDIUM.DIRECTORY_SEPARATOR );

      $image
         ->copy($this->module()->getDataDir().$this->subDir.self::DIR_SMALL.DIRECTORY_SEPARATOR, true, null, false)
         ->getData()
         ->crop($x, $y, $w, $h)
         ->resize(
            $this->category()->getParam('small_width',VVE_IMAGE_THUMB_W),
            $this->category()->getParam('small_height',VVE_IMAGE_THUMB_H),
            File_Image_Base::RESIZE_EXACT
      )->save();
      $this->infoMsg()->addMessage($this->tr('Miniatura byla vytvořena'));
   }

   public function imageRotateController()
   {
      $this->checkWritebleRights();

      if(!isset($_POST['id']) || !isset($_POST['degree']) ){
         throw new BadRequestException($this->tr('Nebyly předány všechny potřebné parametry'));
      }


      $id = (int)$this->getRequestParam('id');
      $degree = (int)$this->getRequestParam('degree');

      if($degree == 0){
         throw new BadRequestException($this->tr('Nelze otáčet o 0 stupňů'));
         return;
      }
      $m = new PhotoGalery_Model_Images();
      $imageRec = $m->record( $id );

      if(!$imageRec){
         throw new BadRequestException($this->tr('Zadaný obrázek neexistuje'));
      }
      // rotate original
      $image = new File_Image( $imageRec->{PhotoGalery_Model_Images::COLUMN_FILE},
         $this->module()->getDataDir().$this->subDir.self::DIR_ORIGINAL.DIRECTORY_SEPARATOR );

      // rotate medium
      $image = new File_Image( $imageRec->{PhotoGalery_Model_Images::COLUMN_FILE},
         $this->module()->getDataDir().$this->subDir.self::DIR_MEDIUM.DIRECTORY_SEPARATOR );
      $image->getData()->rotate($degree)->save();

      // create thumbnail
      $thumb = $image->copy($this->module()->getDataDir().$this->subDir.self::DIR_SMALL.DIRECTORY_SEPARATOR, true, null, false);
      $crop = $this->category()->getParam('small_crop', VVE_IMAGE_THUMB_CROP) == true ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO;

      $thumb->getData()->resize(
         $this->category()->getParam('small_width',VVE_IMAGE_THUMB_W),
         $this->category()->getParam('small_height',VVE_IMAGE_THUMB_H),
         $crop
      )->save();

      $this->infoMsg()->addMessage($this->tr('Obrázek byl otočen'));
   }

   public static function getImagesURL(Category $category, Model_ORM_Record $article = null, $sizeDir = Photogalery_Controller::DIR_SMALL)
   {
      return $category->getModule()->getDataDir(true)
         . ( $article != null ? $article[Articles_Model::COLUMN_URLKEY][Locales::getDefaultLang()]."/" : null)
         . $sizeDir . "/";
   }


   protected function resizeImages($w, $h, $crop, $dir = self::DIR_MEDIUM)
   {
       $reg = '#'.preg_quote(DIRECTORY_SEPARATOR).'([^'.preg_quote(DIRECTORY_SEPARATOR).']+)'.preg_quote(DIRECTORY_SEPARATOR).'original#';
       $repl = '/$1/'.$dir;

      $path = $this->module()->getDataDir()."original".DIRECTORY_SEPARATOR."{*.gif,*.jpg,*.png,*.GIF,*.JPG,*.PNG}";
      $results = glob($path, GLOB_NOSORT|GLOB_BRACE);

      if($results != false){
         foreach ($results as $file) {
            $pathInfo = pathinfo($file);
            $imgFile = new File_Image($pathInfo['basename'], $pathInfo['dirname']);
            $newPath = preg_replace($reg, $repl, $pathInfo['dirname']);
            $newFile = $imgFile->copy($newPath, true, null, false);
            $newFile->getData()->resize($w, $h, $crop == true ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO);
            $newFile->save();
         }
      }

      $path = $this->module()->getDataDir()."*".DIRECTORY_SEPARATOR."original".DIRECTORY_SEPARATOR."{*.gif,*.jpg,*.png,*.GIF,*.JPG,*.PNG}";
      $results = glob($path, GLOB_NOSORT|GLOB_BRACE);

      if($results != false){
         foreach ($results as $file) {
            $pathInfo = pathinfo($file);
            $imgFile = new File_Image($pathInfo['basename'], $pathInfo['dirname']);
            $newPath = preg_replace($reg, $repl, $pathInfo['dirname']);
            $newFile = $imgFile->copy($newPath, true, null, false);
            $newFile->getData()->resize($w, $h, $crop == true ? File_Image_Base::RESIZE_CROP : File_Image_Base::RESIZE_AUTO);
            $newFile->save();
         }
      }
   }

   public function settings(&$settings,Form &$form)
   {
      $fGrpViewSet = $form->addGroup('view', $this->tr('Nastavení vzhledu'));

      $fGrpEditSet = $form->addGroup('editSettings', $this->tr('Nastavení úprav'));

      $elemEditorType = new Form_Element_Select('editor_type', $this->tr('Typ editoru'));
      $elemEditorType->setOptions(array(
         $this->tr('žádný (pouze textová oblast)') => 'none',
         $this->tr('jednoduchý (Wysiwyg)') => 'simple',
         $this->tr('pokročilý (Wysiwyg)') => 'advanced',
         $this->tr('kompletní (Wysiwyg)') => 'full'
      ));
      $elemEditorType->setValues('advanced');
      if(isset($settings[self::PARAM_EDITOR_TYPE])) {
         $elemEditorType->setValues($settings[self::PARAM_EDITOR_TYPE]);
      }

      $form->addElement($elemEditorType, $fGrpEditSet);

      $form->addGroup('images', 'Nastavení obrázků');

      $elemSW = new Form_Element_Text('small_width', 'Šířka miniatury (px)');
      $elemSW->addValidation(new Form_Validator_IsNumber());
      $elemSW->setSubLabel('Výchozí: <span class="param_small">'.VVE_IMAGE_THUMB_W.'</span>px');
      $form->addElement($elemSW, 'images');
      if(isset($settings[self::PARAM_SMALL_W])) {
         $form->small_width->setValues($settings[self::PARAM_SMALL_W]);
      }

      $elemSH = new Form_Element_Text('small_height', 'Výška miniatury (px)');
      $elemSH->addValidation(new Form_Validator_IsNumber());
      $elemSH->setSubLabel('Výchozí: <span class="param_small">'.VVE_IMAGE_THUMB_H.'</span>px');
      $form->addElement($elemSH, 'images');
      if(isset($settings[self::PARAM_SMALL_H])) {
         $form->small_height->setValues($settings[self::PARAM_SMALL_H]);
      }

      $elemSC = new Form_Element_Checkbox('small_crop', 'Ořezávat miniatury');
      $elemSC->setValues(true);
      if(isset($settings[self::PARAM_SMALL_C])) {
         $elemSC->setValues($settings[self::PARAM_SMALL_C]);
      }
      $form->addElement($elemSC, 'images');

      $elemW = new Form_Element_Text('medium_width', 'Šířka obrázku (px)');
      $elemW->addValidation(new Form_Validator_IsNumber());
      $elemW->setSubLabel('Výchozí: <span class="param_small">'.VVE_DEFAULT_PHOTO_W.'</span>px');
      $form->addElement($elemW, 'images');
      if(isset($settings[self::PARAM_MEDIUM_W])) {
         $form->medium_width->setValues($settings[self::PARAM_MEDIUM_W]);
      }

      $elemH = new Form_Element_Text('medium_height', 'Výška obrázku (px)');
      $elemH->addValidation(new Form_Validator_IsNumber());
      $elemH->setSubLabel('Výchozí: <span class="param_small">'.VVE_DEFAULT_PHOTO_H.'</span>px');
      $form->addElement($elemH, 'images');
      if(isset($settings[self::PARAM_MEDIUM_H])) {
         $form->medium_height->setValues($settings[self::PARAM_MEDIUM_H]);
      }

      $elemC = new Form_Element_Checkbox('medium_crop', 'Ořezávat obrázky');
      $elemC->setValues(false);
      if(isset($settings[self::PARAM_MEDIUM_C])) {
         $elemC->setValues($settings[self::PARAM_MEDIUM_C]);
      }
      $form->addElement($elemC, 'images');

      $elemResize = new Form_Element_Checkbox('resizeImages', 'Změnit velikosti');
      $elemResize->setSubLabel($this->tr('Změnit velikosti již uložených obrázků pokud se liší od původních. POZOR! Tato změna může trvat déle!'));
      if(isset($settings[self::PARAM_SMALL_W]) || isset($settings[self::PARAM_SMALL_H]) || isset($settings[self::PARAM_SMALL_C])
         || isset($settings[self::PARAM_MEDIUM_W]) || isset($settings[self::PARAM_MEDIUM_H]) || isset($settings[self::PARAM_MEDIUM_C]) ){
         $elemResize->setValues(true);
      }

      $form->addElement($elemResize, 'images');
      if($form->isValid()){
         // resize images if need
         if($form->resizeImages->getValues() == true){
            // zjištění původsních a nových velikostí
            $origSmallW = isset($settings[self::PARAM_SMALL_W]) ? $settings[self::PARAM_SMALL_W] : VVE_IMAGE_THUMB_W;
            $origSmallH = isset($settings[self::PARAM_SMALL_H]) ? $settings[self::PARAM_SMALL_H] : VVE_IMAGE_THUMB_H;
            $origSmallC = isset($settings[self::PARAM_SMALL_C]) ? $settings[self::PARAM_SMALL_C] : VVE_IMAGE_THUMB_CROP;
            $origMedW = isset($settings[self::PARAM_MEDIUM_W]) ? $settings[self::PARAM_MEDIUM_W] : VVE_DEFAULT_PHOTO_W;
            $origMedH = isset($settings[self::PARAM_MEDIUM_H]) ? $settings[self::PARAM_MEDIUM_H] : VVE_DEFAULT_PHOTO_H;
            $origMedC = isset($settings[self::PARAM_MEDIUM_C]) ? $settings[self::PARAM_MEDIUM_C] : false;

            $newSmallW = $form->small_width->getValues() != null ? $form->small_width->getValues() : VVE_IMAGE_THUMB_W;
            $newSmallH = $form->small_height->getValues() != null ? $form->small_height->getValues() : VVE_IMAGE_THUMB_H;
            $newSmallC = $form->small_crop->getValues() != null ? $form->small_crop->getValues() : VVE_IMAGE_THUMB_CROP;
            $newMedW = $form->medium_width->getValues() != null ? $form->medium_width->getValues() : VVE_DEFAULT_PHOTO_W;
            $newMedH = $form->medium_height->getValues() != null ? $form->medium_height->getValues() : VVE_DEFAULT_PHOTO_H;
            $newMedC = $form->medium_crop->getValues() != null ? $form->medium_crop->getValues() : false;

            // small
            if( $origSmallW != $newSmallW || $origSmallH != $newSmallH || $origSmallC != $newSmallC ){
               $this->resizeImages($newSmallW, $newSmallH, $newSmallC, self::DIR_SMALL);
            }

            if( $origMedW != $newMedW || $origMedH != $newMedH || $origMedC != $newMedC ){
               $this->resizeImages($newMedW, $newMedH, $newMedC, self::DIR_MEDIUM);
            }
         }

         // save options
         $settings[self::PARAM_SMALL_W] = $form->small_width->getValues();
         $settings[self::PARAM_SMALL_H] = $form->small_height->getValues();
         $settings[self::PARAM_SMALL_C] = $form->small_crop->getValues();
         $settings[self::PARAM_MEDIUM_W] = $form->medium_width->getValues();
         $settings[self::PARAM_MEDIUM_H] = $form->medium_height->getValues();
         $settings[self::PARAM_MEDIUM_C] = $form->medium_crop->getValues();
         $settings[self::PARAM_EDITOR_TYPE] = $form->editor_type->getValues();
      }
   }
}
