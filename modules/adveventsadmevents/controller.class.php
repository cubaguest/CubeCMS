<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class AdvEventsAdmEvents_Controller extends AdvEventsBase_Controller {

   public function init()
   {
      parent::init();
      $this->checkControllRights();
   }

   public function mainController()
   {

      parent::mainController();

      $this->processEventDelete();

//       načtení 
      $filterText = $this->getRequestParam('filter');
      $filterPlace = $this->getRequestParam('filterPlace');
      $filterLocation = $this->getRequestParam('filterLocation');
      $filterOrg = $this->getRequestParam('filterOrg');
      $dateFromStr = $this->getRequestParam('filterDateFrom', Utils_DateTime::fdate('%Y-%m-%d'));
      $dateToStr = $this->getRequestParam('filterDateTo', '2999-12-31');
      $filterNotApproved = $this->getRequestParam('filterNotApproved', false);
      $dateFrom = new DateTime($dateFromStr);
      $dateFrom->setTime(0, 0, 0);
      $dateTo = new DateTime($dateToStr != null ? $dateToStr : '2999-12-31'); // tak dlouho to snad nepojede ;-)
      $dateTo->setTime(23, 59, 59);

      $compScroll = new Component_Scroll();
      $compScroll->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE, 20);
      $params = array(
          'activeOnly' => false,
          'fulltext' => $filterText,
          'idPlace' => $filterPlace,
          'idLocation' => $filterLocation,
          'idOrganizer' => $filterOrg,
          'notApprovedOnly' => $filterNotApproved,
          'allEvents' => true,
          );
      $compScroll->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, 
          AdvEventsBase_Model_Events::getCountEventsByDateRange($dateFrom, $dateTo, $params));

      $this->view()->scroll = $compScroll;
      $this->view()->places = AdvEventsBase_Model_Places::getAllRecords();
      $this->view()->locations = AdvEventsBase_Model_Locations::getAllRecords();
      $mOrg = new AdvEventsBase_Model_Organizers();
      $this->view()->organizers = $mOrg->order(AdvEventsBase_Model_Organizers::COLUMN_NAME)->records();
      
      $params['offset'] = $compScroll->getStartRecord();
      $params['limit'] = $compScroll->getRecordsOnPage();
      $this->view()->events = AdvEventsBase_Model_Events::getEventsByDateRange($dateFrom, $dateTo, $params);
   }

   public function addEventController()
   {
      $form = $this->createEventForm();
      
      if($form->isSend() && $form->send->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         $event = $this->processEventForm($form);
         $this->infoMsg()->addMessage($this->tr('Událost byla uložena'));
         $this->link()->route('editEvent', array('id' => $event->getPK()))->param('tab', 'times')->redirect();
      }
      
      $this->view()->formEdit = $form;
   }
   
   public function editEventController($id)
   {
      $event = AdvEventsBase_Model_Events::getRecord($id);
      if(!$event){
         throw new InvalidArgumentException($this->tr('Požadovaná událost neexistuje'));
      }
      
      $this->processImageDelete();
      $this->processImageTitle($event);
      $this->processImageHP($event);
      $this->processTimeDelete();
      $this->processEventDelete();
      
      $form = $this->createEventForm($event);
      
      if($form->isSend() && $form->send->getValues() == false){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         $this->processEventForm($form, $event);
         $this->infoMsg()->addMessage($this->tr('Událost byla uložena'));
         $this->link()->param('tab', 'base')->redirect();
      }

      $formUpload = $this->createEventImagesForm($event);
      
      if($formUpload->isValid()){
         $this->processAddEventImagesForm($formUpload, $event);
         $this->infoMsg()->addMessage($this->tr('Obrázky byly nahrány'));
         $this->link()->param('tab', 'images')->redirect();
      }
      
      $formTimes = $this->createEventTimesForm($event);
      
      if($formTimes->isValid()){
         $this->processAddEventTimesForm($formTimes, $event);
         $this->infoMsg()->addMessage($this->tr('Období bylo uloženo'));
         $this->link()->param('tab', 'times')->redirect();
      }
      
      $this->view()->event = $event;
      $this->view()->eventUser = $event->getAddUser();
      $this->view()->formEdit = $form;
      $this->view()->formUplaod = $formUpload;
      $this->view()->formTimes = $formTimes;
      
      $this->view()->eventTimes = $event->getTimes();
      $this->view()->imagesEvent = $event->getImages();
      $this->view()->imagesPlace = AdvEventsBase_Model_Places::getImagesUrl($event->{AdvEventsBase_Model_Events::COLUMN_ID_PLACE});
   }
   
   public function eventActionController($id)
   {
      $event = AdvEventsBase_Model_Events::getRecord($id);
      if(!$event){
         throw new InvalidArgumentException($this->tr('Požadovaná událost neexistuje'));
      }
   
      if(!$this->getRequestParam('action', false)){
         throw new InvalidArgumentException($this->tr('Nebyl předán požadavek na akci'));
      }
      
      $this->view()->success = false;
      switch ($this->getRequestParam('action')){
         case 'changeState':
            $this->view()->state = !$event->{AdvEventsBase_Model_Events::COLUMN_ACTIVE};
            
            
            $event->{AdvEventsBase_Model_Events::COLUMN_ACTIVE} = !$event->{AdvEventsBase_Model_Events::COLUMN_ACTIVE};
            if($event->{AdvEventsBase_Model_Events::COLUMN_ACTIVE} && $event->{AdvEventsBase_Model_Events::COLUMN_APPROVED} == false){
               $this->sendEventActivationUserMail($event);
            }
            $event->{AdvEventsBase_Model_Events::COLUMN_APPROVED} = true;
            
            $event->save();
            $this->view()->success = true;
            break;
         case 'approve':
            if($event->{AdvEventsBase_Model_Events::COLUMN_APPROVED} == false){
               $this->sendEventActivationUserMail($event);
            }
            $event->{AdvEventsBase_Model_Events::COLUMN_APPROVED} = true;
            
            $event->save();
            $this->view()->success = true;
            $this->infoMsg()->addMessage($this->tr('Událost byla schválena'));
            break;
         case 'recommended':
            $this->view()->state = !$event->{AdvEventsBase_Model_Events::COLUMN_RECOMMENDED};
            $event->{AdvEventsBase_Model_Events::COLUMN_RECOMMENDED} = !$event->{AdvEventsBase_Model_Events::COLUMN_RECOMMENDED};
            $event->save();
            $this->view()->success = true;
            break;
      }
      if($this->getRequestParam('back')){
         $this->link()->redirect((string)$this->getRequestParam('back'));
      }
   }
   
   
   public function detailEventController($id)
   {
      $place = AdvEventsBase_Model_Categories::getRecord($id);
      if(!$place){
         throw new InvalidArgumentException($this->tr('Požadovaná kategorie neexistuje'));
      }
      
      $this->view()->evcat = $place;
   }
   
   /* obslužné metody */
   
   protected function createEventImagesForm(Model_ORM_Record $event = null) 
   {
      $form = new Form('advevent_images_');
      
      $elemImages = new Form_Element_File('images', $this->tr('Obrázky'));
      $elemImages->addValidation(new Form_Validator_FileExtension(Form_Validator_FileExtension::IMG));
      $elemImages->setOverWrite(false);
      $elemImages->setMultiple();
      FS_Dir::checkStatic(self::getEventImagesDir($event->getPK()));
      $elemImages->setUploadDir(self::getEventImagesDir($event->getPK()));
      $form->addElement($elemImages);
      
      $elemUpload = new Form_Element_Submit('send', $this->tr('Nahrát'));
      $form->addElement($elemUpload);
      return $form;
   }
   
   protected function createEventTimesForm(Model_ORM_Record $event = null) 
   {
      $form = new Form('advevent_times_');
      
      $elemDateFrom = new Form_Element_Text('date_from', $this->tr('Od'));
      $elemDateFrom->addValidation(new Form_Validator_NotEmpty());
      $elemDateFrom->addValidation(new Form_Validator_Date());
      $form->addElement($elemDateFrom);
      
      $elemTimeFrom = new Form_Element_Text('time_from', $this->tr('Čas začátku'));
      $elemTimeFrom->addValidation(new Form_Validator_Time());
      $form->addElement($elemTimeFrom);

      $elemDateTo = new Form_Element_Text('date_to', $this->tr('Do'));
      $elemDateTo->addValidation(new Form_Validator_Date());
      $form->addElement($elemDateTo);
      
      $elemTimeTo = new Form_Element_Text('time_to', $this->tr('Čas konce'));
      $elemTimeTo->addValidation(new Form_Validator_Time());
      $form->addElement($elemTimeTo);
      
      $elemNote = new Form_Element_Text('note', $this->tr('Poznámka'));
      $form->addElement($elemNote);
      
      $elemSave = new Form_Element_Submit('send', $this->tr('Přidat'));
      $form->addElement($elemSave);
      
      return $form;
   }
   
   protected function processImageDelete()
   {
      $form = new Form('advevent_img_remove');
      $eId = new Form_Element_Hidden('id');
      $form->addElement($eId);
      
      $eDel = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $form->addElement($eDel);
      
      if($form->isValid()){
         $model = new AdvEventsBase_Model_EventsImages();
         $model->delete($form->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Obrázek byl smazán'));
         $this->link()->redirect();
      }
      $this->view()->formImageDelete = $form;
   }
   
   protected function processEventDelete()
   {
      $form = new Form('advevent_event_remove');
      $eId = new Form_Element_Hidden('id');
      $form->addElement($eId);
      
      $eDel = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $form->addElement($eDel);
      
      if($form->isValid()){
         
         $event = AdvEventsBase_Model_Events::getRecord($form->id->getValues());
         // pokud je uživatelský event, odešli upozornění o smazání
         if($event->{AdvEventsBase_Model_Events::COLUMN_APPROVED} == false){
            $this->sendEventDeletedUserMail($event);
         }
         
         $m = new AdvEventsBase_Model_Events();
         $m->delete($event);
         
         $this->infoMsg()->addMessage($this->tr('Událost byla smazána'));
         $this->link()->redirect();
      }
      $this->view()->formEventDelete = $form;
   }
   
   protected function processTimeDelete()
   {
      $form = new Form('advevent_time_remove');
      $eId = new Form_Element_Hidden('id');
      $form->addElement($eId);
      
      $eDel = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $form->addElement($eDel);
      
      if($form->isValid()){
         $model = new AdvEventsBase_Model_EventsTimes();
         $model->delete($form->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Období bylo smazáno'));
         $this->link()->param('tab', 'times')->redirect();
      }
      $this->view()->formTimeDelete = $form;
   }
   
   protected function processImageTitle(Model_ORM_Record $event)
   {
      $form = new Form('advevent_img_title');
      $eId = new Form_Element_Hidden('id');
      $form->addElement($eId);
      
      $eSet = new Form_Element_Submit('set', $this->tr('Nastavit jako titulní'));
      $form->addElement($eSet);
      
      if($form->isValid()){
         $model = new AdvEventsBase_Model_EventsImages();
         $model->where(AdvEventsBase_Model_EventsImages::COLUMN_ID_EVENT." = :ide", array('ide' => $event->getPK()))
             ->update(array(AdvEventsBase_Model_EventsImages::COLUMN_IS_TITLE => 0));
         
         $model->where(AdvEventsBase_Model_EventsImages::COLUMN_ID." = :id", array('id' => $form->id->getValues()))
             ->update(array(AdvEventsBase_Model_EventsImages::COLUMN_IS_TITLE => 1));
         
//         $model->delete($form->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Obrázek byl nasatven jako titulní'));
         $this->link()->param('tab', 'images')->redirect();
      }
      $this->view()->formImageTitle = $form;
   }
   
   protected function processImageHP(Model_ORM_Record $event)
   {
      $form = new Form('advevent_img_homepage');
      $eId = new Form_Element_Hidden('id');
      $form->addElement($eId);
      
      $eImage = new Form_Element_File('image', $this->tr('Obrázek'));
//      $eImage->addValidation(new Form_Validator_NotEmpty());
      $eImage->setUploadDir(self::getEventImagesDir($event->getPK()).self::DIR_HOMEPAGE.DIRECTORY_SEPARATOR);
      $form->addElement($eImage);
      
      $eEnable = new Form_Element_Checkbox('enable', $this->tr('Zobrazit na HomePage        '));
      $eEnable->setValues((bool)$event->{AdvEventsBase_Model_Events::COLUMN_SHOW_ON_HP});
      $form->addElement($eEnable);
      
      $eSet = new Form_Element_Submit('set', $this->tr('Nahrát a uložit'));
      $form->addElement($eSet);
      
      if($form->isValid()){
         // přejmenování a uložení
         if($form->image->getValues()){
            /* @var $file File */
            $file = $form->image->createFileObject();
            $file->rename(self::HOMEPAGE_IMAGE, false);
         }
         // zapnutí pokud obrázek existuje
         if( $form->enable->getValues() == true && file_exists(self::getEventImagesDir($event->getPK()).self::DIR_HOMEPAGE.DIRECTORY_SEPARATOR.self::HOMEPAGE_IMAGE)){
            $event->{AdvEventsBase_Model_Events::COLUMN_SHOW_ON_HP} = true;
         } else {
            $event->{AdvEventsBase_Model_Events::COLUMN_SHOW_ON_HP} = false;
         }
         $event->save();
         $this->infoMsg()->addMessage($this->tr('Obrázek na titulní stránce byl uložen'));
         $this->link()->param('tab', 'images')->redirect();
      }
      $this->view()->formImageHP = $form;
   }
   
   protected function processDelete()
   {
      $form = new Form('cat_del');
      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemId);

      $elemSave = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $form->addElement($elemSave);

      if($form->isValid()){
         $model = new AdvEventsBase_Model_Categories();
         $model->delete($form->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Kateogire byla smazána'));
         $this->link()->redirect();
      }
      $this->view()->formDelete = $form;
   }

   public function settings(&$settings, Form &$form)
   {
      parent::settings($settings, $form);
   }
}
