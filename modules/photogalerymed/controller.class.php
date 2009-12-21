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

class Photogalerymed_Controller extends Controller {
   const DIR_SMALL = 'small';
   const DIR_MEDIUM = 'medium';
   const DIR_ORIGINAL = 'original';

   const SMALL_WIDTH = 75;
   const SMALL_HEIGHT = 75;

   const MEDIUM_WIDTH = 600;
   const MEDIUM_HEIGHT = 400;

   /**
    * Kontroler pro zobrazení fotogalerii
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $this->view()->template()->addTplFile("list.phtml");
   }

   /**
    * Přidání galerie
    */
   public function addController() {
      $form = $this->formEditGalery();
      $form->save->setLabel($this->_('Pokračovat'));

      if($form->isValid()) {
         $urlkey = $form->urlkey->getValues();
         $names = $form->name->getValues();
         foreach ($urlkey as $lang => $variable) {
            if($variable == null AND $names[$lang] == null) {
               $urlkey[$lang] = null;
            } else if($variable == null) {
               $urlkey[$lang] = vve_cr_url_key($names[$lang]);
            } else {
               $urlkey[$lang] = vve_cr_url_key($variable);
            }
         }

         $artModel = new Articles_Model_Detail();
         $artID = $artModel->saveArticle($names, $form->text->getValues(), $urlkey,
                 $this->category()->getId(), $this->auth()->getUserId());

         //načtení vytvořené galerie
         $gal = $artModel->getArticleById($artID);

         if($artID != 0) {
            $this->infoMsg()->addMessage($this->_('Galerie byla uložen'));
            // redirekt na editaci obrázků
            $this->link()->route('editimages', array('urlkey' => $gal->{Articles_Model_Detail::COLUMN_URLKEY}))->reload();
         } else {
            $this->errMsg()->addMessage($this->_('Galerii se nepodařilo uložit'));
         }

      }

      $this->view()->template()->form = $form;
      $this->view()->template()->edit = false;
      $this->view()->template()->addTplFile('edittext.phtml');
   }

