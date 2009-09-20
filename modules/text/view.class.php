<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class Text_View extends View {
   public function mainView() {
      if((bool)$this->category()->getModule()->getParam(Text_Controller::PARAM_FILES, true)){
         $this->template()->addJsPlugin(new JsPlugin_LightBox());
      }
      $this->template()->addTplFile("text.phtml");
      $this->template()->addCssFile("style.css");

      $model = new Text_Model_Detail();
      $this->template()->text = $model->getText();

      if($this->rights()->isWritable()){
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_text', $this->_("Upravit"),
            $this->link()->route('edit'),
            $this->_("Upravit text"), "text_edit.png");
         $this->template()->toolbox = $toolbox;
      }
   }
   /*EOF mainView*/

   public function editView() {
      $this->template()->addTplFile("textedit.phtml");
      $this->template()->addCssFile("style.css");
      $this->template()->setPageTitle($this->_("úprava textu"));

      $tinymce = new JsPlugin_TinyMce();
//      if($this->category()->getModule()->getParam(Text_Controller::PARAM_THEME, 'advanced') == 'simple'){
//         $tinymce->setTheme(JsPlugin_TinyMce::TINY_THEME_ADVANCED_SIMPLE);
//         if($this->module()->getParam(Text_Controller::PARAM_FILES, true)){
//            $tinymce->addImagesIcon();
//         }
//      } else if($this->category()->getModule()->getParam(Text_Controller::PARAM_THEME, 'advanced') == 'full'){
//         $tinymce->setTheme(JsPlugin_TinyMce::TINY_THEME_FULL);
//      }

      //NOTE soubory
//      if($this->category()->getModule()->getParam(Text_Controller::PARAM_FILES, true)){
//         $tinymce->setImagesList($this->EPLfiles->getImagesListLink());
//         $tinymce->setLinksList($this->EPLfiles->getLinksListLink());
//      }

      $this->template()->addJsPlugin($tinymce);
//      $this->template()->addJsPlugin(new JsPlugin_LightBox());

      $text = new Text_Model_Detail();
      $this->template()->texts = $text->getAllLangText($this->category()->getId());

      $jquery = new JsPlugin_JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }
   // EOF edittextView

   public function listJsView() {
      print "list View OK with JS output";

   }
}

?>