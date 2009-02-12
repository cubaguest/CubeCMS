<?php
/**
 * Třída pluginu pro tvorbu xspf Playlistů. Jedná se o playlist definovaný pomocí
 * XML dokumentu
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.5.0 $Revision: $
 * @author        $Author: $ $Date:$
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract      Třída pro generování xspf playlistů
 * @see           http://www.xspf.org/quickstart/
 */
class XspfPlaylistPlugin {
   /**
    * Popisky k jednotlivým prvkům, jsou použity také pro strukturu pole se stopami
    */
   const PL_TITLE = 'title';
   const PL_CREATOR = 'creator';
   const PL_INFO = 'info';

   const TRACK_LOCATION = 'location';
   const TRACK_CREATOR = 'creator';
   const TRACK_ALBUM = 'album';
   const TRACK_TITLE = 'title';
   const TRACK_ANNOTATION = 'annotation';
   const TRACK_DURATION = 'duration';
   const TRACK_IMAGE = 'image';
   const TRACK_INFO = 'info';


   /**
    * Proměnná obsahuje název playlistu
    * @var string
    */
   private $title = null;


   /**
    * Proměnná obsahuje tvůrce playlistu
    * @var string
    */
   private $creator = null;


   /**
    * Proměnná obsahuje informace o playlistu (url)
    * @var string
    */
   private $info = null;

   /**
    * Proměná obsahuje pole se stopami
    * @var array
    */
   private $tracks = array();

   /**
    * Konstruktor
    */
   public function  __construct() {
   }

   /**
    * Metoda nastavuje a vrací titulek playlistu
    * @param string $title -- titulek playlistu
    * @return string
    */
   public function title($title = null) {
      if($title != null){
         $this->title = $title;
      }
      return $this->title;
   }

   /**
    * Metoda nastavuje a vrací tvůrce playlistu
    * @param string $title -- tvůrce playlistu
    * @return string
    */
   public function creator($creator = null) {
      if($creator != null){
         $this->creator = $creator;
      }
      return $this->creator;
   }

   /**
    * Metoda nastavuje a vrací info o playlistu
    * @param string $title -- info o playlistu
    * @return string
    */
   public function info($info = null) {
      if($info != null){
         $this->info = $info;
      }
      return $this->info;
   }

   /**
    * Metoda přidá stopu do seznamu skladeb
    * @param string $location -- adresa souboru (http://www/file.mp3)
    * @param string $title -- název stopy
    * @param string $album -- název alba
    * @param string $creator -- autor
    * @param string $image -- obrázek
    * @param string $info -- informace
    * @param string $annotation -- popis
    * @param int $duration -- délka trvání v milisekundách
    */
   public function addTrack($location, $title = null, $album = null, $creator = null,
      $image = null, $info = null, $annotation = null, $duration = null) {

      $track = array();
      $track[self::TRACK_LOCATION] = $location;
      $track[self::TRACK_TITLE] = $title;
      $track[self::TRACK_ALBUM] = $album;
      $track[self::TRACK_CREATOR] = $creator;
      $track[self::TRACK_IMAGE] = $image;
      $track[self::TRACK_INFO] = $info;
      $track[self::TRACK_ANNOTATION] = $annotation;
      $track[self::TRACK_DURATION] = $duration;
      array_push($this->tracks, $track);
   }

   /**
    * Metoda vytvoří a vrátí playlist
    * @return string -- playlist
    */
   public function getPlaylist() {
      $playlist = null;

      $xw = new xmlWriter();
      $xw->openMemory();

      $xw->startDocument('1.0','UTF-8');
      $xw->startElement ('playlist');
      $xw->writeAttribute( 'version', '1');
      $xw->writeAttribute( 'xmlns', 'http://xspf.org/ns/0/');

      // informace o playlistu
      if($this->title() != null){
         $xw->writeElement (self::PL_TITLE, $this->title());
      }
      if($this->creator() != null){
         $xw->writeElement (self::PL_CREATOR, $this->creator());
      }
      if($this->info() != null){
         $xw->writeElement (self::PL_INFO, $this->info());
      }

      //Výpis traků
      $xw->startElement('trackList');
      foreach ($this->tracks as $track) {
         $xw->startElement('track');
         if($track[self::TRACK_LOCATION] != null){
            $xw->writeElement (self::TRACK_LOCATION, $track[self::TRACK_LOCATION]);
         }
         if($track[self::TRACK_CREATOR] != null){
            $xw->writeElement (self::TRACK_CREATOR, $track[self::TRACK_CREATOR]);
         }
         if($track[self::TRACK_ALBUM] != null){
            $xw->writeElement (self::TRACK_ALBUM, $track[self::TRACK_ALBUM]);
         }
         if($track[self::TRACK_TITLE] != null){
            $xw->writeElement (self::TRACK_TITLE, $track[self::TRACK_TITLE]);
         }
         if($track[self::TRACK_ANNOTATION] != null){
            $xw->writeElement (self::TRACK_ANNOTATION, $track[self::TRACK_ANNOTATION]);
         }
         if($track[self::TRACK_DURATION] != null){
            $xw->writeElement (self::TRACK_DURATION, $track[self::TRACK_DURATION]);
         }
         if($track[self::TRACK_IMAGE] != null){
            $xw->writeElement (self::TRACK_IMAGE, $track[self::TRACK_IMAGE]);
         }
         if($track[self::TRACK_INFO] != null){
            $xw->writeElement (self::TRACK_INFO, $track[self::TRACK_INFO]);
         }
         $xw->endElement(); // </track>
      }

      $xw->endElement(); // </trackList>
      $xw->endElement(); // </playlist>
      return $xw->outputMemory(true);
   }

   /**
    * Metoda uloží playlist do zadaného souboru a adresáře
    * @param string $file -- název souboru
    * @param string $dir -- adresář souboru
    * @todo má cenu implementovat????
    */
//   public function savePlaylist($file, $dir) {
//      ;
//   }

}
?>
