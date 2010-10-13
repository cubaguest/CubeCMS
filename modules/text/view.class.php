<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Text_View extends View {
   public function mainView() {
      $this->template()->addTplFile("text.phtml");

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_PEN);

         $toolET = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->_('Upravit text'),
                 $this->link()->route('edit'));
         $toolET->setIcon('page_edit.png')->setTitle($this->_("Upravit text"));
         $toolbox->addTool($toolET);

         $modelP = new Model_Panel();
         if($modelP->havePanels($this->category()->getId()) == true){
            $toolEP = new Template_Toolbox2_Tool_PostRedirect('edit_textpanel', $this->_('Upravit text panelu'),
                 $this->link()->route('editpanel'));
            $toolEP->setIcon('page_edit.png')->setTitle($this->_("Upravit text v panel"));
            $toolbox->addTool($toolEP);
         }

         if($this->category()->getParam(Text_Controller::PARAM_ALLOW_PRIVATE, false) == true){
            $toolboxP = new Template_Toolbox2();
            $toolboxP->setIcon(Template_Toolbox2::ICON_PEN);
            $toolETP = new Template_Toolbox2_Tool_PostRedirect('edit_textprivate', $this->_('Upravit privátní text'),
                 $this->link()->route('editPrivate'));
            $toolETP->setIcon('page_edit.png')->setTitle($this->_("Upravit privátní text"));
            $toolboxP->addTool($toolETP);
            $this->template()->toolboxPrivate = $toolboxP;
         }

         $this->template()->toolbox = $toolbox;
      }
   }
   /*EOF mainView*/

   public function contentView() {
      echo (string)$this->text->{Text_Model_Detail::COLUMN_TEXT};
   }

   public function editView() {
      $this->template()->addTplFile("textedit.phtml");
   }

   public function editPrivateView() {
      $this->template()->addTplFile("textprivateedit.phtml");
   }

   public function editPanelView() {
      $this->template()->addTplFile("textpaneledit.phtml");
   }
   // EOF edittextView
   public function textHtmlView() {
      $model = new Text_Model_Detail();
      $text = $model->getText(Category::getSelectedCategory()->getId());
      if($text != false) {
         $text = $text->{Text_Model_Detail::COLUMN_TEXT};
      } else {
         $text = $this->_("Text nebyl definován, vytvoříte jej v administraci");
      }
      print ($text);
   }

   public function exportTextHtmlView() {
      Template_Core::setMainIndexTpl(Template_Core::INDEX_PRINT_TEMPLATE);
      $this->template()->addTplFile('texthtml.phtml');
   }

   public function exportTextPdfView() {
      // pokud není uložen mezivýstup
      $fileName = md5($this->category()->getUrlKey()).'.pdf';;
      if(!file_exists(AppCore::getAppCacheDir().$fileName)) {
         $c = $this->createPdf();
         $c->pdf()->Output(AppCore::getAppCacheDir().$fileName, 'F');
      }
      Template_Output::addHeader('Content-Disposition: attachment; filename="'
              .$this->category()->getUrlKey().'.pdf"');
      Template_Output::sendHeaders();
      // send Output
      $fp = fopen(AppCore::getAppCacheDir().$fileName,"r");
      while (! feof($fp)) {
         $buff = fread($fp,4096);
         print $buff;
      }
      exit();
   }

   protected function createPdf() {
      $text = $this->text;
      // komponenta TcPDF
      $c = new Component_Tcpdf();
      // vytvoření pdf objektu
      $c->pdf()->SetTitle($this->category()->getName());
      $c->pdf()->SetSubject(VVE_WEB_NAME." - ".$this->category()->getName());
      $c->pdf()->SetKeywords($this->category()->getCatDataObj()->{Model_Category::COLUMN_KEYWORDS});

      // ---------------------------------------------------------
      $c->pdf()->setHeaderFont(array(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN-2));
      $c->pdf()->setHeaderData('', 0, VVE_WEB_NAME." - ".$this->category()->getName(),
         strftime("%x")." - ".$this->link());

      // add a page
      $c->pdf()->AddPage();
      // nadpis
      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, 'B', VVE_PDF_FONT_SIZE_MAIN-2);
      $name = "<h1>".$this->category()->getName()."</h1>";
      $c->pdf()->writeHTML($name, true, 0, true, 0);

      $c->pdf()->Ln();

      $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
      $c->pdf()->writeHTML((string)$text->{Text_Model::COLUMN_TEXT}, true, 0, true, 10);

      // pokud je private přidáme jej
      if($this->category()->getParam(Text_Controller::PARAM_ALLOW_PRIVATE, false) == true){
         $c->pdf()->SetFont(VVE_PDF_FONT_NAME_MAIN, '', VVE_PDF_FONT_SIZE_MAIN);
         $c->pdf()->writeHTML((string)$text->{Text_Model::COLUMN_TEXT}, true, 0, true, 10);
      }

      return $c;
   }
}

?>