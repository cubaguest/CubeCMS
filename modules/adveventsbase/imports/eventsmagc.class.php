<?php

class AdveventsBase_Imports_EventsMagc extends AdveventsBase_Imports_Events {

   public function process()
   {
      FS_Dir::checkStatic(AppCore::getAppCacheDir() . '/advevents/');
      // debug
      $this->importActions();
   }

   protected function importActions()
   {
      $pdo = $this->getDB();

      $modelEvents = new AdvEventsBase_Model_Events();
      $modelEventsTimes = new AdvEventsBase_Model_EventsTimes();
      $modelEventsImages = new AdvEventsBase_Model_EventsImages();

      // načtení akcí
      $stmt = $pdo->prepare('SELECT ac.*, cat.data_dir, cat.urlkey_cs AS curlkey FROM web_actions ac'
          . ' LEFT JOIN web_categories cat USING (id_Category) '
          . ' WHERE ( `action_start_date` >= NOW() OR '
          . ' ( `action_stop_date` IS NOT NULL AND `action_start_date` <= NOW() AND `action_stop_date` >= NOW()) ) '
          . ' AND `action_public` = 1');

      $stmt->execute();
      $idOrg = 51;
      $idPlace = 6;
      $idCat = 10;
      $prefixUrl = 'http://www.magc.cz/data/';
      $siteUrl = 'http://www.magc.cz/';

      $actions = $stmt->fetchAll(PDO::FETCH_OBJ);

      // kontrola existence akce v lokální db
      foreach ($actions as $action) {

         $sourceId = 'magc' . $action->id_action;
         $event = AdvEventsBase_Model_Events::getEventyBySourceID($sourceId);

         if (!$event) {
            $event = $modelEvents->newRecord();
            $event->{AdvEventsBase_Model_Events::COLUMN_SOURCE_ID} = $sourceId;
         }

         $event->{AdvEventsBase_Model_Events::COLUMN_NAME} = $action->action_name_cs;
         $event->{AdvEventsBase_Model_Events::COLUMN_SUBNAME} = $action->action_subname_cs;
         // úprava obsahu
         $content = $action->action_text_cs;
         $content = str_replace(array('<img src="data/'), array('<img src="' . $prefixUrl), $content);
         $event->{AdvEventsBase_Model_Events::COLUMN_TEXT} = $content;
         $event->{AdvEventsBase_Model_Events::COLUMN_NOTICE} = $action->action_note_cs;
         $event->{AdvEventsBase_Model_Events::COLUMN_WEBSITE} = $siteUrl . $action->curlkey . '/' . $action->action_urlkey_cs . '/';

         $event->{AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY} = $idCat;
         $event->{AdvEventsBase_Model_Events::COLUMN_ID_PLACE} = $idPlace;
         $event->{AdvEventsBase_Model_Events::COLUMN_ID_ORGANIZER} = $idOrg;

         $event->save();
         // new time
         AdvEventsBase_Model_EventsTimes::clearEventTimes($event->getPK());
         $time = $modelEventsTimes->newRecord();
         $time->{AdvEventsBase_Model_EventsTimes::COLUMN_ID_EVENT} = $event->getPK();
         $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN} = $action->action_start_date;
         $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END} = $action->action_stop_date;
         $time->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN} = $action->action_time;
         $time->save();


         $image = $prefixUrl . ($action->data_dir != null ? $action->data_dir : $action->curlkey) . '/' . $action->action_urlkey_cs . '/' . $action->action_image;
         // stažení obrázku
         if ($action->action_image != null) {
            $file = $this->downloadImage($image, AppCore::getAppCacheDir() . '/advevents/');
            $file->move(AdvEventsBase_Controller::getEventImagesDir($event->getPK()));
            AdvEventsBase_Model_EventsImages::clearEventImages($event->getPK());
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
      return new Db_PDO("mysql:host=" . $this->params['host'] . ";dbname=" . $this->params['dbname'], $this->params['user'], $this->params['pass'], array(
          PDO::ATTR_PERSISTENT => false,
          PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
          PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
      ));
   }

}
