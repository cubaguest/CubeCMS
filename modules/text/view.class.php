<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Text_View extends View {
   public function mainView() {
      $this->template()->addFile('tpl://'.$this->category()->getParam(Text_Controller::PARAM_TPL_MAIN, 'text.phtml'));
      // text nebyl zadán
      if($this->text == false){
         $this->text = new Object();
         $this->text->{Text_Model_Detail::COLUMN_TEXT} = null;
         if($this->category()->getRights()->isWritable()){
            $this->text->{Text_Model_Detail::COLUMN_TEXT} = $this->_('Text nebyl vytvořen. Upravíte jej v administraci.');
         }
      } else {
         $this->text->{Text_Model_Detail::COLUMN_TEXT} = $this->template()->filter((string)$this->text->{Text_Model_Detail::COLUMN_TEXT}, array('anchors','filesicons'));
      }
      
      // private  text
      if($this->category()->getParam(Text_Controller::PARAM_ALLOW_PRIVATE, false) == true){
         if($this->textPrivate == false OR strip_tags((string)$this->textPrivate->{Text_Model_Detail::COLUMN_TEXT}) == null){
            $this->textPrivate = new Object();
            $this->textPrivate->{Text_Model_Detail::COLUMN_TEXT} = null;
            if($this->category()->getRights()->isWritable()){
               $this->textPrivate->{Text_Model_Detail::COLUMN_TEXT} = $this->_('Privátní text nebyl vytvořen. Upravíte jej v administraci.');
            }
         }
         $this->textPrivate->{Text_Model_Detail::COLUMN_TEXT} = $this->template()->filter(
            (string)$this->textPrivate->{Text_Model_Detail::COLUMN_TEXT}, array('anchors','filesicons'));
      }

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

   public function contentView() {
      echo (string)$this->text->{Text_Model_Detail::COLUMN_TEXT};
   }

   public function editView() {
      Template_Module::setEdit(true);
      $this->h1 = sprintf($this->_('úprava textu "%s"'), $this->category()->getName());
      Template_Core::setPageTitle($this->h1);
      $this->addTinyMCE();
      $this->template()->addTplFile("textedit.phtml");
   }

   public function editPrivateView() {
      $this->editView();
      $this->h1 = sprintf($this->_('úprava privátního textu "%s"'), $this->category()->getName());
      Template_Core::setPageTitle($this->h1);
   }

   public function editPanelView() {
      $this->editView();
      $this->h1 = sprintf($this->_('úprava textu panelu "%s"'), $this->category()->getName());
      Template_Core::setPageTitle($this->h1);
   }

   private function addTinyMCE() {
      $type = $this->category()->getParam(Text_Controller::PARAM_EDITOR_TYPE, 'advanced');
      if($type == 'none') return;
      $this->form->text->html()->addClass("mceEditor");
      $this->tinyMCE = new Component_TinyMCE();
      switch ($type) {
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