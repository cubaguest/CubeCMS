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

class HPSlideShowAdv_Controller extends Controller {

   const DATA_DIR = 'hpslideshowadv';


   public function init() 
   {
      $this->checkControllRights();
      $this->module()->setDataDir(self::DATA_DIR);
      $this->view()->imagesUrl = $this->module()->getDataDir(true);
   }

   public function mainController()
   {
      $dimensions = $this->category()->getParam('dimensions');
      if($this->category()->getParam('enabled', false) == false || $dimensions == null || empty($dimensions)
      ){
         $this->errMsg()->addMessage($this->tr('Šablona nemá implementovánu obrázkovou SlideShow'));
         return;
      }

      // check delete
      $formRemove = $this->createRemoveSlideForm();
      if($formRemove->isValid()){
         $model = new HPSlideShowAdv_Model();
         $slide = $model->record($formRemove->id->getValues());

//         if($panel->{Model_Panel::COLUMN_BACK_IMAGE} != null){
//            $file = new Filesystem_File($panel->{Model_Panel::COLUMN_BACK_IMAGE},
//                    AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
//              .Panel_Obj::DATA_DIR.DIRECTORY_SEPARATOR);
//            $file->delete();
//         }

         $model->delete($formRemove->id->getValues());

         $this->infoMsg()->addMessage($this->tr('Slajd byl smazán'));
         $this->link()->reload();
      }
      $this->view()->formRemove = $formRemove;
      
      // load slides
      $slidesModel = new HPSlideShowAdv_Model();
      $slides = $slidesModel
          ->columns(array('*', 'items_count' => 'COUNT('.HPSlideShowAdv_Model_Items::COLUMN_ID.')'))
          ->join(HPSlideShowAdv_Model::COLUMN_ID, 'HPSlideShowAdv_Model_Items', HPSlideShowAdv_Model_Items::COLUMN_ID_SLIDE)
          ->groupBy(array(HPSlideShowAdv_Model_Items::COLUMN_ID_SLIDE))
          ->records();
      
      $this->view()->slides = $slides;
      
      return;
   }

   
   public function addSlideController()
   {
      
      $formInfo = $this->createFormSlideInfo();
      if($formInfo->isSend() && $formInfo->send->getValues() == false){
         $this->link()->route()->redirect();
      }
      if($formInfo->isValid()){
         $slide = $this->processFormSlideInfo($formInfo);
         $this->infoMsg()->addMessage($this->tr('Slajd byl uložen'));
         $this->link()->route('editSlide', array('id' => $slide->getPK()))->redirect();
      }
      $this->view()->formSlideInfo = $formInfo;
   }
   
   
   public function editSlideController($id)
   {
      $this->view()->dimensions = $this->category()->getParam('dimensions');
      
      $slide = HPSlideShowAdv_Model::getRecord($id);
      if(!$slide){
         throw new UnexpectedPageException();
      }
      
      $formInfo = $this->createFormSlideInfo($slide);
      
      if($formInfo->isSend() && $formInfo->send->getValues() == false){
         $this->link()->route()->redirect();
      }
      if($formInfo->isValid()){
         $slide = $this->processFormSlideInfo($formInfo, $slide);
         $this->infoMsg()->addMessage($this->tr('Slajd byl uložen'));
         $this->link()->redirect();
      }
      
      $formUploadImage = $this->createFormSlideUplaodImage($slide);
      $this->view()->formSlideInfo = $formInfo;
      $this->view()->formUplaodImage = $formUploadImage;
      $this->view()->slide = $slide;
      $this->view()->slideItems = $slide->getItems(HPSlideShowAdv_Model_Items::COLUMN_DELAY);
      $this->view()->itemClasses = $this->category()->getParam('classes', array());
      $this->view()->itemAnimations = $this->category()->getParam('animations', array());
      $this->view()->itemAnimationsOut = $this->category()->getParam('animationsOut', array());
   }
   
   public function editSlideParamsController()
   {
      $slide = HPSlideShowAdv_Model::getRecord($this->getRequestParam('id'));
      if(!$slide){
         throw new UnexpectedPageException();
      }
      
      switch ($this->getRequestParam('action')) {
         case 'changestate':
            $slide->{HPSlideShowAdv_Model::COLUMN_ACTIVE} = !$slide->{HPSlideShowAdv_Model::COLUMN_ACTIVE};
            $slide->save();
            break;
         case 'changepos':
            $newPos = $this->getRequestParam('pos', 1);
            HPSlideShowAdv_Model::setRecordPosition($this->getRequestParam('id'), $newPos);
            break;
      }
      $this->view()->slide = $slide->__toArray();
   }
   
