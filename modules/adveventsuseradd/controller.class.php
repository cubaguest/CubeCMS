<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class AdvEventsUserAdd_Controller extends AdvEventsBase_Controller {

   public function init()
   {
      parent::init();
   }

   public function mainController()
   {
      parent::mainController();

      if (Auth::isAdmin()) {
         Url_Link::getCategoryAdminLink(ADVEVENT_CAT_EVENTS)->route('addEvent')->redirect();
      }


      if (Auth::isLogin()) {
         $form = $this->createEventForm();

         // tady bude organizátor skupiny, pokud je přihlášená
         // načti organizátora podle skupiny uivatele
         $mOrg = new AdvEventsBase_Model_Organizers();
         $organizers = $mOrg->where(AdvEventsBase_Model_Organizers::COLUMN_ID_GROUP . " = :idg", array('idg' => Auth::getGroupId()))->records();
         $form->organizer->setOptions(array());
         $form->organizer->addOption($this->tr('-- anonymní --'), 0);
         foreach ($organizers as $organizer) {
            $form->organizer->addOption($organizer->{AdvEventsBase_Model_Organizers::COLUMN_NAME}, $organizer->{AdvEventsBase_Model_Organizers::COLUMN_ID});
         }
      } else {
         $form = $this->createAnonymEditEventForm();
      }

//      if ($form->isSend() && isset($form->dates) && $form->dates->getValues() != null) {
//         $t = json_decode($form->dates->getValues());
//         Debug::log($t);
//      }
      if ($form->isValid()) {
         if (Auth::isLogin()) {
//            $form = $this->createEventForm();
         } else {
            $this->procesAnonymEditEventForm($form);


//            $this->infoMsg()->addMessage($this->tr('Vaše událost byla uložena. O jejím schválení Vás budeme informovat.'));
//            $this->link()->redirect();
         }
      }

      $this->view()->formEdit = $form;
   }

   protected function createAnonymEditEventForm(Model_ORM_Record $event = null)
   {
      $form = new Form('advevent_guestadd_');

      $fGrpEventInfo = $form->addGroup('event', $this->tr('Informace o události'));
      $fGrpContact = $form->addGroup('contact', $this->tr('Kontakntí údaje'));

      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $elemName->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemName, $fGrpEventInfo);

      $elemSubName = new Form_Element_Text('subname', $this->tr('Podnázev'));
//      $elem->addValidation(new Form_Validator_NotEmpty());
      $elemSubName->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemSubName, $fGrpEventInfo);

      $places = AdvEventsBase_Model_Places::getPlaces();
      $elemPlace = new Form_Element_Select('place', $this->tr('Místo konání'));
      $elemPlace->setCheckOptions(false);
      $elemPlace->addOption($this->tr(''), '');
      $elemPlace->addValidation(new Form_Validator_NotEmpty());
//      $elemPlace->addOption($this->tr('-- vlastní --'), -1);
      foreach ($places as $place) {
         $elemPlace->addOption(
             $place->{AdvEventsBase_Model_Places::COLUMN_NAME} . ' (' . $place->{AdvEventsBase_Model_Locations::COLUMN_NAME} . ')', $place->{AdvEventsBase_Model_Places::COLUMN_ID});
      }
      $form->addElement($elemPlace, $fGrpEventInfo);
      if ($form->isSend()) {
         if (!is_numeric($elemPlace->getValues())) {
            $elemPlace->addOption($elemPlace->getValues(), $elemPlace->getValues());
         }
      }

//      $elemPlaceCustom = new Form_Element_Text('placeCustom', $this->tr('Vlastní místo konání'));
//      $form->addElement($elemPlaceCustom);

      $elemArea = new Form_Element_Select('area', $this->tr('Oblast'));
      $areas = AdvEventsBase_Model_Locations::getAllRecords();
      foreach ($areas as $area) {
         $elemArea->addOption($area->{AdvEventsBase_Model_Locations::COLUMN_NAME}, $area->getPK());
      }
      $elemArea->setValues(1);
      $form->addElement($elemArea, $fGrpEventInfo);

//      $elemOrg = new Form_Element_Text('organizer', $this->tr('Organizátor'));
//      $form->addElement($elemOrg, $fGrpEventInfo);


      $cats = AdvEventsBase_Model_Categories::getAllRecords();
      $elemCat = new Form_Element_Select('category', $this->tr('Kategorie'));
      $elemCat->addValidation(new Form_Validator_NotEmpty());
      $elemCat->setCheckOptions(false);
      $elemCat->addOption($this->tr(''), '');
