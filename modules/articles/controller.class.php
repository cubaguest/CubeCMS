<?php
class Articles_Controller extends Controller {
   const DEFAULT_ARTICLES_IN_PAGE = 5;
   const DEFAULT_CLOSED_COMMENTS_DAYS = 0;

   const PARAM_SORT = 'sort';
   const PARAM_PRIVATE_ZONE = 'private';
   const PARAM_EDITOR_TYPE = 'editor';
   const PARAM_DISABLE_LIST = 'dislist';

   const PARAM_TPL_LIST = 'tpllist';
   const PARAM_TPL_DETAIL = 'tpldet';
   const PARAM_TPL_ARCHIVE = 'tplarchive';

   const DEFAULT_SORT = 'date';

   const SORT_TOP = 'top';
   const SORT_DATE = 'date';
   const SORT_ALPHABET = 'alphabet';

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //        Kontrola práv
      $this->checkReadableRights();

      // načtení článků
      $artModel = new Articles_Model();
      $query = $artModel;
      if($this->category()->getRights()->isControll()){
         $artModel->setSelectAllLangs(true);
         $query->where(Articles_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL '
            ,array('idc' => $this->category()->getId()));
      } else if($this->category()->getRights()->isWritable()){
         $query->where(
            // články který nejsou koncepty nebo je napsal uživatel
            '('.Articles_Model::COLUMN_CONCEPT.' = 0 OR '.Articles_Model::COLUMN_ID_USER.' = :idusr) '
            // články jsoupřidány po aktuálním času nebo je napsal uživatel
            .'AND ('.Articles_Model::COLUMN_ADD_TIME.' <= NOW() OR  '.Articles_Model::COLUMN_ID_USER.' = :idusr2)'
            // kategorie a vyplněný urlkey
            .'AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc '
            .'AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL ',

            array(
            'idusr' => Auth::getUserId(),
            'idusr2' => Auth::getUserId(),
            'idc' => $this->category()->getId()));
      } else {
         $query->where(Articles_Model::COLUMN_CONCEPT.' = 0 '
            .'AND '.Articles_Model::COLUMN_ADD_TIME.' < NOW() '
            .'AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc '
            .'AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL ',

            array('idc' => $this->category()->getId()));
      }

      $numRows = 0;
      $scrollComponent = null;
      if($this->category()->getParam('scroll', self::DEFAULT_ARTICLES_IN_PAGE) != 0){
         $scrollComponent = new Component_Scroll();
         $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $artModel->count());

