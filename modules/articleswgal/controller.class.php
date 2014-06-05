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

class ArticlesWGal_Controller extends Articles_Controller {
   const DEFAULT_IMAGES_IN_LIST = 0;
   
   protected function init()
   {
      parent::init();
      // registrace modulu fotogalerie pro obsluhu galerie
      $this->registerModule('photogalery');
   }
   
   /**
    * Kontroler pro zobrazení fotogalerii
    */
   public function mainController() 
   {
      parent::mainController();
   }

   public function showController($urlkey) 
   {
      $this->checkReadableRights();
      if(parent::showController($urlkey) === false) return false;

      // fotogalerie
      $this->view()->pCtrl = new Photogalery_Controller($this);
      $this->view()->pCtrl->loadText = false;
      $this->view()->pCtrl->idItem = $this->view()->article->{Articles_Model_Detail::COLUMN_ID};
      $this->view()->pCtrl->subDir = $this->view()->article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR;
      $this->view()->pCtrl->mainController();

      // adresáře k fotkám
      $this->view()->subdir = $this->view()->pCtrl->subDir;
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $this->view()->pCtrl->subDir);
   }

   /**
    * Metoda smaže článek z dat - overriding
    * @param int $idArticle
    */
   protected function deleteArticle($idArticle) 
   {
      $artM = new Articles_Model_Detail();
      // smazání fotek
      $photogalCtrl = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $photogalCtrl->subDir = $this->view()->article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR;
      $photogalCtrl->deleteImages($idArticle);
      unset ($photogalCtrl);

      //odstranění adresáře s fotkama
      $dir = new Filesystem_Dir($this->category()->getModule()->getDataDir().$this->view()->article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]);
      $dir->rmDir();

      $artM->deleteArticle($idArticle);
      $this->infoMsg()->addMessage($this->tr('Článek s galerií byl smazán'));
   }

   /**
    * Uložení samotného článku
    * @param <type> $names
    * @param <type> $urlkeys
    * @param <type> $form
    */
   protected function saveArticle($names, $urlkeys,Form $form, Model_ORM_Record $article=null) 
   {
      // přejmenování adresáře
      $id = parent::saveArticle($names, $urlkeys, $form, $article);

      if($article !== null) {
         $model = new Articles_Model_Detail();
         $newArticle = $model->getArticleById($article->{Articles_Model_Detail::COLUMN_ID});

         if($article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
                 != $newArticle[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
                 AND file_exists($this->category()->getModule()->getDataDir().$article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()])) {
            $dir = new Filesystem_Dir($this->category()->getModule()->getDataDir()
                            .$article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]);
            $dir->rename($urlkeys[Locales::getDefaultLang()]);
         }
      }

      return $id;
   }

   public function exportArticleController($urlkey, $output)
   {
      parent::exportArticleController($urlkey, $output);
      // načtení fotek z článku, první bude zobrazena
      $photosM = new PhotoGalery_Model_Images();
      $images = $photosM->getImages($this->category()->getId(), $this->view()->article->{Articles_Model_Detail::COLUMN_ID});
      $this->view()->images = $images;
      $this->view()->imagesCount = $photosM->getCountImages($this->category()->getId(), $this->view()->article->{Articles_Model_Detail::COLUMN_ID});
   }

   /**
    * Poslední článek
    */
   public function currentArticleController()
   {
      parent::currentArticleController();
      $photosM = new PhotoGalery_Model_Images();
      $images = $photosM->getImages($this->category()->getId(), $this->view()->article->{Articles_Model_Detail::COLUMN_ID}, 1);
      $this->view()->images = $images;
   }

   /**
    * Metoda pro přípravu spuštění registrovaného modulu
    * @param Controller $ctrl -- kontroler modulu
    * @param string $module -- název modulu
    * @param string $action -- akce
    * @return type 
    */
   protected function callRegisteredModule(Controller $ctrl, $module, $action)
   {
      $artModel = new Articles_Model();
      $art = $artModel->where(Articles_Model::COLUMN_URLKEY,$this->getRequest('urlkey'))->record();
      if($art == false) return false;
      // base setup variables
      $ctrl->idItem = $art->{Articles_Model_Detail::COLUMN_ID};
      $ctrl->subDir = $art[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR;
      $ctrl->linkBack = $this->link()->route('detail');
      
      $ctrl->view()->name = $art->{Articles_Model_Detail::COLUMN_NAME};
      $ctrl->view()->link = $this->link()->route('detail');
   }

   public function settings(&$settings,Form &$form) 
   {
      $phCtrl = new Photogalery_Controller($this);
      $phCtrl->settings($settings, $form);
      $form->removeElement('tplMain');

      parent::settings($settings, $form);

      $elemImgList = new Form_Element_Text('imagesinlist', $this->tr('Počet obrázků v seznamu'));
      $elemImgList->setSubLabel('Výchozí: '.self::DEFAULT_IMAGES_IN_LIST.' obrázků');
      $elemImgList->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgList,'view',2);

      if(isset($settings['imagesinlist'])) {
         $form->imagesinlist->setValues($settings['imagesinlist']);
      }

      if($form->isValid()){
         $settings['imagesinlist'] = $form->imagesinlist->getValues();
      }
   }
}
?>