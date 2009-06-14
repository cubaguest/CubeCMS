<?php
class News_View extends View {
   public function mainView() {
      if($this->rights()->isWritable()){
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('add_article', $this->_m("Přidat"),
            $this->link()->action($this->sys()->action()->addNews()),
            $this->_m("Přidat novinku"), "text_add.png");
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
         $toolbox->addTool('edit_news', $this->_m("Upravit"),
            $this->link()->action($this->sys()->action()->editNews()),
            $this->_m("Upravit novinku"), "text_edit.png")
         ->addTool('news_delete', $this->_m("Smazat"), $this->link(),
            $this->_m("Smazat novinku"), "remove.png", "news_id",
            $this->template()->new[News_Model_Detail::COLUMN_NEWS_ID_NEW],
            $this->_m("Opravdu smazat novinku")."?");
         $this->template()->toolbox = $toolbox;
      }

      $this->template()->addTplFile("newDetail.phtml");
      $this->template()->addCssFile("style.css");
   }

   /**
    * Viewer pro přidání novinky
    */
   public function addNewsView() {
      $this->template()->addTplFile('editNews.phtml');
      $this->template()->addCssFile("style.css");
      $this->template()->setActionTitle($this->_m("přidání článku"));

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
      $this->template()->new = $newsModel->getNewsDetailAllLangs($this->sys()->article());

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