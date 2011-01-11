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
   const DEFAULT_IMAGES_IN_LIST = 4;
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
      $this->setOption('deleteMsg', $this->tr('Galerie byla smazána'));
      $this->setOption('publicMsg', $this->tr('Galerie byla zveřejněna'));
      if(parent::showController() === false) return false;
      if($this->view()->formDelete instanceof Form){
         $this->view()->formDelete->delete->setLabel($this->tr('Smazat galerii'));
      }
      if($this->view()->formPublic instanceof Form){
         $this->view()->formPublic->public->setLabel($this->tr('Zveřejnit galerii'));
      }

      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('idArt', $this->view()->article->{Articles_Model_Detail::COLUMN_ID});
      $ctr->setOption('subdir', $this->view()->article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR);
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
   protected function deleteArticle($idArticle) {
      // smazání fotek
      $photogalCtrl = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $photogalCtrl->setOption('subdir', $this->view()->article[Articles_Model::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR);
      $photogalCtrl->deleteImages($idArticle);
      unset ($photogalCtrl);

      //odstranění adresáře s fotkama
      $dir = new Filesystem_Dir($this->category()->getModule()->getDataDir().$this->view()->article[Articles_Model::COLUMN_URLKEY][Locales::getDefaultLang()]);
      $dir->rmDir();
      // delete article record
      parent::deleteArticle($idArticle);
   }

   /**
    * Přidání galerie
    *
    * @todo  Zbytečně tady !!! proč se to dělá znovu a nedědí
    */
   public function addController() {
      $this->checkWritebleRights();
      $this->setOption('textEmpty', true);
      $this->setOption('actionAfterAdd', 'editphotos');

      parent::addController();
      $this->view()->form->save->setLabel(array($this->tr('Pokračovat'), $this->tr('Zrušit')));
   }

   /**
    * @todo  Zbytečně tady !!! proč se to dělá znove a nedědí
    */
   public function editController() {
      $this->setOption('textEmpty', true);
      parent::editController();
   }

   /**
    * Uložení samotného článku
    * @param <type> $names
    * @param <type> $urlkeys
    * @param <type> $form
    */
   protected function saveArticle($names, $urlkeys,Form $form, Model_ORM_Record $article=null) {
      $id = parent::saveArticle($names, $urlkeys, $form, $article);
      // přejmenování adresáře
      if($article !== null) {
         $model = new Articles_Model();
         $newArticle = $model->record($id);

         if($article[Articles_Model::COLUMN_URLKEY][Locales::getDefaultLang()]
                 != $newArticle[Articles_Model::COLUMN_URLKEY][Locales::getDefaultLang()]) {
            $dir = new Filesystem_Dir($this->category()->getModule()->getDataDir()
                            .$article[Articles_Model::COLUMN_URLKEY][Locales::getDefaultLang()]);
            $dir->rename($urlkeys[Locales::getDefaultLang()]);
         }
      }
      return $id;
   }

   public function editphotosController() {
      $artModel = new Articles_Model();
      $art = $artModel->where(Articles_Model::COLUMN_URLKEY,$this->getRequest('urlkey'))->record();
      if($art == false) return false;
      // TOHLE chce dořešit, prasárna, ale ve 4 ráno nic nenapadne
      if($this->category()->getRights()->isControll() OR
         ($this->category()->getRights()->isWritable() AND Auth::getUserId() == $art->{Articles_Model_Detail::COLUMN_ID_USER})) {
      } else {
         return false;
      }

      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('idArt', $art->{Articles_Model_Detail::COLUMN_ID});
      $ctr->setOption('subdir', $art[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR);
      $art->{Articles_Model_Detail::COLUMN_EDIT_TIME} = new DateTime();
      $art->{Articles_Model_Detail::COLUMN_ID_USER_LAST_EDIT} = Auth::getUserId();
      $artModel->save($art);
      $ctr->editphotosController($this->link()->route('detail', array('urlkey' => $art->{Articles_Model_Detail::COLUMN_URLKEY})));
      $this->view()->article = $art;
   }

   public function checkFileController() {
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->checkFileController();
   }

   public function uploadFileController() {
      $artModel = new Articles_Model_Detail();
//      $art = $artModel->getArticleById((int)$this->getRequestParam('addimage_idArt'));
      $art = $artModel->getArticle($this->getRequest('urlkey'));
      if($art == false) return false;
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());

      if($art !== false) {
         $ctr->setOption('idArt', $art->{Articles_Model_Detail::COLUMN_ID});
         $ctr->setOption('subdir', $art[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR);
         $artModel->setLastChange($art->{Articles_Model_Detail::COLUMN_ID});
      }
      $ctr->uploadFileController();
   }

   public function editphotoController() {
      $artModel = new Articles_Model_Detail();
      $art = $artModel->getArticle($this->getRequest('urlkey'));
      if($art == false) return false;
      $ctr = new Photogalery_Controller($this->category(), $this->routes(), $this->view());
      $ctr->setOption('subdir', $art[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR);
      $ctr->editphotoController();
   }

   public function exportArticleController(){
      $this->checkReadableRights();
      $model= new Articles_Model_Detail();
      $article = $model->getArticle($this->getRequest('urlkey'));
      if($article === false) return false;

      $modelPhotos = new PhotoGalery_Model_Images();
      $this->view()->article = $article;
      $this->view()->images = $modelPhotos->getImages($this->category()->getId(), $article->{Articles_Model_Detail::COLUMN_ID});

      // adresáře k fotkám
      $this->view()->subdir = $this->view()->article[Articles_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()].DIRECTORY_SEPARATOR;
      $this->view()->websubdir = str_replace(DIRECTORY_SEPARATOR, URL_SEPARATOR, $this->view()->subdir);
   }

   public function settings(&$settings,Form &$form) {
      $phCtrl = new Photogalery_Controller($this->category(), $this->routes(), $this->view(), $this->link());
      $phCtrl->settings($settings, $form);

      parent::settings($settings, $form);

      $elemImgList = new Form_Element_Text('imagesinlist', 'Počet obrázků v seznamu');
      $elemImgList->setSubLabel('Výchozí: '.self::DEFAULT_IMAGES_IN_LIST.' obrázků');
      $elemImgList->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemImgList,'view');

      if(isset($settings['imagesinlist'])) {
         $form->imagesinlist->setValues($settings['imagesinlist']);
      }
      
      if($form->isValid()){
         $settings['imagesinlist'] = $form->imagesinlist->getValues();
      }
   }
}
?>