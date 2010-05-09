<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class KzMainPage_Controller extends Controller {
   const XML_WEBS_FILE = 'kzmainpage.xml';
   const XML_WEBS_SETTINGS_FILE = 'kzmainpagesettings.xml';
   const CACHED_FILE = 'kzmainpage.tmp';
   const DEFAULT_CACHED_HOURS = 5;
   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      $cachedFile = new Filesystem_File_Text(self::CACHED_FILE, AppCore::getAppCacheDir());
      $cachedSec = $this->category()->getParam('cached_hours', self::DEFAULT_CACHED_HOURS)*60*60;

      if($cachedFile->exist() == false OR $cachedFile->changeTime()+$cachedSec < time()) {
         $cntFile = new Filesystem_File_Text(self::XML_WEBS_FILE, AppCore::getAppWebDir()
                         .VVE_DATA_DIR.DIRECTORY_SEPARATOR);

         $infos = array();
         if($cntFile->exist()) {
            $websXml = new SimpleXMLElement($cntFile->getContent());
            // načítání jednotlivých adre
            foreach ($websXml->box as $box) {
               if((string)$box['url'] == NULL OR (string)$box['url'] == '' OR !vve_url_exists((string)$box['url'])) continue;
               $cnt = file_get_contents((string)$box['url']);
               $api = new Component_Api_VVEArticle();
               try {
               // načtení obsahu
                  $api->setContent($cnt);
                  $b = array('data' => $api->getArray(), 'name' => (string)$box);
                  array_push($infos, $b);
               } catch (Exception $exc) {
                  continue;
               }
            }
         }
         // uložíme do cache
         $cachedFile->setContent(serialize($infos));
      } else {
         $infos = unserialize($cachedFile->getContent());
      }
      $this->view()->infos = $infos;
   }

   public function edititemsController() {
      $this->checkWritebleRights();
      $fileApis = new Filesystem_File_Text(self::XML_WEBS_SETTINGS_FILE, AppCore::getAppWebDir()
                      .VVE_DATA_DIR.DIRECTORY_SEPARATOR);

      $webs = array();
      $boxApis = array();
      if($fileApis->exist()) {
         $websApisXml = new SimpleXMLElement($fileApis->getContent());

         // načítání jednotlivých adre
         foreach ($websApisXml->web as $web) {
            if(!vve_url_exists((string)$web['listurl']) AND !vve_url_exists((string)$web['actualurl'])) continue; // kontrola url
            $boxApis[(string)$web] = (string)$web['boxname'];

            $box = (string)$web['boxname'];
            if(!isset ($webs[$box])) $webs[$box]
                       = array('name' => (string)$web, 'options' => array(),
                       'actualurl' => (string)$web['actualurl']);
            $cnt = file_get_contents((string)$web['listurl']);
            // načtení seznamu
            $webDataXml = new SimpleXMLElement($cnt);
            // procházení seznamu článků
            foreach ($webDataXml->article as $article) {
               $date = new DateTime((string)$article['date']);
               $time = vve_date("%Y - %B", $date);
               if(!isset ($webs[$box]['options'][$time])) {
                  $webs[$box]['options'][$time] = array();
               }
               $webs[$box]['options'][$time][(string)$article->name.' - '.vve_date("%x",$date)] = (string)$article->url;
            }
         }
      }
      $this->view()->websApis = $webs;

      // načítání uložených boxů
      $websSavedFile = new Filesystem_File_Text(self::XML_WEBS_FILE, AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR);
      $currentBoxes = array();

      if($websSavedFile->exist()) {
         $websXml = new SimpleXMLElement($websSavedFile->getContent());
         foreach ($websXml->box as $box) {
            array_push($currentBoxes, $box);
         }
      }
      $this->view()->curBoxes = $currentBoxes;

      // form pro uložení
      $form = new Form('webs');

      $elemApis = new Form_Element_Select('api', $this->_('Skupina'));
      $elemApis->setOptions($boxApis);
      $elemApis->setDimensional();
      $form->addElement($elemApis);

      $elemOption = new Form_Element_Radio('sel', $this->_('Vybrat'));
      $elemOption->setOptions(array('Aktuální' => 'actual', 'Dle výběru' => 'selected'));
      $elemOption->setDimensional();
      $form->addElement($elemOption);

      $elemSelArticle = new Form_Element_Select('sel_article', $this->_('Článek'));
      $elemSelArticle->setDimensional();
      $form->addElement($elemSelArticle);

      $elemActual = new Form_Element_Hidden('actualurl');
      $elemActual->setDimensional();
      $form->addElement($elemActual);


      $elemS = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemS);

      if($form->isValid()) {
         $xml = new XMLWriter();
         //         $xml->openURI('php://output');
         $xml->openMemory();
         // hlavička
         $xml->startDocument('1.0', 'UTF-8');
         $xml->setIndent(4);

         // rss hlavička
         $xml->startElement('boxes'); // SOF article
         $xml->writeAttribute('xmlns','http://www.vveframework.eu/v6/boxes');
         $xml->writeAttribute('xml:lang', Locale::getLang());

         $boxes = $form->api->getValues();
         $types = $form->sel->getValues();
         $urls = $form->sel_article->getValues();
         foreach ($boxes as $key => $box) {
            $xml->startElement('box'); // SOF article
            if($types[$key] == 'selected') {
               $xml->writeAttribute('url',$urls[$key]);
            } else {
               $xml->writeAttribute('url',$webs[$boxes[$key]]['actualurl']);
            }
            $xml->writeAttribute('type', $types[$key]);
            $xml->text($boxes[$key]);
            $xml->endElement();
         }
         $xml->endElement(); // eof article
         $xml->endDocument(); //EOF document
         file_put_contents(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
                 .self::XML_WEBS_FILE, $xml->outputMemory());

         $this->infoMsg()->addMessage($this->_('Rozložení boxů bylo uloženo'));
         $this->clearCacheFile();
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
   }

   public function loadArticlesController() {
      $this->checkWritebleRights();
      $websApisXml = new SimpleXMLElement(file_get_contents(AppCore::getAppWebDir()
              .VVE_DATA_DIR.DIRECTORY_SEPARATOR.self::XML_WEBS_SETTINGS_FILE));

      // načítání jednotlivých adre
      $articles = array();
      foreach ($websApisXml->web as $web) {
         if((string)$web['boxname'] != $this->getRequestParam('type')) continue;
         // načtení seznamu
         $webDataXml = new SimpleXMLElement(file_get_contents((string)$web['listurl']));
         // procházení seznamu článků
         foreach ($webDataXml->article as $article) {
            $date = new DateTime((string)$article['date']);
            $time = vve_date("%Y - %B", $date);
            if(!isset ($articles[$time])) {
               $articles[$time] = array();
            }
            $articles[$time][(string)$article->name.' - '.vve_date("%x", $date)] = (string)$article->url;
         }
      }
      $this->view()->articles = $articles;
   }

   /**
    * Metoda vymaže soubor z cache
    */
   private static function clearCacheFile() {
      $cachedFile = new Filesystem_File_Text(self::CACHED_FILE, AppCore::getAppCacheDir());
      $cachedFile->remove();
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings,Form &$form) {
      $form->addGroup('basic', 'Základní nasatvení');

      $elemCachedHours = new Form_Element_Text('cache', 'Počet hodin kešování obsahu titulní strany');
      $elemCachedHours->setSubLabel('Výchozí: '.self::DEFAULT_CACHED_HOURS.' hod.');
      $elemCachedHours->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemCachedHours, 'basic');

      if(isset($settings['cached_hours'])) {
         $form->cache->setValues($settings['cached_hours']);
      }
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['cached_hours'] = $form->cache->getValues();
      }
   }
}

?>