<?php
class BandsProgram_View extends View {
   public function mainView() {
      $this->template()->addTplFile("program.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('program_edit', $this->_("Upravit program"),
                 $this->link()->route('edit'),
                 $this->_("upravit program"), "page_edit.png");
         $this->toolbox = $toolbox;
      }
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->template()->addTplFile("edit.phtml", 'bandsprogram');
   }

   public function programItemView(){
      $this->template()->addTplFile('items.phtml');
   }

   public function exportProgramHtmlView(){
      Template_Core::setMainIndexTpl(Template_Core::INDEX_PRINT_TEMPLATE);
      $this->hideArtTools = true;
      $this->template()->addTplFile("program.phtml");
   }

   public function exportProgramPdfView(){
            // pokud není uložen mezivýstup
      $c = $this->createPdf();
      $c->pdf()->Output();
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
      $c->pdf()->SetAuthor(VVE_WEB_MASTER_NAME);
      $c->pdf()->SetTitle($this->_('Program').' - '.VVE_WEB_NAME);
      // ---------------------------------------------------------
      $c->pdf()->setHeaderFont(array(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN-2));
      $c->pdf()->setHeaderData('', 0, VVE_WEB_NAME." - ".$this->_('Program'));

      // add a page
      $c->pdf()->AddPage();
      // nadpis
      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN+2);
      $c->pdf()->writeHTML("<h1>".$this->_('Program')."</h1>", true, 0, true, 0);

      $c->pdf()->Ln();

      $mainW = 150;

      $curTime = new DateTime($item->time);
      foreach ($this->currentProgram as $item) {
         switch ($item['type']) {
            case 'day':
               $c->pdf()->SetFillColor(220,217,26);
               $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN+5);
               $c->pdf()->MultiCell($mainW, 9,$item->text, 'B', 'L', 1, 1, VVE_PDF_MARGIN_LEFT+20, '', true);
               $curTime = new DateTime($item->time);
               break;
            case 'stage':
               $c->pdf()->SetFillColor(220,217,26);
               $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN+1);
               $c->pdf()->MultiCell($mainW, 7, $item->text, 'B', 'L', 1, 1, VVE_PDF_MARGIN_LEFT+20, '', true);
               $curTime = new DateTime($item->time);
               break;
            case 'band':
               $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
               $c->pdf()->MultiCell(20, 5, $curTime->format("G:i"), 0, 'L', 0, 0, VVE_PDF_MARGIN_LEFT, '', true);
               $c->pdf()->MultiCell($mainW, 5, $this->bands[(int)$item->bandid]['name'], 0, 'L', 0, 1, VVE_PDF_MARGIN_LEFT+20, '', true);
               // parsing času
               $matches = array();
               preg_match('/([0-9]{1,2}):([0-9]{2})/', (string)$item->time, $matches);
               $curTime->modify(sprintf('+ %s hours %s minutes', $matches[1],$matches[2]));
               break;
            case 'other':
               $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
               $c->pdf()->MultiCell(20, 5, $curTime->format("G:i"), 0, 'L', 0, 0, VVE_PDF_MARGIN_LEFT, '', true);
               $c->pdf()->MultiCell($mainW, 5, $item->text, 0, 'L', 0, 1, VVE_PDF_MARGIN_LEFT+20, '', true);
               // parsing času
               $matches = array();
               preg_match('/([0-9]{1,2}):([0-9]{2})/', (string)$item->time, $matches);
               $curTime->modify(sprintf('+ %s hours %s minutes', $matches[1],$matches[2]));
               break;
            case 'note':
               $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'I', VVE_PDF_FONT_SIZE_MAIN-2);
               $c->pdf()->MultiCell($mainW, 5, $item->text, 0, 'L', 0, 1, VVE_PDF_MARGIN_LEFT+20, '', true);
               break;
            case 'space':
               $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'I', VVE_PDF_FONT_SIZE_MAIN-2);
               $c->pdf()->MultiCell($mainW, 5, "", 0, 'L', 0, 1, VVE_PDF_MARGIN_LEFT+20, '', true);
               break;
         }
      }
      return $c;
   }
}

?>
