<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class AdvEventsAdmPlaces_Controller extends AdvEventsBase_Controller {

   public function init()
   {
      parent::init();
      $this->checkControllRights();
   }

   public function mainController()
   {
      parent::mainController();

      $this->processDelete();

      // načtení sportů
      $model = new AdvEventsBase_Model_Places();
      $compScroll = new Component_Scroll();
      $compScroll->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE, 20);
      $compScroll->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());
      $this->view()->scroll = $compScroll;

      if($this->getRequestParam('filter', false)){
         $places = AdvEventsBase_Model_Places::getPlacesByString($this->getRequestParam('filter'),
            $compScroll->getRecordsOnPage(), $compScroll->getStartRecord(), AdvEventsBase_Model_Places::COLUMN_NAME);
      } else {
         $places = AdvEventsBase_Model_Places::getPlaces($compScroll->getRecordsOnPage(), $compScroll->getStartRecord(), AdvEventsBase_Model_Places::COLUMN_NAME);
      }

      $this->view()->places = $places;
   }

   public function addPlaceController()
   {
      $form = $this->createPlaceForm();
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         $place = $this->processPlaceForm($form);
         $this->infoMsg()->addMessage($this->tr('Místo bylo uloženo'));
         Template_Core::closePopupWindow($place->toArray());
         $this->link()->route()->redirect();
      }
      
      $this->view()->formEdit = $form;
   }
   
   public function editPlaceController($id)
   {
      $place = AdvEventsBase_Model_Places::getRecord($id);
      if(!$place){
         throw new InvalidArgumentException($this->tr('Požadované místo neexistuje'));
      }
      $form = $this->createPlaceForm($place);
      
      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         $this->processPlaceForm($form, $place);
         $this->infoMsg()->addMessage($this->tr('Místo bylo uloženo'));
         $this->link()->route()->redirect();
      }

      $this->view()->place = $place;
      $this->view()->formEdit = $form;
   }
   
   public function detailPlaceController($id)
   {
      $place = AdvEventsBase_Model_Places::getRecord($id);
      if(!$place){
         throw new InvalidArgumentException($this->tr('Požadované místo neexistuje'));
      }
      
      $this->view()->place = $place;
   }
   
   /* obslužné metody */
   
   
   protected function createPlaceForm(Model_ORM_Record $place = null)
   {
      $form = new Form('place_');

      $grpBase = $form->addGroup('base', $this->tr('Základní parametry'));
      $grpContacts = $form->addGroup('contacts', $this->tr('Kontakty'));
      $grpOther = $form->addGroup('other', $this->tr('Ostatní'));
          
      
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);

      $elem = new Form_Element_Text('name', $this->tr('Název'));
      $elem->addValidation(new Form_Validator_NotEmpty());
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem, $grpBase);

      $elem = new Form_Element_TextArea('desc', $this->tr('Popis'));
//      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem, $grpBase);

      $elem = new Form_Element_TextArea('address', $this->tr('Adresa'));
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem, $grpContacts);

      $elemLoc = new Form_Element_Select('location', $this->tr('Lokace'));
      $locations = AdvEventsBase_Model_Locations::getAllRecords();
      foreach ($locations as $loc) {
         $elemLoc->addOption($loc->{AdvEventsBase_Model_Locations::COLUMN_NAME}, $loc->getPK());
      }
      $elemLoc->setValues(1);
      $form->addElement($elemLoc, $grpContacts);
      
//      $elem = new Form_Element_Text('mapUrl', $this->tr('Odkaz na mapu'));
//      $elem->addValidation(new Form_Validator_Url());
//      $elem->addFilter(new Form_Filter_StripTags());
//      $form->addElement($elem);
      
      $elem = new Form_Element_Checkbox('mapEnabled', $this->tr('Mapa zapnuta'));
      $form->addElement($elem, $grpContacts);
      
      $elem = new Form_Element_Hidden('mapLat');
      $elem->setValues('49.4721519');
      $form->addElement($elem, $grpContacts);
      $elem = new Form_Element_Hidden('mapLng');
      $elem->setValues('17.973342');
      $form->addElement($elem, $grpContacts);

      
