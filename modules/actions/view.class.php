<?php
class Actions_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml", 'actions');

      $this->createListToolbox();
   }

   protected function createListToolbox() {
      if($this->rights()->isWritable()) {
         if( ($this->toolbox instanceof Template_Toolbox2 ) == false ){
            $this->toolbox = new Template_Toolbox2();
            $this->toolbox->setIcon(Template_Toolbox2::ICON_PEN);
         }
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_action', $this->tr("Přidat událost"),
         $this->link()->route('add'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->tr('Přidat novou událost'));
         $this->toolbox->addTool($toolAdd);
         
         $toolArchive = new Template_Toolbox2_Tool_PostRedirect('archive', $this->tr("Zobrazit archiv"),
            $this->link()->route('archive'));
         $toolArchive->setIcon('box.png')->setTitle($this->tr('Zobrazit archiv událostí'));
         $this->toolbox->addTool($toolArchive);
         
         if($this->rights()->isControll() && !$this->category()->getParam(Actions_Controller::PARAM_SHOW_EVENT_DIRECTLY, false) ) {
            $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolAdd = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr("Upravit úvodní text"),
            $this->link()->route('editlabel'));
            $toolAdd->setIcon('page_edit.png')->setTitle($this->tr('Upravit úvodní text'));
            $this->toolbox->addTool($toolAdd);
         }
      }
   }

   public function showView() {
      if($this->category()->getParam(Actions_Controller::PARAM_SHOW_EVENT_DIRECTLY, false)){
         $this->createListToolbox();
      }
      $this->createDetailToolbox();
      if($this->category()->getParam(Actions_Controller::PARAM_SHOW_EVENT_DIRECTLY, false)){
         $this->addBaseToolBox();
      }
      $this->addMetaTags($this->action);
      $this->template()->addFile("tpl://detail.phtml");
      Template_Navigation::addItem($this->action->{Actions_Model::COLUMN_NAME}, $this->link());
   }
   
   public function moveView() {
      $this->template()->addFile("tpl://actions:move.phtml");
      Template_Navigation::addItem($this->action->{Actions_Model::COLUMN_NAME}, $this->link(), true);
   }

   public function previewView() {
      $this->addMetaTags($this->action);
      $this->template()->addFile("tpl://detail.phtml");
      $this->template()->addFile('tpl://actions:previewform.phtml');
      // remove not necessary items
      $this->toolbox = new Template_Toolbox2();
      $toolLangLoader = new Template_Toolbox2_Tool_LangLoader($this->action->{Actions_Model::COLUMN_URLKEY});
      $this->toolbox->addTool($toolLangLoader);
      
      $this->articleTools = false;
      $this->isPreview = true;
      if($this->action->isNew()){
         Template_Navigation::addItem($this->action->{Actions_Model::COLUMN_NAME}, $this->link()->route('add')->param('tmp', true));
      } else {
         Template_Navigation::addItem($this->action->{Actions_Model::COLUMN_NAME}, $this->link()
            ->route('detail', array('urlkey' => $this->action->{Actions_Model::COLUMN_URLKEY})) );
      }
      Template_Navigation::addItem($this->tr('Náhled'), $this->link());
   }
   
   protected function createDetailToolbox() {
      if($this->category()->getRights()->isControll() OR
              ($this->category()->getRights()->isWritable() AND
                      $this->action->{Actions_Model_Detail::COLUMN_ID_USER} == Auth::getUserId())) {
         if( ($this->toolbox instanceof Template_Toolbox2 ) == false ){
            $this->toolbox = new Template_Toolbox2();
         }
         $this->toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_action', $this->tr("Upravit"),
         $this->link()->route('edit'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit akci'));
         $this->toolbox->addTool($toolEdit);
         
         $toolMove = new Template_Toolbox2_Tool_PostRedirect('move_action', $this->tr("Přesunout"),
         $this->link()->route('move'));
         $toolMove->setIcon(Template_Toolbox2::ICON_MOVE)->setTitle($this->tr('Přesunout akci do jiné kateogrie'));
         $this->toolbox->addTool($toolMove);

         $tooldel = new Template_Toolbox2_Tool_Form($this->formDelete);
         $tooldel->setIcon('page_delete.png')->setTitle($this->tr('Smazat'))
            ->setConfirmMeassage($this->tr('Opravdu smazat akci?'));
         $this->toolbox->addTool($tooldel);

      }
      
   }

   public function archiveView() {
      $this->template()->addTplFile("archive.phtml");
      Template_Navigation::addItem($this->tr('Archiv'), $this->link());
   }

   protected function addMetaTags($action)
   {
//      if ((string) $action->{Actions_Model_Detail::COLUMN_KEYWORDS} != null) {
//         Template_Core::setPageKeywords($action->{Articles_Model::COLUMN_KEYWORDS});
//      }
//      if ((string) $action->{Actions_Model_Detail::COLUMN_DESCRIPTION} != null) {
//         Template_Core::setPageDescription($action->{Articles_Model::COLUMN_DESCRIPTION});
//      } else if ((string) $action->{Articles_Model::COLUMN_ANNOTATION} != null) {
//         Template_Core::setPageDescription($action->{Articles_Model::COLUMN_ANNOTATION});
//      }
      Template_Core::setMetaTag('author', $action->{Model_Users::COLUMN_USERNAME});
      if ($action->{Actions_Model_Detail::COLUMN_IMAGE} != null) {
         Template_Core::setCoverImage(vve_tpl_art_title_image($action->{Actions_Model_Detail::COLUMN_IMAGE}));
      } else if((string)$action->{Actions_Model_Detail::COLUMN_TEXT} != null){
         // zkusit načíst kvůli meta tagům
         $doc = new DOMDocument();
         @$doc->loadHTML((string)$action->{Actions_Model_Detail::COLUMN_TEXT});
         $xml = simplexml_import_dom($doc); // just to make xpath more simple
         $images = $xml->xpath('//img');
         if(!empty ($images) && isset ($images[0])){
            if(strpos($images[0]['src'], 'http') !== false ){
               Template_Core::setCoverImage($images[0]['src']);
            } else {
               Template_Core::setCoverImage(Url_Request::getBaseWebDir(false).$images[0]['src']);
            }
         }
      }
   }
   
   /**
    * Viewer pro přidání novinky
    */
   public function addView() 
   {
      $this->editView();
      Template_Navigation::addItem($this->tr('Přidání události'), $this->link());
   }

   public function detailExportView() {
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
         $placePriceStr .= sprintf(strtolower($this->tr('Vstupné: %d Kč')),
                 $action->{Actions_Model_Detail::COLUMN_PRICE}).', ';
      }
      if((int)$action->{Actions_Model_Detail::COLUMN_PREPRICE} != null|0) {
         $placePriceStr .= sprintf(strtolower($this->tr('V předprodeji: %d Kč')),
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
                 .$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
                 .DIRECTORY_SEPARATOR.$action->{Actions_Model_Detail::COLUMN_IMAGE});
//         $c->pdf()->Cell(0, $sizes['heigth']);
         $c->pdf()->writeHTML('<img src="'.$this->category()->getModule()->getDataDir(true)
                 .$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
                 .URL_SEPARATOR.$action->{Actions_Model_Detail::COLUMN_IMAGE}.'" width="200" />', true, 0, true, 0);
         $c->pdf()->Ln();
//         print ('<img src="'.$this->category()->getModule()->getDataDir(true)
//                 .$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
//                 .URL_SEPARATOR.$action->{Actions_Model_Detail::COLUMN_IMAGE}.'" />');
//         $c->pdf()->Image($this->category()->getModule()->getDataDir()
//                 .$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getDefaultLang()]
//                 .DIRECTORY_SEPARATOR.$action->{Actions_Model_Detail::COLUMN_IMAGE});
//         flush();exit();
      }

      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
      $c->pdf()->writeHTML($action->{Actions_Model_Detail::COLUMN_TEXT}, true, 0, true, 0);

      return $c;
   }

   protected function createActionXml($action) {
      $api = new Component_Api_VVEArticle();

//      $category = new Category($action->curlkey);
      $api->setCategory($action->{Model_Category::COLUMN_NAME}, $this->link()->clear()->category($action->curlkey));
      
      $img = null;
      if($action->{Actions_Model_Detail::COLUMN_IMAGE} != null){
         $img = Utils_CMS::getArticleTitleImage($action->{Actions_Model_Detail::COLUMN_IMAGE});
      }

      $api->setArticle($action->{Actions_Model_Detail::COLUMN_NAME},
              $this->link()->clear()
          ->route('detail', array('urlkey'=>$action->{Actions_Model_Detail::COLUMN_URLKEY}))
          ->category($action->curlkey),
          Utils_String::truncate(Utils_Html::stripTags($action->{Actions_Model_Detail::COLUMN_TEXT}),400),$img);

      if((int)$action->{Actions_Model_Detail::COLUMN_PRICE} != null|0) {
         $api->setData('price', $action->{Actions_Model_Detail::COLUMN_PRICE});
      }
      if((int)$action->{Actions_Model_Detail::COLUMN_PREPRICE} != null|0) {
         $api->setData('preprice', $action->{Actions_Model_Detail::COLUMN_PREPRICE});
      }

      $api->flush();
   }
   
   /**
    * Viewer pro editaci 
    */
   public function editView() {
      Template::setFullWidth(true);
      $this->setTinyMCE($this->form->text, 'advanced');
      $this->template()->addFile('tpl://actions:edit.phtml');
      if($this->action != null){
         Template_Navigation::addItem($this->action->{Actions_Model::COLUMN_NAME}, $this->link()->route('detail'));
         Template_Navigation::addItem($this->tr('Úprava události'), $this->link());
      }
   }

   public function featuredListView() {
      switch ($this->type) {
         case 'xml':
         default:
            Template_Output::setOutputType('xml');
            Template_Output::sendHeaders();
            // start xml writer
            $xml = new XMLWriter();
            $xml->openURI('php://output');
            // hlavička
            $xml->startDocument('1.0', 'UTF-8');
            $xml->setIndent(4);
            // rss hlavička
            $xml->startElement('articles'); // SOF article
            $xml->writeAttribute('xmlns','http://www.vveframework.eu/v6/featuredarticles');
            $xml->writeAttribute('xml:lang', Locales::getLang());

            foreach ($this->actions as $row) {
               $xml->startElement('article'); // sof article
               $xml->writeAttribute('date', $row->{Actions_Model::COLUMN_DATE_START});
               $xml->writeElement('name', $row->{Actions_Model::COLUMN_NAME});
               $xml->writeElement('url', $this->link()->clear()
                   ->category($row->curlkey)
                   ->route('detailExport',array('urlkey' => $row->{Actions_Model::COLUMN_URLKEY})));
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
      Template::setFullWidth(true);
      $this->setTinyMCE($this->form->text, 'advanced');
      $this->template()->addFile('tpl://actions:editlabel.phtml');
   }
}