//      $elemCat->addOption($this->tr('-- vlastní --'), -1);
      foreach ($cats as $cat) {
         $elemCat->addOption($cat->{AdvEventsBase_Model_Categories::COLUMN_NAME}, $cat->{AdvEventsBase_Model_Categories::COLUMN_ID});
      }
      $form->addElement($elemCat, $fGrpEventInfo);

      $elemText = new Form_Element_TextArea('text', $this->tr('Text'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemText, $fGrpEventInfo);

      $elemWebSite = new Form_Element_Text('website', $this->tr('Webová stránka'));
      $elemWebSite->addValidation(new Form_Validator_Url());
      $elemWebSite->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemWebSite, $fGrpEventInfo);

      $elemFacebook = new Form_Element_Text('facebook', $this->tr('Facebook'));
      $elemFacebook->addValidation(new Form_Validator_Url());
      $elemFacebook->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemFacebook, $fGrpEventInfo);

      $elemTitleImage = new Form_Element_File('titleImage', $this->tr('Titulní obrázek/plakát'));
      $elemTitleImage->addValidation(new Form_Validator_FileSize(1 * 1024 * 1024));
      $form->addElement($elemTitleImage, $fGrpEventInfo);

      $elemImages = new Form_Element_ImagesUploader('images', $this->tr('Ostatní obrázky'));
      $elemImages->addValidation(new Form_Validator_FileSize(1 * 1024 * 1024));
      $form->addElement($elemImages, $fGrpEventInfo);


      // parametry eventu, asi tady nakonec nebudou
      $elemImagesOrder = new Form_Element_Hidden('imagesOrder');
      $form->addElement($elemImagesOrder, $fGrpEventInfo);
      $elemTimes = new Form_Element_Hidden('dates');
      $elemTimes->addValidation(new Form_Validator_NotEmpty($this->tr('Nebyly vyplněna žádná data konání události')));
      $form->addElement($elemTimes, $fGrpEventInfo);

      if (!$event) {
         $eContactName = new Form_Element_Text('contactName', $this->tr('Jméno a přijmení'));
         $eContactName->addValidation(new Form_Validator_NotEmpty());
         $form->addElement($eContactName, $fGrpContact);

         $eContactEmail = new Form_Element_Text('contactEmail', $this->tr('E-mail'));
         $eContactEmail->addValidation(new Form_Validator_NotEmpty());
         $eContactEmail->addValidation(new Form_Validator_Email());
         $form->addElement($eContactEmail, $fGrpContact);

         $eContactPhone = new Form_Element_Text('contactPhone', $this->tr('Telefon'));
         $eContactPhone->addValidation(new Form_Validator_Regexp(Form_Validator_Regexp::PHONE));
         $form->addElement($eContactPhone, $fGrpContact);

         $eContactNote = new Form_Element_TextArea('contactNote', $this->tr('Poznámka'));
         $form->addElement($eContactNote, $fGrpContact);

         $eContactCaptcha = new Form_Element_Captcha('contactCaptcha', $this->tr('Kontrola'));
         $form->addElement($eContactCaptcha, $fGrpContact);
      }


      if ($event) {
//         $form->name->setValues($event->{AdvEventsBase_Model_Events::COLUMN_NAME});
//         $form->subname->setValues($event->{AdvEventsBase_Model_Events::COLUMN_SUBNAME});
//         $form->place->setValues($event->{AdvEventsBase_Model_Events::COLUMN_ID_PLACE});
//         $form->category->setValues($event->{AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY});
//         $form->text->setValues($event->{AdvEventsBase_Model_Events::COLUMN_TEXT});
//         $form->website->setValues($event->{AdvEventsBase_Model_Events::COLUMN_WEBSITE});
//         $form->active->setValues($event->{AdvEventsBase_Model_Events::COLUMN_ACTIVE});
//         $form->recommended->setValues($event->{AdvEventsBase_Model_Events::COLUMN_RECOMMENDED});
//         $form->organizer->setValues($event->{AdvEventsBase_Model_Events::COLUMN_ID_ORGANIZER});
//         $form->facebook->setValues($event->{AdvEventsBase_Model_Events::COLUMN_URL_FACEBOOK});
      }

