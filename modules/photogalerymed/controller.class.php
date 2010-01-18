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

class Photogalerymed_Controller extends Articles_Controller {
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
      parent::mainController();
   }

   public function showController() {
      $this->checkReadableRights();
      parent::showController();

      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('idArt', $this->view()->article->{Articles_Model_Detail::COLUMN_ID});
      $ctr->setOption('subdir', $this->view()->article[Articles_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()].DIRECTORY_SEPARATOR);
      $ctr->mainController();

      // adresáře k fotkám
      $this->view()->subdir = $ctr->getOption('subdir',null);
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $ctr->getOption('subdir', null));
      unset ($ctr);
   }

   /**
    * Metoda smaže článek z dat - overriding
    * @param int $idArticle
    */
   protected function deleteArticle($idArticle){
      $artM = new Articles_Model_Detail();
      // smazání fotek
      $photogalCtrl = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $photogalCtrl->deleteImages($idArticle);

      $artM->deleteArticle($idArticle);
      $this->infoMsg()->addMessage($this->_('Galerie byla smazána'));
   }

   /**
    * Přidání galerie
    *
    * @todo  Zbytečně tady !!! proč se to dělá znovu a nedědí
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
                 $this->category()->getId(), Auth::getUserId(),$form->public->getValues());

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

   /**
    * @todo  Zbytečně tady !!! proč se to dělá znove a nedědí
    */
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
         // přejmenování adresáře
         if($art[Articles_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()] != $urlkeys[Locale::getDefaultLang()]){
            $dir = new Filesystem_Dir($this->category()->getModule()->getDataDir().$art[Articles_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]);
            $dir->rename($urlkeys[Locale::getDefaultLang()]);
         }

         $model->saveArticle($names, $form->text->getValues(), $urlkeys,
                 $this->category()->getId(), Auth::getUserId(),
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
      if($art == false) return false;

      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('idArt', $art->{Articles_Model_Detail::COLUMN_ID});
      $ctr->setOption('subdir', $art[Articles_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()].DIRECTORY_SEPARATOR);
      $artModel->setLastChange($art->{Articles_Model_Detail::COLUMN_ID});
      $ctr->editphotosController();

      $this->view()->template()->article = $art;
   }

   public function checkFileController() {
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->checkFileController();
   }

   public function uploadFileController() {
      $artModel = new Articles_Model_Detail();
      $art = $artModel->getArticleById((int)$_POST['addimage_idArt']);
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());

      if($art !== false) {
         $ctr->setOption('idArt', $art->{Articles_Model_Detail::COLUMN_ID});
         $ctr->setOption('subdir', $art[Articles_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()].DIRECTORY_SEPARATOR);
         $artModel->setLastChange($art->{Articles_Model_Detail::COLUMN_ID});
      }
      $ctr->uploadFileController();
   }

   public function editphotoController() {
      $artModel = new Articles_Model_Detail();
      $art = $artModel->getArticle($this->getRequest('urlkey'));
      if($art == false) return false;
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('subdir', $art[Articles_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()].DIRECTORY_SEPARATOR);
      $ctr->editphotoController();
   }
}
?>