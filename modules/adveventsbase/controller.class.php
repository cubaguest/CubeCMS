<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class AdvEventsBase_Controller extends Controller {
   const DIR_BASE = 'advevents';
   const DIR_EVENTS = 'events';
   const DIR_PALCES = 'places';
   const DIR_IMAGES = 'places';
   const DIR_HOMEPAGE = 'homepage';
   const HOMEPAGE_IMAGE = 'homepage.jpg';

   public function init()
   {
      $this->module()->setDataDir(self::DIR_BASE);
   }

   public function mainController()
   {}

   // základní metody
   public function getPlacesController()
   {
      $this->checkReadableRights();

      if($this->getRequestParam('search', false)){
         $placesRecords = SvbBase_Model_Places::getPlacesByString(
            $this->getRequestParam('search'), $this->getRequestParam('limit', 20), $this->getRequestParam('from', 0));
      } else {
         $placesRecords = SvbBase_Model_Places::getPlaces($this->getRequestParam('limit', 20), $this->getRequestParam('from', 0));
      }

      $results = array();
      if(!empty($placesRecords)){
         foreach($placesRecords as $place){
            $results[$place->getPK()] = $place->toArray();
         }
      }
      $this->view()->results = $results;
   }

   public static function getEventsDir($url = true)
   {
      if($url){
         return Url_Request::getBaseWebDir(false).VVE_DATA_DIR.'/'.self::DIR_BASE.'/'.self::DIR_EVENTS.'/';
      }
      return AppCore::getAppDataDir().self::DIR_BASE.DIRECTORY_SEPARATOR.self::DIR_EVENTS.DIRECTORY_SEPARATOR;
   }
   
   protected function createEventForm(Model_ORM_Record $event = null, $params = array())
   {
      $params += array(
          
      );
      
      $form = new Form('advevent_');

      $grpBase = $form->addGroup('base', $this->tr('Základní parametry'));
      $grpContacts = $form->addGroup('contacts', $this->tr('Datum a čas Konání'));
      $grpOther = $form->addGroup('other', $this->tr('Ostatní'));
          
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);
      
      $elemName = new Form_Element_Text('name', $this->tr('Název'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $elemName->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemName);
      
      $elemSubName = new Form_Element_Text('subname', $this->tr('Podnázev'));
//      $elem->addValidation(new Form_Validator_NotEmpty());
      $elemSubName->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemSubName);
      
      $places = AdvEventsBase_Model_Places::getPlaces(100000);
      $elemPlace = new Form_Element_Select('place', $this->tr('Místo konání'));
      $elemPlace->addOption($this->tr('-- neznámé --'), 0);
      foreach ($places as $place) {
         $elemPlace->addOption(
             $place->{AdvEventsBase_Model_Places::COLUMN_NAME}.' ('.$place->{AdvEventsBase_Model_Locations::COLUMN_NAME}.')',
             $place->{AdvEventsBase_Model_Places::COLUMN_ID});
      }
      $form->addElement($elemPlace);
      
      $mOrg = new AdvEventsBase_Model_Organizers();
      $organizes = $mOrg->order(AdvEventsBase_Model_Organizers::COLUMN_NAME)->records();
      $elemOrg = new Form_Element_Select('organizer', $this->tr('Organizátor'));
      $elemOrg->addOption($this->tr('-- anonymní --'), 0);
      foreach ($organizes as $organizer) {
         $elemOrg->addOption($organizer->{AdvEventsBase_Model_Organizers::COLUMN_NAME}, $organizer->{AdvEventsBase_Model_Organizers::COLUMN_ID});
      }
      $form->addElement($elemOrg);
      
      
      $cats = AdvEventsBase_Model_Categories::getAllRecords();
      $elemCat = new Form_Element_Select('category', $this->tr('Kategorie'));
      $elemCat->addOption($this->tr('-- nepřiřazena --'), 0);
      foreach ($cats as $cat) {
         $elemCat->addOption($cat->{AdvEventsBase_Model_Categories::COLUMN_NAME}, $cat->{AdvEventsBase_Model_Categories::COLUMN_ID});
      }
      $form->addElement($elemCat);
      
      
      $elemText = new Form_Element_TextArea('text', $this->tr('Text'));
//      $elem->addValidation(new Form_Validator_NotEmpty());
      $elemText->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemText);
      
      $elemWebSite = new Form_Element_Text('website', $this->tr('Webová stránka'));
      $elemWebSite->addValidation(new Form_Validator_Url());
      $elemWebSite->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemWebSite);
      
      $elemFacebook = new Form_Element_Text('facebook', $this->tr('Facebook'));
      $elemFacebook->addValidation(new Form_Validator_Url());
      $elemFacebook->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemFacebook);
      
      $elemActive = new Form_Element_Checkbox('active', $this->tr('Publikováno'));
      $elemActive->setValues(true);
      $form->addElement($elemActive);
      
      $elemRecommended = new Form_Element_Checkbox('recommended', $this->tr('Doporučeno'));
      $elemRecommended->setValues(false);
      $form->addElement($elemRecommended);
      
      if($event && $event->haveHomePageImage()){
         $elemShowHP = new Form_Element_Checkbox('homepage', $this->tr('Zobrazit na titulní stránce'));
         $elemShowHP->setValues($event->{AdvEventsBase_Model_Events::COLUMN_SHOW_ON_HP});
         $form->addElement($elemShowHP);
      }

      // parametry eventu, asi tady nakonec nebudou
      $elemImagesOrder = new Form_Element_Hidden('imagesOrder');
      $form->addElement($elemImagesOrder);
      $elemTimes = new Form_Element_Hidden('imagesTimes');
      $form->addElement($elemTimes);
      
      
      if($event){
         $form->name->setValues($event->{AdvEventsBase_Model_Events::COLUMN_NAME});
         $form->subname->setValues($event->{AdvEventsBase_Model_Events::COLUMN_SUBNAME});
         $form->place->setValues($event->{AdvEventsBase_Model_Events::COLUMN_ID_PLACE});
         $form->category->setValues($event->{AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY});
         $form->text->setValues($event->{AdvEventsBase_Model_Events::COLUMN_TEXT});
         $form->website->setValues($event->{AdvEventsBase_Model_Events::COLUMN_WEBSITE});
         $form->active->setValues($event->{AdvEventsBase_Model_Events::COLUMN_ACTIVE});
         $form->recommended->setValues($event->{AdvEventsBase_Model_Events::COLUMN_RECOMMENDED});
         $form->organizer->setValues($event->{AdvEventsBase_Model_Events::COLUMN_ID_ORGANIZER});
         $form->facebook->setValues($event->{AdvEventsBase_Model_Events::COLUMN_URL_FACEBOOK});
      }
      
