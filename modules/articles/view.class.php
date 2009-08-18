<?php
class Articles_View extends View {
   /**
    * Název parametru s počtem článků na stránce
    */
   const PARAM_NUM_ARTICLES_ON_PAGE = 'scroll';

   public function mainView() {
      if($this->rights()->isWritable()){
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('add_article', $this->_("Přidat"),
            $this->link()->action($this->sys()->action()->addarticle()),
            $this->_("Přidat článek"), "text_add.png");
         $this->template()->toolbox = $toolbox;
      }

      $this->template()->addTplFile("list.phtml");
      $this->template()->addCssFile("style.css");

      //		Vytvoření modelu
      $articleModel = new Articles_Model_List($this->sys());
      //		Scrolovátka
      $scroll = new Eplugin_Scroll($this->sys());
      $scroll->setCountRecordsOnPage($this->module()->getParam(self::PARAM_NUM_ARTICLES_ON_PAGE, 10));
      $scroll->setCountAllRecords($articleModel->getCountArticles());
//      var_dump($scroll);
      //		Vybrání článků
      $this->template()->articlesArray = $articleModel->getSelectedListArticles($scroll->getStartRecord(), $scroll->getCountRecords());
      $this->template()->EPLscroll = $scroll;

      $list = $this->template()->articlesArray;
      foreach ($list as &$article) {
         $out = array();
         preg_match("/(<img[^>]*\/?>)/i", $article[Articles_Model_Detail::COLUMN_ARTICLE_TEXT], $out);
         if(!empty ($out[1])){
            preg_match('/src="([^"]*)"/i', $out[1], $out);
            $article['title_image'] = $out[1];
         } else {
            $article['title_image'] = null;
         }
      }
      $this->template()->articlesArray = $list;

   }

   public function showView(){
      if($this->rights()->isWritable()){
         // editační tlačítka
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_article', $this->_("Upravit"),
            $this->link()->action($this->sys()->action()->editarticle()),
            $this->_("Upravit článek"), "text_edit.png")
         ->addTool('article_delete', $this->_("Smazat"), $this->link(),
            $this->_("Smazat článek"), "remove.png", "article_id", 
            $this->template()->article[Articles_Model_Detail::COLUMN_ARTICLE_ID],
            $this->_("Opravdu smazat článek")."?");
         $this->template()->toolbox = $toolbox;
      }

      //převedeme všechny lightbox převedeme na lightbox rel
      if((bool)$this->module()->getParam(Articles_Controller::PARAM_FILES, true)){
         $this->template()->addJsPlugin(new JsPlugin_LightBox());
         $this->template()->lightBox = true;
      }

      $this->template()->addTplFile("articleDetail.phtml");
      $this->template()->addCssFile("style.css");
      $this->template()->setArticleName($this->template()->article[Articles_Model_Detail::COLUMN_ARTICLE_LABEL]);
   }

   /**
    * Viewer pro přidání článku
    */
   public function addarticleView() {
      $this->template()->addTplFile('editArticle.phtml');
      $this->template()->addCssFile("style.css");
      $this->template()->setActionName($this->_("přidání článku"));

      // tiny mce
      $tinymce = new JsPlugin_TinyMce();
      if($this->module()->getParam(Articles_Controller::PARAM_EDITOR_THEME) == 'simple'){
         $tinymce->setTheme(JsPlugin_TinyMce::TINY_THEME_ADVANCED_SIMPLE);
         if((bool)$this->module()->getParam(Articles_Controller::PARAM_FILES, true)){
            $tinymce->addImagesIcon();
         }
      } else if($this->module()->getParam(Articles_Controller::PARAM_EDITOR_THEME, 'advanced') == 'advanced'){
         $tinymce->setTheme(JsPlugin_TinyMce::TINY_THEME_FULL);
      }

      //NOTE soubory
      if((bool)$this->module()->getParam(Articles_Controller::PARAM_FILES, true)){
         $tinymce->setImagesList($this->EPLfiles->getImagesListLink());
         $tinymce->setLinksList($this->EPLfiles->getLinksListLink());
      }
      // vložení šablony epluginu TinyMCE
      $this->template()->addJsPlugin($tinymce);

      //Tabulkové uspořádání
      $jquery = new JsPlugin_JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editArticleView() {
      $this->template()->addTplFile('editArticle.phtml');
      $this->template()->addCssFile("style.css");

      $model = new Articles_Model_Detail($this->sys());
      $this->template()->article = $model->getArticleDetailAllLangs($this->sys()->article()->getArticle());
      $this->template()->setArticleName($this->template()
         ->article[Articles_Model_Detail::COLUMN_ARTICLE_LABEL][Locale::getLang()]);
      $this->template()->setActionName($this->_("úprava článku"));

      // tiny mce
      $tinymce = new JsPlugin_TinyMce();
      if($this->module()->getParam(Articles_Controller::PARAM_EDITOR_THEME) == 'simple'){
         $tinymce->setTheme(JsPlugin_TinyMce::TINY_THEME_ADVANCED_SIMPLE);
         if((bool)$this->module()->getParam(Articles_Controller::PARAM_FILES, true)){
            $tinymce->addImagesIcon();
         }
      } else if($this->module()->getParam(Articles_Controller::PARAM_EDITOR_THEME, 'advanced') == 'advanced'){
         $tinymce->setTheme(JsPlugin_TinyMce::TINY_THEME_FULL);
      }

      //NOTE soubory
      if((bool)$this->module()->getParam(Articles_Controller::PARAM_FILES, true)){
         $tinymce->setImagesList($this->EPLfiles->getImagesListLink());
         $tinymce->setLinksList($this->EPLfiles->getLinksListLink());
      }

      // vložení šablony epluginu TinyMCE
      $this->template()->addJsPlugin($tinymce);

      //Tabulkové uspořádání
      $jquery = new JsPlugin_JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }
}

?>
