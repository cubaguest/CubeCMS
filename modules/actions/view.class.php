<?php
class Actions_View extends View {
   public function mainView() {
      $this->template()->addTplFile("list.phtml", 'actions');

      $this->createListToolbox();
   }

   protected function createListToolbox() {
      if($this->rights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_PEN);
         $toolAdd = new Template_Toolbox2_Tool_PostRedirect('add_action', $this->tr("Přidat akci"),
         $this->link()->route('add'));
         $toolAdd->setIcon('page_add.png')->setTitle($this->tr('Přidat novou akci'));
         $toolbox->addTool($toolAdd);
         
         if($this->rights()->isControll()) {
            $toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
            $toolAdd = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr("Upravit úvodní text"),
            $this->link()->route('editlabel'));
            $toolAdd->setIcon('page_edit.png')->setTitle($this->tr('Upravit úvodní text'));
            $toolbox->addTool($toolAdd);
         }

         $this->toolbox = $toolbox;
      }
   }

   public function showView() {
      $this->createDetailToolbox();
      $this->addMetaTags($this->action);
      $this->template()->addTplFile("detail.phtml");
   }

   protected function createDetailToolbox() {
      if($this->category()->getRights()->isControll() OR
              ($this->category()->getRights()->isWritable() AND
                      $this->action->{Actions_Model_Detail::COLUMN_ID_USER} == Auth::getUserId())) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_WRENCH);
         $toolEdit = new Template_Toolbox2_Tool_PostRedirect('edit_action', $this->tr("Upravit"),
         $this->link()->route('edit'));
         $toolEdit->setIcon('page_edit.png')->setTitle($this->tr('Upravit akci'));
         $toolbox->addTool($toolEdit);

         $tooldel = new Template_Toolbox2_Tool_Form($this->formDelete);
         $tooldel->setIcon('page_delete.png')->setTitle($this->tr('Smazat'))
            ->setConfirmMeassage($this->tr('Opravdu smazat akci?'));
         $toolbox->addTool($tooldel);

         $this->toolbox = $toolbox;
      }
   }

   public function archiveView() {
      $this->template()->addTplFile("archive.phtml");
   }

   private function addTinyMCE() {
      if($this->form->haveElement('text')){
         $this->form->text->html()->addClass("mceEditor");
      }
      $this->tinyMCE = new Component_TinyMCE();
      $settings = new Component_TinyMCE_Settings_Advanced();
      $settings->setSetting('height', '600');
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
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
         Template_Core::setMetaTag('og:image', vve_tpl_art_title_image($action->{Actions_Model_Detail::COLUMN_IMAGE}));
      } else if((string)$action->{Actions_Model_Detail::COLUMN_TEXT} != null){
         // zkusit načíst kvůli meta tagům
         $doc = new DOMDocument();
         @$doc->loadHTML((string)$action->{Actions_Model_Detail::COLUMN_TEXT});
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
    * Viewer pro přidání novinky
    */
   public function addView() {
      $this->editView();
   }

   public function showDataView() {
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

      $api->setCategory($this->category()->getName(), $this->link()->clear());

      $img = null;
      if($action->{Actions_Model_Detail::COLUMN_IMAGE} != null){
         $img = $this->category()->getModule()->getDataDir(true)
                  .$action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getLang()]
                  .URL_SEPARATOR.$action->{Actions_Model_Detail::COLUMN_IMAGE};
      }

      $api->setArticle($action->{Actions_Model_Detail::COLUMN_NAME},
              $this->link()->route('detail', array('urlkey'=>$action->{Actions_Model_Detail::COLUMN_URLKEY})),
              vve_tpl_truncate(vve_strip_tags($action->{Actions_Model_Detail::COLUMN_TEXT}),400),$img);

      if((int)$action->{Actions_Model_Detail::COLUMN_PRICE} != null|0) {
         $api->setData('price', $action->{Actions_Model_Detail::COLUMN_PRICE});
      }
      if((int)$action->{Actions_Model_Detail::COLUMN_PREPRICE} != null|0) {
         $api->setData('preprice', $action->{Actions_Model_Detail::COLUMN_PREPRICE});
      }

      $api->flush();
   }
   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->addTinyMCE();
      $this->template()->addFile('tpl://actions:edit.phtml');
      Template_Module::setEdit(true);
   }

   public function featuredListView() {
      switch ($this->type) {
         case 'xml':
         default:
            $xml = new XMLWriter();
            $xml->openURI('php://output');
            // hlavička
            $xml->startDocument('1.0', 'UTF-8');
            $xml->setIndent(4);

            // rss hlavička
            $xml->startElement('articles'); // SOF article
            $xml->writeAttribute('xmlns','http://www.vveframework.eu/v6/featuredarticles');
            $xml->writeAttribute('xml:lang', Locales::getLang());

            while ($row = $this->actions->fetch()) {
               $xml->startElement('article'); // sof article
               $xml->writeAttribute('date', $row->{Actions_Model_Detail::COLUMN_DATE_START});
               $xml->writeElement('name', $row->{Actions_Model_Detail::COLUMN_NAME});
               $xml->writeElement('url', $this->link()->route('detailExport',
                       array('urlkey' => $row->{Actions_Model_Detail::COLUMN_URLKEY})));
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
      $this->addTinyMCE();
      $this->template()->addFile('tpl://actions:editlabel.phtml');
   }
}

?>