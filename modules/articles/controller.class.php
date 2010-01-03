<?php
class Articles_Controller extends Controller {
/**
 * Kontroler pro zobrazení novinek
 */
   public function mainController() {
   //		Kontrola práv
      $this->checkReadableRights();

      // načtení článků
      $artModel = new Articles_Model_List();

      $scrollComponent = new Component_Scroll();
      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS,
         $artModel->getCountArticles($this->category()->getId()));
      
      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
         $this->category()->getModule()->getParam('scroll', 2));

      $scrollComponent->runCtrlPart();

      if($this->rights()->isWritable()){
         $onlyPublic = false;
      } else {
         $onlyPublic = true;
      }

      $articles = $artModel->getList($this->category()->getId(),$onlyPublic,
         $scrollComponent->getConfig(Component_Scroll::CONFIG_START_RECORD),
         $scrollComponent->getConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE));


      $this->view()->template()->scrollComp = $scrollComponent;
      $this->view()->template()->articles = $articles;
   }

   /**
    * Kontroler pro zobrazení novinek
    */
   public function topController() {
   //		Kontrola práv
      $this->checkReadableRights();

      // načtení článků
      $artModel = new Articles_Model_List();
      $scrollComponent = new Component_Scroll();
      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS,
         $artModel->getCountArticles($this->category()->getId()));

      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
         $this->category()->getModule()->getParam('article_scroll', 2));

      $scrollComponent->runCtrlPart();

      if($this->rights()->isWritable()){
         $onlyPublic = false;
      } else {
         $onlyPublic = true;
      }

      $articles = $artModel->getListTop($this->category()->getId(),$onlyPublic,
         $scrollComponent->getConfig(Component_Scroll::CONFIG_START_RECORD),
         $scrollComponent->getConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE));
      
      $this->view()->template()->articles = $articles;
      $this->view()->template()->scrollComp = $scrollComponent;
      $this->view()->template()->top = true;
      $this->view()->template()->addTplFile("list.phtml");
      $this->view()->template()->addCssFile("style.css");
   }

   public function archiveController() {
      $this->checkReadableRights();

      $m = new Articles_Model_List();
      $this->view()->template()->articles = $m->getListAll($this->category()->getId());
   }

   public function showController() {
      $this->checkReadableRights();

      $artM = new Articles_Model_Detail();
      $article = $artM->getArticle($this->getRequest('urlkey'));
      if($article == false){
         AppCore::setErrorPage(true);
         return false;
      }
      $artM->addShowCount($this->getRequest('urlkey'));


      $deleteForm = new Form('article_');

      $feId = new Form_Element_Hidden('id');
      $feId->addValidation(new Form_Validator_IsNumber());
      $deleteForm->addElement($feId);

      $feSubmit = new Form_Element_Submit('delete');
      $deleteForm->addElement($feSubmit);

      if($this->category()->getRights()->isWritable() AND $deleteForm->isValid()){
         if($artM->deleteArticle($deleteForm->id->getValues())){
            $this->infoMsg()->addMessage($this->_('Článek byl smazán'));
            $this->link()->route()->rmParam()->reload();
         } else {
            $this->errMsg()->addMessage($this->_('Článek se nepodařilo smazat'));
         }
      }

      // komponenta pro vypsání odkazů na sdílení
      $shares = new Component_Share();
      $shares->setConfig('url', (string)$this->link());
      $shares->setConfig('title', $article->{Articles_Model_Detail::COLUMN_NAME});

      $this->view()->template()->shares=$shares;
      $this->view()->template()->article=$article;
      $this->view()->template()->addTplFile("detail.phtml");
      $this->view()->template()->addCssFile("style.css");
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addController() {
      $this->checkWritebleRights();
      $addForm = $this->createForm();

      if($addForm->isValid()) {
      // generování url klíče
         $urlkey = $addForm->urlkey->getValues();
         $names = $addForm->name->getValues();
         foreach ($urlkey as $lang => $variable) {
            if($variable == null AND $names[$lang] == null) {
               $urlkey[$lang] = null;
            } else if($variable == null) {
               $urlkey[$lang] = vve_cr_url_key($names[$lang]);
            } else {
               $urlkey[$lang] = vve_cr_url_key($variable);
            }
         }

         $artModel = new Articles_Model_Detail();
         $count = $artModel->saveArticle($names, $addForm->text->getValues(), $urlkey,
             $this->category()->getId(), $this->auth()->getUserId(),$editForm->public->getValues());
         if($count != 0) {
            $art = $artModel->getArticleById($count);

            $this->infoMsg()->addMessage($this->_('Uloženo'));
            $this->link()->route('detail', array('urlkey' => $art->{Articles_Model_Detail::COLUMN_URLKEY}))->reload();
         } else {
            $this->errMsg()->addMessage($this->_('Text se nepodařilo uložit'));
         }
      }

      $this->view()->template()->addForm = $addForm;
   }

   /**
    * controller pro úpravu novinky
    */
   public function editController() {
      $this->checkWritebleRights();

      $editForm = $this->createForm();
      // doplnění id
      $iIdElem = new Form_Element_Hidden('art_id');
      $iIdElem->addValidation(new Form_Validator_IsNumber());
      $editForm->addElement($iIdElem);

      // načtení dat
      $artModel = new Articles_Model_Detail();
      $article = $artModel->getArticle($this->getRequest('urlkey'));

      $editForm->name->setValues($article->{Articles_Model_Detail::COLUMN_NAME});
      $editForm->text->setValues($article->{Articles_Model_Detail::COLUMN_TEXT});
      $editForm->urlkey->setValues($article->{Articles_Model_Detail::COLUMN_URLKEY});
      $editForm->art_id->setValues($article->{Articles_Model_Detail::COLUMN_ID});
      $editForm->public->setValues($article->{Articles_Model_Detail::COLUMN_PUBLIC});

      if($editForm->isValid()) {
      // generování url klíče
         $urlkey = $editForm->urlkey->getValues();
         $names = $editForm->name->getValues();
         foreach ($urlkey as $lang => $variable) {
            if($variable == null AND $names[$lang] == null) {
               $urlkey[$lang] = null;
            } else if($variable == null) {
               $urlkey[$lang] = vve_cr_url_key($names[$lang]);
            } else {
               $urlkey[$lang] = vve_cr_url_key($variable);
            }
         }

         if($artModel->saveArticle($names, $editForm->text->getValues(), $urlkey,
         $this->category()->getId(), $this->auth()->getUserId(),$editForm->public->getValues(),
         $editForm->art_id->getValues())) {
            // nahrání nové verze článku (kvůli url klíči)
            $article = $artModel->getArticleById($editForm->art_id->getValues());

            $this->infoMsg()->addMessage($this->_('Uloženo'));
            $this->link()->route('detail',array('urlkey' => $article->{Articles_Model_Detail::COLUMN_URLKEY}))->reload();
         } else {
            $this->errMsg()->addMessage($this->_('Text se nepodařilo uložit'));
         }
      }

      $this->view()->template()->addForm = $editForm;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   public function createForm() {
      $form = new Form('ardicle_');

      $iName = new Form_Element_Text('name', $this->_('Nadpis'));
      $iName->setLangs();
      $iName->addValidation(New Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $form->addElement($iName);

      $iText = new Form_Element_TextArea('text', $this->_('Text'));
      $iText->setLangs();
      $iText->addValidation(New Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $form->addElement($iText);

      $iUrlKey = new Form_Element_Text('urlkey', $this->_('Url klíč'));
      $iUrlKey->setLangs();
      $iUrlKey->setSubLabel($this->_('Pokud není klíč zadán, je generován automaticky'));
      $form->addElement($iUrlKey);

      $iPub = new Form_Element_Checkbox('public', $this->_('Veřejný'));
      $iPub->setSubLabel($this->_('Veřejný - viditelný všem návštěvníkům'));
      $iPub->setValues(true);
      $form->addElement($iPub);

      $iSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($iSubmit);

      return $form;
   }

   /**
    * Smazání článků při odstranění kategorie
    * @param int $idCat
    */
   public static function clearOnRemove($idCat) {
      $model = new Articles_Model_Detail();
      $model->deleteArticleByCat($idCat);
   }
}
?>