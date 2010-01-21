<?php
class Actions_View extends View {
   public function init() {
      $this->template()->addCssFile("style.css");
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

   public function showPdfView() {
      // načtení článku
      $model = new Actions_Model_Detail();
      $action = $model->getAction($this->urlkey);

      if($action == false) return false;

      $c = $this->createPdf($action);
      
      // výstup
      $c->flush($action->{Actions_Model_Detail::COLUMN_URLKEY}.'.pdf');
   }

   protected function createPdf($action){
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
              strftime("%x")." - ".$this->link()->route('detail'));
      // add a page
      $c->pdf()->AddPage();
      // nadpis
      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN+2);
      $name = "<h1>".$action->{Actions_Model_Detail::COLUMN_NAME}."</h1>";
      $c->pdf()->writeHTML($name, true, 0, true, 0);

      $c->pdf()->Ln();
      // datum
      $startDate = strftime("%x", $action->{Actions_Model_Detail::COLUMN_DATE_START});
      $stopDate = strftime("%x", $action->{Actions_Model_Detail::COLUMN_DATE_STOP});
      if($startDate != $stopDate) {
         $stopDateString = " - ".$stopDate;
      }

      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'BI', VVE_PDF_FONT_SIZE_MAIN);
      $author = "<p>".$startDate.$stopDateString."</p>";
      $c->pdf()->writeHTML($author, true, 0, true, 0);
      $c->pdf()->Ln();


      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
      $c->pdf()->writeHTML($action->{Actions_Model_Detail::COLUMN_TEXT}, true, 0, true, 0);
      
      return $c;
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
      $model = new Actions_Model_List();
      $actions = $model->getActionsByAdded($this->category()->getId(), VVE_FEED_NUM);

      while ($action = $actions->fetch()) {
         $startDate = strftime("%x", $action->{Actions_Model_Detail::COLUMN_DATE_START});
         $stopDate = strftime("%x", $action->{Actions_Model_Detail::COLUMN_DATE_STOP});
         $stopDateString = null;
         if($startDate != $stopDate) {
            $stopDateString = " - ".$stopDate;
         }
         $desc = "<h3>".$startDate.$stopDateString."</h3>";
         $desc .= $action->{Actions_Model_Detail::COLUMN_TEXT};

         $feed->addItem($action->{Actions_Model_Detail::COLUMN_NAME},$desc,
                 $this->link()->route('detail', array('urlkey' => $action->{Actions_Model_Detail::COLUMN_URLKEY})),
                 new DateTime($action->{Actions_Model_Detail::COLUMN_ADDED}),
                 $action->{Model_Users::COLUMN_USERNAME},null,null,
                 $action->{Actions_Model_Detail::COLUMN_URLKEY}."_".$action->{Actions_Model_Detail::COLUMN_ID}."_".
                 $action->{Actions_Model_Detail::COLUMN_CHANGED});
      }

      $feed->flush();
   }
}

?>