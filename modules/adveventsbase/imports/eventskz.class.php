<?php

class AdveventsBase_Imports_EventsKz extends AdveventsBase_Imports_Events {

   protected $actionsTablePrefixes = array(
       'amfiteatr_',
       'galerie_',
       'mklub_',
       'sklub_',
   );
   protected $moviesTablePrefixes = array(
       'kinosvet_',
   );

   public function process()
   {
      FS_Dir::checkStatic(AppCore::getAppCacheDir() . '/advevents/');
      // debug
      $modelEvents = new AdvEventsBase_Model_Events();
      $modelEventsTimes = new AdvEventsBase_Model_EventsTimes();
      $modelEventsImages = new AdvEventsBase_Model_EventsImages();
      $this->importActions();
      $this->importMovies();
   }

   protected function importActions()
   {
      $pdo = $this->getKZDB();

      $modelEvents = new AdvEventsBase_Model_Events();
      $modelEventsTimes = new AdvEventsBase_Model_EventsTimes();
      $modelEventsImages = new AdvEventsBase_Model_EventsImages();

      foreach ($this->actionsTablePrefixes as $prefix) {
         // načtení akcí
         $stmt = $pdo->prepare('SELECT ac.*, cat.data_dir, cat.urlkey_cs AS curlkey FROM ' . $prefix . 'actions ac'
             . ' LEFT JOIN ' . $prefix . 'categories cat USING (id_Category) '
             . ' WHERE ( `start_date` >= NOW() OR '
          . ' ( `stop_date` IS NOT NULL AND `start_date` <= NOW() AND `stop_date` >= NOW()) ) '
          . ' AND `public` = 1');

         $stmt->execute();
         $idOrg = 40;
         $idPlace = 6;
         $idCat = 0;
         $prefixUrl = $prefix . 'http://www.kzvalmez.cz/data/';
         $siteUrl = 'http://www.kzvalmez.cz/';
         switch ($prefix) {
            case "amfiteatr_":
               $prefixUrl = 'http://amfiteatr.kzvalmez.cz/data/';
               $siteUrl = 'http://amfiteatr.kzvalmez.cz/';
               break;
            case "galerie_":
               $idCat = 10;
               $prefixUrl = 'http://galerie.kzvalmez.cz/data/';
               $siteUrl = 'http://galerie.kzvalmez.cz/';
               break;
            case "sklub_":
               $prefixUrl = 'http://sklub.kzvalmez.cz/data/';
               $siteUrl = 'http://sklub.kzvalmez.cz/';
               $idOrg = 3;
               break;
            case "mklub_":
               $prefixUrl = 'http://mklub.kzvalmez.cz/data/';
               $siteUrl = 'http://mklub.kzvalmez.cz/';
               $idOrg = 2;
               break;
         }

         $actions = $stmt->fetchAll(PDO::FETCH_OBJ);

         // kontrola existence akce v lokální db
         foreach ($actions as $action) {
            if ($prefix == 'galerie_' && $action->id_action == 12) { // sýpka
               $idPlace = 3;
               $idOrg = 15;
            } else if ($prefix == 'galerie_' && $action->id_action == 8) { // kaple
               $idPlace = 22;
               $idOrg = 44;
            }

            $sourceId = $prefix . $action->id_action;
            $event = AdvEventsBase_Model_Events::getEventyBySourceID($sourceId);

            if (!$event) {
               $event = $modelEvents->newRecord();
               $event->{AdvEventsBase_Model_Events::COLUMN_SOURCE_ID} = $sourceId;
            }

            $event->{AdvEventsBase_Model_Events::COLUMN_NAME} = $action->name_cs;
            $event->{AdvEventsBase_Model_Events::COLUMN_SUBNAME} = $action->subname_cs;
            // úprava obsahu
            $content = $action->text_cs;
            $content = str_replace(array('<img src="data/'), array('<img src="' . $prefixUrl), $content);
            $event->{AdvEventsBase_Model_Events::COLUMN_TEXT} = $content;
            $event->{AdvEventsBase_Model_Events::COLUMN_NOTICE} = $action->note_cs;
            $event->{AdvEventsBase_Model_Events::COLUMN_WEBSITE} = $siteUrl . $action->curlkey . '/' . $action->urlkey_cs . '/';

            $event->{AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY} = $idCat;
            $event->{AdvEventsBase_Model_Events::COLUMN_ID_PLACE} = $idPlace;
            $event->{AdvEventsBase_Model_Events::COLUMN_ID_ORGANIZER} = $idOrg;

            $event->save();
            // new time
            AdvEventsBase_Model_EventsTimes::clearEventTimes($event->getPK());
            $time = $modelEventsTimes->newRecord();
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_ID_EVENT} = $event->getPK();
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN} = $action->start_date;
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END} = $action->stop_date;
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN} = $action->time;
            $time->save();


            $image = $prefixUrl . ($action->data_dir != null ? $action->data_dir : $action->curlkey) . '/' . $action->urlkey_cs . '/' . $action->image;
            // stažení obrázku
            if ($prefix == 'sklub_') {
//               Debug::log($image);
            }
            if ($action->image != null) {

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
   }

   protected function importMovies()
   {
      $pdo = $this->getKZDB();
      $modelEvents = new AdvEventsBase_Model_Events();
      $modelEventsTimes = new AdvEventsBase_Model_EventsTimes();
      $modelEventsImages = new AdvEventsBase_Model_EventsImages();

      $stmt = $pdo->prepare('SELECT * FROM kzvalmezcz.kinosvet_cinemaprogram_movies m '
          . ' LEFT JOIN kinosvet_cinemaprogram_time t USING (id_movie) '
          . ' WHERE `date` >= SUBDATE(CURDATE(),1) '
          . ' GROUP BY m.id_movie ');
      
      $stmt->execute();
      
      $movies = $stmt->fetchAll(PDO::FETCH_OBJ);
      
      $prefixUrl = 'http://kino.kzvalmez.cz/';
      $imagesSrc = 'http://kino.kzvalmez.cz/data/aktualni-program/';
      
      $timesstmt = $pdo->prepare('SELECT * FROM kinosvet_cinemaprogram_time WHERE id_movie = :id');
      foreach ($movies as $m) {
         $timesstmt->bindValue(':id', $m->id_movie, PDO::PARAM_INT);
         $timesstmt->execute();
         $times = $timesstmt->fetchAll(PDO::FETCH_OBJ);
         
//         Debug::log(ucfirst(mb_strtolower($m->name)));
//         Debug::log($times);
         
         $sourceId = 'kinosvet' . $m->id_movie;
         $event = AdvEventsBase_Model_Events::getEventyBySourceID($sourceId);

         if (!$event) {
            $event = $modelEvents->newRecord();
            $event->{AdvEventsBase_Model_Events::COLUMN_SOURCE_ID} = $sourceId;
         }

         $event->{AdvEventsBase_Model_Events::COLUMN_NAME} = ucfirst(mb_strtolower($m->name));
         $event->{AdvEventsBase_Model_Events::COLUMN_SUBNAME} = ucfirst(mb_strtolower($m->name_orig));
         // úprava obsahu
         $content = $m->label;
         $content = str_replace(array('<img src="data/'), array('<img src="' . $prefixUrl), $content);
         $event->{AdvEventsBase_Model_Events::COLUMN_TEXT} = $content;
//         $event->{AdvEventsBase_Model_Events::COLUMN_NOTICE} = $action->note_cs;
//         $event->{AdvEventsBase_Model_Events::COLUMN_WEBSITE} = $siteUrl . $action->curlkey . '/' . $action->urlkey_cs . '/';

         $event->{AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY} = 1;
         $event->{AdvEventsBase_Model_Events::COLUMN_ID_PLACE} = 23;
         $event->{AdvEventsBase_Model_Events::COLUMN_ID_ORGANIZER} = 1;
         $event->{AdvEventsBase_Model_Events::COLUMN_URL_YOUTUBE} = $m->trailer;

         $event->save();
         // new time
         AdvEventsBase_Model_EventsTimes::clearEventTimes($event->getPK());
         
         foreach ($times as $t) {
            $time = $modelEventsTimes->newRecord();
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_ID_EVENT} = $event->getPK();
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN} = $t->date;
            $time->{AdvEventsBase_Model_EventsTimes::COLUMN_TIME_BEGIN} = $t->time;
            $time->save();
         }
         
         if ($m->image != null) {
               $file = $this->downloadImage($imagesSrc.$m->image, AppCore::getAppCacheDir() . '/advevents/');
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
   protected function getKZDB()
   {
      return new Db_PDO("mysql:host=" . $this->params['host'] . ";dbname=" . $this->params['dbname'], $this->params['user'], $this->params['pass'], array(
          PDO::ATTR_PERSISTENT => false,
          PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
          PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
      ));
   }

}
