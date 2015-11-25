<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class AdvEventsAdm_Controller extends AdvEventsBase_Controller {

   public function init()
   {
      parent::init();
      $this->checkControllRights();
   }

   public function mainController()
   {
      $this->checkControllRights();
      parent::mainController();

      $this->processDelete();
      $this->processDuplicate();
      $this->processChangeState();
      $this->processChangeRecommended();
//
//      // načtení sportů
//      $model = new SvbBase_Model_Events();
//      $compScroll = new Component_Scroll();
//      $compScroll->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE, 20);
//      $compScroll->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());
//      $this->view()->scroll = $compScroll;
//
//      $dateBegin = $this->getRequestParam('date', false) ?
//            new DateTime($this->getRequestParam('date')) : new DateTime();
//      $dateEnd = clone $dateBegin;
//
//      $date_range = isset($_GET['date_range']) ? $_GET['date_range'] : 'month';
//      switch ($date_range) {
//         case 'week':
//            $dateEnd->modify('+7 days');
//            break;
//         case 'year':
//            $dateEnd->modify('+1 year');
//            break;
//         case 'month':
//         default:
//            $dateEnd->modify('+1 month');
//
//      }
//      $this->view()->date_range = $date_range;
//
//
////      if($this->getRequestParam('filter', false)){
////         $events = SvbBase_Model_Events::getEventsByName($this->getRequestParam('filter'), false);
////      } else {
//         $events = SvbBase_Model_Events::getEventsByDateRange($dateBegin, $dateEnd, false);
////      }
//
//      $this->view()->events = $events;
//
//      // odkazy předchozí a další týden
//      $this->view()->linkPrev = $this->link()->param('date', $dateBegin->modify('-7 days')->format('d.m.Y'));
//      $this->view()->linkNext = $this->link()->param('date', $dateBegin->modify('+14 days')->format('d.m.Y'));
   }

   public function addEventController()
   {
      $this->processEditEvent();
   }

   public function editEventController()
   {
      $model = new SvbBase_Model_Events();
      $event = $model->where(SvbBase_Model_Events::COLUMN_ID." = :id", array('id' => $this->getRequest('idEvent', false)))->record();
      if(!$event){
         throw new UnexpectedPageException();
      }
      $this->processEditEvent($event);
      $this->view()->event = $event;
   }

   protected function processEditEvent(Model_ORM_Record $event = null)
   {
      $form = new Form('event_');

      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);

      $elem = new Form_Element_Text('name', $this->tr('Název'));
      $elem->addValidation(new Form_Validator_NotEmpty());
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem);

      $elem = new Form_Element_TextArea('perex', $this->tr('Perex'));
      $elem->addFilter(new Form_Filter_HTMLPurify());
      $elem->addValidation(new Form_Validator_MaxLength(200));
      $form->addElement($elem);

      $elem = new Form_Element_TextArea('desc', $this->tr('Popisek'));
