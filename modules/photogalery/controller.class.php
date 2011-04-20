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

   const SMALL_WIDTH = 140;
   const SMALL_HEIGHT = 140;

   const MEDIUM_WIDTH = VVE_DEFAULT_PHOTO_W;
   const MEDIUM_HEIGHT = VVE_DEFAULT_PHOTO_H;

   public function init() {
      $this->setOption('idArt', $this->category()->getId());
   }

   /**
    * Kontroler pro zobrazení fotogalerii
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      $modelImages = new PhotoGalery_Model_Images();
      $this->view()->images = $modelImages->getImages($this->category()->getId(),
              $this->getOption('idArt'))->fetchAll();

      $this->view()->subdir = $this->getOption('subdir', null);
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $this->getOption('subdir', null));

      $model = new Text_Model_Detail();
      $this->view()->text = $model->getText($this->category()->getId());
   }

   public function edittextController() {
      $this->checkWritebleRights();
      
      $form = new Form("text_");
      $textarea = new Form_Element_TextArea('text', $this->tr("Text"));
      $textarea->setLangs();
      $form->addElement($textarea);

      $model = new Text_Model_Detail();
      $text = $model->getText($this->category()->getId());
      if($text != false) {
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }

      $submit = new Form_Element_SaveCancel('send');
      $form->addElement($submit);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->link()->route()->reload();
      }

      if($form->isValid()) {
         try {
            $model->saveText($form->text->getValues(), null, $this->category()->getId());
            $this->infoMsg()->addMessage($this->tr('Text byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }

      // view
      $this->view()->template()->form = $form;
   }

   public function editphotosController(Url_Link $backLink = null) {
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
      $imgRotation = new Form_Element_Select('rotate', $this->tr('Otočit do leva'));
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

      $eGoBack = new Form_Element_Checkbox('goBack', $this->tr('Zavřít'));
      $eGoBack->setValues(false);
      $editForm->addElement($eGoBack);

      $submit = new Form_Element_SaveCancel('save', array($this->tr('Uložit'), $this->tr('Zavřít')));
      $submit->setCancelConfirm(false);
      $editForm->addElement($submit);

      if($editForm->isSend() AND $editForm->save->getValues() == false){
         if($backLink === null){
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
            if($editForm->delete->getValues($id) === true) {
               $this->deleteImage($id);
            } else {
               // ukládají změny
               $imagesM->saveImage($this->category()->getId(), $this->getOption('idArt'),
                       null, $names[$id], $descs[$id],$orders[$id],$id);
               // rotace pokud je
               if($editForm->rotate->getValues($id) != 0){
                  $file = $imagesM->getImage($id)->{PhotoGalery_Model_Images::COLUMN_FILE};
                  $image = new Filesystem_File_Image($file, $this->category()->getModule()->getDataDir(false)
                     .$this->getOption('subdir', null).Photogalery_Controller::DIR_MEDIUM);
                  $image->rotateImage($editForm->rotate->getValues($id));
                  $image->save();
                  $image = new Filesystem_File_Image($file, $this->category()->getModule()->getDataDir(false)
                     .$this->getOption('subdir', null).Photogalery_Controller::DIR_SMALL);
                  $image->rotateImage($editForm->rotate->getValues($id));
                  $image->save();
                  unset ($image);
               }
            }
         }

         $this->infoMsg()->addMessage($this->tr('Obrázky byly uloženy'));
         if($editForm->goBack->getValues() == true){
            if($backLink === null){
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

      $this->view()->template()->images = $imagesM->getImages($this->category()->getId(), $this->getOption('idArt'))->fetchAll();
      $this->view()->template()->addForm = $addForm;
      $this->view()->template()->editForm = $editForm;
      $this->view()->template()->idArt = $this->getOption('idArt');
      // adresáře k fotkám
      $this->view()->subdir = $this->getOption('subdir', null);
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $this->getOption('subdir', null));
   }

   /**
    * Metoda vymaže obrázek
    */
   private function deleteImage($idImage) {
      $imagesM = new PhotoGalery_Model_Images();
      $img = $imagesM->getImage($idImage);
      // smazání souborů
      $file = new Filesystem_File($img->{PhotoGalery_Model_Images::COLUMN_FILE},
              $this->category()->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_SMALL.DIRECTORY_SEPARATOR);
      $file->remove();
      $file = new Filesystem_File($img->{PhotoGalery_Model_Images::COLUMN_FILE},
              $this->category()->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_MEDIUM.DIRECTORY_SEPARATOR);
      $file->remove();
      $file = new Filesystem_File($img->{PhotoGalery_Model_Images::COLUMN_FILE},
              $this->category()->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_ORIGINAL.DIRECTORY_SEPARATOR);
      $file->remove();
      // remove z db
      $imagesM->deleteImage($idImage);
   }

   /**
    * Metoda vymaže obrázky od článku
    */
   public function deleteImages($idArt) {
      $imagesM = new PhotoGalery_Model_Images();
      $images = $imagesM->getImages($this->category()->getId(), $idArt);
      while ($image = $images->fetch()) {
         $this->deleteImage($image->{PhotoGalery_Model_Images::COLUMN_ID});
      }
   }

   private function savePhotoForm() {
      $addForm = new Form('addimage_');
      $addFile = new Form_Element_File('image', $this->tr('Obrázek'));
      $addFile->addValidation(new Form_Validator_FileExtension(array('jpg', 'jpeg', 'png', 'gif')));
      $addFile->setUploadDir($this->category()->getModule()->getDataDir()
              .$this->getOption('subdir', null).self::DIR_ORIGINAL.DIRECTORY_SEPARATOR);
      $addForm->addElement($addFile);

      $idArt = new Form_Element_Hidden('idArt');
      $idArt->setValues($this->getOption('idArt'));
      $addForm->addElement($idArt);

      $addSubmit = new Form_Element_Submit('send',$this->tr('Odeslat'));
      $addForm->addElement($addSubmit);

      if($addForm->isValid()) {
         $file = $addFile->getValues();
         $image = new Filesystem_File_Image($file['name'], $this->category()->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_ORIGINAL);
         $image->saveAs($this->category()->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_SMALL,
                 $this->category()->getParam('small_width', VVE_IMAGE_THUMB_W),
                 $this->category()->getParam('small_height', VVE_IMAGE_THUMB_H), $this->category()->getParam('small_crop', true));
         $image->saveAs($this->category()->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_MEDIUM,
                 $this->category()->getParam('medium_width', self::MEDIUM_WIDTH),
                 $this->category()->getParam('medium_height', self::MEDIUM_HEIGHT), $this->category()->getParam('medium_crop', false));

         // zjistíme pořadí
         $imagesM = new PhotoGalery_Model_Images();

         $count = (int)$imagesM->getCountImages($this->category()->getId(), $addForm->idArt->getValues());

         // uloženhí do db
         $imagesM->saveImage($this->category()->getId(), $addForm->idArt->getValues(),
                 $image->getName(), $image->getName(), null, $count+1);

      }
      return $addForm;
   }

   /**
    * Metoda pro upload fotek pomocí Ajax requestu
    */
   public function uploadFileController() {
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

   public function checkFileController() {
      $fileArray = array();
      foreach ($_POST as $key => $value) {
         if ($key != 'folder') {
            if (file_exists($this->getModule()->getDataDir() . $this->getOption('subdir', null).self::DIR_ORIGINAL.DIRECTORY_SEPARATOR . $value)) {
               $fileArray[$key] = $value;
            }
         }
      }
      echo json_encode($fileArray);
   }

   public function editphotoController() {
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
                 $this->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_MEDIUM.DIRECTORY_SEPARATOR);
         $imageF->cropAndSave($this->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_SMALL.DIRECTORY_SEPARATOR,
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
      $this->view()->subdir = $this->getOption('subdir', null);
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $this->getOption('subdir', null));
   }

   public function settings(&$settings,Form &$form) {
      $fGrpViewSet = $form->addGroup('view', $this->tr('Nastavení vzhledu'));
      
      $componentTpls = new Component_ViewTpl();
      $componentTpls->setConfig(Component_ViewTpl::PARAM_MODULE, 'photogalery');

      $elemTplMain = new Form_Element_Select('tplMain', $this->tr('Hlavní šablona'));
      $elemTplMain->setOptions(array_flip($componentTpls->getTpls()));
      if(isset($settings[self::PARAM_TPL_MAIN])) {
         $elemTplMain->setValues($settings[self::PARAM_TPL_MAIN]);
      }
      $form->addElement($elemTplMain, $fGrpViewSet);
      unset ($componentTpls);
      
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
      $elemSW->setSubLabel('Výchozí: '.VVE_IMAGE_THUMB_W.'px');
      $form->addElement($elemSW, 'images');
      if(isset($settings['small_width'])) {
         $form->small_width->setValues($settings['small_width']);
      }

      $elemSH = new Form_Element_Text('small_height', 'Výška miniatury (px)');
      $elemSH->addValidation(new Form_Validator_IsNumber());
      $elemSH->setSubLabel('Výchozí: '.VVE_IMAGE_THUMB_H.'px');
      $form->addElement($elemSH, 'images');
      if(isset($settings['small_height'])) {
         $form->small_height->setValues($settings['small_height']);
      }

      $elemSC = new Form_Element_Checkbox('small_crop', 'Ořezávat miniatury');
      $elemSC->setValues(true);
      if(isset($settings['small_crop'])) {
         $elemSC->setValues($settings['small_crop']);
      }
      $form->addElement($elemSC, 'images');

      $elemW = new Form_Element_Text('medium_width', 'Šířka obrázku (px)');
      $elemW->addValidation(new Form_Validator_IsNumber());
      $elemW->setSubLabel('Výchozí: '.self::MEDIUM_WIDTH.'px');
      $form->addElement($elemW, 'images');
      if(isset($settings['medium_width'])) {
         $form->medium_width->setValues($settings['medium_width']);
      }

      $elemH = new Form_Element_Text('medium_height', 'Výška obrázku (px)');
      $elemH->addValidation(new Form_Validator_IsNumber());
      $elemH->setSubLabel('Výchozí: '.self::MEDIUM_HEIGHT.'px');
      $form->addElement($elemH, 'images');
      if(isset($settings['medium_height'])) {
         $form->medium_height->setValues($settings['medium_height']);
      }

      $elemC = new Form_Element_Checkbox('medium_crop', 'Ořezávat obrázky');
      $elemC->setValues(false);
      if(isset($settings['medium_crop'])) {
         $elemC->setValues($settings['medium_crop']);
      }
      $form->addElement($elemC, 'images');
      
      if($form->isValid()){
         $settings['small_width'] = $form->small_width->getValues();
         $settings['small_height'] = $form->small_height->getValues();
         $settings['small_crop'] = $form->small_crop->getValues();
         $settings['medium_width'] = $form->medium_width->getValues();
         $settings['medium_height'] = $form->medium_height->getValues();
         $settings['medium_crop'] = $form->medium_crop->getValues();
         $settings[self::PARAM_EDITOR_TYPE] = $form->editor_type->getValues();
         $settings[self::PARAM_TPL_MAIN] = $form->tplMain->getValues();
      }
   }
}
?>