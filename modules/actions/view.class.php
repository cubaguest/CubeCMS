<?php
class Actions_View extends View {
   public function init() {
   }


   public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }

   public function showView() {
      $this->template()->addTplFile("detail.phtml");
   }

   public function archiveView() {
      $this->template()->addTplFile("archive.phtml");
   }

   /**
    * Viewer pro přidání novinky
    */
   public function addView() {
      $this->editView();
   }

   public function showDataView() {
      switch ($this->output) {
         case 'pdf':
            $c = $this->createPdf($this->action);
            // výstup
            $c->flush($this->action->{Actions_Model_Detail::COLUMN_URLKEY}.'.pdf');

            break;
         case 'xml':
         default:
            $c = $this->createActionXml($this->action);
            break;
      }

   }

   protected function createPdf($action) {
      // komponenta TcPDF
      $c = new Component_Tcpdf();
      // vytvoření pdf objektu
      $c->pdf()->SetAuthor($action->{Model_Users::COLUMN_USERNAME});
      $c->pdf()->SetTitle($action->{Actions_Model_Detail::COLUMN_NAME});
      $c->pdf()->SetSubject(VVE_WEB_NAME." - ".$this->category()->getLabel());
      $c->pdf()->SetKeywords($this->category()->getCatDataObj()->{Model_Category::COLUMN_KEYWORDS});

      // ---------------------------------------------------------
      $c->pdf()->setHeaderFont(array(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN-2));
      $c->pdf()->setHeaderData('', 0, VVE_WEB_NAME." - ".$this->category()->getLabel()
              ." - ".$action->{Actions_Model_Detail::COLUMN_NAME},
//              strftime("%x")." - ".$this->link()->route('detail'));
              $this->link()->route('detail'));
      // add a page
      $c->pdf()->AddPage();
      // nadpis
      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN+2);
      $name = "<h1>".$action->{Actions_Model_Detail::COLUMN_NAME}."</h1>";
      $c->pdf()->writeHTML($name, true, 0, true, 0);

      $c->pdf()->Ln();
      // datum a čas
      $dateTimeStr = null;
      $dateTimeStr = vve_date("%x", new DateTime($action->{Actions_Model_Detail::COLUMN_DATE_START}));
      $stopDate = vve_date("%x", new DateTime($action->{Actions_Model_Detail::COLUMN_DATE_STOP}));
      if($startDate != $stopDate AND $action->{Actions_Model_Detail::COLUMN_DATE_STOP} != null) {
         $dateTimeStr .= ' - '.$stopDate;
      }
      if($action->{Actions_Model_Detail::COLUMN_TIME} != null) {
         $time = new DateTime($action->{Actions_Model_Detail::COLUMN_TIME});
         $dateTimeStr .= ' - '.$time->format("G:i");
      }

      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'BI', VVE_PDF_FONT_SIZE_MAIN);
      $c->pdf()->writeHTML("<p>".$dateTimeStr."</p>", true, 0, true, 0);

      // místo a cena
      $placePriceStr = null;
      if($action->{Actions_Model_Detail::COLUMN_PLACE} != null) {
         $placePriceStr .= $action->{Actions_Model_Detail::COLUMN_PLACE}.', ';
      }
      if((int)$action->{Actions_Model_Detail::COLUMN_PRICE} != null|0) {
         $placePriceStr .= sprintf(strtolower($this->_('Vstupné: %d Kč')),
                 $action->{Actions_Model_Detail::COLUMN_PRICE}).', ';
      }
      if((int)$action->{Actions_Model_Detail::COLUMN_PREPRICE} != null|0) {
         $placePriceStr .= sprintf(strtolower($this->_('V předprodeji: %d Kč')),
                 $action->{Actions_Model_Detail::COLUMN_PREPRICE}).' ';
      }
      if($placePriceStr != null) {
         $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'BI', VVE_PDF_FONT_SIZE_MAIN);
         $c->pdf()->writeHTML("<p>".$placePriceStr."</p>", true, 0, true, 0);
         $c->pdf()->Ln();
      }

      // obrázek
      if($action->{Actions_Model_Detail::COLUMN_IMAGE} != null) {
         $sizes = getimagesize($this->category()->getModule()->getDataDir()
                 .$action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]
                 .DIRECTORY_SEPARATOR.$action->{Actions_Model_Detail::COLUMN_IMAGE});
//         $c->pdf()->Cell(0, $sizes['heigth']);
         $c->pdf()->writeHTML('<img src="'.$this->category()->getModule()->getDataDir(true)
                 .$action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]
                 .URL_SEPARATOR.$action->{Actions_Model_Detail::COLUMN_IMAGE}.'" width="200" />', true, 0, true, 0);
         $c->pdf()->Ln();
