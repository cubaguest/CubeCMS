<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Text_View extends View {
   public function mainView() {
//      $this->template()->addFile('tpl://'.$this->category()->getParam(Text_Controller::PARAM_TPL_MAIN, 'text.phtml'));
      $this->template()->addFile($this->getTemplate('main'));

      if($this->category()->getRights()->isWritable()) {
         $toolbox = new Template_Toolbox2();
         $toolbox->setIcon(Template_Toolbox2::ICON_PEN);

         $toolET = new Template_Toolbox2_Tool_PostRedirect('edit_text', $this->tr('Upravit text'),
                 $this->link()->route('edit'));
         $toolET->setIcon('page_edit.png')->setTitle($this->tr("Upravit text"));
         $toolbox->addTool($toolET);

         $modelP = new Model_Panel();
         if($modelP->havePanels($this->category()->getId()) == true){
            $toolEP = new Template_Toolbox2_Tool_PostRedirect('edit_textpanel', $this->tr('Upravit text panelu'),
                 $this->link()->route('editpanel'));
            $toolEP->setIcon('page_edit.png')->setTitle($this->tr("Upravit text v panelu"));
            $toolbox->addTool($toolEP);
         }

         if($this->category()->getParam(Text_Controller::PARAM_ALLOW_PRIVATE, false) == true){
            $toolboxP = new Template_Toolbox2();
            $toolboxP->setIcon(Template_Toolbox2::ICON_PEN);
            $toolETP = new Template_Toolbox2_Tool_PostRedirect('edit_textprivate', $this->tr('Upravit privátní text'),
                 $this->link()->route('editPrivate'));
            $toolETP->setIcon('page_edit.png')->setTitle($this->tr("Upravit privátní text"));
            $toolboxP->addTool($toolETP);
            $this->toolboxPrivate = $toolboxP;
         }

         $this->toolbox = $toolbox;

         if($this->text != false){
            $toolLangLoader = new Template_Toolbox2_Tool_LangLoader($this->text->{Text_Model::COLUMN_TEXT});
            $this->toolbox->addTool($toolLangLoader);
         }

         if(isset ($_GET['l']) AND isset ($this->text[Text_Model::COLUMN_TEXT][$_GET['l']])){
            $l = $_GET['l'];
            $this->text->{Text_Model::COLUMN_TEXT} = $this->text[Text_Model::COLUMN_TEXT][$l];
            if($this->text[Text_Model::COLUMN_LABEL][$l] != null){
               $this->text->{Text_Model::COLUMN_LABEL} = $this->text[Text_Model::COLUMN_LABEL][$l];
            } else {
               $obj = Category::getSelectedCategory()->getCatDataObj();
               $this->text->{Text_Model::COLUMN_LABEL} = $obj[Model_Category::COLUMN_NAME][$l];
            }
            unset ($obj);
         }
      }

      $textFilters = array('anchors','filesicons');
      if(defined('VVE_CACHE_TEXT_IMAGES') && VVE_CACHE_TEXT_IMAGES == true){
         $textFilters[] = 'cacheimages';
      }
      
      // text nebyl zadán
      if($this->text){
         $this->text->{Text_Model::COLUMN_TEXT} = $this->template()->filter((string)$this->text->{Text_Model::COLUMN_TEXT}, $textFilters);
      }

      // private  text
      if($this->category()->getParam(Text_Controller::PARAM_ALLOW_PRIVATE, false) == true){
         if($this->textPrivate == false OR strip_tags((string)$this->textPrivate->{Text_Model::COLUMN_TEXT}) == null){
            $this->textPrivate = new Object();
            $this->textPrivate->{Text_Model::COLUMN_TEXT} = null;
            if($this->category()->getRights()->isWritable()){
               $this->textPrivate->{Text_Model::COLUMN_TEXT} = $this->tr('Privátní text nebyl vytvořen. Upravíte jej v administraci.');
            }
         }
         $this->textPrivate->{Text_Model::COLUMN_TEXT} = 
            $this->template()->filter((string)$this->textPrivate->{Text_Model::COLUMN_TEXT}, $textFilters);
      }
   }
   
   public function previewView() {
      $this->mainView();
      $this->template()->addFile('tpl://text:previewform.phtml');
      // remove not necessary items
      if($this->toolbox instanceof Template_Toolbox2){
         unset($this->toolbox);            
      }
      $this->textPrivate = null;
      $this->toolboxPrivate = null;
      $this->articleTools = false;
      Template_Navigation::addItem($this->tr('Náhled'), $this->link());
   }

   public function contentView() {
      echo (string)$this->text->{Text_Model::COLUMN_TEXT};
   }

   public function editView() {
      Template_Module::setFullWidth(true);
      $this->h1 = sprintf($this->tr('úprava textu kategorie "%s"'), $this->category()->getName());
      Template_Core::setPageTitle($this->h1);
      $this->setTinyMCE($this->form->text, $this->category()->getParam(Text_Controller::PARAM_EDITOR_TYPE));
      $this->template()->addFile("tpl://text:textedit.phtml");
      Template_Navigation::addItem($this->tr('Úprava obsahu'), $this->link());

      if($this->fields){
         $filedsEditor = $this->getCurrentTemplateParam('customFieldsEditor', array(), 'main');
         foreach($this->fields as $field){
            $element = 'filed_'.$field;
            if($this->form->$element instanceof Form_Element_Text || $this->form->$element instanceof Form_Element_TextArea ){
               $this->setTinyMCE($this->form->$element, isset($filedsEditor[$field]) ? $filedsEditor[$field] : 'simple');
            }
         }
      }
   }

   public function editPrivateView() {
      $this->editView();
      $this->h1 = sprintf($this->tr('úprava privátního textu "%s"'), $this->category()->getName());
      Template_Core::setPageTitle($this->h1);
      Template_Navigation::addItem($this->tr('Úprava privátního textu'), $this->link());
   }

   public function editPanelView() {
      Template_Module::setFullWidth(true);
      $this->h1 = sprintf($this->tr('úprava textu panelu kategorie "%s"'), $this->category()->getName());
      Template_Core::setPageTitle($this->h1);
      $this->setTinyMCE($this->form->text, $this->category()->getParam(Text_Controller::PARAM_EDITOR_TYPE, 'advanced'));
      $this->template()->addFile("tpl://text:textedit.phtml");
      Template_Navigation::addItem($this->tr('Úprava obsahu panelu'), $this->link());
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