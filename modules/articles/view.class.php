<?php
class Articles_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml");
      $this->createListToolbox();
   }

   public function topView() {
      $this->mainView();
   }

   /**
    * @deprecated není třeba!!
    */
   public function contentView() {
      $this->template()->addTplFile("contentlist.phtml");
      echo $this->template();
   }

   public function showView() {
      $this->template()->addTplFile("detail.phtml", 'articles');
      if ((string) $this->article->{Articles_Model::COLUMN_KEYWORDS} != null) {
         Template_Core::setPageKeywords($this->article->{Articles_Model::COLUMN_KEYWORDS});
      }
      if ((string) $this->article->{Articles_Model::COLUMN_DESCRIPTION} != null) {
         Template_Core::setPageDescription($this->article->{Articles_Model::COLUMN_DESCRIPTION});
      } else if ((string) $this->article->{Articles_Model::COLUMN_ANNOTATION} != null) {
         Template_Core::setPageDescription($this->article->{Articles_Model::COLUMN_ANNOTATION});
      }
      $this->createDetailToolbox();
   }

   /**
    * Vytvoření toolboxů v detailu
    */
   protected function createDetailToolbox() {
      if($this->category()->getRights()->isControll() OR
              ($this->category()->getRights()->isWritable() AND
                      $this->article->{Articles_Model::COLUMN_ID_USER} == Auth::getUserId())) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_article', $this->_("Upravit článek"), $this->link()->route('edit'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->_('Upravit článek'));
         $toolbox->addTool($toolEdit);

         if($this->formPublic instanceof Form){
            $tooldel = new Template_Toolbox2_Tool_Form($this->formPublic);
            $tooldel->setIcon('page_preview.png')->setTitle($this->_('Zveřejnit článek'));
            $toolbox->addTool($tooldel);
         }

         $tooldel = new Template_Toolbox2_Tool_Form($this->formDelete);
         $tooldel->setIcon('page_delete.png')->setTitle($this->_('Smazat článek'))
            ->setConfirmMeassage($this->_('Opravdu smazat článek?'));
         $toolbox->addTool($tooldel);

         $this->toolbox = $toolbox;

         if($this->category()->getParam(Articles_Controller::PARAM_PRIVATE_ZONE, false) == true){
            $toolboxP = new Template_Toolbox2();
            $toolboxP->setIcon(Template_Toolbox2::ICON_PEN);
            $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_articlepr', $this->_("Upravit privátní text"),
            $this->link()->route('editPrivate'));
            $toolEdit->setIcon('page_edit.png')->setTitle($this->_('Upravit privátní text'));
            $toolboxP->addTool($toolEdit);
            $this->toolboxPrivate = $toolboxP;
         }
      }
   }

   protected function createListToolbox() {
      if($this->rights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_ADD);
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_article', $this->_("Přidat článek"),
         $this->link()->route('add'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->_('Přidat nový článek'));
         $toolbox->addTool($toolAdd);
         $this->toolbox = $toolbox;
      }
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

   public function editPrivateView() {
       $this->template()->addTplFile("editPrivate.phtml", 'articles');
   }

   public function exportArticlePdfView() {
      // pokud není uložen mezivýstup
      $fileName = $this->pdfFileCacheName();
      if(!file_exists(AppCore::getAppCacheDir().$fileName)) {
         $c = $this->createPdf();
         $c->pdf()->Output(AppCore::getAppCacheDir().$fileName, 'F');
      }
      Template_Output::addHeader('Content-Disposition: attachment; filename="'
              .$this->article->{Articles_Model::COLUMN_URLKEY}.'.pdf"');
      Template_Output::sendHeaders();
      // send Output
      $fp = fopen(AppCore::getAppCacheDir().$fileName,"r");
      while (! feof($fp)) {
         $buff = fread($fp,4096);
         print $buff;
      }
      exit();
   }

   protected function pdfFileCacheName() {
      return md5($this->article->{Articles_Model::COLUMN_ID}
              .'_'.(string)$this->article->{Articles_Model::COLUMN_TEXT_CLEAR}).'.pdf';
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
      $c->pdf()->SetTitle($article->{Articles_Model::COLUMN_NAME});
      $c->pdf()->SetSubject(VVE_WEB_NAME." - ".$this->category()->getLabel());
      $c->pdf()->SetKeywords($this->category()->getCatDataObj()->{Model_Category::COLUMN_KEYWORDS});

      // ---------------------------------------------------------
      $c->pdf()->setHeaderFont(array(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN-2));
      $c->pdf()->setHeaderData('', 0, VVE_WEB_NAME." - ".$this->category()->getLabel()
              ." - ".vve_tpl_truncate($article->{Articles_Model::COLUMN_NAME}, 70)
              , strftime("%x")." - ".$this->link()->route('detail'));

      // add a page
      $c->pdf()->AddPage();
      // nadpis
      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN-2);
      $name = "<h1>".$article->{Articles_Model::COLUMN_NAME}
              ."</h1>";
      $c->pdf()->writeHTML($name, true, 0, true, 0);

      $c->pdf()->Ln();

      // datum autor
      $date = new DateTime($article->{Articles_Model::COLUMN_ADD_TIME});
      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'BI', VVE_PDF_FONT_SIZE_MAIN-2);
      $author = "<p>(".strftime("%x", $date->format("U"))
              ." - ".$article->{Model_Users::COLUMN_USERNAME}.")</p>";
      $c->pdf()->writeHTML($author, true, 0, true, 0);
//      $c->pdf()->Ln();

      if((string)$article->{Articles_Model::COLUMN_ANNOTATION} != null){
         $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
         $c->pdf()->writeHTML((string)$article->{Articles_Model::COLUMN_ANNOTATION}, true, 0, true, 10);
         $c->pdf()->Ln();
      }

      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
      $c->pdf()->writeHTML((string)$article->{Articles_Model::COLUMN_TEXT}, true, 0, true, 10);

      // pokud je private přidáme jej
      if($this->allowPrivate){
         $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
         $c->pdf()->writeHTML((string)$article->{Articles_Model::COLUMN_TEXT_PRIVATE}, true, 0, true, 10);
      }

      return $c;
   }

   public function currentArticleView() {
      $this->createArticleXml();
   }

   public function exportArticleXmlView() {
      $this->createArticleXml();
   }

   public function exportArticleHtmlView() {
      Template_Core::setMainIndexTpl(Template_Core::INDEX_PRINT_TEMPLATE);
      $this->template()->addTplFile('contentdetail.phtml', 'articles');
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
      $xml->writeAttribute('xml:lang', Locales::getLang());

      while ($row = $this->articles->fetch()) {
         $date = new DateTime($row->{Articles_Model::COLUMN_ADD_TIME});
         $xml->startElement('article'); // sof article
         $xml->writeAttribute('date', $date->format('Y-m-d'));
         $xml->writeElement('name', vve_tpl_truncate($row->{Articles_Model::COLUMN_NAME}, 50));
         $xml->writeElement('url', $this->link()->route('detailExport',
                 array('urlkey' => $row->{Articles_Model::COLUMN_URLKEY})));
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
      $api = new Component_Api_VVEArticle();
      $api->setCategory($this->category()->getName(), $this->link()->clear());
      $article = $this->article;
      if($article != null OR $article != false) {
         $api->setArticle($article->{Articles_Model::COLUMN_NAME},
                 $this->link()->route('detail', array('urlkey'=>$article->{Articles_Model::COLUMN_URLKEY})),
                 vve_tpl_truncate(vve_strip_tags($article->{Articles_Model::COLUMN_TEXT}),400));
      }
      $api->flush();
   }
}

?>