//      $elem = new Form_Element_SaveCancel('save');
      $elem = new Form_Element_Submit('save', $this->tr('Přidat událost'));
      $form->addElement($elem);


      return $form;
   }

   protected function procesAnonymEditEventForm(Form $form, Model_ORM_Record $event = null)
   {
      // byla zadána vlastní místo?
      $idPlace = $form->place->getValues();
      if (!is_numeric($form->place->getValues())) {
         $place = AdvEventsBase_Model_Places::getNewRecord();
         $place->{AdvEventsBase_Model_Places::COLUMN_ID_LOCATION} = $form->area->getValues();
         $place->{AdvEventsBase_Model_Places::COLUMN_NAME} = $form->place->getValues();

         $place->save();
         $idPlace = $place->getPK();
      }

      // byla zadána vlastní kategorie?
      $idCategory = $form->category->getValues();
      if (!is_numeric($form->category->getValues())) {
         $cat = AdvEventsBase_Model_Categories::getNewRecord();
         $cat->{AdvEventsBase_Model_Categories::COLUMN_NAME} = $form->category->getValues();
         $cat->save();
         $idCategory = $cat->getPK();
      }

      // uložení eventu
      if ($event == null) {
         $event = AdvEventsBase_Model_Events::getNewRecord();
      }
      $event->{AdvEventsBase_Model_Events::COLUMN_NAME} = $form->name->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_ACTIVE} = false;
      $event->{AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY} = $idCategory;
      $event->{AdvEventsBase_Model_Events::COLUMN_ID_PLACE} = $idPlace;
      $event->{AdvEventsBase_Model_Events::COLUMN_SUBNAME} = $form->subname->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_URL_FACEBOOK} = $form->facebook->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_WEBSITE} = $form->website->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_TEXT} = $form->text->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_APPROVED} = 0; // musí být schávelno
      $event->save();

      // uložení informací o uživateli, který akci přidal
      $user = AdvEventsBase_Model_UserAdds::getNewRecord();
      $user->{AdvEventsBase_Model_UserAdds::COLUMN_NAME} = $form->contactName->getValues();
      $user->{AdvEventsBase_Model_UserAdds::COLUMN_EMAIL} = $form->contactEmail->getValues();
      $user->{AdvEventsBase_Model_UserAdds::COLUMN_ID_EVENT} = $event->getPK();
      $user->{AdvEventsBase_Model_UserAdds::COLUMN_NOTE} = $form->contactNote->getValues();
      $user->{AdvEventsBase_Model_UserAdds::COLUMN_PHONE} = $form->contactPhone->getValues();
      $user->save();
      
      
      // uložení obrázků
      // titulní
      if ($form->titleImage->getValues() != null) {
         /* @var $file File */
         $file = $form->titleImage->createFileObject();
         $file->move(AdvEvents_Controller::getEventImagesDir($event->getPK()));
         $imgRec = AdvEventsBase_Model_EventsImages::getNewRecord();
         $imgRec->{AdvEventsBase_Model_EventsImages::COLUMN_IS_TITLE} = true;
         $imgRec->{AdvEventsBase_Model_EventsImages::COLUMN_ID_EVENT} = $event->getPK();
         $imgRec->{AdvEventsBase_Model_EventsImages::COLUMN_FILE} = $file->getName();
         $imgRec->save();
      }

      // ostatní
      if ($form->images->getValues() != null) {
         $images = $form->images->getValues();
         foreach ($images as $img) {
            $file = new File($img);
            $file->move(AdvEvents_Controller::getEventImagesDir($event->getPK()));
            $imgRec = AdvEventsBase_Model_EventsImages::getNewRecord();
            $imgRec->{AdvEventsBase_Model_EventsImages::COLUMN_IS_TITLE} = false;
            $imgRec->{AdvEventsBase_Model_EventsImages::COLUMN_ID_EVENT} = $event->getPK();
            $imgRec->{AdvEventsBase_Model_EventsImages::COLUMN_FILE} = $file->getName();
            $imgRec->save();
         }
      }
      
      // uložení časů
      if (isset($form->dates) && $form->dates->getValues() != null) {
         $t = json_decode($form->dates->getValues());
         foreach ($t as $dateObj) {
            $time = AdvEventsBase_Model_EventsTimes::getNewRecord();
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_ID_EVENT} = $event->getPK();
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN} = new DateTime($dateObj->dateFrom);
            if($dateObj->dateTo != null){
               $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END} = new DateTime($dateObj->dateTo);
            }
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN} = $dateObj->timeFrom;
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_END} = $dateObj->timeTo;
            $time->save();
         }
         
      }
      $this->sendNewEventAdminMail($event, $user);
      
      // $this->sendMailToUser($event, $user);
      // odeslání emailů pro schválení
   }
   
   
   protected function sendMailToUser(AdvEventsBase_Model_Events_Record $event, Model_ORM_Record $user)
   {
      // odkaz na smazání a editaci
      
   }

   public function settings(&$settings, Form &$form)
   {
      parent::settings($settings, $form);
   }

}
