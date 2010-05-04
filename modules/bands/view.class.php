<?php
class Bands_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('add_band', $this->_("Přidat kapelu"),
                 $this->link()->route('add'),
                 $this->_("Přidat novou kapelu"), "page_add.png");
         $this->toolbox = $toolbox;
      }
   }

   public function showView() {
      $this->template()->addTplFile("detail.phtml");

      if($this->category()->getRights()->isControll() OR
              ($this->category()->getRights()->isWritable())) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('band_edit', $this->_("Upravit"),
                 $this->link()->route('edit'),
                 $this->_("Upravit zobrazenou skupinu"), "page_edit.png");
         $toolbox->addTool('band_delete', $this->_("Smazat"),
                 $this->link(), $this->_("Smazat zobrazenou skupinu"), "page_delete.png",
                 'band_id', (int)$this->band->{Bands_Model::COLUMN_ID},
                 $this->_('Opravdu smazat skupinu?'));
         $this->toolbox = $toolbox;
      }
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      $this->template()->addTplFile("edit.phtml", 'bands');
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->edit = true;
      $this->addView();
   }

   public function exportBandHtmlView() {
      Template_Core::setMainIndexTpl(Template_Core::INDEX_PRINT_TEMPLATE);
      $this->template()->addTplFile("print.phtml", 'bands');
   }

   public function exportBandPdfView() {
//      // pokud není uložen mezivýstup
//      $fileName = $this->pdfFileCacheName();
//      if(!file_exists(AppCore::getAppCacheDir().$fileName)) {
//         $c = $this->createPdf();
//         $c->pdf()->Output(AppCore::getAppCacheDir().$fileName, 'F');
//      }
//      Template_Output::addHeader('Content-Disposition: attachment; filename="'
//              .$this->article->{Articles_Model_Detail::COLUMN_URLKEY}.'.pdf"');
//      Template_Output::sendHeaders();
//      // send Output
//      $fp = fopen(AppCore::getAppCacheDir().$fileName,"r");
//      while (! feof($fp)) {
//         $buff = fread($fp,4096);
//         print $buff;
//      }
//      exit();
   }

   protected function pdfFileCacheName() {
//      return md5($this->article->{Articles_Model_Detail::COLUMN_ID}
//              .'_'.(string)$this->article->{Articles_Model_Detail::COLUMN_TEXT_CLEAR}).'.pdf';
   }

   /**
    * Metoda vytvoří pdf soubor
    * @param Object $article -- článek
    * @return Component_Tcpdf
    */
   protected function createPdf() {
//      $article = $this->article;
//      // komponenta TcPDF
//      $c = new Component_Tcpdf();
//      // vytvoření pdf objektu
//      $c->pdf()->SetAuthor($article->{Model_Users::COLUMN_USERNAME});
//      $c->pdf()->SetTitle($article->{Articles_Model_Detail::COLUMN_NAME});
//      $c->pdf()->SetSubject(VVE_WEB_NAME." - ".$this->category()->getLabel());
//      $c->pdf()->SetKeywords($this->category()->getCatDataObj()->{Model_Category::COLUMN_KEYWORDS});
//
//      // ---------------------------------------------------------
//      $c->pdf()->setHeaderFont(array(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN-2));
//      $c->pdf()->setHeaderData('', 0, VVE_WEB_NAME." - ".$this->category()->getLabel()
//              ." - ".$article->{Articles_Model_Detail::COLUMN_NAME}
//              , strftime("%x")." - ".$this->link()->route('detail'));
//
//      // add a page
//      $c->pdf()->AddPage();
//      // nadpis
//      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN+2);
//      $name = "<h1>".$article->{Articles_Model_Detail::COLUMN_NAME}
//              ."</h1>";
//      $c->pdf()->writeHTML($name, true, 0, true, 0);
//
//      $c->pdf()->Ln();
//
//      // datum autor
//      $date = new DateTime($article->{Articles_Model_Detail::COLUMN_ADD_TIME});
//      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'BI', VVE_PDF_FONT_SIZE_MAIN);
//      $author = "<p>(".strftime("%x", $date->format("U"))
//              ." - ".$article->{Model_Users::COLUMN_USERNAME}.")</p>";
//      $c->pdf()->writeHTML($author, true, 0, true, 0);
////      $c->pdf()->Ln();
//
//      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
//      $c->pdf()->writeHTML((string)$article->{Articles_Model_Detail::COLUMN_TEXT}, true, 0, true, 10);
//
//      return $c;
   }

}

?>