   public function editItemController($id, $idItem)
   {
      $id = (int)$id;
      $idItem = (int)$idItem;
      if($idItem == 0){
         $item = HPSlideShowAdv_Model_Items::getNewRecord();
         $item->{HPSlideShowAdv_Model_Items::COLUMN_ID_SLIDE} = $id;
      } else {
         $item = HPSlideShowAdv_Model_Items::getRecord($idItem);
      }
      if(!$item){
         throw new UnexpectedPageException();
      }
      
      $action = $this->getRequestParam('action');
      
      switch ($action) {
         case 'remove':
            $m = new HPSlideShowAdv_Model_Items();
            $m->delete($item);
            return; // musí tu být, protože se nic dalšího vrátit nedá
            break;
         case 'save':
            $item->{HPSlideShowAdv_Model_Items::COLUMN_CONTENT} = 
                $this->getRequestParam('content', $item->{HPSlideShowAdv_Model_Items::COLUMN_CONTENT}); 
            $item->{HPSlideShowAdv_Model_Items::COLUMN_POS_X} = 
                $this->getRequestParam('posx', $item->{HPSlideShowAdv_Model_Items::COLUMN_POS_X},0); 
            $item->{HPSlideShowAdv_Model_Items::COLUMN_POS_Y} = 
                $this->getRequestParam('posy', $item->{HPSlideShowAdv_Model_Items::COLUMN_POS_Y},0); 
            $item->{HPSlideShowAdv_Model_Items::COLUMN_WIDTH} = 
                $this->getRequestParam('width', $item->{HPSlideShowAdv_Model_Items::COLUMN_WIDTH}, 100); 
            $item->{HPSlideShowAdv_Model_Items::COLUMN_HEIGHT} = 
                $this->getRequestParam('height', $item->{HPSlideShowAdv_Model_Items::COLUMN_HEIGHT}, 100); 
            
            $item->{HPSlideShowAdv_Model_Items::COLUMN_CLASSES} = 
                $this->getRequestParam('classes', $item->{HPSlideShowAdv_Model_Items::COLUMN_CLASSES}, null); 
            $item->{HPSlideShowAdv_Model_Items::COLUMN_ANIMATION} = 
                $this->getRequestParam('animation', $item->{HPSlideShowAdv_Model_Items::COLUMN_ANIMATION}, null); 
            $item->{HPSlideShowAdv_Model_Items::COLUMN_ANIMATION_SPEED} = 
                $this->getRequestParam('animationspeed', $item->{HPSlideShowAdv_Model_Items::COLUMN_ANIMATION_SPEED}, 0); 
            $item->{HPSlideShowAdv_Model_Items::COLUMN_ANIMATION_OUT} = 
                $this->getRequestParam('animationout', $item->{HPSlideShowAdv_Model_Items::COLUMN_ANIMATION_OUT}, null); 
            $item->{HPSlideShowAdv_Model_Items::COLUMN_ANIMATION_SPEED_OUT} = 
                $this->getRequestParam('animationspeedout', $item->{HPSlideShowAdv_Model_Items::COLUMN_ANIMATION_SPEED_OUT}, 0); 
            $item->{HPSlideShowAdv_Model_Items::COLUMN_DELAY} = 
                $this->getRequestParam('delay', $item->{HPSlideShowAdv_Model_Items::COLUMN_DELAY}, 0); 
            $item->{HPSlideShowAdv_Model_Items::COLUMN_LINK} = 
                $this->getRequestParam('link', $item->{HPSlideShowAdv_Model_Items::COLUMN_LINK}, null); 
            $item->{HPSlideShowAdv_Model_Items::COLUMN_STYLES} = 
                $this->getRequestParam('styles', $item->{HPSlideShowAdv_Model_Items::COLUMN_STYLES}, null); 
            
            $item->save();
            break;
      }
      
      $arrItem = $item->__toArray();
      foreach ($arrItem as $key => $val) {
         $arrItem[$key] = (string)$val;
      }
      
      $this->view()->item = $arrItem;
      $this->view()->action = $action;
   }
   
   public function uploadSlideItemController($id)
   {
      $id = (int)$id;
      $item = HPSlideShowAdv_Model_Items::getNewRecord();
      $item->{HPSlideShowAdv_Model_Items::COLUMN_ID_SLIDE} = $id;
      
      $form = $this->createFormSlideUplaodImage();
      
      if($form->isValid()){
         $item = $this->processFormSlideUplaodImage($form, $item);
         $this->infoMsg()->addMessage($this->tr('Obrázek byl nahrán'));
         $this->link()->redirect();
      }
      $arrItem = $item->__toArray();
      foreach ($arrItem as $key => $val) {
         $arrItem[$key] = (string)$val;
      }
      
      $this->view()->item = $arrItem;
   }
   
   /* interní metody pro zpracování */
   
   
   protected function createRemoveSlideForm()
   {
      $formRemove = new Form('slideDelete');
      $elemId = new Form_Element_Hidden('id');
      $formRemove->addElement($elemId);

      $elemSubmit = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $formRemove->addElement($elemSubmit);
      return $formRemove;
   }
   
