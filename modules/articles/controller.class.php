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
              $this->category()->getModule()->getParam('scroll', 5));

      $scrollComponent->runCtrlPart();

      $articles = $artModel->getList($this->category()->getId(),
              $scrollComponent->getConfig(Component_Scroll::CONFIG_START_RECORD),
              $scrollComponent->getConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE),!$this->rights()->isWritable());

      $this->view()->scrollComp = $scrollComponent;
      $this->view()->articles = $articles;
      // odkaz zpět
      $this->link()->backInit();
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

      $articles = $artModel->getListTop($this->category()->getId(),
              $scrollComponent->getConfig(Component_Scroll::CONFIG_START_RECORD),
              $scrollComponent->getConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE),!$this->rights()->isWritable());

      $this->view()->articles = $articles;
      $this->view()->scrollComp = $scrollComponent;
      $this->view()->top = true;
      // odkaz zpět
      $this->link()->backInit();
   }

   public function archiveController() {
      $this->checkReadableRights();
      $m = new Articles_Model_List();
      $this->view()->articles = $m->getListAll($this->category()->getId());
      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 0);
   }

   public function showController() {
      $this->checkReadableRights();

      $artM = new Articles_Model_Detail();
      $article = $artM->getArticle($this->getRequest('urlkey'));
      if($article == false) {
         AppCore::setErrorPage(true);
         return false;
      }
      $this->view()->article=$article;
      
      $artM->addShowCount($this->getRequest('urlkey'));

      if($this->category()->getRights()->isWritable()
              OR $this->category()->getRights()->isControll()) {
         $deleteForm = new Form('article_');

         $feId = new Form_Element_Hidden('id');
         $feId->addValidation(new Form_Validator_IsNumber());
         $deleteForm->addElement($feId);

         $feSubmit = new Form_Element_Submit('delete');
         $deleteForm->addElement($feSubmit);

         if($deleteForm->isValid()) {
            $this->deleteArticle($deleteForm->id->getValues());
            $this->link()->route()->rmParam()->reload();
         }
      }

      // komponenta pro vypsání odkazů na sdílení
      $shares = new Component_Share();
      $shares->setConfig('url', (string)$this->link()->rmParam());
      $shares->setConfig('title', $article->{Articles_Model_Detail::COLUMN_NAME});

      $this->view()->shares=$shares;
      
      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 1);
   }

   /**
    * Metoda smaže článek z dat
    * @param int $idArticle
    */
   protected function deleteArticle($idArticle) {
      $artM = new Articles_Model_Detail();
      $artM->deleteArticle($idArticle);
      $this->infoMsg()->addMessage($this->_('Článek byl smazán'));
   }

   public function showPdfController() {
      $this->checkReadableRights();

      $this->view()->urlkey = $this->getRequest('urlkey');
   }

   /**
    * Kontroler pro přidání novinky
    */
   public function addController() {
      $this->checkWritebleRights();
      $addForm = $this->createForm();

      if($addForm->isValid()) {
         // generování url klíče
         $urlkeys = $addForm->urlkey->getValues();
         $names = $addForm->name->getValues();
         $urlkeys = $this->createUrlKey($urlkeys, $names);
         $model = new Articles_Model_Detail();
         $lasId = $this->saveArticle($names, $urlkeys, $addForm);
         $art = $model->getArticleById($lasId);
         $this->infoMsg()->addMessage($this->_('Uloženo'));
         $this->link()->route($this->getOption('actionAfterAdd', 'detail'),
                 array('urlkey' => $art->{Articles_Model_Detail::COLUMN_URLKEY}))->reload();
      }

      $this->view()->form = $addForm;
      $this->view()->edit = false;
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
      $model = new Articles_Model_Detail();
      $article = $model->getArticle($this->getRequest('urlkey'));

      $editForm->name->setValues($article->{Articles_Model_Detail::COLUMN_NAME});
      $editForm->text->setValues($article->{Articles_Model_Detail::COLUMN_TEXT});
      $editForm->urlkey->setValues($article->{Articles_Model_Detail::COLUMN_URLKEY});
      $editForm->art_id->setValues($article->{Articles_Model_Detail::COLUMN_ID});
      $editForm->public->setValues($article->{Articles_Model_Detail::COLUMN_PUBLIC});

      if($editForm->isValid()) {
         // generování url klíče
         $urlkeys = $editForm->urlkey->getValues();
         $names = $editForm->name->getValues();
         $urlkeys = $this->createUrlKey($urlkeys, $names);

         $this->saveArticle($names, $urlkeys, $editForm, $article);
         // nahrání nové verze článku (kvůli url klíči)
         $article = $model->getArticleById($editForm->art_id->getValues());
         $this->link()->route('detail',array('urlkey' => $article->{Articles_Model_Detail::COLUMN_URLKEY}))->reload();
      }

      $this->view()->form = $editForm;
      $this->view()->edit = true;
   }

   /**
    * Uložení samotného článku
    * @param <type> $names
    * @param <type> $urlkeys
    * @param <type> $form
    */
   protected function saveArticle($names, $urlkeys, $form, $article=null) {
      if($form->art_id == null) $idart = null;
      else $idart = $form->art_id->getValues();
      $model = new Articles_Model_Detail();
      $lastId = $model->saveArticle($names, $form->text->getValues(), $urlkeys,
              $this->category()->getId(), Auth::getUserId(),$form->public->getValues(),$idart);

      $this->infoMsg()->addMessage($this->_('Uloženo'));
      return $lastId;
   }

   /**
    * Metoda vygeneruje url klíče
    * @param <type> $urlkeys
    * @param <type> $names
    * @return <type>
    */
   protected function createUrlKey($urlkeys, $names) {
      foreach ($urlkeys as $lang => $variable) {
         if($variable == null AND $names[$lang] == null) {
            $urlkeys[$lang] = null;
         } else if($variable == null) {
            $urlkeys[$lang] = vve_cr_url_key($names[$lang]);
         } else {
            $urlkeys[$lang] = vve_cr_url_key($variable);
         }
      }
      return $urlkeys;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm() {
      $form = new Form('ardicle_');

      $iName = new Form_Element_Text('name', $this->_('Nadpis'));
      $iName->setLangs();
      $iName->addValidation(New Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      $form->addElement($iName);

      $iText = new Form_Element_TextArea('text', $this->_('Text'));
      $iText->setLangs();
      if($this->getOption('textEmpty', false) == false) {
         $iText->addValidation(New Form_Validator_NotEmpty(null, Locale::getDefaultLang(true)));
      }
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
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {
      $model = new Articles_Model_Detail();
      $model->deleteArticleByCat($category->getId());
   }

   // RSS
   public function exportController() {
      $this->checkReadableRights();

      $this->view()->type = $this->getRequest('type', 'rss');
   }
}
?>