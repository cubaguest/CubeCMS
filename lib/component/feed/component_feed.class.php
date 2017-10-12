<?php
/**
 * Třída pro generování informačních kanálů
 * Třída slouží pro generování informačních kanálů ze stránek
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro generování informačních kanálů
 * @see           http://interval.cz/clanky/rss-20/
 */

class Component_Feed extends Component {
   const RSS_AVAIL_TAGS = '<p><a><strong><br><img><em><h1><h2><h3><h4><h5>';

   /**
    * Pole s prvky kanálu
    * @var array
    */
   private $feeds = array();

   /**
    * Pole s konfiguračními hodnotami
    * @var array
    */
   protected $config = array('type' => 'rss',
           'numFeeds' => VVE_FEED_NUM,
           'css' => null,
           'title' => null,
           'desc' => null,
           'link' => null,
           'image' => null,
           'feedLink' => null,
           'urlArgName' => null,
           'tpl_file' => 'feeds.phtml',
           'sortByTime' => 'ASC'
   );

   public function addItem($title, $desc, $link,DateTime $pubDate, $author = null, $authorEmail = null, $category = null,
           $guid = null, $source = null, $enclosure = null, $image = null) {
//      if($guid == null) $guid = $link;
      // doplnění odkazů do linků na obrázky
      vve_create_full_url_path($desc);

      $item = array(
              'title' => $title,
              'desc' => $desc,
              'link' => (string)$link,
              'pubDate' => $pubDate,
              'author' => htmlspecialchars($author),
              'authorEmail' => $authorEmail,
              'category' => $category,
              'guid' => $guid,
              'source' => $source,
              'enclosure' => $enclosure,
              'image' => $image,
      );

      array_push($this->feeds, $item);
   }


   /**
    * Vygeneruje a odešle výstup a ukončí script
    */
   public function flush() {
      $out = new Template_Output($this->getConfig('type'));
      $out->sendHeaders();
      $feed = null;
      switch ($this->getConfig('type')) {
         case 'atom':
            $feed = $this->createAtomFeed();
            break;
         case 'rss':
         default:
            $feed = $this->createRssFeed();
            break;
      }
      $feed->flush();
//      flush();
      exit();
   }

   /**
    * Metoda provede seřazení výsledků podle data
    */
   private function sortFeed() {
      usort($this->feeds, array($this, ($this->getConfig('sortByTime', 'ASC') == 'ASC' ? 'cmpAsc' : 'cmpDesc') ) );
   }
   // Compare function
   private function cmpAsc($a, $b) {
      $ax = clone $a['pubDate'];
      $bx = clone $b['pubDate'];
      
      if ($ax == $bx) {
            return 0;
        }
        return ($ax < $bx) ? +1 : -1;
   }
   
   // Compare function
   private function cmpDesc($a, $b) {
      $ax = clone $a['pubDate'];
      $bx = clone $b['pubDate'];
      
      if ($ax == $bx) {
            return 0;
        }
        return ($ax < $bx) ? -1 : +1;
   }

