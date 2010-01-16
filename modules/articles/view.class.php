<?php
class Articles_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
      $this->template()->addCssFile("style.css");
//      $this->template()->addFile("tpl://list.phtml");
//      $this->template()->addFile("tpl://articles/list.phtml");
//      $this->template()->addFile("css://style.css");
//      $this->template()->addFile("js://functions.js");
      $feeds = new Component_Feed();
      $feeds->setConfig('feedLink', $this->link()->clear()->route('export'));
      $feeds->setConfig('urlArgName', "{type}");
      $this->template()->feedsComp = $feeds;
   }

   public function topView(){
      $this->mainView();
   }

   public function showView() {
      $this->template()->addTplFile("detail.phtml");
   }

   public function archiveView() {
      $this->template()->addTplFile("archive.phtml");
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      $this->template()->addTplFile("edit.phtml");
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->addView();
   }

   public function showPdfView() {
      // načtení článku
      $artM = new Articles_Model_Detail();
      $article = $artM->getArticle($this->urlkey);

      if($article == false) return false;
      $c = $this->createPdf($article);
      
      // výstup
      $c->flush($article->{Articles_Model_Detail::COLUMN_URLKEY}.'.pdf');
   }

   /**
    * Metoda vytvoří pdf soubor
    * @param Object $article -- článek
    * @return Component_Tcpdf
    */
   protected function createPdf($article){
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
      $c->pdf()->Ln();


      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
      $c->pdf()->writeHTML($article->{Articles_Model_Detail::COLUMN_TEXT}, true, 0, true, 0);

      return $c;
   }

   public function exportView() {
   $feed = new Component_Feed(true);

   $feed ->setConfig('type', $this->type);
   $feed ->setConfig('css', 'rss.css');
   $feed ->setConfig('title', $this->category()->getName());
   $feed ->setConfig('desc', $this->category()->getCatDataObj()->{Model_Category::COLUMN_DESCRIPTION});
   $feed ->setConfig('link', $this->link());

   $model = new Articles_Model_List();
   $articles = $model->getList($this->category()->getId(), 0, VVE_FEED_NUM);

   while ($article = $articles->fetch()) {
      $feed->addItem($article->{Articles_Model_Detail::COLUMN_NAME},
              $article->{Articles_Model_Detail::COLUMN_TEXT},
              $this->link()->route('detail', array('urlkey' => $article->{Articles_Model_Detail::COLUMN_URLKEY})),
              new DateTime($article->{Articles_Model_Detail::COLUMN_ADD_TIME}),
              $article->{Model_Users::COLUMN_USERNAME}, null, null,
              $article->{Articles_Model_Detail::COLUMN_URLKEY}."_".$article->{Articles_Model_Detail::COLUMN_ID}
              ."_".$article->{Articles_Model_Detail::COLUMN_EDIT_TIME});
   }

   $feed->flush();
}
}

?>
