<?php
class Articles_View extends View {
   /**
    * Název parametru s počtem článků na stránce
    */
   const PARAM_NUM_ARTICLES_ON_PAGE = 'scroll';

   public function mainView() {
      if($this->rights()->isWritable()){
         $toolbox = new TplToolbox();
         $toolbox->addTool('add_article', $this->_m("Přidat"),
            $this->link()->action($this->sys()->action()->addarticle()),
            $this->_m("Přidat článek"), "text_add.png");
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
         $jquery = new JQuery();
         $this->template()->addJsPlugin($jquery);

         $toolbox = new TplToolbox();
         $toolbox->addTool('edit_article', $this->_m("Upravit"),
            $this->link()->action($this->sys()->action()->editarticle()),
            $this->_m("Upravit článek"), "text_edit.png")
         ->addTool('delete_article', $this->_m("Smazat"), $this->link(),
            $this->_m("Smazat článek"), "remove.png", "delete_article_id", 5, $this->_m("Opravdu smazat článek")."?");
         $this->template()->toolbox = $toolbox;
      }

      //převedeme všechny lightbox převedeme na lightbox rel
      if((bool)$this->module()->getParam(Articles_Controller::PARAM_FILES, true)){
         $this->template()->addJsPlugin(new JsPlugin_LightBox());
         $this->template()->lightBox = true;
      }

      $model = new Articles_Model_Detail($this->sys());
      $this->template()->article = $model->getArticleDetailSelLang($this->sys()->article()->getArticle());

      $this->template()->addTplFile("articleDetail.phtml");
      $this->template()->addCssFile("style.css");
      $this->template()->setArticleTitle($this->template()->article[Articles_Model_Detail::COLUMN_ARTICLE_LABEL]);
   }

   /**
    * Viewer pro přidání článku
    */
   public function addarticleView() {
      $this->template()->addTplFile('editArticle.phtml');
      $this->template()->addCssFile("style.css");

      //      $this->template()->setTplSubLabel(_m('Přidání článku'));
      //      $this->template()->setSubTitle(_m('Přidání článku'), true);
      //      $this->template()->addVar("ADD_ARTICLE_LABEL",_m('Přidání článku'));
      //
      //      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
      //      $this->assignLabels();

      // tiny mce
      $tinymce = new TinyMce();
      if($this->module()->getParam(ArticlesController::PARAM_EDITOR_THEME) == 'simple'){
         $tinymce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
         if((bool)$this->module()->getParam(ArticlesController::PARAM_FILES, true)){
            $tinymce->addImagesIcon();
         }
      } else if($this->module()->getParam(ArticlesController::PARAM_EDITOR_THEME, 'advanced') == 'advanced'){
         $tinymce->setTheme(TinyMce::TINY_THEME_FULL);
      }

      //NOTE soubory
      if((bool)$this->module()->getParam(ArticlesController::PARAM_FILES, true)){
         $tinymce->setImagesList($this->EPLfiles->getImagesListLink());
         $tinymce->setLinksList($this->EPLfiles->getLinksListLink());
      }

      // vložení šablony epluginu TinyMCE
      $this->template()->addJsPlugin($tinymce);

      //Tabulkové uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editArticleView() {
      $this->template()->addTplFile('editArticle.phtml');
      $this->template()->addCssFile("style.css");

//      $this->template()->setTplSubLabel(_m("Úprava článku").' - '.$this->container()->getData('ARTICLE_NAME'));
//      $this->template()->setSubTitle(_m("Úprava článku").' - '.$this->container()->getData('ARTICLE_NAME'), true);
//      $this->template()->addVar("ADD_NEWS_LABEL",_m("Úprava článku").' - '.$this->container()->getData('ARTICLE_NAME'));

//      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na článek'));
//      $this->assignLabels();

      $model = new ArticleDetailModel($this->sys());
      $this->template()->article = $model->getArticleDetailAllLangs($this->sys()->article()->getArticle());

      // tiny mce
      $tinymce = new TinyMce();
      if($this->module()->getParam(ArticlesController::PARAM_EDITOR_THEME) == 'simple'){
         $tinymce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
         if((bool)$this->module()->getParam(ArticlesController::PARAM_FILES, true)){
            $tinymce->addImagesIcon();
         }
      } else if($this->module()->getParam(ArticlesController::PARAM_EDITOR_THEME, 'advanced') == 'advanced'){
         $tinymce->setTheme(TinyMce::TINY_THEME_FULL);
      }

      //NOTE soubory
      if((bool)$this->module()->getParam(ArticlesController::PARAM_FILES, true)){
         $tinymce->setImagesList($this->EPLfiles->getImagesListLink());
         $tinymce->setLinksList($this->EPLfiles->getLinksListLink());
      }

      // vložení šablony epluginu TinyMCE
      $this->template()->addJsPlugin($tinymce);

      //Tabulkové uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }
}

?>
