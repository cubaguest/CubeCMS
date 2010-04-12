<?php
class Articles_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
      $feeds = new Component_Feed();
      $feeds->setConfig('feedLink', $this->link()->clear()->route('export'));
      $feeds->setConfig('urlArgName', "{type}");
      $this->template()->feedsComp = $feeds;
   }

   public function topView() {
      $this->mainView();
   }

   public function contentView() {
      $this->template()->addTplFile("contentlist.phtml");
      echo $this->template();
   }

   public function showView() {
      $this->template()->addTplFile("detail.phtml");
   }

   public function archiveView() {
      $this->template()->addTplFile("archive.phtml",'articles');
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      $this->template()->addTplFile("edit.phtml", 'articles');
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->addView();
   }

   public function exportArticlePdfView() {
      // pokud není uložen mezivýstup
      $fileName = $this->pdfFileCacheName();
      if(!file_exists(AppCore::getAppCacheDir().$fileName)) {
         $c = $this->createPdf();
         $c->pdf()->Output(AppCore::getAppCacheDir().$fileName, 'F');
      }
      Template_Output::addHeader('Content-Disposition: attachment; filename="'
              .$this->article->{Articles_Model_Detail::COLUMN_URLKEY}.'.pdf"');
      Template_Output::sendHeaders();
      // send Output
      $fp = fopen(AppCore::getAppCacheDir().$fileName,"r");
      while (! feof($fp)) {
         $buff = fread($fp,4096);
         print $buff;
      }
      exit();
   }

   protected function pdfFileCacheName(){
      return md5($this->article->{Articles_Model_Detail::COLUMN_ID}
              .'_'.(string)$this->article->{Articles_Model_Detail::COLUMN_TEXT_CLEAR}).'.pdf';
   }

   /**
    * Metoda vytvoří pdf soubor
    * @param Object $article -- článek
    * @return Component_Tcpdf
    */
   protected function createPdf() {
      $article = $this->article;
      // komponenta TcPDF
      $c = new Component_Tcpdf();
      // vytvoření pdf objektu
      $c->pdf()->SetAuthor($article->{Model_Users::COLUMN_USERNAME});
      $c->pdf()->SetTitle($article->{Articles_Model_Detail::COLUMN_NAME});
      $c->pdf()->SetSubject(VVE_WEB_NAME." - ".$this->category()->getLabel());
      $c->pdf()->SetKeywords($this->category()->getCatDataObj()->{Model_Category::COLUMN_KEYWORDS});

      // ---------------------------------------------------------
      $c->pdf()->setHeaderFont(array(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN-2));
      $c->pdf()->setHeaderData('', 0, VVE_WEB_NAME." - ".$this->category()->getLabel()
              ." - ".$article->{Articles_Model_Detail::COLUMN_NAME}
              , strftime("%x")." - ".$this->link()->route('detail'));

      // add a page
      $c->pdf()->AddPage();
      // nadpis
      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN+2);
      $name = "<h1>".$article->{Articles_Model_Detail::COLUMN_NAME}
              ."</h1>";
      $c->pdf()->writeHTML($name, true, 0, true, 0);

      $c->pdf()->Ln();

      // datum autor
      $date = new DateTime($article->{Articles_Model_Detail::COLUMN_ADD_TIME});
      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'BI', VVE_PDF_FONT_SIZE_MAIN);
      $author = "<p>(".strftime("%x", $date->format("U"))
              ." - ".$article->{Model_Users::COLUMN_USERNAME}.")</p>";
      $c->pdf()->writeHTML($author, true, 0, true, 0);
//      $c->pdf()->Ln();

      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
      $c->pdf()->writeHTML((string)$article->{Articles_Model_Detail::COLUMN_TEXT}, true, 0, true, 10);

      return $c;
   }

   public function exportFeedView() {
      $feed = new Component_Feed(true);

      $feed ->setConfig('type', $this->type);
      $feed ->setConfig('css', 'rss.css');
      $feed ->setConfig('title', $this->category()->getName());
      $feed ->setConfig('desc', $this->category()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION});
      $feed ->setConfig('link', $this->link());

      while ($article = $this->template()->articles->fetch()) {
         $feed->addItem($article->{Articles_Model_Detail::COLUMN_NAME},
                 $article->{Articles_Model_Detail::COLUMN_TEXT},
                 $this->link()->route('detail', array('urlkey' => $article->{Articles_Model_Detail::COLUMN_URLKEY})),
                 new DateTime($article->{Articles_Model_Detail::COLUMN_ADD_TIME}),
                 $article->{Model_Users::COLUMN_USERNAME}, null, null,
                 $article->{Articles_Model_Detail::COLUMN_URLKEY}."_".$article->{Articles_Model_Detail::COLUMN_ID});
      }

      $feed->flush();
   }

   public function currentArticleView() {
      $this->createArticleXml();
   }

   public function exportArticleXmlView() {
      $this->createArticleXml();
   }

   public function lastListXmlView() {
      $xml = new XMLWriter();
      $xml->openURI('php://output');
      // hlavička
      $xml->startDocument('1.0', 'UTF-8');
      $xml->setIndent(4);

      // rss hlavička
      $xml->startElement('articles'); // SOF article
      $xml->writeAttribute('xmlns','http://www.vveframework.eu/v6/featuredarticles');
      $xml->writeAttribute('xml:lang', Locale::getLang());

      while ($row = $this->articles->fetch()) {
         $date = new DateTime($row->{Articles_Model_Detail::COLUMN_ADD_TIME});
         $xml->startElement('article'); // sof article
         $xml->writeAttribute('date', $date->format('Y-m-d'));
         $xml->writeElement('name', vve_tpl_truncate($row->{Articles_Model_Detail::COLUMN_NAME}, 50));
         $xml->writeElement('url', $this->link()->route('detailExport',
                 array('urlkey' => $row->{Articles_Model_Detail::COLUMN_URLKEY})));
         $xml->endElement(); // eof article
      }

      $xml->endElement(); // eof article
      $xml->endDocument(); //EOF document

      $xml->flush();
   }

   /**
    * Metoda vytvoří xml s článkem a odešlě
    * @param Object $article
    */
   protected function createArticleXml() {
      $article = $this->article;
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
      if($article != null OR $article != false){
      $xml->writeElement('name', $article->{Articles_Model_Detail::COLUMN_NAME});
      $xml->writeElement('url', $this->link()->route('detail',
              array('urlkey'=>$article->{Articles_Model_Detail::COLUMN_URLKEY})));
      $xml->writeElement('shorttext', vve_tpl_truncate(vve_strip_tags(
              $article->{Articles_Model_Detail::COLUMN_TEXT}),400));
      }
      $xml->endElement(); // eof article
      $xml->endDocument(); //EOF document

      $xml->flush();
   }
}

?>