//      $elem = new Form_Element_TextArea('openingHours', $this->tr('Otevírací doba'));
//      $elem->addFilter(new Form_Filter_StripTags());
//      $form->addElement($elem, $grpOther);

      $elem = new Form_Element_Text('url', $this->tr('Odkaz na stránky'));
      $elem->addValidation(new Form_Validator_Url());
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem, $grpOther);
      
      $eimages = new Form_Element_ImagesUploader('images', $this->tr('Obrázky'));
      $eimages->setUploadDir(self::getPlaceImagesDir(0));
      if($place){
         $eimages->setUploadDir(self::getPlaceImagesDir($place->getPK()));
      } 
      $form->addElement($eimages, $grpOther);
      
      if($place){
         $form->name->setValues($place->{AdvEventsBase_Model_Places::COLUMN_NAME});
         $form->desc->setValues($place->{AdvEventsBase_Model_Places::COLUMN_DESC});
         $form->address->setValues($place->{AdvEventsBase_Model_Places::COLUMN_ADDRESS});
         $form->url->setValues($place->{AdvEventsBase_Model_Places::COLUMN_URL});
         $form->location->setValues($place->{AdvEventsBase_Model_Places::COLUMN_ID_LOCATION});
         if($place->{AdvEventsBase_Model_Places::COLUMN_LAT} != 0 && $place->{AdvEventsBase_Model_Places::COLUMN_LNG} != 0 ){
            $form->mapLat->setValues($place->{AdvEventsBase_Model_Places::COLUMN_LAT});
            $form->mapLng->setValues($place->{AdvEventsBase_Model_Places::COLUMN_LNG});
            $form->mapEnabled->setValues(true);
         }
      }
      
      $elem = new Form_Element_SaveCancel('save');
      $form->addElement($elem);

      
      return $form;
   }
   
   protected function processPlaceForm(Form $form, Model_ORM_Record $place = null)
   {
      $processImages = false;
      if($place == null){
         $place = AdvEventsBase_Model_Places::getNewRecord();
         $processImages = true;
      }
      // uložení místa
      $place->{AdvEventsBase_Model_Places::COLUMN_NAME} = $form->name->getValues();
      $place->{AdvEventsBase_Model_Places::COLUMN_DESC} = $form->desc->getValues();
      $place->{AdvEventsBase_Model_Places::COLUMN_ID_LOCATION} = $form->location->getValues();
//      $place->{SvbBase_Model_Places::COLUMN_OPENING_HOURS} = $form->openingHours->getValues();
      $place->{AdvEventsBase_Model_Places::COLUMN_ADDRESS} = $form->address->getValues();
//         $record->{SvbBase_Model_Places::COLUMN_MAP_URL} = $form->mapUrl->getValues();
      $place->{AdvEventsBase_Model_Places::COLUMN_URL} = $form->url->getValues();
      if($form->mapEnabled->getValues() == true){
         $place->{AdvEventsBase_Model_Places::COLUMN_LAT} = $form->mapLat->getValues();
         $place->{AdvEventsBase_Model_Places::COLUMN_LNG} = $form->mapLng->getValues();
      } else {
         $place->{AdvEventsBase_Model_Places::COLUMN_LAT} = 0;
         $place->{AdvEventsBase_Model_Places::COLUMN_LNG} = 0;
      }
      $place->save();
      
      
      // přesun obrázků do cílové složky k eventu
      if($processImages){
         $dir = self::getPlaceImagesDir($place->getPK());
         $dir->check();

         $files = $form->images->getValues();
         if(!empty($files)){
            foreach ($files as $file) {
               $fObj = new File($file);
               $fObj->move($dir);
            }
         }
      }
      return $place;
   }

   protected function processDelete()
   {
      $form = new Form('place_del');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);

      $elemSave = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $form->addElement($elemSave);

      if($form->isValid()){
         $model = new AdvEventsBase_Model_Places();
         $model->delete($form->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Místo bylo smazáno'));
         $this->link()->redirect();
      }
      $this->view()->formDelete = $form;
   }

   public function settings(&$settings, Form &$form)
   {
      parent::settings($settings, $form);
   }
}
