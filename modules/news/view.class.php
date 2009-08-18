<?php
class News_View extends View {
   public function mainView() {
      if($this->rights()->isWritable()){
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('add_article', $this->_("Přidat"),
            $this->link()->action($this->sys()->action()->addNews()),
            $this->_("Přidat novinku"), "text_add.png");
         $this->template()->toolbox = $toolbox;
      }

      $this->template()->addTplFile("list.phtml");
      $this->template()->addCssFile("style.css");
   }

   public function showView(){
      $this->template()->new;
      if($this->rights()->isControll() OR
         $this->template()->new[News_Model_Detail::COLUMN_NEWS_ID_USER]
         == $this->rights()->getAuth()->getUserId()){
         // editační tlačítka
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_news', $this->_("Upravit"),
            $this->link()->action($this->sys()->action()->editNews()),
            $this->_("Upravit novinku"), "text_edit.png")
         ->addTool('news_delete', $this->_("Smazat"), $this->link(),
            $this->_("Smazat novinku"), "remove.png", "news_id",
            $this->template()->new[News_Model_Detail::COLUMN_NEWS_ID_NEW],
            $this->_("Opravdu smazat novinku")."?");
         $this->template()->toolbox = $toolbox;
      }

//      $this->template()->setArticleName($this->template()->new[News_Model_Detail::COLUMN_NEWS_LABEL]
//         ." - ".strftime("%x", $this->template()->new[News_Model_Detail::COLUMN_NEWS_TIME]));
      $this->template()->setArticleName($this->template()->new[News_Model_Detail::COLUMN_NEWS_LABEL]);

      $this->template()->addTplFile("newDetail.phtml");
      $this->template()->addCssFile("style.css");
   }

   /**
    * Viewer pro přidání novinky
    */
   public function addNewsView() {
      $this->template()->addTplFile('editNews.phtml');
      $this->template()->addCssFile("style.css");
      $this->template()->setActionName($this->_("přidání novinky"));

      // Tiny MCE plugin
      $tinymce = new JsPlugin_TinyMce();
      $tinymce->setTheme(JsPlugin_TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinymce);

      //Tabulkové uspořádání
      $jquery = new JsPlugin_JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editNewsView() {
      $this->template()->addTplFile('editNews.phtml');
      $this->template()->addCssFile("style.css");

      $newsModel = new News_Model_Detail($this->sys());
      $this->template()->new = $newsModel->getNewsDetail($this->sys()->article());

      $this->template()->setArticleName($this->template()->new[News_Model_Detail::COLUMN_NEWS_LABEL][Locale::getLang()]);
      $this->template()->setActionName($this->_("úprava novinky"));

      // Tiny MCE plugin
      $tinymce = new JsPlugin_TinyMce();
      $tinymce->setTheme(JsPlugin_TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinymce);

      //Taby - uspořádání
      $jquery = new JsPlugin_JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }
}

?>