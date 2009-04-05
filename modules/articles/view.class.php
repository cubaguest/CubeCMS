<?php
class ArticlesView extends View {
   public function mainView() {
      if($this->getRights()->isWritable()){
         $this->template()->addTpl('addButton.tpl');
         $this->template()->addVar('LINK_TO_ADD_ARTICLE_NAME', _m("Přidat článek"));

         // editační tlačítka
         $jquery = new JQuery();
         $this->template()->addJsPlugin($jquery);
      }

      $this->template()->addTpl("list.tpl");

      $this->template()->addVar("ARTICLES_LIST_NAME", _m("Články"));
      $this->template()->addVar("ARTICLES_MORE_NAME", _m("Více"));
      $this->template()->addCss("style.css");

      //TODO korektní cestu
      $this->template()->addTpl($this->container()->getEplugin('scroll')->getTpl(), true);
      $this->container()->getEplugin('scroll')->assignToTpl($this->template());
   }

   public function showView(){
      if($this->getRights()->isWritable()){
         $this->template()->addTpl('editButtons.tpl');
         $this->template()->addVar('LINK_TO_ADD_ARTICLE_NAME', _m("Přidat článek"));

         $this->template()->addVar('LINK_TO_EDIT_ARTICLE_NAME', _m("Upravit"));

         $this->template()->addVar('LINK_TO_DELETE_ARTICLE_NAME', _m("Smazat"));
         $this->template()->addVar('DELETE_CONFIRM_MESSAGE', _m("Smazat článek"));

         //			JSPlugin pro potvrzení mazání
         $submitForm = new SubmitForm();
         $this->template()->addJsPlugin($submitForm);

         // editační tlačítka
         $jquery = new JQuery();
         $this->template()->addJsPlugin($jquery);
      }

      $this->template()->addTpl("articleDetail.tpl");
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel($this->container()->getData('ARTICLE_LABEL'));
      $this->template()->setSubTitle($this->container()->getData('ARTICLE_LABEL'), true);

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
   }

   /**
    * Viewer pro přidání článku
    */
   public function addarticleView() {
      $this->template()->addTpl('editArticle.tpl');
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel(_m('Přidání článku'));
      $this->template()->setSubTitle(_m('Přidání článku'), true);
      $this->template()->addVar("ADD_ARTICLE_LABEL",_m('Přidání článku'));

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
      $this->assignLabels();

      // tiny mce
      $tinymce = new TinyMce();
      if($this->getModule()->getParam(ArticlesController::PARAM_EDITOR_THEME) == 'simple'){
         $tinymce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
         if((bool)$this->getModule()->getParam(ArticlesController::PARAM_IMAGES, true)){
            $tinymce->addImagesIcon();
         }
      } else if($this->getModule()->getParam(ArticlesController::PARAM_EDITOR_THEME, 'advanced') == 'advanced'){
         $tinymce->setTheme(TinyMce::TINY_THEME_FULL);
      }

      //NOTE soubory
      if((bool)$this->getModule()->getParam(ArticlesController::PARAM_FILES, true)){
         $this->template()->addTpl($this->container()->getEplugin('files')->getTpl(), true);
         $this->container()->getEplugin('files')->assignToTpl($this->template());
      }

		//NOTE obrázky
      if((bool)$this->getModule()->getParam(ArticlesController::PARAM_IMAGES, true)){
         $eplImages = $this->container()->getEplugin('images');
         $this->template()->addTpl($eplImages->getTpl(), true);
         $eplImages->assignToTpl($this->template());
         $tinymce->setImagesList($eplImages->getImagesListLink(UserImagesEplugin::FILE_IMAGES_FORMAT_TINYMCE));
      }
      
      // vložení šablony epluginu TinyMCE
      $this->template()->addJsPlugin($tinymce);

      //Tabulkové uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
   }

   /**
    * Metoda přiřadí popisky do šablony
    */
   private function assignLabels() {
      $this->template()->addVar('ARTICLE_LABEL_NAME', _m('název'));
      $this->template()->addVar('ARTICLE_TEXT_NAME', _m('Text'));

      $this->template()->addVar('BUTTON_RESET', _m('Obnovit'));
      $this->template()->addVar('BUTTON_SEND', _m('Uložit'));
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editArticleView() {
      $this->template()->addTpl('editArticle.tpl');
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel(_m("Úprava článku").' - '.$this->container()->getData('ARTICLE_NAME'));
      $this->template()->setSubTitle(_m("Úprava článku").' - '.$this->container()->getData('ARTICLE_NAME'), true);
      $this->template()->addVar("ADD_NEWS_LABEL",_m("Úprava článku").' - '.$this->container()->getData('ARTICLE_NAME'));

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na článek'));
      $this->assignLabels();

      // tiny mce
      $tinymce = new TinyMce();
      if($this->getModule()->getParam(ArticlesController::PARAM_EDITOR_THEME) == 'simple'){
         $tinymce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
         if((bool)$this->getModule()->getParam(ArticlesController::PARAM_IMAGES, true)){
            $tinymce->addImagesIcon();
         }
      } else if($this->getModule()->getParam(ArticlesController::PARAM_EDITOR_THEME, 'advanced') == 'advanced'){
         $tinymce->setTheme(TinyMce::TINY_THEME_FULL);
      }

      //NOTE soubory
      if((bool)$this->getModule()->getParam(ArticlesController::PARAM_FILES, true)){
         $this->template()->addTpl($this->container()->getEplugin('files')->getTpl(), true);
         $this->container()->getEplugin('files')->assignToTpl($this->template());
      }

		//NOTE obrázky
      if((bool)$this->getModule()->getParam(ArticlesController::PARAM_IMAGES, true)){
         $eplImages = $this->container()->getEplugin('images');
         $this->template()->addTpl($eplImages->getTpl(), true);
         $eplImages->assignToTpl($this->template());
         $tinymce->setImagesList($eplImages->getImagesListLink(UserImagesEplugin::FILE_IMAGES_FORMAT_TINYMCE));
      }

      // vložení šablony epluginu TinyMCE
      $this->template()->addJsPlugin($tinymce);

      //Taby - uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }
}

?>