   private function formEditGalery() {
      $form = new Form('galery_');

      $iName = new Form_Element_Text('name', $this->_('Název'));
      $iName->setLangs();
      $iName->addValidation(New Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $form->addElement($iName);

      $iText = new Form_Element_TextArea('text', $this->_('Text'));
      $iText->setLangs();
      $form->addElement($iText);

      $iUrlKey = new Form_Element_Text('urlkey', $this->_('Url klíč'));
      $iUrlKey->setLangs();
      $iUrlKey->setSubLabel($this->_('Pokud není klíč zadán, je generován automaticky'));
      $form->addElement($iUrlKey);

      $iSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($iSubmit);

      return $form;
   }

//   public function edittextController() {
//      $this->checkWritebleRights();
//
//      $form = new Form("text_");
//      $textarea = new Form_Element_TextArea('text', $this->_("Text"));
//      $textarea->setLangs();
//      $form->addElement($textarea);
//
//      $model = new Text_Model_Detail();
//      $text = $model->getText($this->category()->getId());
//      if($text != false) {
//         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
//      }
//
//      $submit = new Form_Element_Submit('send', $this->_("Uložit"));
//      $form->addElement($submit);
//
//      if($form->isValid()) {
//         try {
//            $model->saveText($form->text->getValues(), null,
//                    null, $this->category()->getId());
//            $this->infoMsg()->addMessage($this->_('Text byl uložen'));
//            $this->link()->route()->reload();
//         } catch (PDOException $e) {
//            new CoreErrors($e);
//         }
//      }
//
//      // view
//      $this->view()->template()->form = $form;
//
//      $this->view()->template()->addTplFile("edittext.phtml");
//   }

   public function editimagesController() {
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('idArt', 28);
      $ctr->editimagesController();

      $this->view()->template()->addTplFile('editimages.phtml', 'photogalery');
   }

   public function checkFileController() {
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->checkFileController();
   }
   
   public function uploadFileController() {
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('idArt', 28);
      $ctr->uploadFileController();
   }

   public function editphotoController() {
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->editphotoController();

      $this->view()->template()->addTplFile("editphoto.phtml", 'photogalery');
   }


//      $this->checkWritebleRights();
//
//      $imagesM = new PhotoGalery_Model_Images();
//
//      $addForm = $this->saveImageForm();
//      if($addForm->isValid()) {
//         $this->infoMsg()->addMessage($this->_('Obrázek byl uložen'));
//         $this->link()->reload();
//      }
//
//      $editForm = new Form('editimage_');
//      $imgName = new Form_Element_Text('name', $this->_('Název'));
//      $imgName->setLangs();
//      $editForm->addElement($imgName);
//      $imgOrd = new Form_Element_Text('ord', $this->_('Pořadí'));
//      $editForm->addElement($imgOrd);
//      $imgDesc = new Form_Element_TextArea('desc', $this->_('Popis'));
//      $imgDesc->setLangs();
//      $editForm->addElement($imgDesc);
//      $imgDel = new Form_Element_Checkbox('delete', $this->_('Smazat'));
//      $editForm->addElement($imgDel);
//      $imgId = new Form_Element_Hidden('id');
//      $editForm->addElement($imgId);
////      $imgFile = new Form_Element_Hidden('file');
////      $editForm->addElement($imgFile);
//
//      $submit = new Form_Element_Submit('save', $this->_('Uložit'));
//      $editForm->addElement($submit);
//
//      if($editForm->isValid()) {
////         $files = $editForm->file->getValues();
//         $names = $editForm->name->getValues();
//         $descs = $editForm->desc->getValues();
//         $orders = $editForm->ord->getValues();
//         $ids = $editForm->id->getValues();
//
//         foreach ($ids as $id) {
//            if($editForm->delete->getValues($id) === true) {
//               $img = $imagesM->getImage($id);
//               // smazání souborů
//               $file = new Filesystem_File($img->{PhotoGalery_Model_Images::COLUMN_FILE},
//                       $this->category()->getModule()->getDataDir().self::DIR_SMALL.DIRECTORY_SEPARATOR);
//               $file->remove();
//               $file = new Filesystem_File($img->{PhotoGalery_Model_Images::COLUMN_FILE},
//                       $this->category()->getModule()->getDataDir().self::DIR_MEDIUM.DIRECTORY_SEPARATOR);
//               $file->remove();
//               $file = new Filesystem_File($img->{PhotoGalery_Model_Images::COLUMN_FILE},
//                       $this->category()->getModule()->getDataDir());
//               $file->remove();
//               // remove z db
//               $imagesM->deleteImage($id);
//            } else {
//               // ukládají změny
//               $imagesM->saveImage($this->category()->getId(), null, $names[$id], $descs[$id],$orders[$id],$id);
//            }
//         }
//         $this->infoMsg()->addMessage($this->_('Obrázky byly uloženy'));
//         $this->link()->reload();
//      }
//
//
//      $this->view()->template()->images = $imagesM->getImages($this->category()->getId());
//      $this->view()->template()->addForm = $addForm;
//      $this->view()->template()->editForm = $editForm;
//      $this->view()->template()->urlKey = $this->getRequest('urlkey');
//      $this->view()->template()->addTplFile("editimages.phtml");
//   }

//   private function saveImageForm() {
//      $addForm = new Form('addimage_');
//      $addFile = new Form_Element_File('image', $this->_('Obrázek'));
//      $addFile->addValidation(new Form_Validator_FileExtension(array('jpg', 'jpeg', 'png', 'gif')));
//      $addFile->setUploadDir($this->category()->getModule()->getDataDir());
//      $addForm->addElement($addFile);
//
//      $addSubmit = new Form_Element_Submit('send',$this->_('Odeslat'));
//      $addForm->addElement($addSubmit);
//
//      if($addForm->isValid()) {
//         $file = $addFile->getValues();
//         $image = new Filesystem_File_Image($file['name'], $this->category()->getModule()->getDataDir());
//         $image->saveAs($this->category()->getModule()->getDataDir().self::DIR_SMALL,
//                 $this->category()->getModule()->getParam('imagesmallwidth', self::SMALL_WIDTH),
//                 $this->category()->getModule()->getParam('imagesmallheight', self::SMALL_HEIGHT), true);
//         $image->saveAs($this->category()->getModule()->getDataDir().self::DIR_MEDIUM,
//                 $this->category()->getModule()->getParam('imagewidth', self::MEDIUM_WIDTH),
//                 $this->category()->getModule()->getParam('imageheight', self::MEDIUM_HEIGHT));
//
//         // uloženhí do db
//         $imagesM = new PhotoGalery_Model_Images();
//         $imagesM->saveImage($this->category()->getId(), $image->getName(), $image->getName());
//      }
//      return $addForm;
//   }

   /**
    * Metoda pro upload fotek pomocí Ajax requestu
    */
//   public function uploadFileController() {
//      $this->checkWritebleRights();
//      if($this->saveImageForm()->isValid()) {
//         echo "1";
//      } else {
//         echo $this->_('Neplatný typ souboru');
//      }
//   }

//   public function checkFileController() {
//      $fileArray = array();
//      foreach ($_POST as $key => $value) {
//         if ($key != 'folder') {
//            if (file_exists($this->getModule()->getDataDir() . $value)) {
//               $fileArray[$key] = $value;
//            }
//         }
//      }
//      echo json_encode($fileArray);
//   }

//   public function editphotoController() {
//      $this->checkWritebleRights();
//
//      $m = new PhotoGalery_Model_Images();
//      $image = $m->getImage($this->getRequest('id'));
//
//      $editForm = new Form('image_');
//
//      $elemX = new Form_Element_Hidden('start_x');
//      $editForm->addElement($elemX);
//      $elemY = new Form_Element_Hidden('start_y');
//      $editForm->addElement($elemY);
//
//      $elemW = new Form_Element_Hidden('width');
//      $editForm->addElement($elemW);
//
//      $elemH = new Form_Element_Hidden('height');
//      $editForm->addElement($elemH);
//
//      $elemSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
//      $editForm->addElement($elemSubmit);
//
//      if($editForm->isValid()) {
//         $image = new Filesystem_File_Image($image->{PhotoGalery_Model_Images::COLUMN_FILE},
//                 $this->getModule()->getDataDir().self::DIR_MEDIUM.DIRECTORY_SEPARATOR);
//         $image->cropAndSave($this->getModule()->getDataDir().self::DIR_SMALL.DIRECTORY_SEPARATOR,
//                 self::SMALL_WIDTH, self::SMALL_HEIGHT,
//                 $editForm->start_x->getValues(), $editForm->start_y->getValues(),
//                 $editForm->width->getValues(), $editForm->height->getValues());
//         $this->infoMsg()->addMessage($this->_('Miniatura byla upravena'));
//         $this->link()->route('editimages')->reload();
//      }
//
//
//      $this->view()->template()->form = $editForm;
//      $this->view()->template()->image = $image;
//      $this->view()->template()->addTplFile("editphoto.phtml");
//   }
}
?>