      /**
    * Metoda vytvoří kanál typu rss v2
    * @return XMLWriter
    */
   private function createRssFeed() {
      $this->sortFeed();
      $feed = new XMLWriter();
      $feed->openURI('php://output');
      // hlavička
      $feed->startDocument('1.0', 'UTF-8');

      // css styl pokud je
      if($this->getConfig('css') != null) {
         $href = $this->getConfig('css');
         if(strpos($this->getConfig('css'), 'http') === false){
            $href = tp().Template::STYLESHEETS_DIR.$this->getConfig('css');
         }
         $feed->writePi("xml-stylesheet", 'type="text/css" href="'.$href.'"');
      }
      $feed->setIndent(4);

      // rss hlavička
      $feed->startElement('rss'); // SOF rss
      $feed->writeAttribute('version', '2.0');
         $feed->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');


      $feed->startElement("channel"); // SOF chanel
      //-------------------- SOF HEADER ---------------------------
      if($this->getConfig('title') != null){
         $feed->writeElement('title',  $this->getConfig('title') ." - ". VVE_WEB_NAME);
      } else {
         $feed->writeElement('title',  VVE_MAIN_PAGE_TITLE);
      }
      $feed->writeElement('description', $this->getConfig('desc'));
      if(VVE_WEB_COPYRIGHT != null) {
         $feed->writeElement('copyright', str_replace('{Y}', date("Y"), VVE_WEB_COPYRIGHT));
      }
      $feed->writeElement('language', Locales::getLang());

      $feed->writeElement('docs', "http://www.rssboard.org/rss-specification");

      $link = $this->getConfig('link');
      $feed->writeElement('link', clone $link->clear());
              
      $feed->startElement("atom:link");
      if($link instanceof Url_Link_Module){
         $feed->writeAttribute("href", $link->route('feed', array('type' => 'rss')));
      } else {
         $feed->writeAttribute("href", $link->category()->file(Url_Request::URL_FILE_RSS));
      }
      $feed->writeAttribute("rel", "self");
      $feed->writeAttribute("type", "application/rss+xml");
      $feed->endElement();

      $feed->writeElement('ttl', VVE_FEED_TTL);
      $now = new DateTime();
//      $now->setTimezone(new DateTimeZone('Europe/Prague'));
//      var_dump($now);
      $feed->writeElement('pubDate', $now->format(DateTime::RSS));
      $feed->writeElement('generator', AppCore::ENGINE_NAME." ".number_format(AppCore::ENGINE_VERSION,1,'.', ''));
      $feed->writeElement('webMaster', VVE_WEB_MASTER_EMAIL." (".VVE_WEB_MASTER_NAME.")");

      if($this->getConfig('image') != null) {
         $image = $this->getConfig('image');
         $feed->startElement('image'); // SOF image
         $feed->writeElement('title', $image['title']);
         $feed->writeElement('link', $this->getConfig('link'));
         // todo dořešit výpočet velikost a adresy
         $feed->writeElement('url', $image['url']);
         $feed->writeElement('width', $image['width']);
         $feed->writeElement('height', $image['height']);
         $feed->endElement();// EOF image
      }
      //----------------- EOF HEADER ------------------------------
      //----------------- ITEMS ------------------------------
      $feedsNum = $this->getConfig('numFeeds');
      $step = 0;
      foreach ($this->feeds as $item) {
         if($step == $feedsNum) break;
         $feed->startElement("item");// SOF item
         $feed->writeElement('title', $item['title']);
         $feed->writeElement('link', $item['link']);
         $feed->writeElement('description', strip_tags(nl2br($item['desc']), self::RSS_AVAIL_TAGS));
         if($item['guid'] != null){
            if(strpos($item['guid'], 'http') === false){
               $feed->startElement("guid");// SOF guid
               $feed->writeAttribute('isPermaLink', 'false');
               $feed->text($item['guid']);
               $feed->endElement();
            } else {
               $feed->writeElement('guid', $item['guid']);
            }
         } else {
            $feed->writeElement('guid', $item['link']);
         }
         if($item['authorEmail'] != null) {
            $feed->writeElement('author', $item['authorEmail']);
         } else {
//            $feed->writeElement('dc:creator', $item['author']);
         }
         
         if($item['image']){
            $imgPath = Utils_Url::urlToSystemPath($item['image']);

            if(is_file($imgPath)){
               
               $img = getimagesize($imgPath);
               
               $feed->startElementNs('media', 'content', 'http://search.yahoo.com/mrss/');
               $feed->writeAttribute('url', $item['image']);
               $feed->writeAttribute('type', $img['mime']);
               $feed->writeAttribute('width', $img[0]);
               $feed->writeAttribute('height', $img[1]);
               $feed->writeAttribute('medium', 'image');
               
               $feed->endElement();
            }
         }
         $feed->writeElement('pubDate', str_replace('+0100', '+0200', $item['pubDate']->format(DateTime::RSS)));
//         $feed->writeElement('pubDate', $item['pubDate']->format(DateTime::RSS));

         if($item['category'] != null) {
            $feed->startElement('category'); // SOF category
            $feed->writeAttribute('domain', $item['category']['link']);
            $feed->text($item['category']['name']);
            $feed->endElement(); // EOF category
         }
         $feed->endElement(); // EOF item
         $step++;
      }
      //------------------- EOF ITEMS ---------------------------

      $feed->endElement(); // EOF CHANEL
      $feed->endElement(); // EOF RSS
      $feed->endDocument(); //EOF document

      return $feed;
   }

