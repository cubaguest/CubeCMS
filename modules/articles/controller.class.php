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
         $this->category()->getModule()->getParam('article_scroll', 2));

      $scrollComponent->runCtrlPart();
  
      $articles = $artModel->getList($this->category()->getId(),
         $scrollComponent->getConfig(Component_Scroll::CONFIG_START_RECORD),
         $scrollComponent->getConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE));


      $this->view()->template()->scrollComp = $scrollComponent;
      $this->view()->template()->articles = $articles;
      $this->view()->template()->addTplFile("list.phtml");
      $this->view()->template()->addCssFile("style.css");
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
         $scrollComponent->getConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE));
      
      $this->view()->template()->articles = $articles;
      $this->view()->template()->scrollComp = $scrollComponent;
      $this->view()->template()->top = true;
      $this->view()->template()->addTplFile("list.phtml");
      $this->view()->template()->addCssFile("style.css");
   }

   public function showController() {
      $this->checkReadableRights();

      $artM = new Articles_Model_Detail();
      $artM->addShowCount($this->getRequest('articlekey'));
      $article = $artM->getArticle($this->getRequest('articlekey'));
      
      if(empty ($article)){
         AppCore::setErrorPage(true);
         return false;
      }


      $deleteForm = new Form('article_');

      $feId = new Form_Element_Hidden('id');
      $feId->addValidation(new Form_Validator_IsNumber());
      $deleteForm->addElement($feId);

      $feSubmit = new Form_Element_Submit('delete');
      $deleteForm->addElement($feSubmit);

      if($this->category()->getRights()->isWritable() AND $deleteForm->isValid()){
         $artM = new Articles_Model_Detail();
         if($artM->deleteArticle($deleteForm->id->getValues())){
            $this->infoMsg()->addMessage($this->_('Článek byl smazán'));
            $this->link()->route()->rmParam()->reload();
         } else {
            $this->errMsg()->addMessage($this->_('Článek se nepodařilo smazat'));
         }
      }

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
             $this->category()->getId(), $this->rights()->getAuth()->getUserId());

         if($count != 0) {
            $this->infoMsg()->addMessage($this->_('Článek byl uložen'));
            $this->link()->route()->reload();
         } else {
            $this->errMsg()->addMessage($this->_('Článek se nepodařilo uložit'));
         }
      }

      $this->view()->template()->addForm = $addForm;
      $this->view()->template()->addTplFile("edit.phtml");
      $this->view()->template()->addCssFile("style.css");
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
      $article = $artModel->getArticle($this->getRequest('articlekey'));

      $editForm->name->setValues($article->{Articles_Model_Detail::COLUMN_NAME});
      $editForm->text->setValues($article->{Articles_Model_Detail::COLUMN_TEXT});
      $editForm->urlkey->setValues($article->{Articles_Model_Detail::COLUMN_URLKEY});
      $editForm->art_id->setValues($article->{Articles_Model_Detail::COLUMN_ID});

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
         $this->category()->getId(), $this->rights()->getAuth()->getUserId(),
         $editForm->art_id->getValues())) {
            // nahrání nové verze článku (kvůli url klíči)
            $article = $artModel->getArticleById($editForm->art_id->getValues());

            $this->infoMsg()->addMessage($this->_('Článek byl uložen'));
            $this->link()->route('detail',array('articlekey' => $article->{Articles_Model_Detail::COLUMN_URLKEY}))->reload();
         } else {
            $this->errMsg()->addMessage($this->_('Článek se nepodařilo uložit'));
         }
      }

      $this->view()->template()->addForm = $editForm;
      $this->view()->template()->addTplFile("edit.phtml");
      $this->view()->template()->addCssFile("style.css");
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

      $iSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($iSubmit);

      return $form;
   }
}
?>