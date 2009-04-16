<?php
class ProductsView extends View {
   public function mainView() {
      if($this->getRights()->isWritable()){
         $this->template()->addTpl('addButton.tpl');
         $this->template()->addVar('LINK_TO_ADD_PRODUCT_NAME', _m("Přidat produkt"));

         // editační tlačítka
         $jquery = new JQuery();
         $this->template()->addJsPlugin($jquery);
      }

      $this->template()->addTpl("list.tpl");
/*
      $lists = $this->container()->getData('ARTICLE_LIST_ARRAY');
      foreach ($lists as $key => $article) {
         $out = array();
         preg_match("/(<img[^>]*\/?>)/i", $article[ProductsController::COLUMN_PRODUCT_TEXT], $out);
         if(!empty ($out[1])){
            preg_match('/src="([^"]*)"/i', $out[1], $out);
            $lists[$key]['title_image'] = $out[1];
         } else {
            $lists[$key]['title_image'] = null;
         }
      }
      $this->template()->addVar('ARTICLE_LIST_ARRAY', $lists);
*/
      $this->template()->addVar("PRODUCTS_LIST_NAME", _m("Produkty"));
      $this->template()->addVar("PRODUCTS_MORE_NAME", _m("Více"));
      $this->template()->addCss("style.css");

      //TODO korektní cestu
      $this->template()->addTpl($this->container()->getEplugin('scroll'));
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

      //převedeme všechny lightbox převedeme na lightbox rel
      if((bool)$this->getModule()->getParam(ProductsController::PARAM_FILES, true)){
         $this->template()->addJsPlugin(new LightBox());
         $this->template()->addVar('LIGHTBOX', true);
      }

      $this->template()->addTpl("articleDetail.tpl");
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel($this->container()->getData('ARTICLE_LABEL'));
      $this->template()->setSubTitle($this->container()->getData('ARTICLE_LABEL'), true);

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
   }

   /**
    * Viewer pro přidání produktu
    */
   public function addproductView() {
      $this->template()->addTpl('editProduct.tpl');
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel(_m('Přidání produktu'));
      $this->template()->setSubTitle(_m('Přidání produktu'), true);
      $this->template()->addVar("ADD_PRODUCTS_LABEL",_m('Přidání produktu'));

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
      $this->assignLabels();

      // tiny mce
      $tinymce = new TinyMce();
      $tinymce->setTheme(TinyMce::TINY_THEME_FULL);

      //NOTE soubory
      if((bool)$this->getModule()->getParam(ProductsController::PARAM_FILES, true)){
         $eplFiles = $this->container()->getEplugin('files');
         $this->template()->addTpl($eplFiles->getTpl(), true);
         $eplFiles->assignToTpl($this->template());
         $tinymce->setImagesList($eplFiles->getImagesListLink());
         $tinymce->setLinksList($eplFiles->getLinksListLink());
      }

      // vložení šablony epluginu TinyMCE
      $this->template()->addJsPlugin($tinymce);
      if((bool)$this->getModule()->getParam(ProductsController::PARAM_FILES, true)){
         $this->template()->addJsPlugin(new LightBox());
         $this->template()->addVar('LIGHTBOX', true);
      }
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
      $this->template()->addVar('PRODUCT_LABEL_NAME', _m('Název'));
      $this->template()->addVar('PRODUCT_TEXT_NAME', _m('Text'));
      $this->template()->addVar('PRODUCT_IMAGE_NAME', _m('Titulní obrázek'));

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
      $tinymce->setTheme(TinyMce::TINY_THEME_FULL);

      //NOTE soubory
      if((bool)$this->getModule()->getParam(ProductsController::PARAM_FILES, true)){
         $eplFiles = $this->container()->getEplugin('files');
         $this->template()->addTpl($eplFiles->getTpl(), true);
         $eplFiles->assignToTpl($this->template());
         $tinymce->setImagesList($eplFiles->getImagesListLink());
         $tinymce->setLinksList($eplFiles->getLinksListLink());
      }

      // vložení šablony epluginu TinyMCE
      $this->template()->addJsPlugin($tinymce);
      if((bool)$this->getModule()->getParam(ProductsController::PARAM_FILES, true)){
         $this->template()->addJsPlugin(new LightBox());
         $this->template()->addVar('LIGHTBOX', true);
      }
      
      //Taby - uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }
}

?>