   private function createAtomFeed(){
      $feed = new XMLWriter();
      $feed->openURI('php://output');
      // hlavička
      $feed->startDocument('1.0', 'UTF-8');

      // css styl pokud je
      if($this->getConfig('feedcss') != null) {
         $feed->writePi("xml-stylesheet", 'type="text/css" href="'.$this->getConfig('feedcss').'"');
      }
      $feed->setIndent(4);

      // rss hlavička
      $feed->startElement('feed'); // SOF feed
      $feed->writeAttribute('xmlns','http://www.w3.org/2005/Atom');
         $feed->writeAttribute('xml:lang', Locales::getLang());


      //-------------------- SOF HEADER ---------------------------
      if($this->getConfig('title') != null){
         $feed->writeElement('title',  VVE_WEB_NAME." - ".$this->getConfig('title'));
      } else {
         $feed->writeElement('title',  VVE_MAIN_PAGE_TITLE);
      }
      $feed->writeAttribute('type', "text");
      $feed->writeElement('subtitle', $this->getConfig('desc')."test");
      $feed->writeAttribute('type', "text");

      $feed->startElement('link');
      if($this->getConfig('link') instanceof Url_Link_Module){
         $feed->writeAttribute("href", $this->getConfig('link')->route('feed', array('type' => 'atom')));
      } else {
         $feed->writeAttribute("href", $this->getConfig('link')->category()->file(Url_Request::URL_FILE_ATOM));
      }
         $feed->writeAttribute('rel', "self");
         $feed->writeAttribute('type', "application/atom+xml");
      $feed->endElement();
      
      $feed->startElement('link');
         $feed->writeAttribute('href', $this->getConfig('link')->clear());
         $feed->writeAttribute('rel', "alternate");
      $feed->endElement();

      $feed->writeElement('id', 'tag:'.$_SERVER['SERVER_NAME'].','.date("Y").':'.$this->getConfig('link'));
      $feed->writeElement('updated', date(DATE_RFC3339));

      $feed->startElement('author'); // SOF athor
         $feed->writeElement('name', VVE_WEB_MASTER_NAME);
         $feed->writeElement('email', VVE_WEB_MASTER_EMAIL);
      $feed->endElement();

//      if($this->getConfig('image') != null) {
//         $image = $this->getConfig('image');
//         $feed->startElement('image'); // SOF image
//         $feed->writeElement('title', $image['title']);
//         $feed->writeElement('link', $this->getConfig('link'));
//         // todo dořešit výpočet velikost a adresy
//         $feed->writeElement('url', $image['url']);
//         $feed->writeElement('width', $image['width']);
//         $feed->writeElement('height', $image['height']);
//         $feed->endElement();// EOF image
//      }
      //----------------- EOF HEADER ------------------------------
      //----------------- ITEMS ------------------------------
      $feedsNum = $this->getConfig('numFeeds');
      $step = 0;
      foreach ($this->feeds as $item) {
         if($step == $feedsNum) break;
         $feed->startElement("entry");// SOF item
         $feed->writeElement('title', $item['title']);

         $feed->startElement('link');
         $feed->writeAttribute('type', 'text/html');
         $feed->writeAttribute('href', $item['link']);
         $feed->endElement();

         $feed->writeElement('id', 'tag:'.$_SERVER['SERVER_NAME'].','.date("Y").':'.$item['link']);


         $feed->startElement('author');
            $feed->writeElement('name', $item['author']);
         $feed->endElement();

         $feed->writeElement('updated', $item['pubDate']->format(DATE_RFC3339));

//         $feed->startElement('summary');
//            $feed->text(vve_tpl_truncate(strip_tags($item['desc']),400));
//          $feed->endElement(); // eof summary

//         <link rel="alternate" type="text/html" href="http://example.org/2003/12/13/atom03.html"/>
         $feed->startElement('link');
         $feed->writeAttribute('rel', 'alternate');
//         $feed->writeAttribute('type', 'text/html');
         $feed->writeAttribute('href', $item['link']);
         $feed->endElement();


//         <content type="xhtml" xml:lang="en"   xml:base="http://diveintomark.org/">
         $feed->startElement('content');
            $feed->writeAttribute('type', 'xhtml');
//            $feed->writeAttribute('xml:lang', Locales::getLang());
//            $feed->writeAttribute('xml:base', "http://diveintomark.org/");
            $feed->startElement('div');
               $feed->writeAttribute('xmlns', 'http://www.w3.org/1999/xhtml');
               $feed->text(vve_tpl_xhtml_cut(strip_tags($item['desc'], "<p><a><strong><br><img>"), 400));
            $feed->endElement(); // eof div
         $feed->endElement(); // eof content

//         if($item['category'] != null) {
//            $feed->startElement('category'); // SOF category
//            $feed->writeAttribute('domain', $item['category']['link']);
//            $feed->text($item['category']['name']);
//            $feed->endElement(); // EOF category
//         }
         $feed->endElement(); // EOF item
         $step++;
      }
      //------------------- EOF ITEMS ---------------------------

      $feed->endElement(); // EOF feed
      $feed->endDocument(); //EOF document

      return $feed;


//      <feed xmlns="http://www.w3.org/2005/Atom" xml:lang="cs-CZ">
//
//  <title>Root.cz</title>
//  <link href="http://rss.root.cz/2/clanky/"/>
//  <subtitle type="text">informace nejen ze světa Linuxu</subtitle>
//
//  <id>/export/rss2/clanky</id>
//  <updated>2006-01-08T23:00:00+01:00</updated>
//  <author><name>Root.cz</name></author>
//
//  <entry>
//    <title>XML Prague 2006</title>;
//    <id>/export/rss2/clanky/xml-prague-2006</id>
//    <link href="/clanky/xml-prague-2006/"/>
//    <summary type="text">Také v letošním roce ...</summary>
//
//    <updated>2006-01-08T23:00:00+01:00</updated>
//  </entry>
//
//</feed>
   }

   /**
    * Metoda pro výpis komponenty
    */
   public function mainView() {
      $this->template()->linkRss = (string)$this->getConfig('feedLink').Url_Request::URL_FILE_RSS;
      $this->template()->linkAtom =(string)$this->getConfig('feedLink').Url_Request::URL_FILE_ATOM;
      $this->template()->addTplFile($this->getConfig('tpl_file'));
      
   }
}