//      $elem->addFilter(new Form_Filter_HTMLPurify());
      $form->addElement($elem);

      $elem = new Form_Element_Text('dateBegin', $this->tr('Datum začátku'));
      $elem->addValidation(new Form_Validator_NotEmpty());
      $elem->addValidation(new Form_Validator_Date());
      $elem->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($elem);

      $elem = new Form_Element_Text('dateEnd', $this->tr('Datum konce'));
      $elem->addValidation(new Form_Validator_Date());
      $elem->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($elem);

      $elem = new Form_Element_Text('timeBegin', $this->tr('Čas začátku'));
      $elem->addValidation(new Form_Validator_Time());
      $form->addElement($elem);

      $elem = new Form_Element_Text('timeEnd', $this->tr('Čas konce'));
      $elem->addValidation(new Form_Validator_Time());
      $form->addElement($elem);

      $elem = new Form_Element_Text('place', $this->tr('Místo konání'));
      $elem->addValidation(new Form_Validator_NotEmpty());
      $elem->setSubLabel($this->tr('Pokud místo není uloženo, vytvoří se automaticky nové. U existujícíhc míst musí být uvedeno jejich ID.'));
      $form->addElement($elem);

      $elemMapUrl = new Form_Element_Text('mapUrl', $this->tr('URL adresa mapy'));
      $elemMapUrl->addValidation(new Form_Validator_Url());
      $elemMapUrl->setSubLabel($this->tr('URL adresa mapy s Google Maps'));
      $form->addElement($elemMapUrl);

      $elem = new Form_Element_Select('sports', $this->tr('Sporty'));
      $elem->setMultiple(true);
      $sports = SvbBase_Model_Sports::getSports(1000);
      foreach($sports as $sport){
         $elem->addOption($sport->{SvbBase_Model_Sports::COLUMN_NAME}, $sport->getPK());
      }
      $form->addElement($elem);

      $elem = new Form_Element_Text('url', $this->tr('Odkaz na stránky'));
      $elem->addValidation(new Form_Validator_Url());
      $elem->addFilter(new Form_Filter_StripTags());
      $form->addElement($elem);

      $eImage = new Form_Element_File('titleImageUpload', $this->tr('Titulní obrázek'));
      $eImage->addValidation(new Form_Validator_FileExtension('jpg;png;gif'));
      $form->addElement($eImage);

      if(is_dir(self::getEventsDir(false))){
         $images = glob(self::getEventsDir(false) . "*.{jpg,gif,png,JPG,GIF,PNG}", GLOB_BRACE);
         //print each file name
         if(!empty ($images)){
            $elemImgSel = new Form_Element_Select('titleImage', $this->tr('Uložené titulní obrázky'));
            $elemImgSel->setOptions(array($this->tr('Žádný') => null));

            foreach($images as $image) {
               $elemImgSel->setOptions(array(basename($image) => basename($image)), true);
            }
            $form->addElement($elemImgSel);
         }
      }

      $elem = new Form_Element_Checkbox('active', $this->tr('Aktivní'));
      $elem->setValues(true);
      $form->addElement($elem);

      $elem = new Form_Element_Checkbox('recommended', $this->tr('Doporučeno'));
      $elem->setSubLabel($this->tr('Záznamy se označí "Doporučené redakcí"'));
      $form->addElement($elem);

      $elem = new Form_Element_Checkbox('cheeringOnly', $this->tr('Pouze sledování'));
      $form->addElement($elem);

      $elem = new Form_Element_SaveCancel('save');
      $form->addElement($elem);

      if($event){
         $form->id->setValues($event->getPK());
         $form->name->setValues($event->{SvbBase_Model_Events::COLUMN_NAME});
         $form->desc->setValues($event->{SvbBase_Model_Events::COLUMN_TEXT});
         $form->perex->setValues($event->{SvbBase_Model_Events::COLUMN_PEREX});
         $form->recommended->setValues($event->{SvbBase_Model_Events::COLUMN_RECOMMENDED});
         $form->active->setValues($event->{SvbBase_Model_Events::COLUMN_ACTIVE});
         $form->dateBegin->setValues(vve_date("%x", $event->{SvbBase_Model_Events::COLUMN_DATE_BEGIN}));
         if($event->{SvbBase_Model_Events::COLUMN_DATE_END}){
            $form->dateEnd->setValues(vve_date("%x", $event->{SvbBase_Model_Events::COLUMN_DATE_END}));
         }
         if($event->{SvbBase_Model_Events::COLUMN_TIME_BEGIN}){
            $form->timeBegin->setValues(vve_date("%G:%i", $event->{SvbBase_Model_Events::COLUMN_TIME_BEGIN}));
         }
         if($event->{SvbBase_Model_Events::COLUMN_TIME_END}){
            $form->timeEnd->setValues(vve_date("%G:%i", $event->{SvbBase_Model_Events::COLUMN_TIME_END}));
         }
         $form->url->setValues($event->{SvbBase_Model_Events::COLUMN_WEBSITE});
         $form->cheeringOnly->setValues($event->{SvbBase_Model_Events::COLUMN_CHEERING_ONLY});

         $place = SvbBase_Model_Places::getPlace($event->{SvbBase_Model_Events::COLUMN_ID_PLACE});
         $form->place->setValues(
            $place->{SvbBase_Model_Places::COLUMN_NAME}.' (ID:'.$place->getPK().')'
         );

         $form->titleImage->setValues($event->{SvbBase_Model_Events::COLUMN_IMAGE});
         $form->mapUrl->setValues($event->{SvbBase_Model_Events::COLUMN_MAP_URL});

         $sports = SvbBase_Model_Sports::getSportsByEventID($event->getPK());
         if($sports){
            $selectedSportsIDs = array();
            foreach($sports as $sport){
               $selectedSportsIDs[] = $sport->getPK();
            }
            $form->sports->setValues($selectedSportsIDs);
         }
      }

      if($form->isSend() && $form->save->getValues() == false){
         $this->link()->route()->redirect();
      }

      if($form->isValid()){
         // načíst id místa pokud je
         $matchID = array();
         $idPlace = 0;
         if(preg_match('/\(ID:([0-9]+)\)/', $form->place->getValues(), $matchID)){
            $idPlace = $matchID[1];
         } else {
            $modelPlaces = new SvbBase_Model_Places();
            $place = $modelPlaces->newRecord();
            $place->{SvbBase_Model_Places::COLUMN_NAME} = $form->place->getValues();
            $place->save();
            $idPlace = $place->getPK();
         }

         // uložit novou údálost
         if(!$event){
            $model = new SvbBase_Model_Events();
            $event = $model->newRecord();
         }

         /* TITLE IMAGE */
         if(isset ($form->titleImage)){
            $event->{SvbBase_Model_Events::COLUMN_IMAGE} = $form->titleImage->getValues();
         }
         if(isset ($form->titleImageUpload) AND $form->titleImageUpload->getValues() != null){
            $image = new File_Image($form->titleImageUpload);
            $image->move(self::getEventsDir(false));
            $event->{SvbBase_Model_Events::COLUMN_IMAGE} = $image->getName();
         }

         /*
          *  @todo tady přidat systém opakování
          */

         $event->{SvbBase_Model_Events::COLUMN_NAME} = $form->name->getValues();
         $event->{SvbBase_Model_Events::COLUMN_TEXT} = $form->desc->getValues();
         $event->{SvbBase_Model_Events::COLUMN_DATE_BEGIN} = $form->dateBegin->getValues();
         $event->{SvbBase_Model_Events::COLUMN_DATE_END} = $form->dateEnd->getValues();
         $event->{SvbBase_Model_Events::COLUMN_TIME_BEGIN} = $form->timeBegin->getValues();
         $event->{SvbBase_Model_Events::COLUMN_TIME_END} = $form->timeEnd->getValues();
         $event->{SvbBase_Model_Events::COLUMN_ID_PLACE} = $idPlace;
         $event->{SvbBase_Model_Events::COLUMN_WEBSITE} = $form->url->getValues();
         $event->{SvbBase_Model_Events::COLUMN_CHEERING_ONLY} = $form->cheeringOnly->getValues();
         $event->{SvbBase_Model_Events::COLUMN_RECOMMENDED} = $form->recommended->getValues();
         $event->{SvbBase_Model_Events::COLUMN_ACTIVE} = $form->active->getValues();
         $event->{SvbBase_Model_Events::COLUMN_PEREX} = $form->perex->getValues();
         $event->{SvbBase_Model_Events::COLUMN_MAP_URL} = $form->mapUrl->getValues();
         $event->save();

         // přiřadit sporty
         $selectedSport = $form->sports->getValues();
         if(!empty($selectedSport)){
            $modelEvSports = new SvbBase_Model_EventHasSports();
            // remove old connections
            $modelEvSports->where(SvbBase_Model_EventHasSports::COLUMN_ID_EVENT." = :ide", array('ide' => $event->getPK()))->delete();
            foreach($selectedSport as $id){
               $rec = $modelEvSports->newRecord();
               $rec->{SvbBase_Model_EventHasSports::COLUMN_ID_EVENT} = $event->getPK();
               $rec->{SvbBase_Model_EventHasSports::COLUMN_ID_SPORT} = $id;
               $rec->save();
            }
         }

         $this->infoMsg()->addMessage($this->tr('Událost byla uložena'));
         $this->link()->route()->redirect();
      }
      $this->view()->form = $form;
   }

   protected function processDelete()
   {
      $form = new Form('event_del');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);

      $elemSave = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $form->addElement($elemSave);

      if($form->isValid()){
         $model = new SvbBase_Model_Events();
         $model->delete($form->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Událost byla smazána'));
         $this->link()->redirect();
      }
      $this->view()->formDelete = $form;
   }

   protected function processChangeState()
   {
      $form = new Form('event_changeState');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);

      $elemSave = new Form_Element_Submit('change', $this->tr('Změnit stav'));
      $form->addElement($elemSave);

      if($form->isValid()){
         $event = SvbBase_Model_Events::getEvent($form->id->getValues());
         $event->{SvbBase_Model_Events::COLUMN_ACTIVE} = !$event->{SvbBase_Model_Events::COLUMN_ACTIVE};
         $event->save();
         $this->infoMsg()->addMessage( $event->{SvbBase_Model_Events::COLUMN_ACTIVE} ? $this->tr('Událost byla aktivována') : $this->tr('Událost byla deaktivována'));
         $this->link()->redirect();
      }
      $this->view()->formChangeState = $form;
   }

   protected function processChangeRecommended()
   {
      $form = new Form('event_changeRecommended');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);

      $elemSave = new Form_Element_Submit('change', $this->tr('Změnit stav'));
      $form->addElement($elemSave);

      if($form->isValid()){
         $event = SvbBase_Model_Events::getEvent($form->id->getValues());
         $event->{SvbBase_Model_Events::COLUMN_RECOMMENDED} = !$event->{SvbBase_Model_Events::COLUMN_RECOMMENDED};
         $event->save();
         $this->infoMsg()->addMessage( $event->{SvbBase_Model_Events::COLUMN_RECOMMENDED} ? $this->tr('Doporučení bylo nastaveno') : $this->tr('Doporučení bylo zrušeno'));
         $this->link()->redirect();
      }
      $this->view()->formChangeRecommended = $form;
   }

   protected function processDuplicate()
   {
      $form = new Form('event_copy');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);

      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($eName);

      $eDateBegin = new Form_Element_Text('dateBegin', $this->tr('Datum začátku'));
      $eDateBegin->addValidation(new Form_Validator_NotEmpty());
      $eDateBegin->addValidation(new Form_Validator_Date());
      $form->addElement($eDateBegin);

      $eDateEnd = new Form_Element_Text('dateEnd', $this->tr('Datum konce'));
      $eDateEnd->addValidation(new Form_Validator_Date());
      $form->addElement($eDateEnd);

      $eTimeBegin = new Form_Element_Text('timeBegin', $this->tr('Čas začátku'));
      $eTimeBegin->addValidation(new Form_Validator_Time());
      $form->addElement($eTimeBegin);

      $eTimeEnd = new Form_Element_Text('timeEnd', $this->tr('čas konce'));
      $eTimeEnd->addValidation(new Form_Validator_Time());
      $form->addElement($eTimeEnd);

      $elemSave = new Form_Element_Submit('copy', $this->tr('Kopírovat'));
      $form->addElement($elemSave);

      if($form->isValid()){
         $model = new SvbBase_Model_Events();
         $eventId = $form->id->getValues();
         $event = SvbBase_Model_Events::getEvent($eventId);
         $event->setNew();

         $event->{SvbBase_Model_Events::COLUMN_ID_PARENT} = $eventId;
         $event->{SvbBase_Model_Events::COLUMN_NAME} = $form->name->getValues();
         $event->{SvbBase_Model_Events::COLUMN_DATE_BEGIN} = new DateTime($form->dateBegin->getValues());

         $event->{SvbBase_Model_Events::COLUMN_DATE_END} = null;
         if($form->dateEnd->getValues() != null){
            $event->{SvbBase_Model_Events::COLUMN_DATE_END} = new DateTime($form->dateEnd->getValues());
         }

         $event->{SvbBase_Model_Events::COLUMN_TIME_BEGIN} = $form->timeBegin->getValues();
         $event->{SvbBase_Model_Events::COLUMN_TIME_END} = $form->timeEnd->getValues();

         $newId = $event->save();

         // duplikace sportů
         $evSportModel  = new SvbBase_Model_EventHasSports();
         $connections = $evSportModel->where(SvbBase_Model_EventHasSports::COLUMN_ID_EVENT." = :id", array('id' => $eventId))->records();
         foreach($connections as $connection){
            $connection->setNew();
            $connection->{SvbBase_Model_EventHasSports::COLUMN_ID_EVENT} = $newId;
            $connection->save();
         }


         $this->infoMsg()->addMessage($this->tr('Událost byla zkopírována'));
         $this->link()->redirect();
      }
      $this->view()->formCopy = $form;
   }

   public function eventController()
   {
      $event = SvbBase_Model_Events::getEvent($this->getRequest('idEvent'));
      if(!$event){
         http_throttle( new UnexpectedPageException());
      }

      $this->view()->event = $event;
   }


   public function settings(&$settings, Form &$form)
   {
      parent::settings($settings, $form);
   }
}
