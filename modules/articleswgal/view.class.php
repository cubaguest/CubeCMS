<?php
class ArticlesWGal_View extends Articles_View {
   /**
    * Inicializace
    */
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }

   public function showView() {
      $this->template()->addTplFile("detail.phtml");
   }

   public function editphotosView() {
      $this->template()->addPageTitle($this->template()->article->{Articles_Model_Detail::COLUMN_NAME}
              ." - ".$this->_('úprava obrázků'));
      $this->template()->addPageHeadline($this->template()->article->{Articles_Model_Detail::COLUMN_NAME}
              ." - ".$this->_('úprava obrázků'));
      $this->template()->addTplFile('addimage.phtml', 'photogalery');
      $this->template()->addTplFile('editphotos.phtml', 'photogalery');
   }

   public function editphotoView() {
      $this->template()->addTplFile("editphoto.phtml", 'photogalery');
   }

   public function checkFileView() {

   }
   public function uploadFileView() {

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
      $xml->writeElement('webname', VVE_WEB_NAME);
      $xml->writeElement('weburl', Url_Link::getMainWebDir());

      // informace o článku/akci
      $xml->writeElement('name', $article->{Articles_Model_Detail::COLUMN_NAME});
      $xml->writeElement('url', $this->link()->route('detail',
              array('urlkey'=>$article->{Articles_Model_Detail::COLUMN_URLKEY})));
      $xml->writeElement('shorttext', vve_tpl_truncate(vve_strip_tags(
              $article->{Articles_Model_Detail::COLUMN_TEXT}),400));

      // pokud je fotka
      $image = $this->images->fetch();
      if($image != false) {
         $xml->writeElement('image', $this->category()->getModule()->getDataDir(true)
                 .$article[Articles_Model_Detail::COLUMN_URLKEY][Locale::getLang()]
                 .URL_SEPARATOR.Photogalery_Controller::DIR_MEDIUM.URL_SEPARATOR
                 .$image->{PhotoGalery_Model_Images::COLUMN_FILE});
      }

      $xml->endElement(); // eof article
      $xml->endDocument(); //EOF document

      $xml->flush();
   }

   protected function pdfFileCacheName() {
      return md5($this->article->{Articles_Model_Detail::COLUMN_ID}
              .'_'.(string)$this->article->{Articles_Model_Detail::COLUMN_TEXT_CLEAR}
              .'_'.$this->imagesCount).'.pdf';
   }

   /**
    * Metoda vytvoří pdf soubor
    * @param Object $article -- článek
    * @return Component_Tcpdf
    */
   protected function createPdf() {
      $article = $this->article;
      // komponenta TcPDF
      $c = parent::createPdf();

      if($this->imagesCount != 0) {
         $c->pdf()->AddPage();
         // nadpis
         $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN);
         $c->pdf()->writeHTML("<h2>".$this->_('Fotky')."</h2>", true, 0, true, 0);
         $c->pdf()->Ln(5);
         // přibalení obrázků s galerie

         $c->pdf()->setJPEGQuality(75); // set JPEG quality
         $row = $coll = $maxHeight = 0;

         while ($image = $this->images->fetch()) {
            $sizes = getimagesize($this->category()->getModule()->getDataDir()
                    .$article[Articles_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()].DIRECTORY_SEPARATOR
                    .Photogalery_Controller::DIR_MEDIUM
                    .DIRECTORY_SEPARATOR.$image->{PhotoGalery_Model_Images::COLUMN_FILE});

            if($sizes[0] > 80/0.27) {
               $width = 80;
            } else {
               $width = $sizes[0]*0.27;
            } // převody mezi px<>mm

            $c->pdf()->Image($this->category()->getModule()->getDataDir()
                    .$article[Articles_Model_Detail::COLUMN_URLKEY][Locale::getDefaultLang()].DIRECTORY_SEPARATOR
                    .Photogalery_Controller::DIR_MEDIUM.DIRECTORY_SEPARATOR.$image->{PhotoGalery_Model_Images::COLUMN_FILE},
                    $coll*100+VVE_PDF_MARGIN_LEFT, '', $width);

            $height = $sizes[1] * ($width/$sizes[0]);
            if($height > $maxHeight) $maxHeight = $height;

            $coll++;
            if($coll == 2) {
               $c->pdf()->Ln(($maxHeight)+3);
               $row++;
               $coll = $maxHeight = 0;
            }
         }
      }

      return $c;
   }
}

?>