//      $elem = new Form_Element_SaveCancel('save');
      $elem = new Form_Element_SaveCancel('send', array($this->tr('Uložit'), $this->tr('Zavřít')));
      $form->addElement($elem);

      
      return $form;
   }
   
   /**
    * 
    * @param Form $form
    * @param Model_ORM_Record $event
    * @return Model_ORM_Record
    */
   protected function processEventForm(Form $form, Model_ORM_Record $event = null)
   {
      if($event == null){
         $event = AdvEventsBase_Model_Events::getNewRecord();
      }
      $event->{AdvEventsBase_Model_Events::COLUMN_NAME} = $form->name->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_SUBNAME} = $form->subname->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_ID_PLACE} = $form->place->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY} = $form->category->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_TEXT} = $form->text->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_WEBSITE} = $form->website->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_ACTIVE} = $form->active->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_RECOMMENDED} = $form->recommended->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_ID_ORGANIZER} = $form->organizer->getValues();
      $event->{AdvEventsBase_Model_Events::COLUMN_URL_FACEBOOK} = $form->facebook->getValues();
      if(isset($form->homepage)){
         $event->{AdvEventsBase_Model_Events::COLUMN_SHOW_ON_HP} = $form->homepage->getValues();
      }
      $event->save();
      
      return $event;
   }
   
   protected function processAddEventImagesForm(Form $form, AdvEventsBase_Model_Events_Record $event = null)
   {
      $images = $form->images->getValues();
      if(!empty($images)){
         $model = new AdvEventsBase_Model_EventsImages();
         
         $storedImages = $event->getImages();
         
         foreach ($images as $key => $image) {
            $imgRec = $model->newRecord();
//            $imgRec->{AdvEventsBase_Model_EventsImages::COLUMN_NAME} = $image['name'];
            $imgRec->{AdvEventsBase_Model_EventsImages::COLUMN_FILE} = $image['name'];
            $imgRec->{AdvEventsBase_Model_EventsImages::COLUMN_ID_EVENT} = $event->getPK();
            if($key == 0 && count($storedImages) == 0){
               $imgRec->{AdvEventsBase_Model_EventsImages::COLUMN_IS_TITLE} = true;
            }
            $imgRec->save();
         }
      }
      return $event;
   }
   
   protected function processAddEventTimesForm(Form $form, Model_ORM_Record $event = null)
   {
      // uložení časů do db
      $time = AdvEventsBase_Model_EventsTimes::getNewRecord();
      $time->{AdvEventsBase_Model_EventsTimes::COLUMN_ID_EVENT} = $event->getPK();
      $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN} = new DateTime($form->date_from->getValues());
      if($form->date_to->getValues() != null){
         $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END} = new DateTime($form->date_to->getValues());
      }
      $time->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN} = $form->time_from->getValues();
      $time->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_END} = $form->time_to->getValues();
      $time->{AdvEventsBase_Model_EventsTimes::COLUMN_NOTE} = $form->note->getValues();
      $time->save();
   }
   
   
   protected function activateEvent($idOrEvent)
   {
      
      
      
   }
   
   /**
    * Metoda pro odeslání upozornění o aktivaci uživatelského eventu
    * @param Model_ORM_Record $event
    * @param Model_ORM_Record $user
    */
   protected function sendEventActivationUserMail(Model_ORM_Record $event, Model_ORM_Record $user = null)
   {
      // odkazy na smazání a editaci
      
      
   }
   
   /**
    * Metoda pro odeslání upozornění o smazání uživatelského eventu
    * @param Model_ORM_Record $event
    * @param Model_ORM_Record $user
    */
   protected function sendEventDeletedUserMail(Model_ORM_Record $event, Model_ORM_Record $user = null)
   {
      
      
      
   }
   
   /**
    * Meotda upozorní admina na nový event
    * @param Model_ORM_Record $event
    * @param Model_ORM_Record $user
    */
   protected function sendNewEventAdminMail(Model_ORM_Record $event, Model_ORM_Record $user = null)
   {
      // odkazy na aktivaci, smazání
      
      
   }
   
   
   /*
    * Podpůrné funkce
    */
   
   public static function getPlaceImagesDir($idPlace)
   {
      return new FS_Dir(AppCore::getAppDataDir().self::DIR_BASE.DIRECTORY_SEPARATOR.self::DIR_PALCES.DIRECTORY_SEPARATOR.'place-'.$idPlace.DIRECTORY_SEPARATOR);
   }
   
   public static function getPlaceImagesUrl($idPlace)
   {
      return Url_Request::getBaseWebDir(true).VVE_DATA_DIR."/".self::DIR_BASE."/".self::DIR_PALCES."/".'place-'.$idPlace.'/';
   }
   
   
   public static function getEventImagesDir($idEvent)
   {
      return new FS_Dir(AppCore::getAppDataDir().self::DIR_BASE.DIRECTORY_SEPARATOR.self::DIR_EVENTS.DIRECTORY_SEPARATOR.'event-images-'.$idEvent.DIRECTORY_SEPARATOR);
   }
   
   public static function getEventImagesUrl($idEvent)
   {
      return Url_Request::getBaseWebDir(true).VVE_DATA_DIR."/".self::DIR_BASE."/".self::DIR_EVENTS."/".'event-images-'.$idEvent.'/';
   }
   
   public static function getEventUrl($idEvent)
   {
      return Url_Request::getBaseWebDir(true).VVE_DATA_DIR."/".self::DIR_BASE."/".self::DIR_EVENTS.'/';
   }
   public static function getEventDir($idEvent)
   {
      return new FS_Dir(AppCore::getAppDataDir().self::DIR_BASE.DIRECTORY_SEPARATOR.self::DIR_EVENTS.DIRECTORY_SEPARATOR);
   }

   protected function autoImportEvents()
   {
      $modelEvents = new AdvEventsBase_Model_Events();
      $modelEventsTimes = new AdvEventsBase_Model_EventsTimes();
      $modelEventsImages = new AdvEventsBase_Model_EventsImages();
      
      // výmaz pro testování
//      $modelEvents->truncate();
//      $modelEventsTimes->truncate();
//      $modelEventsImages->truncate();
      
      
      // načte zdrojů a potom import podle zdroje
      $m = new AdvEventsBase_Model_EventsSources();
//      $m->createTable();
      $sources = $m
          ->where(AdvEventsBase_Model_EventsSources::COLUMN_ENABLED." = 1", array())
          ->records();
      if(empty($sources)){
         return;
      }
      foreach ($sources as $source) {
         $class = 'AdveventsBase_Imports_'.$source->{AdvEventsBase_Model_EventsSources::COLUMN_CLASS};
//         $paramsTmp = explode(';', $source->{AdvEventsBase_Model_EventsSources::COLUMN_PARAMS});
         $paramsTmp = str_getcsv($source->{AdvEventsBase_Model_EventsSources::COLUMN_PARAMS}, ';');
         
         $params = array();
         foreach ($paramsTmp as $p) {
//            $parts = explode(':', $p);
            $parts = str_getcsv($p, ':');
            if(isset($parts[0]) && $parts[1]){
               $params[$parts[0]] = $parts[1];
            }
         }
         
         $importer = new $class($this, $params);
         $importer->process();
      }
   }
   
   /* Autorun metody */
   public static function AutoRunDaily()
   {}
   public static function AutoRunHourly()
   {}
   public static function AutoRunMonthly()
   {}
   public static function AutoRunYearly()
   {}
   public static function AutoRunWeekly()
   {}
   
   public function settings(&$settings, Form &$form)
   {}

}
