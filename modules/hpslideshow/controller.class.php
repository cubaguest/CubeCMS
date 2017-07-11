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

   const DATA_DIR = 'hpslideshow';

   public function init()
   {
      $this->checkControllRights();
      $this->module()->setDataDir(self::DATA_DIR);
   }

   public function mainController()
   {
      $dimensions = $this->category()->getParam('dimensions');
      if ($this->category()->getParam('enabled', false) == false || $dimensions == null || empty($dimensions)
      ) {
         $this->errMsg()->addMessage($this->tr('Šablona nemá implementovánu obrázkovou SlideShow'));
         return;
      }

      $formEdit = new Form('img_edit_');
      $formEdit->addElement(new Form_Element_Hidden('id'));

      $elemCats = new Form_Element_Select('catId', $this->tr('Kategorie'));
      $elemCats->addOption($this->tr('Žádný nebo zadaný níže'), 0);

      $modelC = new Model_Category();
      $cats = $modelC
              ->where(Model_Category::COLUMN_DEF_RIGHT . " LIKE 'r__'", array())
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

      $formEdit->addElement(new Form_Element_Checkbox('slogan_background', $this->tr('Zobrazit pozadí popisku')));

      $element_valid_from = new Form_Element_DateTime('valid_from', $this->tr('Platnost slidu od'));
      $element_valid_from->setShowTime(false);
      $formEdit->addElement($element_valid_from);

      $element_valid_to = new Form_Element_DateTime('valid_to', $this->tr('Platnost slidu do'));
      $element_valid_to->setShowTime(false);
      $formEdit->addElement($element_valid_to);

      $elemImage = new Form_Element_File('image', $this->tr('Obrázek'));
      $elemImage->addValidation(new Form_Validator_FileExtension('jpg;png'));
      $formEdit->addElement($elemImage);

      $formEdit->addElement(new Form_Element_Submit('save', $this->tr('Uložit')));

      if ($formEdit->isSend()) {
//          validate the valid_to if is bigger than valid _from
         if ($formEdit->valid_to->getValues()) {
            $from = $formEdit->valid_from->getValues();
            $to = $formEdit->valid_to->getValues();

            if ($to <= $from) {
               $formEdit->valid_to->setError($this->tr('Platnost do musí být větší než platnost od!'));
            }
         }
      }

      if ($formEdit->isValid()) {

         $model = new HPSlideShow_Model();

         $img = $model->record($formEdit->id->getValues());
         $img->{HPSlideShow_Model::COLUMN_ID_CAT} = $formEdit->catId->getValues();
         $img->{HPSlideShow_Model::COLUMN_LINK} = $formEdit->link->getValues();
         $img->{HPSlideShow_Model::COLUMN_LABEL} = $formEdit->label->getValues();

         if ($formEdit->image->getValues() != null) {
            $image = new File_Image($formEdit->image->getValues());
            $image->move($this->module()->getDataDir())->rename($img->getPK() . '.' . $image->getExtension(), false);
            $image->getData()
                    ->resize($dimensions['width'], $dimensions['height'], File_Image_Base::RESIZE_CROP)
                    ->save();
            $img->{HPSlideShow_Model::COLUMN_FILE} = $image->getName();
         }

         $img->{HPSlideShow_Model::COLUMN_SLOGAN_BACKGROUND} = $formEdit->slogan_background->getValues();
         $img->{HPSlideShow_Model::COLUMN_VALID_FROM} = $formEdit->valid_from->getValues();
         $img->{HPSlideShow_Model::COLUMN_VALID_TO} = $formEdit->valid_to->getValues();

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

   public function addSliderImageController()
   {
      $dimensions = $this->category()->getParam('dimensions');
      $formUpload = new Form('upload_');
      $grp = $formUpload->addGroup('file', $this->tr('Nahrání obrázků'));

      $elemFile = new Form_Element_File('images', $this->tr('Obrázky'));
      $elemFile->setMultiple(true);
      $elemFile->addValidation(new Form_Validator_FileExtension('jpg;png'));
      $formUpload->addElement($elemFile, $grp);

      $elemUpload = new Form_Element_Submit('upload', $this->tr('Nahrát'));
      $formUpload->addElement($elemUpload, $grp);

      if ($formUpload->isValid()) {
         $model = new HPSlideShow_Model();

         $images = $formUpload->images->getValues();
         $anchorStep = false;
         foreach ($images as $sendImg) {
            $imgRec = $model->newRecord();
            $imgRec->{HPSlideShow_Model::COLUMN_ORDER} = 0;
            $imgRec->save();
            // resize?
            $image = new File_Image($sendImg);
            $image->move($this->module()->getDataDir())->rename($imgRec->getPK() . '.' . $image->getExtension());
            $image->getData()
                    ->resize($dimensions['width'], $dimensions['height'], File_Image_Base::RESIZE_CROP)
                    ->save();

            $imgRec->{HPSlideShow_Model::COLUMN_FILE} = $imgRec->getPK() . '.' . $image->getExtension();
            $imgRec->save();
            if (!$anchorStep) {
               $anchorStep = $imgRec->getPK();
            }
         }
         $this->infoMsg()->addMessage($this->tr('Obrázky byly nahrány'));
         $this->link()->route()->anchor('image-' . $anchorStep)->redirect();
      }
      $this->view()->formUpload = $formUpload;
   }

   public function sliderSettingsController()
   {
      $options = Face::getCurrent()->getParam('options', 'hpslideshow', array());
      if (!empty($options)) {
         $form = new Form('slider_setting_');

         foreach ($options as $element) {
            $element->setValues(self::getSliderOption($element->getName(false)));
            $form->addElement($element);
         }
         $form->addElement(new Form_Element_Submit('save', $this->tr('Uložit')));

         if ($form->isValid()) {
            foreach ($form as $element) {
               /* @var $element Form_Element */
               if ($element instanceof Form_Element_Text || $element instanceof Form_Element_TextArea || $element instanceof Form_Element_Select
               ) {
                  $value = $element->getValues();
                  Model_Config::setValue('HP_SLIDER_' . strtoupper($element->getName(false)), $value, Model_Config::TYPE_STRING, 4);
               }
            }
            $this->infoMsg()->addMessage($this->tr('Nastavení bylo uloženo'));
            $this->link()->redirect();
         }
         $this->view()->formSliderSettings = $form;
      }
   }

   public static function getSliderOption($name, $default = false)
   {
      return Model_Config::getValue('HP_SLIDER_' . strtoupper($name), $default);
   }

   public function editImageController()
   {
      $action = $this->getRequestParam('action');

      if ($action == 'delete' && $this->getRequestParam('id') != null) {
         $model = new HPSlideShow_Model();

         $rec = $model->record($this->getRequestParam('id'));

         $file = new File($rec->{HPSlideShow_Model::COLUMN_FILE}, $this->module()->getDataDir());
         try {
            $file->delete();
         } catch (File_Exception $e) {
            $this->log($e->getMessage());
         }

         $model->delete($rec);
         $this->infoMsg()->addMessage($this->tr('Obrázek byl smazán'));
         $this->link()->redirect();
      } else if ($action == 'changepos' && $this->getRequestParam('id', false) && $this->getRequestParam('pos', false)) {
         HPSlideShow_Model::setRecordPosition($this->getRequestParam('id'), $this->getRequestParam('pos'));
         $this->infoMsg()->addMessage($this->tr('Změna pozice byla uložena'));
         $this->link()->redirect();
      } else if ($action == 'changestate' && $this->getRequestParam('id', false)) {
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
