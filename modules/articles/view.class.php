<?php
class Articles_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://'.$this->category()->getParam(Articles_Controller::PARAM_TPL_LIST, 'articles:list.phtml'));
      $this->createListToolbox();
      
      if($this->selectedTag != null){
         Template_Navigation::addItem( sprintf( $this->tr('štítek: %s'), $this->selectedTag), $this->link());
      }
      
//      if($this->rights()->isControll() AND $this->text == false){
//         $this->text = new Object();
//         $this->text->{Text_Model::COLUMN_TEXT} = $this->tr('Text nebyl definován. Vytvoříte jej pomocí nastrojů editace.');
//      }
   }

   /**
    * @deprecated není potřeba, filtrování je přímo v jlavní metodě
    */
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
      $this->template()->addFile('tpl://'.$this->category()->getParam(Articles_Controller::PARAM_TPL_DETAIL, 'articles:detail.phtml'));
      $this->addMetaTags($this->article);
      if($this->category()->getParam(Articles_Controller::PARAM_DISABLE_LIST, false)){ // pokud není list přidáme tlačítko pro přidání položky
         $this->createListToolbox();
      }
      $this->createDetailToolbox();
      Template_Navigation::addItem($this->article->{Articles_Model::COLUMN_NAME}, $this->link());
   }
   
   protected function addMetaTags($article)
   {
      if ((string) $article->{Articles_Model::COLUMN_KEYWORDS} != null) {
         Template_Core::setPageKeywords($article->{Articles_Model::COLUMN_KEYWORDS});
      }
      if ((string) $article->{Articles_Model::COLUMN_DESCRIPTION} != null) {
         Template_Core::setPageDescription($article->{Articles_Model::COLUMN_DESCRIPTION});
      } else if ((string) $article->{Articles_Model::COLUMN_ANNOTATION} != null) {
         Template_Core::setPageDescription($article->{Articles_Model::COLUMN_ANNOTATION});
      }
      Template_Core::setMetaTag('author', $article->{Model_Users::COLUMN_USERNAME});
      if ($article->{Articles_Model::COLUMN_TITLE_IMAGE} != null) {
         Template_Core::setMetaTag('og:image', vve_tpl_art_title_image($article->{Articles_Model::COLUMN_TITLE_IMAGE}));
      } else if((string)$article->{Articles_Model::COLUMN_TEXT} != null){
         // zkusit načíst kvůli meta tagům
         $doc = new DOMDocument();
         @$doc->loadHTML((string)$article->{Articles_Model::COLUMN_TEXT});
         $xml = simplexml_import_dom($doc); // just to make xpath more simple
         $images = $xml->xpath('//img');
         if(!empty ($images) && isset ($images[0])){
            if(strpos($images[0]['src'], 'http') !== false ){
               Template_Core::setMetaTag('og:image', $images[0]['src']);
            } else {
               Template_Core::setMetaTag('og:image', Url_Request::getBaseWebDir(false).$images[0]['src']);
            }
         }
      }
   }

   /**
    * Vytvoření toolboxů v detailu
    */
   protected function createDetailToolbox() {
      if($this->category()->getRights()->isControll() OR
              ($this->category()->getRights()->isWritable() AND
                      $this->article->{Articles_Model::COLUMN_ID_USER} == Auth::getUserId())) {
         if(($this->toolbox instanceof Template_Toolbox2) == false){
            $this->toolbox = new Template_Toolbox2();
         }

         $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_article', $this->tr("Upravit položku"), $this->link()->route('edit'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit položku'));
         $this->toolbox->addTool($toolEdit);

         if($this->formPublic instanceof Form){
            $tooldel = new Template_Toolbox2_Tool_Form($this->formPublic);
            $tooldel->setIcon('page_preview.png')->setTitle($this->tr('Zveřejnit položku'));
            $this->toolbox->addTool($tooldel);
         }

         $tooldel = new Template_Toolbox2_Tool_Form($this->formDelete);
         $tooldel->setIcon('page_delete.png')->setTitle($this->tr('Smazat položku'))
            ->setConfirmMeassage($this->tr('Opravdu smazat položku?'));
         $this->toolbox->addTool($tooldel);
         
         if($this->article != false){
            $toolLangLoader = new Template_Toolbox2_Tool_LangLoader($this->article->{Articles_Model::COLUMN_TEXT});
            $this->toolbox->addTool($toolLangLoader);
         }

         if($this->category()->getParam(Articles_Controller::PARAM_PRIVATE_ZONE, false) == true){
            $toolboxP = new Template_Toolbox2();
            $toolboxP->setIcon(Template_Toolbox2::ICON_PEN);
            $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_articlepr', $this->tr("Upravit privátní text"),
            $this->link()->route('editPrivate'));
            $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit privátní text'));
            $toolboxP->addTool($toolEdit);
            $this->toolboxPrivate = $toolboxP;
         }
         
         if(isset ($_GET['l']) AND isset ($this->article[Articles_Model::COLUMN_TEXT][$_GET['l']])){
            $l = $_GET['l'];
            $this->article->{Articles_Model::COLUMN_TEXT} = $this->article[Articles_Model::COLUMN_TEXT][$l];
            $this->article->{Articles_Model::COLUMN_NAME} = $this->article[Articles_Model::COLUMN_NAME][$l];
         }
         $this->article->{Articles_Model::COLUMN_TEXT} = $this->template()->filter((string)$this->article->{Articles_Model::COLUMN_TEXT}, array('anchors'));
      }
   }

   protected function createListToolbox() {
      if($this->rights()->isWritable()) {
         $this->toolbox = new Template_Toolbox2();
         $this->toolbox->setIcon(Template_Toolbox2::ICON_ADD);
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_article', $this->tr("Přidat položku"),
         $this->link()->route('add'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->tr('Přidat novou položku'));
         $this->toolbox->addTool($toolAdd);
         if($this->category()->getRights()->isControll()){
            $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolETView = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr("Upravit text"),
            $this->link()->route('edittext'));
            $toolETView->setIcon('page_edit.png')->setTitle($this->tr('Upravit úvodní text'));
            $this->toolbox->addTool($toolETView);
            
            $toolETags = new Template_Toolbox2_Tool_PostRedirect('edit_tags', $this->tr("Správa štítků"),
            $this->link()->route('editTags'));
            $toolETags->setIcon('flag_green.png')->setTitle($this->tr('Správa štítků položek'));
            $this->toolbox->addTool($toolETags);
            
            $toolEView = new Template_Toolbox2_Tool_PostRedirect('edit_view', $this->tr("Nastavení"),
            $this->link()->route(Routes::MODULE_SETTINGS));
            $toolEView->setIcon('wrench.png')->setTitle($this->tr('Upravit nastavení kategorie'));
            $this->toolbox->addTool($toolEView);
         }
      }
   }

   public function archiveView() {
      $this->template()->addFile('tpl://'.$this->category()->getParam(Articles_Controller::PARAM_TPL_ARCHIVE, 'articles:archive.phtml'));
      Template_Navigation::addItem($this->tr('Archiv'), $this->link());
   }

   /**
    * Viewer pro přidání článku
    */
   public function addView() {
      Template_Module::setEdit(true);
      $this->addTinyMCE();
      $this->template()->addFile('tpl://articles:edit.phtml');
      if(!$this->edit){
         Template_Navigation::addItem($this->tr('Přidání položky'), $this->link());
      }
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->addView();
      Template_Navigation::addItem($this->article->{Articles_Model::COLUMN_NAME}, $this->link()->route('detail'));
      Template_Navigation::addItem($this->tr('Úprava položky'), $this->link());
   }

   public function editTextView() {
      $this->addTinyMCE('simple');
      $this->template()->addFile('tpl://articles:edittext.phtml');
      Template_Navigation::addItem($this->tr('Úprava úvodního textu'), $this->link());
   }
   
   private function addTinyMCE($theme = 'advanced') {
      if($this->category()->getParam(Articles_Controller::PARAM_EDITOR_TYPE, $theme) == 'none') return;
      if($this->form->haveElement('text')){
         $this->form->text->html()->addClass("mceEditor");
      }
      if($this->form->haveElement('textPrivate')){
         $this->form->textPrivate->html()->addClass("mceEditor");
      }
      $this->tinyMCE = new Component_TinyMCE();
      switch ($this->category()->getParam(Articles_Controller::PARAM_EDITOR_TYPE, $theme)) {
         case 'simple':
            $settings = new Component_TinyMCE_Settings_AdvSimple();
            $settings->setSetting('editor_selector', 'mceEditor');
            break;
         case 'full':
            // TinyMCE
            $settings = new Component_TinyMCE_Settings_Full();
            break;
         case 'advanced':
         default:
            $settings = new Component_TinyMCE_Settings_Advanced();
            break;
      }
      $settings->setSetting('height', '600');
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
      
      if($this->form->haveElement('annotation')){
         $this->form->annotation->html()->addClass("mceEditorSimple");
         $this->tinyMCES = new Component_TinyMCE();
         $settingsS = new Component_TinyMCE_Settings_AdvSimple();
         $settingsS->setSetting('editor_selector', 'mceEditorSimple');
         $this->tinyMCES->setEditorSettings($settingsS);
         $this->tinyMCES->mainView();
      }

   }

   public function editPrivateView() {
      Template_Module::setEdit(true);
      $this->addTinyMCE();
      $this->template()->addFile('tpl://articles:editPrivate.phtml');
      Template_Navigation::addItem($this->article->{Articles_Model::COLUMN_NAME}, $this->link()->route('detail'));
      Template_Navigation::addItem($this->tr('Úprava privátní části'), $this->link());
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
      $this->template()->addFile('tpl://articles:contentdetail.phtml');
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

   public function getTagsView()
   {
      echo json_encode($this->tags);
      exit;
   }
   
   public function listTagsView(){
      echo json_encode($this->respond);
   }
   
   public function editTagsView()
   {
      Template_Module::setEdit(true);
      $this->template()->addFile('tpl://articles:edit_tags.phtml');
      Template_Navigation::addItem($this->tr('Správa štítků'), $this->link());
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
