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

      // načtení článků
      $artModel = new Articles_Model_List();

      $scrollComponent = new Component_Scroll();
      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS,
              $artModel->getCountArticles($this->category()->getId()));

      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              $this->category()->getModule()->getParam('scroll', 
                      $this->category()->getModule()->getParam('scroll',10)));

      $scrollComponent->runCtrlPart();

      if($this->rights()->isWritable()){
         $onlyPublic = false;
      } else {
         $onlyPublic = true;
      }

      $articles = $artModel->getList($this->category()->getId(),$onlyPublic,
              $scrollComponent->getConfig(Component_Scroll::CONFIG_START_RECORD),
              $scrollComponent->getConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE));


      $this->view()->template()->scrollComp = $scrollComponent;
      $this->view()->template()->articles = $articles;
   }

   public function showController() {
      $this->checkReadableRights();

      $artM = new Articles_Model_Detail();
      $article = $artM->getArticle($this->getRequest('urlkey'));
      
      if($article == false){
         return false;
      }
      $artM->addShowCount($this->getRequest('urlkey'));
      $imagesM = new PhotoGalery_Model_Images();
      $images = $imagesM->getImages($this->category()->getId(), $article->{Articles_Model_Detail::COLUMN_ID});


      $this->view()->template()->article = $article;
      $this->view()->template()->images = $images;

      unset ($article);
      unset ($artM);
   }

   /**
    * Přidání galerie
    */
   public function addController() {
      $this->checkWritebleRights();
      
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
                 $this->category()->getId(), $this->auth()->getUserId(),$form->public->getValues());

         //načtení vytvořené galerie
         $gal = $artModel->getArticleById($artID);

         if($artID != 0) {
            $this->infoMsg()->addMessage($this->_('Galerie byla uložen'));
            // redirekt na editaci obrázků
            $this->link()->route('editphotos', array('urlkey' => $gal->{Articles_Model_Detail::COLUMN_URLKEY}))->reload();
         } else {
            $this->errMsg()->addMessage($this->_('Galerii se nepodařilo uložit'));
         }

      }

      $this->view()->template()->form = $form;
      $this->view()->template()->edit = false;
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

      $iPub = new Form_Element_Checkbox('public', $this->_('Veřejný'));
      $iPub->setSubLabel($this->_('Veřejný - viditelný všem návštěvníkům'));
      $iPub->setValues(true);
      $form->addElement($iPub);

      $iSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($iSubmit);

      return $form;
   }

   public function edittextController() {
      $this->checkWritebleRights();

      $form = $this->formEditGalery();

      $model = new Articles_Model_Detail();
      $art = $model->getArticle($this->getRequest('urlkey'));

      // doplnění formu
      $form->name->setValues($art->{Articles_Model_Detail::COLUMN_NAME});
      $form->text->setValues($art->{Articles_Model_Detail::COLUMN_TEXT});
      $form->urlkey->setValues($art->{Articles_Model_Detail::COLUMN_URLKEY});
      $form->public->setValues($art->{Articles_Model_Detail::COLUMN_PUBLIC});

      if($form->isValid()) {
         $urlkeys = $form->urlkey->getValues();
         $names = $form->name->getValues();
         foreach ($urlkeys as $lang => $variable) {
            if($variable == null AND $names[$lang] == null) {
               $urlkeys[$lang] = null;
            } else if($variable == null) {
               $urlkeys[$lang] = vve_cr_url_key($names[$lang]);
            } else {
               $urlkeys[$lang] = vve_cr_url_key($variable);
            }
         }
//         print("<br>");
//         print("<br>");
         $model->saveArticle($names, $form->text->getValues(), $urlkeys,
                 $this->category()->getId(), $this->auth()->getUserId(), 
                 $form->public->getValues(),$art->{Articles_Model_Detail::COLUMN_ID});

         //načtení vytvořené galerie
         $this->infoMsg()->addMessage($this->_('Galerie byla uložen'));
         $artNew = $model->getArticleById($art->{Articles_Model_Detail::COLUMN_ID});

         // redirekt na editaci obrázků
         $this->link()->route('detail', array('urlkey' => $artNew->{Articles_Model_Detail::COLUMN_URLKEY}))->reload();
      }

      $this->view()->template()->form = $form;
      $this->view()->template()->edit = true;
   }

   public function editphotosController() {
      $artModel = new Articles_Model_Detail();
      $art = $artModel->getArticle($this->getRequest('urlkey'));


      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('idArt', $art->{Articles_Model_Detail::COLUMN_ID});
      $ctr->editphotosController();

   }

   public function checkFileController() {
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->checkFileController();
   }

   public function uploadFileController() {
      $artModel = new Articles_Model_Detail();
      $art = $artModel->getArticle($this->getRequest('urlkey'));

      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());

      if($art !== false) {
         $ctr->setOption('idArt', $art->{Articles_Model_Detail::COLUMN_ID});
      }
      $ctr->uploadFileController();
   }

   public function editphotoController() {
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->editphotoController();
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