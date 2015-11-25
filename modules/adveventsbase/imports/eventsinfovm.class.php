<?php

class AdveventsBase_Imports_EventsInfovm extends AdveventsBase_Imports_Events {

   public function process()
   {
      $this->importActions();
   }

   protected function importActions()
   {
      $pdo = $this->getDB();
      $modelEvents = new AdvEventsBase_Model_Events();
      $modelEventsTimes = new AdvEventsBase_Model_EventsTimes();
      $modelEventsImages = new AdvEventsBase_Model_EventsImages();
      
      $stmt = $pdo->prepare('SELECT * FROM commonevents_commonevent cev ' 
         .' LEFT JOIN events_event ev ON cev.id = ev.commonevent_ptr_id '
         .' WHERE begin_datetime >= CURRENT_DATE - INTERVAL \'10\' DAY '
          . ' AND organizer_id NOT IN (146, 159, 179, 107, 113, 425)'
         .' ORDER BY id DESC'
          );
      $stmt->execute();
      
      $actions = $stmt->fetchAll(PDO::FETCH_OBJ);
      
      $loadIDS = array();
      foreach ($actions as $a) {
         if($a->term_mother_id == null){
            $loadIDS[$a->commonevent_ptr_id] = true;
         } else {
            $loadIDS[$a->term_mother_id] = true;
         }
      }
      

      $stmtTimes = $pdo->prepare('SELECT * FROM events_event ev'
          .' LEFT JOIN commonevents_commonevent cev ON cev.id = ev.commonevent_ptr_id '
          . ' WHERE ev.commonevent_ptr_id = :id OR ev.term_mother_id = :idm '
          . ' ORDER BY ev.term_mother_id DESC');
      
      $stmtImage = $pdo->prepare('SELECT * FROM events_photoeventorder ep '
          .' LEFT JOIN photos_photo pp ON pp.id = ep.photo_id '
            .' WHERE event_id = :id');
      
      $imgPrefix = 'http://media.info-vm.cz/i/media/';
      
      foreach ($loadIDS as $id => $t) {
         // načtení akcí
         $stmtTimes->bindValue(':id', $id);
         $stmtTimes->bindValue(':idm', $id);
         $stmtTimes->execute();
         
         $times = $stmtTimes->fetchAll(PDO::FETCH_OBJ);
         
         // první je vždy mother ID, tedy z toho bude vytvořena akce
         $action = array_shift($times);
         
         $sourceId = 'infovm' . $action->commonevent_ptr_id;
         $event = AdvEventsBase_Model_Events::getEventyBySourceID($sourceId);
         if (!$event) {
            $event = $modelEvents->newRecord();
            $event->{AdvEventsBase_Model_Events::COLUMN_SOURCE_ID} = $sourceId;
         }

         // texty
         $event->{AdvEventsBase_Model_Events::COLUMN_NAME} = $action->title;
         $event->{AdvEventsBase_Model_Events::COLUMN_SUBNAME} = $action->annotation;
         // úprava obsahu
         $content = $action->content;
         $prefixUrl = null; // otestovat
         $content = str_replace(array('<img src="data/'), array('<img src="' . $prefixUrl), $content);
         $event->{AdvEventsBase_Model_Events::COLUMN_TEXT} = $content;

         $event->{AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY} = $action->category_id;
         
         $idOrg = 0;
         switch ($action->organizer_id){
            case 399:
            case 142:
               $idOrg = 28;
               break;
            case 168:
            case 431:
               $idOrg = 5;
               break;
            case 96:
               $idOrg = 45;
               break;
            case 110:
               $idOrg = 10;
            case 26:
               $idOrg = 40;
               break;
            case 394:
               $idOrg = 46;
               break;
            case 170:
               $idOrg = 16;
               break;
            case 97:
               $idOrg = 47;
               break;
            case 165:
               $idOrg = 33;
               break;
            case 378:
               $idOrg = 9;
               break;
            case 151:
               $idOrg = 48;
               break;
            case 162:
               $idOrg = 13;
               break;
            case 375:
               $idOrg = 50;
               break;
         }
         
         $event->{AdvEventsBase_Model_Events::COLUMN_ID_ORGANIZER} = $idOrg;

         // zkusíme najít místo v db. Pokud jej nemáme, tak vytvoříme nové
         if($action->place != null){
            $place = AdvEventsBase_Model_Places::findPlaceByName($action->place);
            if(!$place){
               $place = AdvEventsBase_Model_Places::getNewRecord();
               $place->{AdvEventsBase_Model_Places::COLUMN_NAME} = $action->place;
               $place->{AdvEventsBase_Model_Places::COLUMN_ID_LOCATION} = 1;
               $place->save();
               $event->{AdvEventsBase_Model_Events::COLUMN_ID_PLACE} = $place->getPK();
            }
         }
         
         $event->save();
         AdvEventsBase_Model_EventsTimes::clearEventTimes($event->getPK());
         // pokud nějaké časy ještě zůstaly, budou připojeny k aktuální akci
         Debug::log($action, $times);
         if(!empty($times)){
            foreach ($times as $timeOrig) {
               $time = $modelEventsTimes->newRecord();
               $time->{AdvEventsBase_Model_EventsTimes::COLUMN_ID_EVENT} = $event->getPK();
               $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN} = $timeOrig->begin_date_display;
               $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END} = 
                   $timeOrig->end_date_display == $timeOrig->begin_date_display ? null : $timeOrig->end_date_display;;
               $time->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN} = $timeOrig->begin_time_display;
               $time->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_END} = $time->end_time_display;
               $time->save();
            }
         } else {
            // nemá žádné další termíny. Uloží se čas z mother
            $time = $modelEventsTimes->newRecord();
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_ID_EVENT} = $event->getPK();
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN} = $action->begin_date_display;
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END} = 
                $action->end_date_display == $action->begin_date_display ? null : $action->end_date_display;
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN} = $action->begin_time_display;
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_END} = $action->end_time_display;
            $time->save();
         }
         
         $stmtImage->bindValue(':id', $action->id);
         $stmtImage->execute();
         $imageObj = $stmtImage->fetch(PDO::FETCH_OBJ);
         if($imageObj){
            AdvEventsBase_Model_EventsImages::clearEventImages($event->getPK());
            $image = $imgPrefix . $imageObj->image;
            $file = $this->downloadImage($image, AppCore::getAppCacheDir() . '/advevents/');
            $file->move(AdvEventsBase_Controller::getEventImagesDir($event->getPK()));
            $image = $modelEventsImages->newRecord();
            $image->{AdvEventsBase_Model_EventsImages::COLUMN_FILE} = $file->getName();
            $image->{AdvEventsBase_Model_EventsImages::COLUMN_ID_EVENT} = $event->getPK();
            $image->{AdvEventsBase_Model_EventsImages::COLUMN_IS_TITLE} = true;
            $image->save();
         }
      }
   }

   /**
    * @return PDO objekt databáze
    */
   protected function getDB()
   {
      return new Db_PDO("pgsql:host=" . $this->params['host'] . ";dbname=" . $this->params['dbname'], $this->params['user'], $this->params['pass'], array(
          PDO::ATTR_PERSISTENT => false,
      ));
   }

}
