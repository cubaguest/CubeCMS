<?php
class Actions_View extends View {
   public function init() {
      $this->template()->addCssFile("style.css");
   }


   public function mainView() {
      $this->template()->addTplFile("list.phtml");
   }

   public function showView(){
      $this->template()->addTplFile("detail.phtml");
   }

   public function archiveView(){
      $this->template()->addTplFile("archive.phtml");
   }

   /**
    * Viewer pro přidání novinky
    */
   public function addView() {
      $this->editView();
   }

   public function showPdfView() {
      // načtení článku
      $model = new Actions_Model_Detail();
      $action = $model->getAction($this->urlkey);

      if($action == false) return false;

      // komponenta TcPDF
      $c = new Component_Tcpdf();
      // vytvoření pdf objektu
      $c->pdf()->SetAuthor($action->{Model_Users::COLUMN_USERNAME});
      $c->pdf()->SetTitle($action->{Actions_Model_Detail::COLUMN_NAME});
      $c->pdf()->SetSubject(VVE_WEB_NAME." - ".$this->category()->getLabel());
      $c->pdf()->SetKeywords($this->category()->getCatDataObj()->{Model_Category::COLUMN_KEYWORDS});

      // ---------------------------------------------------------
      $c->pdf()->setHeaderData('', 0, PDF_HEADER_TITLE." - ".$this->category()->getLabel()
              ." - ".$action->{Actions_Model_Detail::COLUMN_NAME},
              strftime("%x")." - ".$this->link()->route('detail'));
      // add a page
      $c->pdf()->AddPage();
      // nadpis
      $c->pdf()->SetFont(PDF_FONT_NAME_MAIN, 'B', PDF_FONT_SIZE_MAIN+2);
      $name = "<h1>".$action->{Actions_Model_Detail::COLUMN_NAME}."</h1>";
      $c->pdf()->writeHTML($name, true, 0, true, 0);

      $c->pdf()->Ln();
      // datum
      $startDate = strftime("%x", $action->{Actions_Model_Detail::COLUMN_DATE_START});
      $stopDate = strftime("%x", $action->{Actions_Model_Detail::COLUMN_DATE_STOP});
      if($startDate != $stopDate) {
         $stopDateString = " - ".$stopDate;
      }

      $c->pdf()->SetFont(PDF_FONT_NAME_MAIN, 'BI', PDF_FONT_SIZE_MAIN);
      $author = "<p>".$startDate.$stopDateString."</p>";
      $c->pdf()->writeHTML($author, true, 0, true, 0);
      $c->pdf()->Ln();


      $c->pdf()->SetFont(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN);
      $c->pdf()->writeHTML($action->{Actions_Model_Detail::COLUMN_TEXT}, true, 0, true, 0);
      // výstup
      $c->flush($action->{Actions_Model_Detail::COLUMN_URLKEY}.'.pdf');
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->template()->addTplFile('edit.phtml');
   }
}

?>