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

class HPSlideShow_Controller extends Controller {

   public function init() 
   {
      $this->checkControllRights();
      $this->module()->setDataDir('hpslideshow');
   }

   public function mainController()
   {
      $dimensions = $this->category()->getParam('dimensions');
      if($this->category()->getParam('enabled', false) == false || $dimensions == null || empty($dimensions)
      ){
         $this->errMsg()->addMessage($this->tr('Šablona nemá implementovánu obrázkovou SlideShow'));
         return;
      }

      $formUpload = new Form('upload_');
      $grp = $formUpload->addGroup('file', $this->tr('Nahrání obrázků'));

      $elemFile = new Form_Element_File('images', $this->tr('Obrázky'));
      $elemFile->setMultiple(true);
      $elemFile->addValidation(new Form_Validator_FileExtension('jpg;png'));
      $formUpload->addElement($elemFile, $grp);

      $elemUpload = new Form_Element_Submit('upload', $this->tr('Nahrát'));
      $formUpload->addElement($elemUpload, $grp);

      if($formUpload->isValid()){
         $model = new HPSlideShow_Model();

         $images = $formUpload->images->getValues();

         foreach ($images as $sendImg) {
            $imgRec = $model->newRecord();
            $imgRec->{HPSlideShow_Model::COLUMN_ORDER} = 0;
            $imgRec->save();
            // resize?
            $image = new File_Image($sendImg);
            $image->move($this->module()->getDataDir())->rename($imgRec->getPK().'.'.$image->getExtension());
            $image->getData()
               ->resize($dimensions['width'], $dimensions['height'], File_Image_Base::RESIZE_CROP)
               ->save();

            $imgRec->{HPSlideShow_Model::COLUMN_FILE} = $imgRec->getPK().'.'.$image->getExtension();
            $imgRec->save();
         }
         $this->infoMsg()->addMessage($this->tr('Obrázky byly nahrány'));
         $this->link()->redirect();
      }
      $this->view()->formUpload = $formUpload;

      $formEdit = new Form('img_edit_');
      $formEdit->addElement(new Form_Element_Hidden('id'));

      $elemCats = new Form_Element_Select('catId', $this->tr('Kategorie'));
      $elemCats->addOption($this->tr('Žádný nebo zadaný níže'), 0);

      $modelC = new Model_Category();
      $cats = $modelC
         ->where(Model_Category::COLUMN_DEF_RIGHT." LIKE 'r__'", array())
         ->order(Model_Category::COLUMN_NAME)
         ->records();

      foreach ($cats as $c) {
         $elemCats->addOption($c->{Model_Category::COLUMN_NAME}, $c->getPK());
      }

      $formEdit->addElement($elemCats);

      $elemLink = new Form_Element_Text('link', $this->tr('Odkaz'));
      $elemLink->setLangs();
      $elemLink->addValidation(new Form_Validator_Url());
      $formEdit->addElement($elemLink);

      $elemLabel = new Form_Element_TextArea('label', $this->tr('Text'));
      $elemLabel->setLangs();
      $formEdit->addElement($elemLabel);
      
      $elemImage = new Form_Element_File('image', $this->tr('Obrázek'));
      $elemImage->addValidation(new Form_Validator_FileExtension('jpg;png'));
      $formEdit->addElement($elemImage);

      $formEdit->addElement(new Form_Element_Submit('save', $this->tr('Uložit')));

      if($formEdit->isValid()){

         $model = new HPSlideShow_Model();

         $img = $model->record($formEdit->id->getValues());
         $img->{HPSlideShow_Model::COLUMN_ID_CAT} = $formEdit->catId->getValues();
         $img->{HPSlideShow_Model::COLUMN_LINK} = $formEdit->link->getValues();
         $img->{HPSlideShow_Model::COLUMN_LABEL} = $formEdit->label->getValues();

         if($formEdit->image->getValues() != null){
            $image = new File_Image($formEdit->image->getValues());
            $image->move($this->module()->getDataDir())->rename($img->getPK().'.'.$image->getExtension(), false);
            $image->getData()
               ->resize($dimensions['width'], $dimensions['height'], File_Image_Base::RESIZE_CROP)
               ->save();
         }
         
         $img->save();

         $this->infoMsg()->addMessage($this->tr('Uloženo'));
         $this->link()->redirect();
      }

      $this->view()->formEdit = $formEdit;


      // load images
      $model = new HPSlideShow_Model();
      $this->view()->images = $model
         ->order(HPSlideShow_Model::COLUMN_ORDER)
         ->joinFK(HPSlideShow_Model::COLUMN_ID_CAT)
         ->records();

      $this->view()->imagesUrl = $this->module()->getDataDir(true);
   }

   public function editImageController()
   {
      $action = $this->getRequestParam('action');

      if($action == 'delete' && $this->getRequestParam('id') != null){
         $model = new HPSlideShow_Model();

         $rec = $model->record($this->getRequestParam('id'));

         $file = new File($rec->{HPSlideShow_Model::COLUMN_FILE}, $this->module()->getDataDir());
         try {
            $file->delete();
         } catch (File_Exception $e){
            $this->log($e->getMessage());
         }

         $model->delete($rec);
         $this->infoMsg()->addMessage($this->tr('Obrázek byl smazán'));
         $this->link()->redirect();
      } else if($action == 'changepos' && $this->getRequestParam('id', false) && $this->getRequestParam('pos', false)){
         HPSlideShow_Model::changeOrder($this->getRequestParam('id'), $this->getRequestParam('pos'));
         $this->infoMsg()->addMessage($this->tr('Změna pozice byla uložena'));
         $this->link()->redirect();
      } else if($action == 'changestate' && $this->getRequestParam('id', false)){
         $model = new HPSlideShow_Model();
         $rec = $model->record($this->getRequestParam('id'));
         $rec->{HPSlideShow_Model::COLUMN_ACTIVE} = !$rec->{HPSlideShow_Model::COLUMN_ACTIVE};
         $rec->save();
         $this->infoMsg()->addMessage($this->tr('Stav byl uložen'));
         $this->link()->redirect();
      }

   }

}
