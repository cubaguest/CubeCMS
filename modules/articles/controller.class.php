<?php
class Articles_Controller extends Controller {
   const DEFAULT_ARTICLES_IN_PAGE = 5;
   const DEFAULT_CLOSED_COMMENTS_DAYS = 0;
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
              $this->category()->getParam('scroll', self::DEFAULT_ARTICLES_IN_PAGE));

      $articles = $artModel->getList($this->category()->getId(),
              $scrollComponent->getStartRecord(),
              $scrollComponent->getRecordsOnPage(),!$this->rights()->isWritable());

      $this->view()->scrollComp = $scrollComponent;
      $this->view()->articles = $articles;
      // odkaz zpět
      $this->link()->backInit();
   }

   public function contentController(){
      //		Kontrola práv
      $this->checkReadableRights();

      // načtení článků
      $artModel = new Articles_Model_List();
      $articles = $artModel->getList($this->category()->getId(),0,100,!$this->rights()->isWritable());
      $this->view()->articles = $articles;
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
              $this->category()->getParam('scroll', self::DEFAULT_ARTICLES_IN_PAGE));

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
      $articlesAll = $m->getListAll($this->category()->getId());
      $articles = array();

      while ($row = $articlesAll->fetch()){
         $date = new DateTime($row->{Articles_Model_Detail::COLUMN_ADD_TIME});
         $year = $date->format("Y");
         if(!isset ($articles[$year])){
            $articles[$year] = array();
         }
         array_push($articles[$year], $row);
      }

      $this->view()->articles = $articles;
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

      // diskuse
      if($this->category()->getParam('discussion_allow', false) == true){
         $compComments = new Component_Comments();
         $compComments->setConfig(Component_Comments::PARAM_ID_ARTICLE,
                 $article->{Articles_Model_Detail::COLUMN_ID});
         $compComments->setConfig(Component_Comments::PARAM_ID_CATEGORY,
                 $this->category()->getId());
         $compComments->setConfig(Component_Comments::PARAM_ADMIN,
                 $this->category()->getRights()->isControll());
         $compComments->setConfig(Component_Comments::PARAM_NEW_ARE_PUBLIC, 
                 !$this->category()->getParam('discussion_not_public', false));
         // uzavření diskuze
         if($this->category()->getParam('discussion_closed', self::DEFAULT_CLOSED_COMMENTS_DAYS) != 0){
            $timeAdd = new DateTime($article->{Articles_Model_Detail::COLUMN_ADD_TIME});
            $t = $timeAdd->format('U')
                    +$this->category()->getParam('discussion_closed', 0)*24*60*60;
            if($t < time()){
               $compComments->setConfig(Component_Comments::PARAM_CLOSED, true);
            }
         }
         $this->view()->comments = $compComments;
      }

      // private zone
      $this->view()->allowPrivate = false;
      if($this->category()->getParam('allow_private_zone', false) == true
              AND (Auth::getGroupName() == 'admin' OR
              $artM->isPrivateUser(Auth::getUserId(), $article->{Articles_Model_Detail::COLUMN_ID}))){
         $this->view()->allowPrivate = true;
      }
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
         $lasId = $this->saveArticle($names, $urlkeys, $addForm);
         $model = new Articles_Model_Detail();
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
      if($editForm->haveElement('textPrivate')){
         $editForm->textPrivate->setValues($article->{Articles_Model_Detail::COLUMN_TEXT_PRIVATE});
         // přidání uživatelů
         $users = $model->getArticlePrivateUsers($article->{Articles_Model_Detail::COLUMN_ID});
         $selected = array();
         foreach ($users as $user) {
            array_push($selected, $user->{Articles_Model_Detail::COLUMN_A_H_U_ID_USER});
         }
         $editForm->privateUsers->setValues($selected);
         unset ($selected);
         unset ($users);

      }
      $editForm->metaKeywords->setValues($article->{Articles_Model_Detail::COLUMN_KEYWORDS});
      $editForm->metaDesc->setValues($article->{Articles_Model_Detail::COLUMN_DESCRIPTION});
      $editForm->annotation->setValues($article->{Articles_Model_Detail::COLUMN_ANNOTATION});
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
   protected function saveArticle($names, $urlkeys,Form $form, $article=null) {
      if($form->art_id == null) $idart = null;
      else $idart = $form->art_id->getValues();

      $textPrivate = null;
      $idPrivateUsers = array();
      if($form->haveElement('textPrivate') == true){
         $textPrivate = $form->textPrivate->getValues();
         $idPrivateUsers = $form->privateUsers->getValues();
      }

      $model = new Articles_Model_Detail();
      $lastId = $model->saveArticle($names, $form->text->getValues(), $form->annotation->getValues(), $urlkeys,
              $form->metaKeywords->getValues(), $form->metaDesc->getValues(),
              $this->category()->getId(), Auth::getUserId(),$form->public->getValues(),$idart,
              $textPrivate, $idPrivateUsers);

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

      $fGrpTexts = $form->addGroup('texts', $this->_('Texty'));

      $iName = new Form_Element_Text('name', $this->_('Nadpis'));
      $iName->setLangs();
      $iName->addValidation(New Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($iName, $fGrpTexts);

      $iAnnot = new Form_Element_TextArea('annotation', $this->_('Anotace'));
      $iAnnot->setLangs();
      $form->addElement($iAnnot, $fGrpTexts);

      $iText = new Form_Element_TextArea('text', $this->_('Text'));
      $iText->setLangs();
      if($this->getOption('textEmpty', false) == false) {
         $iText->addValidation(New Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      }
      $form->addElement($iText, $fGrpTexts);

      if($this->category()->getParam('allow_private_zone', false) == true){
         $fGrpPrivate = $form->addGroup('privateZone', $this->_('Privátní zóna'),
                 $this->_('Položky vyditelné pouze určitým uživatelům. Administrátorům jsou tyto informace vždy viditelné.'));

         $ePrivateUsers = new Form_Element_Select('privateUsers', $this->_('Uživatelé'));
         $ePrivateUsers->setMultiple(true);

         $modelUsers = new Model_Users();
         $usersList = $modelUsers->getUsersList();
         while ($user = $usersList->fetchObject()) {
            $ePrivateUsers->setOptions(
                 array($user->{Model_Users::COLUMN_USERNAME}.' - '.$user->{Model_Users::COLUMN_NAME}
                 ." ".$user->{Model_Users::COLUMN_SURNAME}.' - '.$user->{Model_Users::COLUMN_GROUP_NAME}
                 => $user->{Model_Users::COLUMN_ID}), true);
         }
         $form->addElement($ePrivateUsers, $fGrpPrivate);

         $iPrivateText = new Form_Element_TextArea('textPrivate', $this->_('Text'));
         $iPrivateText->setLangs();
         $form->addElement($iPrivateText, $fGrpPrivate);
      }

      $fGrpParams = $form->addGroup('params', $this->_('Parametry'));

//      $eImage = new Form_Element_File('image', $this->_('Obrázek'));
//      $eImage->addValidation(new Form_Validator_FileExtension('jpg'));
//      $eImage->setUploadDir($this->category()->getModule()->getDataDir());
//      $form->addElement($eImage, $fGrpParams);

      $iUrlKey = new Form_Element_Text('urlkey', $this->_('Url klíč'));
      $iUrlKey->setLangs();
      $iUrlKey->setSubLabel($this->_('Pokud není klíč zadán, je generován automaticky'));
      $form->addElement($iUrlKey, $fGrpParams);

      $iKeywords = new Form_Element_Text('metaKeywords', $this->_('Klíčová slova'));
      $iKeywords->setLangs();
      $iKeywords->setSubLabel($this->_('Pokud nesjou zadány, jsou použiti z kategorie'));
      $form->addElement($iKeywords, $fGrpParams);

      $iDesc = new Form_Element_TextArea('metaDesc', $this->_('Popisek'));
      $iDesc->setLangs();
      $iDesc->setSubLabel($this->_('Pokud není zadán, je použit z kategorie'));
      $form->addElement($iDesc, $fGrpParams);

      $iPub = new Form_Element_Checkbox('public', $this->_('Veřejný'));
      $iPub->setSubLabel($this->_('Veřejný - viditelný všem návštěvníkům'));
      $iPub->setValues(true);
      $form->addElement($iPub, $fGrpParams);

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

   public function exportArticleController(){
      $this->checkReadableRights();
      $modle= new Articles_Model_Detail();
      $article = $modle->getArticle($this->getRequest('urlkey'));
      if($article === false) return false;
      $this->view()->article = $article;

      // private zone
      $this->view()->allowPrivate = false;
      if($this->category()->getParam('allow_private_zone', false) == true
              AND (Auth::getGroupName() == 'admin' OR
              $modle->isPrivateUser(Auth::getUserId(), $article->{Articles_Model_Detail::COLUMN_ID}))){
         $this->view()->allowPrivate = true;
      }
   }

   /**
    * Poslední článek
    */
   public function currentArticleController(){
      $this->checkReadableRights();

      $model = new Articles_Model_List();
      $article = $model->getList($this->category()->getId(), 0, 1);
      $this->view()->article = $article->fetch();
//      if($this->view()->article === false) return false;
   }

   /**
    * Posledních 20 článeků
    */
   public function lastListController(){
      $this->checkReadableRights();

      $model = new Articles_Model_List();
      $articles = $model->getList($this->category()->getId(), 0, 20);
      if($articles === false) return false;
      $this->view()->articles = $articles;
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings,Form &$form) {
      $form->addGroup('basic', 'Základní nastavení');

      $elemScroll = new Form_Element_Text('scroll', 'Počet článků na stránku');
      $elemScroll->setSubLabel('Výchozí: '.self::DEFAULT_ARTICLES_IN_PAGE.' článků');
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll,'basic');

      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }

      $form->addGroup('discussion', 'Diskuse');

      $elemAllowComments = new Form_Element_Checkbox('discussion_allow', 'Diskuse zapnuta');
      $form->addElement($elemAllowComments, 'discussion');
      if(isset($settings['discussion_allow'])) {
         $form->discussion_allow->setValues($settings['discussion_allow']);
      }

      $elemCommentsNotPublic = new Form_Element_Checkbox('discussion_not_public',
              'Příspěvky čekají na schválení');
      $form->addElement($elemCommentsNotPublic, 'discussion');
      if(isset($settings['discussion_not_public'])) {
         $form->discussion_not_public->setValues($settings['discussion_not_public']);
      }

      $elemCommentsClosed = new Form_Element_Text('discussion_closed',
              'Zavřít diskuzi po dnech');
      $elemCommentsClosed->addValidation(new Form_Validator_IsNumber());
      $elemCommentsClosed->setSubLabel('Výchozí: diskuse nejsou uzavírány');
       $form->addElement($elemCommentsClosed, 'discussion');

      $fGrpPrivate = $form->addGroup('privateZone', 'Privátní zóna', "Privátní zóna povoluje
         vložení textů, které jsou viditelné pouze vybraným uživatelům. U každého článku tak
         vznikne další textové okno s výběrem uživatelů majících přístup k těmto textům.");

      $elemAllowPrivateZone = new Form_Element_Checkbox('allow_private_zone',
              'Povolit privátní zónu');
      $form->addElement($elemAllowPrivateZone, $fGrpPrivate);
      if(isset($settings['allow_private_zone'])) {
         $form->allow_private_zone->setValues($settings['allow_private_zone']);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scroll'] = $form->scroll->getValues();
         $settings['discussion_allow'] = $form->discussion_allow->getValues();
         $settings['discussion_not_public'] = $form->discussion_not_public->getValues();
         $settings['discussion_closed'] = $form->discussion_closed->getValues();
         $settings['allow_private_zone'] = $form->allow_private_zone->getValues();
      }
   }
}
?>