         $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              $this->category()->getParam('scroll', self::DEFAULT_ARTICLES_IN_PAGE));
      }

      // order
      switch ($this->getRequestParam(Articles_Routes::URL_PARAM_SORT, $this->category()->getParam(self::PARAM_SORT, self::DEFAULT_SORT))) {
         case self::SORT_TOP:
            $artModel->order(array(Articles_Model::COLUMN_SHOWED => $this->getRequestParam('order','desc')));
            break;
         case self::SORT_ALPHABET:
            $artModel->order(array(Articles_Model::COLUMN_NAME => $this->getRequestParam('order','asc')));
            break;
         case self::SORT_DATE:
         default:
            $artModel->order(array(Articles_Model::COLUMN_ADD_TIME => $this->getRequestParam('order','desc')));
            // remove url param
            $this->link()->rmParam(Articles_Routes::URL_PARAM_SORT);
            break;
      }

      $artModel->joinFK(Articles_Model::COLUMN_ID_USER, array(Model_Users::COLUMN_USERNAME));
      if($scrollComponent instanceof Component_Scroll){
         $artModel->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage());
      }

      $this->view()->scrollComp = $scrollComponent;
      $this->view()->articles = $artModel->records();

      // pokud je seznam vypnut provede se redirect na detail prvního článku
      if($this->routes()->getActionName() == 'main' AND $this->category()->getParam(self::PARAM_DISABLE_LIST, false) AND $this->view()->articles != false){
         $first = reset($this->view()->articles);
         $this->link()->route('detail', array('urlkey' => (string)$first->{Articles_Model::COLUMN_URLKEY}))->reload();
}

      // odkaz zpět
      $this->link()->backInit();
   }

   public function contentController(){
      //        Kontrola práv
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
      //        Kontrola práv
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
      $m = new Articles_Model();
      if($this->rights()->isControll()){
         $m->where(Articles_Model::COLUMN_ID_CATEGORY.' = :idc ', array('idc' => $this->category()->getId()));
      } else {
         $m->where(Articles_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Articles_Model::COLUMN_CONCEPT.' = 0', array('idc' => $this->category()->getId()));
      }
      $articlesAll = $m->order(array(Articles_Model::COLUMN_ADD_TIME => Model_ORM::ORDER_DESC))->records();
      $articles = array();
      // parse for array by years
      foreach ($articlesAll as $row){
         $date = new DateTime($row->{Articles_Model::COLUMN_ADD_TIME});
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

      $artM = new Articles_Model();

      if($this->rights()->isControll()){
         $artM->where(Articles_Model::COLUMN_URLKEY.' = :urlkey AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc',
            array('urlkey' => $this->getRequest('urlkey'), 'idc' => $this->category()->getId()));
      } else if($this->rights()->isWritable()){
         $artM->where(Articles_Model::COLUMN_URLKEY.' = :urlkey AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc '
            .'AND ('.Articles_Model::COLUMN_ADD_TIME.' <= NOW() OR  '.Articles_Model::COLUMN_ID_USER.' = :idusr2)'
            .'AND ('.Articles_Model::COLUMN_CONCEPT.' = 0 OR '.Articles_Model::COLUMN_ID_USER.' = :idusr )',
            array('idc' => $this->category()->getId(),'urlkey' => $this->getRequest('urlkey'),
            'idusr' => Auth::getUserId(), 'idusr2' => Auth::getUserId()));
      } else {
         $artM->where(Articles_Model::COLUMN_URLKEY.' = :urlkey AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc '
            .'AND ('.Articles_Model::COLUMN_CONCEPT.' = 0 ) '
            .'AND ('.Articles_Model::COLUMN_ADD_TIME.' <= NOW())',
            array('idc' => $this->category()->getId(),'urlkey' => $this->getRequest('urlkey')));
      }

      $artM->joinFK(Articles_Model::COLUMN_ID_USER_LAST_EDIT, array(Model_Users::COLUMN_USERNAME), Model_ORM::JOIN_OUTER)
         ->joinFK(Articles_Model::COLUMN_ID_USER, array('usernameCreated' => Model_Users::COLUMN_USERNAME));
      $article = $artM->record();


      if($article == false) {
         return false;
      }

      if((string)$article->{Model_Users::COLUMN_USERNAME} == null){ // username po vytvoření
         $article->{Model_Users::COLUMN_USERNAME} = $article->usernameCreated;
      }

      $this->view()->article=$article;
      // přičtení zobrazení pokud není admin
      if($this->rights()->isControll() == false AND $article->{Articles_Model::COLUMN_ID_USER} != Auth::getUserId()){
         $article->{Articles_Model::COLUMN_SHOWED} = $article->{Articles_Model::COLUMN_SHOWED}+1;
         $artM->save($article);
      }

      if($this->category()->getRights()->isWritable() OR
              ($this->category()->getRights()->isWritable() AND
                      $this->view()->article->{Articles_Model::COLUMN_ID_USER} == Auth::getUserId())) {
         $deleteForm = new Form('article_');

         $feId = new Form_Element_Hidden('id');
         $feId->addValidation(new Form_Validator_IsNumber());
         $feId->setValues($this->view()->article->{Articles_Model::COLUMN_ID});
         $deleteForm->addElement($feId);

         $feSubmit = new Form_Element_Submit('delete', $this->tr('Smazat položku'));
         $deleteForm->addElement($feSubmit);

         if($deleteForm->isValid()) {
            $this->deleteArticle($deleteForm->id->getValues());
            $this->infoMsg()->addMessage($this->getOption('deleteMsg', $this->tr('Položka byl smazán')));
            $this->link()->route()->rmParam()->reload();
         }
         $this->view()->formDelete = $deleteForm;

         if($this->view()->article->{Articles_Model::COLUMN_CONCEPT} == true){
            $formPublic = new Form('art_pub_');
            $feSubmit = new Form_Element_Submit('public', $this->tr('Zveřejnit'));
            $formPublic->addElement($feSubmit);
            if($formPublic->isValid()){
               $record = $artM->record($this->view()->article->{Articles_Model::COLUMN_ID});
               $record->{Articles_Model::COLUMN_CONCEPT} = false;
               $artM->save($record);
               $this->infoMsg()->addMessage($this->getOption('publicMsg', $this->tr('U položky byl zrušek koncept')));
               $this->link()->reload();
            }
            $this->view()->formPublic = $formPublic;
         }
      }

      // odkaz zpět
      $this->view()->linkBack = $this->link()->back($this->link()->route(), 1);

      // diskuse
      if($this->category()->getParam('discussion_allow', false) == true){
         $compComments = new Component_Comments();
         $compComments->setConfig(Component_Comments::PARAM_ID_ARTICLE,
                 $article->{Articles_Model::COLUMN_ID});
         $compComments->setConfig(Component_Comments::PARAM_ID_CATEGORY,
                 $this->category()->getId());
         $compComments->setConfig(Component_Comments::PARAM_ADMIN,
                 $this->category()->getRights()->isControll());
         $compComments->setConfig(Component_Comments::PARAM_NEW_ARE_PUBLIC,
                 !$this->category()->getParam('discussion_not_public', false));
         $compComments->setConfig(Component_Comments::PARAM_FACEBOOK,
                 $this->category()->getParam('discussion_fcb', false));
         // uzavření diskuze
         if($this->category()->getParam('discussion_closed', self::DEFAULT_CLOSED_COMMENTS_DAYS) != 0){
            $timeAdd = new DateTime($article->{Articles_Model::COLUMN_ADD_TIME});
            $t = $timeAdd->format('U')
                    +$this->category()->getParam('discussion_closed', 0)*24*60*60;
            if($t < time()){
               $compComments->setConfig(Component_Comments::PARAM_CLOSED, true);
            }
         }
         $this->view()->comments = $compComments;
      }

      // private zone
      $this->view()->privateText = false;
      if($this->category()->getParam(self::PARAM_PRIVATE_ZONE, false) == true){
         $modelPrUsers = new Articles_Model_PrivateUsers();
         if($this->category()->getRights()->isControll() OR
            ($this->category()->getRights()->isWritable() AND $article->{Articles_Model::COLUMN_ID_USER} == Auth::getUserId()) OR
            $modelPrUsers->where(Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_ARTICLE.' = :idA AND '
               .Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_USER.' = :idU',
               array('idA' => $article->{Articles_Model::COLUMN_ID}, 'idU' => Auth::getUserId()))->record() != false){
            $this->view()->privateText = true;
         }
      }

      // seznam pokud není list
      if($this->category()->getParam(self::PARAM_DISABLE_LIST, false)){
         $this->mainController();
      }
   }

   /**
    * Metoda smaže článek z dat
    * @param int $idArticle
    */
   protected function deleteArticle($idArticle) {
      // výmaz diskuze
      $comments = new Component_Comments();
      $comments->setConfig(Component_Comments::PARAM_ID_ARTICLE, $idArticle);
      $comments->setConfig(Component_Comments::PARAM_ID_CATEGORY, $this->category()->getId());
      $comments->deleteAll();
      unset ($comments);

      // výmaz článku
      $artM = new Articles_Model();
      $artM->delete($idArticle);
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

      if($addForm->isSend() AND $addForm->save->getValues() == false){
         $this->link()->route()->reload();
      }

      if($addForm->isValid()) {
         // generování url klíče
         $urlkeys = $addForm->urlkey->getValues();
         $names = $addForm->name->getValues();
         $urlkeys = $this->createUrlKey($urlkeys, $names);

         $lasId = $this->saveArticle($names, $urlkeys, $addForm);
         $model = new Articles_Model();
         $art = $model->record($lasId);

//         var_dump($art);flush();exit();
         $this->infoMsg()->addMessage($this->tr('Uloženo'));
         $this->link()->route($this->getOption('actionAfterAdd', 'detail'),
                 array('urlkey' => $art->{Articles_Model::COLUMN_URLKEY}))->reload();
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
      $model = new Articles_Model();
      $article = $model->where(Articles_Model::COLUMN_URLKEY.' = :urlkey AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc',
            array('urlkey' => $this->getRequest('urlkey'), 'idc' => $this->category()->getId()))->record();

      if($article !=false){
         $editForm->name->setValues($article->{Articles_Model::COLUMN_NAME});
         $editForm->text->setValues($article->{Articles_Model::COLUMN_TEXT});
         $editForm->metaKeywords->setValues($article->{Articles_Model::COLUMN_KEYWORDS});
         $editForm->metaDesc->setValues($article->{Articles_Model::COLUMN_DESCRIPTION});
         $editForm->annotation->setValues($article->{Articles_Model::COLUMN_ANNOTATION});
         $editForm->urlkey->setValues($article->{Articles_Model::COLUMN_URLKEY});
         $editForm->art_id->setValues($article->{Articles_Model::COLUMN_ID});
         $addTime = new DateTime($article->{Articles_Model::COLUMN_ADD_TIME});
         $editForm->created_date->setValues(vve_date('%x',$addTime));
         $editForm->created_time->setValues(vve_date('%X',$addTime));
      }

      if($editForm->isSend() AND $editForm->save->getValues() == false){
         $this->link()->route('detail')->reload();
      }
      $this->view()->form = $editForm;
      $this->view()->edit = true;

      if($editForm->isValid()) {
         // generování url klíče
         $urlkeys = $editForm->urlkey->getValues();
         $names = $editForm->name->getValues();
         $urlkeys = $this->createUrlKey($urlkeys, $names, $article->{Articles_Model::COLUMN_ID});

         $oldDir = $article->{Articles_Model::COLUMN_URLKEY}[Locales::getDefaultLang()];
         $newDir = $urlkeys[Locales::getDefaultLang()];
         // přesun adresáře pokud existuje
         if($oldDir != $newDir AND is_dir($this->category()->getModule()->getDataDir().$oldDir)){
            rename($this->category()->getModule()->getDataDir().$oldDir, $this->category()->getModule()->getDataDir().$newDir);
         }
         $this->saveArticle($names, $urlkeys, $editForm, $article);
         // nahrání nové verze článku (kvůli url klíči)
         $article = $model->getArticleById($editForm->art_id->getValues());
         $this->link()->route('detail',array('urlkey' => $article->{Articles_Model::COLUMN_URLKEY}))->reload();
      }

   }

   public function editPrivateController() {
      $this->checkWritebleRights(); // tady kontrola práv k článku ne tohle

      $modelArt = new Articles_Model();
      $article = $modelArt->where(Articles_Model::COLUMN_URLKEY.' = :urlkey AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc',
            array('urlkey' => $this->getRequest('urlkey'), 'idc' => $this->category()->getId()))->record();

      $form = new Form('art_priv_text_');

      $fGrpPrivate = $form->addGroup('privateZone', $this->tr('Privátní zóna'),
              $this->tr('Položky vyditelné pouze určitým uživatelům. Administrátorům jsou tyto informace vždy viditelné.'));

      $ePrivateUsers = new Form_Element_Select('privateUsers', $this->tr('Uživatelé'));
      $ePrivateUsers->setMultiple(true);

      $modelUsers = new Model_Users();
      $usersList = $modelUsers->getUsersList();
      while ($user = $usersList->fetchObject()) {
         $ePrivateUsers->setOptions(
              array($user->{Model_Users::COLUMN_USERNAME}.' - '.$user->{Model_Users::COLUMN_NAME}
              ." ".$user->{Model_Users::COLUMN_SURNAME}.' - '.$user->{Model_Users::COLUMN_GROUP_NAME}
              => $user->{Model_Users::COLUMN_ID}), true);
      }

      // přidání uživatelů
      $modelArtPrivUsers = new Articles_Model_PrivateUsers();
      $users = $modelArtPrivUsers->where(Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_ARTICLE, $article->{Articles_Model::COLUMN_ID})->records();
      $selected = array();
      foreach ($users as $user) {
         array_push($selected, $user->{Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_USER});
      }
      $ePrivateUsers->setValues($selected);
      unset ($selected);
      $form->addElement($ePrivateUsers, $fGrpPrivate);

      $iPrivateText = new Form_Element_TextArea('textPrivate', $this->tr('Text'));
      $iPrivateText->setLangs();
      $iPrivateText->setValues($article->{Articles_Model::COLUMN_TEXT_PRIVATE});
      $form->addElement($iPrivateText, $fGrpPrivate);

      $eSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($eSubmit, $fGrpPrivate);

      if($form->isSend() AND $form->save->getValues() == false){
         $this->link()->route('detail')->reload();
      }

      if($form->isValid()){
         $article->{Articles_Model::COLUMN_TEXT_PRIVATE} = $form->textPrivate->getValues();
         $modelArt->save($article);

         // remove previous connection
         $modelArtPrivUsers->where(Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_ARTICLE, $article->{Articles_Model::COLUMN_ID})->delete();
         foreach ($form->privateUsers->getValues() as $userId) {
            $newRec = $modelArtPrivUsers->newRecord();
            $newRec->{Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_ARTICLE} = $article->{Articles_Model::COLUMN_ID};
            $newRec->{Articles_Model_PrivateUsers::COLUMN_A_H_U_ID_USER} = $userId;
            $modelArtPrivUsers->save($newRec);
         }

         $this->infoMsg()->addMessage($this->tr('Privátní text byl uložen'));
         $this->link()->route('detail')->reload();
      }

      $this->view()->form = $form;
      $this->view()->article = $article;
   }

   /**
    * Uložení samotného článku
    * @param <type> $names
    * @param <type> $urlkeys
    * @param <type> $form
    */
   protected function saveArticle($names, $urlkeys, Form $form, Model_ORM_Record $artRecord = null) {
      // přepsat
      if($form->art_id == null) $idart = null;
      else $idart = $form->art_id->getValues();
      $model = new Articles_Model();
      if($artRecord == null){
         $artRecord = $model->record($idart);
      }
      $artRecord->{Articles_Model::COLUMN_NAME} = $names;
      $artRecord->{Articles_Model::COLUMN_URLKEY} = $urlkeys;
      $artRecord->{Articles_Model::COLUMN_TEXT} = $form->text->getValues();
      $artRecord->{Articles_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($form->text->getValues()); // fulltext
      $artRecord->{Articles_Model::COLUMN_ANNOTATION} = $form->annotation->getValues();
      $artRecord->{Articles_Model::COLUMN_KEYWORDS} = $form->metaKeywords->getValues();
      $artRecord->{Articles_Model::COLUMN_DESCRIPTION} = $form->metaDesc->getValues();
      $artRecord->{Articles_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
      if($artRecord->getPk() == null){
         if($form->haveElement('creatorId')){
            $artRecord->{Articles_Model::COLUMN_ID_USER} = $form->creatorId->getValues();
         } else {
            $artRecord->{Articles_Model::COLUMN_ID_USER} = Auth::getUserId();
         }
      }
      $artRecord->{Articles_Model::COLUMN_CONCEPT} = $form->concept->getValues();

      // add time
      $addDateTime = new DateTime($form->created_date->getValues().' '.$form->created_time->getValues());
      $artRecord->{Articles_Model::COLUMN_ADD_TIME} = $addDateTime;


      $artRecord->{Articles_Model::COLUMN_ID_USER_LAST_EDIT} = Auth::getUserId();
      $artRecord->{Articles_Model::COLUMN_EDIT_TIME} = new DateTime();

      $lastId = $model->save($artRecord);
      $this->infoMsg()->addMessage($this->tr('Uloženo'));
      return $lastId;
   }

   /**
    * Metoda vygeneruje url klíče
    * @param <type> $urlkeys
    * @param <type> $names
    * @return <type>
    */
   protected function createUrlKey($urlkeys, $names, $id = null) {
      // projití url klíčů a pokud není zadán doplní se podle názvu
      $model = new Articles_Model();
      foreach ($urlkeys as $lang => $key) {
         if($key == null AND $names[$lang] == null) {
            $urlkeys[$lang] = null;
         } else if($key == null) {
            $urlkeys[$lang] = vve_cr_url_key($names[$lang]);
         } else {
            $urlkeys[$lang] = vve_cr_url_key($key);
         }
         // kontrola unikátnosti
         $uKey = $urlkeys[$lang];
         $index = 1;

         if($id != null){
            $where = Articles_Model::COLUMN_ID_CATEGORY.' = :idc AND ('.$lang.')'.Articles_Model::COLUMN_URLKEY.' = :ukey AND '.Articles_Model::COLUMN_ID.' != :ida';
            $whereVal = array('idc' => $this->category()->getId(), 'ukey' => $uKey, 'ida' => $id);
         } else {
            $where = Articles_Model::COLUMN_ID_CATEGORY.' = :idc AND ('.$lang.')'.Articles_Model::COLUMN_URLKEY.' = :ukey';
            $whereVal = array('idc' => $this->category()->getId(), 'ukey' => $uKey);
         }

         while ($model->where($where,$whereVal)->record() != false){
            $uKey = $urlkeys[$lang].'-'.$index++;
            $whereVal['ukey'] = $uKey;
         }
         $urlkeys[$lang] = $uKey;
      }
      return $urlkeys;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm() {
      $form = new Form('ardicle_');

      $fGrpTexts = $form->addGroup('texts', $this->tr('Texty'));

      $iName = new Form_Element_Text('name', $this->tr('Nadpis'));
      $iName->setLangs();
      $iName->addValidation(New Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($iName, $fGrpTexts);

      $iAnnot = new Form_Element_TextArea('annotation', $this->tr('Anotace'));
      $iAnnot->setLangs();
      $form->addElement($iAnnot, $fGrpTexts);

      $iText = new Form_Element_TextArea('text', $this->tr('Text'));
      $iText->setLangs();
      if($this->getOption('textEmpty', false) == false) {
         $iText->addValidation(New Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      }
      $form->addElement($iText, $fGrpTexts);

      $fGrpParams = $form->addGroup('params', $this->tr('Parametry'));

//      $eImage = new Form_Element_File('image', $this->tr('Obrázek'));
//      $eImage->addValidation(new Form_Validator_FileExtension('jpg'));
//      $eImage->setUploadDir($this->category()->getModule()->getDataDir());
//      $form->addElement($eImage, $fGrpParams);

      $iUrlKey = new Form_Element_Text('urlkey', $this->tr('Url klíč'));
      $iUrlKey->setLangs();
      $iUrlKey->setSubLabel($this->tr('Pokud není klíč zadán, je generován automaticky'));
      $form->addElement($iUrlKey, $fGrpParams);

      $iKeywords = new Form_Element_Text('metaKeywords', $this->tr('Klíčová slova'));
      $iKeywords->setLangs();
      $form->addElement($iKeywords, $fGrpParams);

      $iDesc = new Form_Element_TextArea('metaDesc', $this->tr('Popisek'));
      $iDesc->setLangs();
      $iDesc->setSubLabel($this->tr('Pokud není zadán pokusí se použít anotaci, jinak zůstne prázdný.'));
      $form->addElement($iDesc, $fGrpParams);

      // pokud jsou práva pro kontrolu, přidám položku s uživateli, kterí mohou daný článek vytvořit
      if($this->category()->getRights()->isControll()){
         $eCreator = new Form_Element_Select('creatorId', $this->tr('Položka vytvořena uživatelem'));
         $modelUsers = new Model_Users();
         foreach ($modelUsers->records() as $user){
            $name = $user->{Model_Users::COLUMN_USERNAME};
            $name .= ' ('.$user->{Model_Users::COLUMN_NAME}.' '.$user->{Model_Users::COLUMN_SURNAME}.')';
            $eCreator->setOptions(array($name => $user->{Model_Users::COLUMN_ID}),true);
         }
         $eCreator->setValues(Auth::getUserId());
         $form->addElement($eCreator, $fGrpParams);
      }

      $fGrpPublic = $form->addgroup('public', $this->tr('Paramtry zveřejnění a vytvoření'));

      $iConcept = new Form_Element_Checkbox('concept', $this->tr('Koncept'));
      $iConcept->setSubLabel($this->tr('Pokud je položka koncept, je viditelná pouze autorovi a administrátorům.'));
      $iConcept->setValues(false);
      $form->addElement($iConcept, $fGrpPublic);

      $eCreatedDate = new Form_Element_Text('created_date', $this->tr('Datum vytvoření'));
      $eCreatedDate->setValues(vve_date("%x"));
      $eCreatedDate->addValidation(new Form_Validator_NotEmpty());
      $eCreatedDate->addValidation(new Form_Validator_Date());
      $form->addElement($eCreatedDate, $fGrpPublic);

      $eCreatedTime = new Form_Element_Text('created_time', $this->tr('Čas vytvoření'));
      $eCreatedTime->setValues(vve_date("%X"));
      $eCreatedTime->addValidation(new Form_Validator_NotEmpty());
      $eCreatedTime->addValidation(new Form_Validator_Time());
      $form->addElement($eCreatedTime, $fGrpPublic);


      $iSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($iSubmit);

      return $form;
   }

   /**
    * Smazání článků při odstranění kategorie
    * @param Category $category
    */
   public static function clearOnRemove(Category $category) {
      $model = new Articles_Model();
      $model->where(Articles_Model::COLUMN_ID_CATEGORY, $category->getId())->delete();
   }

   public function exportArticleController(){
      $this->checkReadableRights();
      $model= new Articles_Model();
      $article = $model->where(Articles_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Articles_Model::COLUMN_URLKEY.' = :ukey',
         array('idc' => $this->category()->getId(), 'ukey' => $this->getRequest('urlkey')))->record();
      if($article === false) return false;
      $this->view()->article = $article;

      // private zone
      $this->view()->allowPrivate = false;
      if($this->category()->getParam('allow_private_zone', false) == true
              AND (Auth::getGroupName() == 'admin' OR
              $model->isPrivateUser(Auth::getUserId(), $article->{Articles_Model::COLUMN_ID}))){
         $this->view()->allowPrivate = true;
      }
   }

   /**
    * Poslední článek
    */
   public function currentArticleController(){
      $this->checkReadableRights();
      $model = new Articles_Model();
      $this->view()->article = $model->where(Articles_Model::COLUMN_ID_CATEGORY, $this->category()->getId())->limit(0, 1)->record();
   }

   /**
    * Posledních 20 článeků
    */
   public function lastListController(){
      $this->checkReadableRights();
      $model = new Articles_Model();
      $articles = $model->where(Articles_Model::COLUMN_ID_CATEGORY, $this->category()->getId())->limit(0, 20)->records();
      if($articles === false) return false;
      $this->view()->articles = $articles;
   }

   /**
    * Metoda pro nastavení modulu
    */
   protected function settings(&$settings,Form &$form) {
      $fGrpView = $form->addGroup('view', $this->tr('Nastavení vzhledu'));

      $elemScroll = new Form_Element_Text('scroll', $this->tr('Počet položek na stránku'));
      $elemScroll->setSubLabel(sprintf($this->tr('Výchozí: %s položek. Pokud je zadána 0 budou vypsány všechny položky'),self::DEFAULT_ARTICLES_IN_PAGE));
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll, $fGrpView);

      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }
      // řazení
      $elemSort = new Form_Element_Select('sort', $this->tr('Řadit podle'));
      $elemSort->setOptions(array(
         $this->tr('Času přidání') => self::SORT_DATE,
         $this->tr('Abecedy') => self::SORT_ALPHABET,
         $this->tr('Počtu zhlédnutí') => self::SORT_TOP
      ));
      if(isset($settings[self::PARAM_SORT])) {
         $elemSort->setValues($settings[self::PARAM_SORT]);
      }
      $form->addElement($elemSort, $fGrpView);


      // šablony
      $componentTpls = new Component_ViewTpl();

      $componentTpls->setConfig(Component_ViewTpl::PARAM_MODULE, $settings['_module']);

      $elemTplList = new Form_Element_Select('tplList', $this->tr('Šablona seznamu'));
      $elemTplList->setOptions(array_flip($componentTpls->getTpls('list')));
      if(isset($settings[self::PARAM_TPL_LIST])) {
         $elemTplList->setValues($settings[self::PARAM_TPL_LIST]);
      }
      $form->addElement($elemTplList, $fGrpView);

      $elemTplDetail = new Form_Element_Select('tplDetail', $this->tr('Šablona detailu'));
      $elemTplDetail->setOptions(array_flip($componentTpls->getTpls('detail')));
      if(isset($settings[self::PARAM_TPL_DETAIL])) {
         $elemTplDetail->setValues($settings[self::PARAM_TPL_DETAIL]);
      }
      $form->addElement($elemTplDetail, $fGrpView);

      $elemTplArchive = new Form_Element_Select('tplArchive', $this->tr('Šablona archivu'));
      $elemTplArchive->setOptions(array_flip($componentTpls->getTpls('archive')));
      if(isset($settings[self::PARAM_TPL_ARCHIVE])) {
         $elemTplArchive->setValues($settings[self::PARAM_TPL_ARCHIVE]);
      }
      $form->addElement($elemTplArchive, $fGrpView);

      unset ($componentTpls);

      $elemDisableList = new Form_Element_Checkbox('disableList', $this->tr('Vypnout úvodní seznam'));
      $elemDisableList->setSubLabel($this->tr('Pokud je list vypnut, stránka je automaticky přesměrována na první položku. V detailu je pak načten seznam položek.'));
      if(isset($settings[self::PARAM_DISABLE_LIST])) {
         $elemDisableList->setValues($settings[self::PARAM_DISABLE_LIST]);
      }
      $form->addElement($elemDisableList, $fGrpView);

      $fGrpEditSet = $form->addGroup('editSettings', $this->tr('Nastavení úprav'));

      $elemEditorType = new Form_Element_Select('editor_type', $this->tr('Typ editoru'));
      $elemEditorType->setOptions(array(
         $this->tr('žádný (pouze textová oblast)') => 'none',
         $this->tr('jednoduchý (Wysiwyg)') => 'simple',
         $this->tr('pokročilý (Wysiwyg)') => 'advanced',
         $this->tr('kompletní (Wysiwyg)') => 'full'
      ));
      $elemEditorType->setValues('advanced');
      if(isset($settings[self::PARAM_EDITOR_TYPE])) {
         $elemEditorType->setValues($settings[self::PARAM_EDITOR_TYPE]);
      }
      $form->addElement($elemEditorType, $fGrpEditSet);

      $form->addGroup('discussion', $this->tr('Diskuse'));

      $elemAllowComments = new Form_Element_Checkbox('discussion_allow', $this->tr('Diskuse zapnuta'));
      $form->addElement($elemAllowComments, 'discussion');
      if(isset($settings['discussion_allow'])) {
         $form->discussion_allow->setValues($settings['discussion_allow']);
      }

      $elemFcbComments = new Form_Element_Checkbox('discussion_fcb', $this->tr('Použít Facebook diskusi'));
      $form->addElement($elemFcbComments, 'discussion');
      if(isset($settings['discussion_fcb'])) {
         $form->discussion_fcb->setValues($settings['discussion_fcb']);
      }

      $elemCommentsNotPublic = new Form_Element_Checkbox('discussion_not_public',
              $this->tr('Příspěvky čekají na schválení'));
      $form->addElement($elemCommentsNotPublic, 'discussion');
      if(isset($settings['discussion_not_public'])) {
         $form->discussion_not_public->setValues($settings['discussion_not_public']);
      }

      $elemCommentsClosed = new Form_Element_Text('discussion_closed',
              $this->tr('Zavřít diskuzi po dnech'));
      $elemCommentsClosed->addValidation(new Form_Validator_IsNumber());
      $elemCommentsClosed->setSubLabel($this->tr('Výchozí: diskuse nejsou uzavírány'));
       $form->addElement($elemCommentsClosed, 'discussion');

      $fGrpPrivate = $form->addGroup('privateZone', $this->tr('Privátní zóna'), $this->tr("Privátní zóna povoluje
         vložení textů, které jsou viditelné pouze vybraným uživatelům. U každé položky tak
         vznikne další textové okno s výběrem uživatelů majících přístup k těmto textům."));

      $elemAllowPrivateZone = new Form_Element_Checkbox('allow_private_zone',
              $this->tr('Povolit privátní zónu'));
      $form->addElement($elemAllowPrivateZone, $fGrpPrivate);
      if(isset($settings[self::PARAM_PRIVATE_ZONE])) {
         $form->allow_private_zone->setValues($settings[self::PARAM_PRIVATE_ZONE]);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scroll'] = (int)$form->scroll->getValues();
         $settings[self::PARAM_SORT] = $form->sort->getValues();
         $settings[self::PARAM_DISABLE_LIST] = $form->disableList->getValues();
         $settings['discussion_allow'] = $form->discussion_allow->getValues();
         $settings['discussion_fcb'] = $form->discussion_fcb->getValues();
         $settings['discussion_not_public'] = $form->discussion_not_public->getValues();
         $settings['discussion_closed'] = $form->discussion_closed->getValues();
         $settings[self::PARAM_PRIVATE_ZONE] = (bool)$form->allow_private_zone->getValues();
         $settings[self::PARAM_TPL_LIST] = $form->tplList->getValues();
         $settings[self::PARAM_TPL_DETAIL] = $form->tplDetail->getValues();
         $settings[self::PARAM_TPL_ARCHIVE] = $form->tplArchive->getValues();
      }
   }
}
?>
