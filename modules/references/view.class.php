<?php
class References_View extends View {
   /**
    * Název parametru s počtem článků na stránce
    */
   const PARAM_NUM_REFERENCES_ON_PAGE = 'scroll';

   public function mainView() {
      if($this->rights()->isWritable()){
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('add_article', $this->_("Přidat"),
            $this->link()->action($this->sys()->action()->addReference()),
            $this->_("Přidat referenci"), "text_add.png");
         $this->template()->toolbox = $toolbox;
      }

      $this->template()->addTplFile("list.phtml");
      $this->template()->addCssFile("style.css");
      
      //		Vytvoření modelu
      $refModel = new References_Model_List($this->sys());
      //		Scrolovátka
      $scroll = new Eplugin_Scroll($this->sys());
      $scroll->setCountRecordsOnPage($this->module()->getParam(self::PARAM_NUM_REFERENCES_ON_PAGE, 10));
      $scroll->setCountAllRecords($refModel->getCountReferences());
      //		Vybrání článků
      $this->template()->referencesArray = $refModel->getSelectedListReferences($scroll->getStartRecord(), $scroll->getCountRecords());
      $this->template()->EPLscroll = $scroll;
   }

   public function showView(){
      if($this->rights()->isWritable()){
         // editační tlačítka
         $toolbox = new Template_Toolbox();
         $toolbox->addTool('edit_reference_text', $this->_m("Upravit text"),
            $this->link()->action($this->sys()->action()->editReferenceText()),
            $this->_m("Upravit text reference"), "text_edit.png")
         ->addTool('edit_reference_photos', $this->_m("Upravit fotky"),
            $this->link()->action($this->sys()->action()->editReferencePhotos()),
            $this->_m("Upravit fotky reference"), "image.png")
         ->addTool('reference_delete', $this->_m("Smazat"), $this->link(),
            $this->_m("Smazat článek"), "remove.png", "reference_id",
            $this->template()->reference[References_Model_Detail::COLUMN_ID],
            $this->_m("Opravdu smazat referenci")."?");
         $this->template()->toolbox = $toolbox;
      }

      $photosM = new References_Model_Photos($this->sys());
      $this->template()->images = $photosM->getList($this->sys()->article());

      //převedeme všechny lightbox převedeme na lightbox rel
      $this->template()->addJsPlugin(new JsPlugin_LightBox());

      $this->template()->addTplFile("referenceDetail.phtml");
      $this->template()->addCssFile("style.css");
      $this->template()->setArticleTitle($this->template()->reference[References_Model_Detail::COLUMN_NAME]);
   }

   /**
    * Viewer pro přidání reference
    */
   public function addReferenceView() {
      $this->template()->addTplFile('editReference.phtml');
      $this->template()->addCssFile("style.css");
      $this->template()->setActionTitle($this->_m("přidání článku"));

      // tiny mce
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
   public function editReferenceTextView() {
      $this->template()->addTplFile('editReference.phtml');
      $this->template()->addCssFile("style.css");

      $model = new References_Model_Detail($this->sys());
      $this->template()->reference = $model->getReferenceDetailAllLangs($this->sys()->article()->getArticle());
      $this->template()->setArticleTitle($this->template()
         ->reference[References_Model_Detail::COLUMN_NAME][Locale::getLang()]);
      $this->template()->setActionTitle($this->_("úprava reference"));

      // tiny mce
      $tinymce = new JsPlugin_TinyMce();
      $tinymce->setTheme(JsPlugin_TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinymce);

      //Tabulkové uspořádání
      $jquery = new JsPlugin_JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }

   /**
    * Viewver pro úpravu fotek
    */
   public function editReferencePhotosView() {
      $this->template()->addTplFile('editPhotos.phtml');
      $this->template()->addCssFile("style.css");

      $model = new References_Model_Photos($this->sys());
      $this->template()->images = $model->getListAllLangs($this->sys()->article());

      
      $this->template()->ajaxLink = new Ajax_Link($this->sys()->module());

      $imageTpl = new Template($this->sys());
      $imageTpl->addTplFile("imageDetail.phtml");
      $this->template()->imageTpl = $imageTpl;

      //Tabulkové uspořádání
      $jquery = new JsPlugin_JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
      $this->template()->addJsPlugin(new JsPlugin_LightBox());
   }

   public function addPhotoAjaxView() {
      $this->template()->addTplFile('photoAjax.phtml');
      $this->template()->image = false;
      $this->template()->imageTpl =
'test
test
asdsa';
      if($this->template()->lastId){
         $model = new References_Model_Photos($this->sys());
         $imageTpl = new Template($this->sys());
         $imageTpl->addTplFile("imageDetail.phtml");
         $imageTpl->image = $model->getPhotoAllLangs($this->template()->lastId);
         $this->template()->imageTpl = $imageTpl;
      }
   }

   public function deletePhotoAjaxView() {

   }

   public function savePhotoLabelAjaxView() {

   }

}

?>
