<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class TextView extends View {
   public function mainView() {
      if((bool)$this->sys()->module()->getParam(TextController::PARAM_FILES, true)){
         $this->template()->addJsPlugin(new LightBox());
      }
      $this->template()->addTplFile("text.phtml");
      $this->template()->addCssFile("style.css");

      $model = $this->createModel("TextDetailModel");
      $this->template()->text = $model->getText();

      if($this->rights()->isWritable()){
         $toolbox = new TplToolbox();
         $toolbox->addTool('edit_text', $this->_m("Upravit"),
            $this->link()->action($this->sys()->action()->edittext()),
            $this->_m("Upravit text"), "text_edit.png");
         $this->template()->toolbox = $toolbox;
      }
   }
   /*EOF mainView*/

   public function edittextView() {
      $this->template()->addTplFile("textedit.phtml");
      $this->template()->addCssFile("style.css");

      $tinymce = new TinyMce();
      if($this->module()->getParam(TextController::PARAM_THEME, 'advanced') == 'simple'){
         $tinymce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
         if($this->module()->getParam(TextController::PARAM_FILES, true)){
            $tinymce->addImagesIcon();
         }
      } else if($this->module()->getParam(TextController::PARAM_THEME, 'advanced') == 'full'){
         $tinymce->setTheme(TinyMce::TINY_THEME_FULL);
      }

      //NOTE soubory
      if($this->module()->getParam(TextController::PARAM_FILES, true)){
         $tinymce->setImagesList($this->EPLfiles->getImagesListLink());
         $tinymce->setLinksList($this->EPLfiles->getLinksListLink());
      }

      $this->template()->addJsPlugin($tinymce);
//      if((bool)$this->module()->getParam(TextController::PARAM_FILES, true)){
         $this->template()->addJsPlugin(new LightBox());
//         $this->template()->addVar('LIGHTBOX', true);
//      }
      $text = $this->createModel("TextDetailModel");
      $this->template()->texts = $text->getAllLangText();

      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }
   // EOF edittextView
}

?>