   protected function createFormSlideInfo(Model_ORM_Record $record = null)
   {
      $f = new Form('slide_info');

      $eName = new Form_Element_Text('name', $this->tr('Název slajdu'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $f->addElement($eName);
      
      $eImage = new Form_Element_File('image', $this->tr('Základní obrázek'));
      $eImage->addValidation(new Form_Validator_FileExtension('jpg'));
      $eImage->setUploadDir($this->module()->getDataDir(false));
      $f->addElement($eImage);
      
      $langs = Locales::getAppLangs();
      if(count($langs) > 1){
         $eLang = new Form_Element_Select('lang', $this->tr('Jazyk'));
         $eLang->setOptions(array_flip(Locales::getAppLangsNames(true)));
         $f->addElement($eLang);
      }
      
      $animations = $this->category()->getParam('slide-animations');
      if($animations && is_array($animations)){
         $eAnimations = new Form_Element_Select('animation', $this->tr('Animace přechodu'));
         $eAnimations->setOptions($animations);
         $f->addElement($eAnimations);
         
         $eDelay = new Form_Element_Text('delay', $this->tr('Délka zobrazení'));
         $eDelay->setSubLabel($this->tr('Jak dlouho bude slajd zobrazen v sekundách'));
         $eDelay->addValidation(new Form_Validator_IsNumber());
         $eDelay->setValues(5);
         $f->addElement($eDelay);
      }
      
      if($record != null){
         $f->name->setValues($record->{HPSlideShowAdv_Model::COLUMN_NAME});
         isset($f->animation) ? $f->animation->setValues($record->{HPSlideShowAdv_Model::COLUMN_ANIMATION}) : null;
         isset($f->delay) ? $f->delay->setValues($record->{HPSlideShowAdv_Model::COLUMN_DELAY}) : null;
         isset($f->lang) ? $f->lang->setValues($record->{HPSlideShowAdv_Model::COLUMN_LANG}) : null;
      }
      
      $eSend = new Form_Element_SaveCancel('send', array($this->tr('Uložit'), $this->tr('Zavřít')));
      $f->addElement($eSend);
      
      return $f;
   }
   
   protected function createFormSlideUplaodImage(Model_ORM_Record $record = null)
   {
      $f = new Form('slide_upload_');

      $eFile = new Form_Element_File('image', $this->tr('Obrázek'));
      $eFile->addValidation(new Form_Validator_FileExtension('png;jpg'));
      $eFile->setUploadDir($this->module()->getDataDir());
      $f->addElement($eFile);
      
      $eSend = new Form_Element_Submit('send', $this->tr('Nahrát'));
      $f->addElement($eSend);
      
      return $f;
   }
   
   protected function processFormSlideInfo(Form $form, Model_ORM_Record $record = null)
   {
      if($record == null){
         $record = HPSlideShowAdv_Model::getNewRecord();
      }
      
      $record->{HPSlideShowAdv_Model::COLUMN_NAME} = $form->name->getValues();
      $record->{HPSlideShowAdv_Model::COLUMN_LANG} = isset($form->lang) ? $form->lang->getValues() : Locales::getDefaultLang();
      $record->{HPSlideShowAdv_Model::COLUMN_ANIMATION} = isset($form->animation) ? $form->animation->getValues() : '';
      $record->{HPSlideShowAdv_Model::COLUMN_DELAY} = isset($form->delay) ? $form->delay->getValues() : 5;
      if($form->image->getValues()){
         $file = new File($form->image);
         $record->{HPSlideShowAdv_Model::COLUMN_IMAGE} = $file->getName();
      }
      $record->save();
      
      return $record;
   }
   
   protected function processFormSlideUplaodImage(Form $form, Model_ORM_Record $record = null)
   {
      if($record == null){
         $record = HPSlideShowAdv_Model_Items::getNewRecord();
      }
      $file = new File_Image($form->image);
      $record->{HPSlideShowAdv_Model_Items::COLUMN_IMAGE} = $file->getName();
      $w = $file->getData()->getWidth();
      $h = $file->getData()->getHeight();
      $d = $this->category()->getParam('dimensions');
      // pokud je větší než dimenze, nasatv jej na 300px
      if($w > $d['width'] || $h > $d['height']){
         if($file->getData()->isLandscape()){ // na šířku
            $w = 300;
            $h = 300 * ($w / $h);
         } else {
            $h = 300;
            $w = 300 * ($h / $w);
         }
      }
      
      $record->{HPSlideShowAdv_Model_Items::COLUMN_WIDTH} = $file->getData()->getWidth();
      $record->{HPSlideShowAdv_Model_Items::COLUMN_HEIGHT} = $file->getData()->getHeight();
      
      
      $record->save();
      
      return $record;
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
         HPSlideShow_Model::setRecordPosition($this->getRequestParam('id'), $this->getRequestParam('pos'));
         $this->infoMsg()->addMessage($this->tr('Změna pozice byla uložena'));
         $this->link()->redirect();
      } else if($action == 'changestate' && $this->getRequestParam('id', false)){
         $model = new HPSlideShow_Model();
         $rec = $model->record($this->getRequestParam('id'));
         $rec->{HPSlideShow_Model::COLUMN_ACTIVE} = !$rec->{HPSlideShow_Model::COLUMN_ACTIVE};
         $rec->save();
         $this->view()->state = $rec->{HPSlideShow_Model::COLUMN_ACTIVE};
         $this->infoMsg()->addMessage($this->tr('Stav byl uložen'));
         $this->link()->redirect();
      }

   }

}
