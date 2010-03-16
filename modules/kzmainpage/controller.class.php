<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class KzMainPage_Controller extends Controller {
   const XML_WEBS_FILE = 'kzmainpage.xml';
   const XML_WEBS_SETTINGS_FILE = 'kzmainpagesettings.xml';
   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      $websXml = new SimpleXMLElement(file_get_contents(AppCore::getAppWebDir()
              .VVE_DATA_DIR.DIRECTORY_SEPARATOR.self::XML_WEBS_FILE));

      // načítání jednotlivých adres
      $infos = array();
      foreach ($websXml->box as $box) {
         if((string)$box['url'] == NULL OR (string)$box['url'] == '' OR !vve_url_exists((string)$box['url'])) continue;
         $cnt = file_get_contents((string)$box['url']);
         // načtení obsahu
         try {
            $webDataXml = new SimpleXMLElement($cnt);
            $b = array('data' => $webDataXml, 'name' => (string)$box);
            array_push($infos, $b);
         } catch (Exception $exc) {
            continue;
         }

      }
      $this->view()->infos = $infos;
   }

   public function edititemsController() {
      $this->checkWritebleRights();
      $websApisXml = new SimpleXMLElement(file_get_contents(AppCore::getAppWebDir()
              .VVE_DATA_DIR.DIRECTORY_SEPARATOR.self::XML_WEBS_SETTINGS_FILE));

      // načítání jednotlivých adres
      $webs = array();
      $boxApis = array();
      foreach ($websApisXml->web as $web) {
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
      $this->view()->websApis = $webs;

      // načítání uložených boxů
      $websXml = new SimpleXMLElement(file_get_contents(AppCore::getAppWebDir()
              .VVE_DATA_DIR.DIRECTORY_SEPARATOR.self::XML_WEBS_FILE));
      $currentBoxes = array();
      foreach ($websXml->box as $box) {
         array_push($currentBoxes, $box);
//         $currentBoxes[(string) $box] = $box;
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
            if($types[$key] == 'selected'){
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
//var_dump($xml->outputMemory());flush();exit;
//         var_dump($xml->outputMemory());flush();exit();
         file_put_contents(AppCore::getAppWebDir().VVE_DATA_DIR.DIRECTORY_SEPARATOR
                 .self::XML_WEBS_FILE, $xml->outputMemory());

         $this->infoMsg()->addMessage($this->_('Rozložení boxů bylo uloženo'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
   }

   public function loadArticlesController() {
      $this->checkWritebleRights();
      $websApisXml = new SimpleXMLElement(file_get_contents(AppCore::getAppWebDir()
              .VVE_DATA_DIR.DIRECTORY_SEPARATOR.self::XML_WEBS_SETTINGS_FILE));

      // načítání jednotlivých adres
      $articles = array();
      foreach ($websApisXml->web as $web) {
         if((string)$web['boxname'] != $this->getRequestParam('type')) continue;

         // načtení seznamu
         $webDataXml = new SimpleXMLElement(file_get_contents((string)$web['listurl']));
         var_dump($webDataXml);
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
}

?>