//         print ('<img src="'.$this->category()->getModule()->getDataDir(true)
//                 .$action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]
//                 .URL_SEPARATOR.$action->{Actions_Model_Detail::COLUMN_IMAGE}.'" />');
//         $c->pdf()->Image($this->category()->getModule()->getDataDir()
//                 .$action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()]
//                 .DIRECTORY_SEPARATOR.$action->{Actions_Model_Detail::COLUMN_IMAGE});
//         flush();exit();
      }

      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
      $c->pdf()->writeHTML($action->{Actions_Model_Detail::COLUMN_TEXT}, true, 0, true, 0);

      return $c;
   }

   protected function createActionXml($action) {
      $xml = new XMLWriter();
      $xml->openURI('php://output');
      // hlavička
      $xml->startDocument('1.0', 'UTF-8');
      $xml->setIndent(4);

      // rss hlavička
      $xml->startElement('article'); // SOF article
      $xml->writeAttribute('xmlns','http://www.vveframework.eu/v6');
      $xml->writeAttribute('xml:lang', Locale::getLang());
      // informace o webu
      $xml->startElement('web');
      $xml->writeAttribute('link', Url_Link::getMainWebDir());
      $xml->writeRaw(VVE_WEB_NAME);
      $xml->endElement();
      // kategorie
      $xml->startElement('category'); // sof article
      $xml->writeAttribute('link', $this->link()->clear());
      $xml->writeRaw($this->category()->getName());
      $xml->endElement();

      // informace o článku/akci
      $xml->writeElement('name', $action->{Actions_Model_Detail::COLUMN_NAME});
      $xml->writeElement('url', $this->link()->route('detail',
              array('urlkey'=>$action->{Actions_Model_Detail::COLUMN_URLKEY})));
      $xml->writeElement('shorttext', vve_tpl_truncate(vve_strip_tags(
              $action->{Actions_Model_Detail::COLUMN_TEXT}),400));
      if((int)$action->{Actions_Model_Detail::COLUMN_PRICE} != null|0) {
         $xml->writeElement('price', $action->{Actions_Model_Detail::COLUMN_PRICE});
      }
      if((int)$action->{Actions_Model_Detail::COLUMN_PREPRICE} != null|0) {
         $xml->writeElement('preprice', $action->{Actions_Model_Detail::COLUMN_PREPRICE});
      }
      if($action->{Actions_Model_Detail::COLUMN_IMAGE} != null) {
         $xml->writeElement('image', $this->category()->getModule()->getDataDir(true)
                 .$action[Actions_Model_Detail::COLUMN_URLKEY][Locale::getLang()].URL_SEPARATOR
                 .$action->{Actions_Model_Detail::COLUMN_IMAGE});
      }


      $xml->endElement(); // eof article
      $xml->endDocument(); //EOF document

      $xml->flush();
   }
   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->template()->addTplFile('edit.phtml');
   }

   public function exportView() {
      $feed = new Component_Feed(true);

      $feed ->setConfig('type', $this->type);
      $feed ->setConfig('title', $this->category()->getName());
      $feed ->setConfig('desc', $this->category()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION});
      $feed ->setConfig('link', $this->link());

      while ($action = $this->actions->fetch()) {

         $dateTimeStr = null;
         $dateTimeStr = ': '.vve_date("%x", new DateTime($action->{Actions_Model_Detail::COLUMN_DATE_START}));
         $stopDate = vve_date("%x", new DateTime($action->{Actions_Model_Detail::COLUMN_DATE_STOP}));
         if($startDate != $stopDate AND $action->{Actions_Model_Detail::COLUMN_DATE_STOP} != null) {
            $dateTimeStr .= ' - '.$stopDate;
         }
         if($action->{Actions_Model_Detail::COLUMN_TIME} != null) {
            $time = new DateTime($action->{Actions_Model_Detail::COLUMN_TIME});
            $dateTimeStr .= ' - '.$time->format("G:i");
         }

         $feed->addItem($action->{Actions_Model_Detail::COLUMN_NAME}.$dateTimeStr,$action->{Actions_Model_Detail::COLUMN_TEXT},
                 $this->link()->route('detail', array('urlkey' => $action->{Actions_Model_Detail::COLUMN_URLKEY})),
                 new DateTime($action->{Actions_Model_Detail::COLUMN_ADDED}),
                 $action->{Model_Users::COLUMN_USERNAME},null,null,
                 $action->{Actions_Model_Detail::COLUMN_URLKEY}."_".$action->{Actions_Model_Detail::COLUMN_ID}."_".
                 $action->{Actions_Model_Detail::COLUMN_CHANGED});
      }

      $feed->flush();
   }

   public function featuredListView() {
      switch ($this->type) {
         case 'xml':
         default:
            $xml = new XMLWriter();
            $xml->openURI('php://output');
            // hlavička
            $xml->startDocument('1.0', 'UTF-8');
            $xml->setIndent(4);

            // rss hlavička
            $xml->startElement('articles'); // SOF article
            $xml->writeAttribute('xmlns','http://www.vveframework.eu/v6/featuredarticles');
            $xml->writeAttribute('xml:lang', Locale::getLang());

            while ($row = $this->actions->fetch()) {
               $xml->startElement('article'); // sof article
               $xml->writeAttribute('date', $row->{Actions_Model_Detail::COLUMN_DATE_START});
               $xml->writeElement('name', $row->{Actions_Model_Detail::COLUMN_NAME});
               $xml->writeElement('url', $this->link()->route('detailExport',
                       array('urlkey' => $row->{Actions_Model_Detail::COLUMN_URLKEY})));
               $xml->endElement(); // eof article

            }

            $xml->endElement(); // eof article
            $xml->endDocument(); //EOF document

            $xml->flush();

            break;
      }
   }

   public function currentActXmlView() {
      if($this->action != null) {
         $c = $this->createActionXml($this->action);
      }
   }

   public function editLabelView() {
      $this->template()->addTplFile('editlabel.phtml');
   }
}

?>