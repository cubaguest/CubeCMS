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

   const SMALL_WIDTH = 140;
   const SMALL_HEIGHT = 140;

   const MEDIUM_WIDTH = 600;
   const MEDIUM_HEIGHT = 400;

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
              $this->getOption('idArt'));

      $this->view()->subdir = $this->getOption('subdir', null);
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $this->getOption('subdir', null));
   }

   public function edittextController() {
      $this->checkWritebleRights();

      $form = new Form("text_");
      $textarea = new Form_Element_TextArea('text', $this->_("Text"));
      $textarea->setLangs();
      $form->addElement($textarea);

      $model = new Text_Model_Detail();
      $text = $model->getText($this->category()->getId());
      if($text != false) {
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }

      $submit = new Form_Element_Submit('send', $this->_("Uložit"));
      $form->addElement($submit);

      if($form->isValid()) {
         try {
            $model->saveText($form->text->getValues(), null,
                    null, $this->category()->getId());
            $this->infoMsg()->addMessage($this->_('Text byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }

      // view
      $this->view()->template()->form = $form;
      $this->view()->template()->addTplFile("edittext.phtml");
   }

   public function editphotosController($delImgCallBackFunc = null) {
      $this->checkWritebleRights();

      $imagesM = new PhotoGalery_Model_Images();

      $addForm = $this->savePhotoForm();
      if($addForm->isValid()) {
         $this->infoMsg()->addMessage($this->_('Obrázek byl uložen'));
         $this->link()->reload();
      }

      $editForm = new Form('editimage_');
      $imgName = new Form_Element_Text('name', $this->_('Název'));
      $imgName->setLangs();
      $editForm->addElement($imgName);
      $imgOrd = new Form_Element_Text('ord', $this->_('Pořadí'));
      $editForm->addElement($imgOrd);
      $imgDesc = new Form_Element_TextArea('desc', $this->_('Popis'));
      $imgDesc->setLangs();
      $editForm->addElement($imgDesc);
      $imgDel = new Form_Element_Checkbox('delete', $this->_('Smazat'));
      $editForm->addElement($imgDel);
      $imgId = new Form_Element_Hidden('id');
      $editForm->addElement($imgId);

      $submit = new Form_Element_Submit('save', $this->_('Uložit'));
      $editForm->addElement($submit);

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
            }
         }

         $this->infoMsg()->addMessage($this->_('Obrázky byly uloženy'));
         $this->link()->reload();
      }

      // odkaz na editaci obrázku
      $this->view()->template()->linkImageCrop = $this->link()->route('editphoto',
              array('id' => '%s'));

      $this->view()->template()->images = $imagesM->getImages($this->category()->getId(), $this->getOption('idArt'));
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
      $addFile = new Form_Element_File('image', $this->_('Obrázek'));
      $addFile->addValidation(new Form_Validator_FileExtension(array('jpg', 'jpeg', 'png', 'gif')));
      $addFile->setUploadDir($this->category()->getModule()->getDataDir()
              .$this->getOption('subdir', null).self::DIR_ORIGINAL.DIRECTORY_SEPARATOR);
      $addForm->addElement($addFile);

      $idArt = new Form_Element_Hidden('idArt');
      $idArt->setValues($this->getOption('idArt'));
      $addForm->addElement($idArt);

      $addSubmit = new Form_Element_Submit('send',$this->_('Odeslat'));
      $addForm->addElement($addSubmit);

      if($addForm->isValid()) {
         $file = $addFile->getValues();
         $image = new Filesystem_File_Image($file['name'], $this->category()->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_ORIGINAL);
         $image->saveAs($this->category()->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_SMALL,
                 $this->category()->getParam('small_width', self::SMALL_WIDTH),
                 $this->category()->getParam('small_height', self::SMALL_HEIGHT), true);
         $image->saveAs($this->category()->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_MEDIUM,
                 $this->category()->getParam('medium_width', self::MEDIUM_WIDTH),
                 $this->category()->getParam('medium_height', self::MEDIUM_HEIGHT));

         // uloženhí do db
         $imagesM = new PhotoGalery_Model_Images();
         $imagesM->saveImage($this->category()->getId(), $addForm->idArt->getValues(),
                 $image->getName(), $image->getName());

      }
      return $addForm;
   }

   /**
    * Metoda pro upload fotek pomocí Ajax requestu
    */
   public function uploadFileController() {
      $this->checkWritebleRights();
      if($this->savePhotoForm()->isValid()) {
         echo "1";
      } else {
         echo $this->_('Neplatný typ souboru');
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

      $elemGoBack = new Form_Element_Checkbox('goBack', $this->_('Přejít po uložení zpět'));
      $elemGoBack->setValues(true);
      $editForm->addElement($elemGoBack);

      $elemSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $editForm->addElement($elemSubmit);

      if($editForm->isValid()) {
         $imageF = new Filesystem_File_Image($image->{PhotoGalery_Model_Images::COLUMN_FILE},
                 $this->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_MEDIUM.DIRECTORY_SEPARATOR);
         $imageF->cropAndSave($this->getModule()->getDataDir().$this->getOption('subdir', null).self::DIR_SMALL.DIRECTORY_SEPARATOR,
                 self::SMALL_WIDTH, self::SMALL_HEIGHT,
                 $editForm->start_x->getValues(), $editForm->start_y->getValues(),
                 $editForm->width->getValues(), $editForm->height->getValues());
         $this->infoMsg()->addMessage($this->_('Miniatura byla upravena'));
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

   public static function settingsController(&$settings,Form &$form) {
      $form->addGroup('images', 'Nastavení obrázků');

      $elemSW = new Form_Element_Text('small_width', 'Šířka miniatury (px)');
      $elemSW->addValidation(new Form_Validator_IsNumber());
      $elemSW->setSubLabel('Výchozí: '.self::SMALL_WIDTH.'px');
      $form->addElement($elemSW, 'images');
      if(isset($settings['small_width'])) {
         $form->small_width->setValues($settings['small_width']);
      }

      $elemSH = new Form_Element_Text('small_height', 'Výška miniatury (px)');
      $elemSH->addValidation(new Form_Validator_IsNumber());
      $elemSH->setSubLabel('Výchozí: '.self::SMALL_HEIGHT.'px');
      $form->addElement($elemSH, 'images');
      if(isset($settings['small_height'])) {
         $form->small_height->setValues($settings['small_height']);
      }

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
      
      // znovu protože už mohl být jednou odeslán

      if($form->isValid()){
         $settings['small_width'] = $form->small_width->getValues();
         $settings['small_height'] = $form->small_height->getValues();
         $settings['medium_width'] = $form->medium_width->getValues();
         $settings['medium_height'] = $form->medium_height->getValues();
      }
   }
}
?>