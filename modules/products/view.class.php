<?php
class Products_View extends View {
   const SHOW_PRODUCTS_NUM = 20;

   public function mainView() {
      if($this->rights()->isWritable()) {
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('add_product', $this->_m("Přidat"),
             $this->link()->action($this->sys()->action()->addProduct()),
             $this->_m("Přidat produkt"), "text_add.png");
         $this->template()->toolbox = $toolbox;
      }

      $this->template()->addTplFile("list.phtml");
      $this->template()->addCssFile("style.css");

      //		Vytvoření modelu
      $productModel = new Products_Model_List($this->sys());
      //		Scrolovátka
      $scroll = new Eplugin_Scroll($this->sys());
      $scroll->setCountRecordsOnPage(self::SHOW_PRODUCTS_NUM);
      $scroll->setCountAllRecords($productModel->getCountProducts());
      //      var_dump($scroll);
      //		Vybrání článků
      $list = $productModel->getSelectedListProducts($scroll->getStartRecord(), $scroll->getCountRecords());
      $this->template()->EPLscroll = $scroll;

      foreach ($list as &$products) {
         $out = array();
         preg_match("/(<img[^>]*\/?>)/i", $products[Products_Model_Detail::COLUMN_PRODUCT_TEXT], $out);
         if(!empty ($out[1])) {
            preg_match('/src="([^"]*)"/i', $out[1], $out);
            $products['title_image'] = $out[1];
         } else {
            $products['title_image'] = null;
         }
      }
      $this->template()->productsArray = $list;
   }

   public function showView() {
      if($this->rights()->isWritable()) {
      // editační tlačítka
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_product', $this->_m("Upravit"),
             $this->link()->action($this->sys()->action()->editProduct()),
             $this->_m("Upravit prrodukt"), "text_edit.png")
             ->addTool('product_delete', $this->_m("Smazat"), $this->link(),
             $this->_m("Smazat produkt"), "remove.png", "product_id",
             $this->template()->product[Products_Model_Detail::COLUMN_PRODUCT_ID],
             $this->_m("Opravdu smazat produkt")."?");
         $this->template()->toolbox = $toolbox;
      }

      //převedeme všechny lightbox převedeme na lightbox rel
      $this->template()->addJsPlugin(new JsPlugin_LightBox());
      $this->template()->lightBox = true;

      $this->template()->addTplFile("productDetail.phtml");
      $this->template()->addCssFile("style.css");
      $this->template()->setArticleTitle($this->template()->product[Products_Model_Detail::COLUMN_PRODUCT_LABEL]);
   }

   /**
    * Viewer pro přidání produktu
    */
   public function addProductView() {
      $this->template()->addTplFile('editProduct.phtml');
      $this->template()->addCssFile("style.css");
      $this->template()->setActionTitle($this->_m("přidání produktu"));

      // tiny mce
      $tinymce = new JsPlugin_TinyMce();
      $tinymce->setTheme(JsPlugin_TinyMce::TINY_THEME_FULL);

      //NOTE soubory
      $tinymce->setImagesList($this->EPLfiles->getImagesListLink());
      $tinymce->setLinksList($this->EPLfiles->getLinksListLink());

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
   public function editproductView() {
      $this->template()->addTplFile('editProduct.phtml');
      $this->template()->addCssFile("style.css");

      $model = new Products_Model_Detail($this->sys());
      $this->template()->product = $model->getProductDetailAllLangs($this->sys()->article()->getArticle());
      $this->template()->setArticleTitle($this->template()
         ->article[Products_Model_Detail::COLUMN_PRODUCT_LABEL][Locale::getLang()]);
      $this->template()->setActionTitle($this->_m("úprava produktu"));

      // tiny mce
      $tinymce = new JsPlugin_TinyMce();
      $tinymce->setTheme(JsPlugin_TinyMce::TINY_THEME_FULL);

      //NOTE soubory
      $tinymce->setImagesList($this->EPLfiles->getImagesListLink());
      $tinymce->setLinksList($this->EPLfiles->getLinksListLink());

      // vložení šablony epluginu TinyMCE
      $this->template()->addJsPlugin($tinymce);

      //Tabulkové uspořádání
      $jquery = new JsPlugin_JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }
}

?>
