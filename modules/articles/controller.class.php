<?php
class Articles_Controller extends Controller {
   const DEFAULT_ARTICLES_IN_PAGE = 5;
   const DEFAULT_CLOSED_COMMENTS_DAYS = 0;

   const PARAM_SORT = 'sort';
   const PARAM_PRIVATE_ZONE = 'private';
   const PARAM_EDITOR_TYPE = 'editor';
   const PARAM_DISABLE_LIST = 'dislist';
   const PARAM_SHOW_CATS = 'shc';
   const PARAM_NOTIFY_RECIPIENTS = 'nfrecid';

   const PARAM_MOUNTED_CATS = 'moc';

   const PARAM_MAIL_NAME = 'mname';
   const PARAM_MAIL_PASSWORD = 'mpass';
   const PARAM_MAIL_SERVER = 'mserver';
   const PARAM_MAIL_SECURE_KEY = 'mseckey';

   const DEFAULT_SORT = 'date';

   const SORT_TOP = 'top';
   const SORT_DATE = 'date';
   const SORT_ALPHABET = 'alphabet';

   const GET_TAG_PARAM = 'tag';
   
   /**
    * Jestli je povoleno zadávání prázdného textu
    * @var bool
    */
   protected $allowEmptyText = false;


   protected function init()
   {
      $this->actionsLabels = array(
          'main' => $this->tr('Přehled položek'),
          'show' => $this->tr('Detail'),
          'archive' => $this->tr('Archiv'),
          'edit' => $this->tr('Úprava položky'),
          'add' => $this->tr('Přidání položky'),
          'editprivate' => $this->tr('Úprava privátního textu položky'),
          'edittext' => $this->tr('Úprava úvodního textu položky'),
          'move' => $this->tr('Přesun položky'),
      );
   }


   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //        Kontrola práv
      $this->checkReadableRights();
      // načtení článků
      $artModel = new Articles_Model();
      $catsIds = array($this->category()->getId());
      $externalCats = explode(';', $this->category()->getParam(self::PARAM_MOUNTED_CATS, "") );
      if( $this->category()->getParam(self::PARAM_MOUNTED_CATS, false) && !empty($externalCats )) {
         $wCatPl = array(':pl_'.$this->category()->getId() => $this->category()->getId() );
         foreach ($externalCats as $externalCatID) {
            $wCatPl[':pl_'.$externalCatID] = $externalCatID;
         }
         
         // načtení oprávnění k připojeným kategoriím
         $modelCat = new Model_Category();
         if(Auth::isAdmin()){
            $cats = $modelCat->where(Model_Category::COLUMN_ID." IN (".implode(',', array_keys($wCatPl) ).")", $wCatPl, true)
               ->records();
         } else {
            $cats = $modelCat->onlyWithAccess()
               ->where(" AND ". Model_Category::COLUMN_ID." IN (".implode(',', array_keys($wCatPl) ).")", $wCatPl, true)
               ->records();
         }
         
         $allowedCatsIDSPL = array();
         foreach ($cats as $c) {
            $allowedCatsIDSPL[':pl_'.$c->{Model_Category::COLUMN_ID}] = $c->{Model_Category::COLUMN_ID};
            $catsIds[] = $c->getPK();
         }
         
         $mWhereString = Articles_Model::COLUMN_ID_CATEGORY.' IN ('.implode(',',array_keys($allowedCatsIDSPL)).')';
         $mWhereBinds = $allowedCatsIDSPL;
         
         $artModel->joinFK(Articles_Model::COLUMN_ID_CATEGORY, array(
               'curlkey' => Model_Category::COLUMN_URLKEY, Model_Category::COLUMN_ID, Model_Category::COLUMN_NAME 
               ), Model_ORM::JOIN_OUTER);
         
      } else {
         $mWhereString = Articles_Model::COLUMN_ID_CATEGORY.' = :idc';
         $mWhereBinds = array('idc' => $this->category()->getId());
      }
      
      if($this->category()->getRights()->isControll()){
         $artModel->setSelectAllLangs(true);
         $mWhereString .= ' AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL ';
         
      } else if($this->category()->getRights()->isWritable()){
         $mWhereString .= 
            // články který nejsou koncepty nebo je napsal uživatel
            " AND ("
            .'('.Articles_Model::COLUMN_CONCEPT.' = 0 OR '.Articles_Model::COLUMN_ID_USER.' = :idusr) '
            // články jsoupřidány po aktuálním času nebo je napsal uživatel
            .'AND ('.Articles_Model::COLUMN_ADD_TIME.' <= NOW() OR  '.Articles_Model::COLUMN_ID_USER.' = :idusr2)'
            // kategorie a vyplněný urlkey
            .'AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL '
            .")";
         $mWhereBinds['idusr'] = Auth::getUserId();
         $mWhereBinds['idusr2'] = Auth::getUserId();
      } else {
         $mWhereString .= 
            " AND ("
            .Articles_Model::COLUMN_CONCEPT.' = 0 '
            .'AND '.Articles_Model::COLUMN_ADD_TIME.' < NOW() '
            .'AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL '
            .")";
      }
      // duplikace, potřebujeme niže pro další dotazy nad články
      $mWhereTagsString = $mWhereString;
      $mWhereTagsBinds = $mWhereBinds; 
      
      /* pokud je vybrán tag */
      if($this->getRequestParam('tag') != null){
         $this->view()->selectedTag = $this->getRequestParam('tag');

         $artModel
            ->join(Articles_Model::COLUMN_ID, array( 'art_tags_conn' => "Articles_Model_TagsConnection"), Articles_Model_TagsConnection::COLUMN_ID_ARTICLE)
            ->join(array( 'art_tags_conn' => Articles_Model_TagsConnection::COLUMN_ID_TAG), "Articles_Model_Tags", Articles_Model_Tags::COLUMN_ID)
            ->groupBy(Articles_Model::COLUMN_ID);
         $mWhereString .= " AND ".Articles_Model_Tags::COLUMN_NAME." = :tagname";
         $mWhereBinds['tagname'] = $this->getRequestParam('tag'); 
      }
      /* only specific year */
      if($this->getRequestParam('year') != null){
         $mWhereString .= 
            " AND YEAR(".Articles_Model::COLUMN_ADD_TIME.") = :year";
         $mWhereBinds[':year'] = $this->getRequestParam('year');
      }
      
      $artModel->where($mWhereString, $mWhereBinds);

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
            $artModel->order(array(Articles_Model::COLUMN_ADD_TIME => $this->getRequestParam('order','desc')));
         default:
            $artModel->order(array(
                  Articles_Model::COLUMN_PRIORITY => Model_ORM::ORDER_DESC,
                  Articles_Model::COLUMN_ADD_TIME => $this->getRequestParam('order','desc')
               ));
            // remove url param
            $this->link()->rmParam(Articles_Routes::URL_PARAM_SORT);
            break;
      }

      $artModel->joinFK(Articles_Model::COLUMN_ID_USER, array(
            Model_Users::COLUMN_USERNAME, 'usernameName' => Model_Users::COLUMN_NAME, 'usernameSurName' => Model_Users::COLUMN_SURNAME));
      if($scrollComponent instanceof Component_Scroll){
         $artModel->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage());
      }
      
      $this->view()->scrollComp = $scrollComponent;
      
      $cacheKey = md5($mWhereString.serialize($mWhereBinds).Locales::getLang());
      
      $cache = new Cache();
      if( ($articles = $cache->get($cacheKey)) == false || $this->category()->getRights()->isWritable()){
         $articles = $artModel->records();
         $cache->set($cacheKey, $articles);
      }
      
      /**
       * priority -- projít články a kontrola prioritních
       * @todo - přepočet priorit přeřadit do plánovače úloh
       */
      
      $this->view()->articles = $articles;

      // pokud je seznam vypnut provede se redirect na detail prvního článku
      if($this->routes()->getActionName() == 'main' AND $this->category()->getParam(self::PARAM_DISABLE_LIST, false) AND $this->view()->articles != false){
         $first = reset($this->view()->articles);
         $this->link()->route('detail', array('urlkey' => (string)$first->{Articles_Model::COLUMN_URLKEY}))->reload();
      }

      // načtení tagů
      if($this->view()->articles != false){
         if( ($articlesTags = $cache->get($cacheKey."_tags")) == false || $this->category()->getRights()->isWritable()){
            $articlesTags = array();
            $placeholders = array();
            foreach ($this->view()->articles as $article) {
               $placeholders[':pl_'.$article->{Articles_Model::COLUMN_ID}] = $article->{Articles_Model::COLUMN_ID};
            }
      
            $modelTags = new Articles_Model_TagsConnection();
            $tags = $modelTags
               ->joinFK(Articles_Model_TagsConnection::COLUMN_ID_TAG)
               ->where(Articles_Model_TagsConnection::COLUMN_ID_ARTICLE." IN (".implode(',', array_keys($placeholders) ).")", 
                  $placeholders )
               ->order(array(Articles_Model_Tags::COLUMN_NAME => Model_ORM::ORDER_ASC ))
               ->records();
         
            foreach ($tags as $tag) {
               $id = $tag->{Articles_Model_TagsConnection::COLUMN_ID_ARTICLE};
               if(!isset( $articlesTags[$id] )){
                  $articlesTags[$id] = array();
               }
               $articlesTags[$id][] = $tag->{Articles_Model_Tags::COLUMN_NAME};
            }
            $cache->set($cacheKey."_tags", $articlesTags);
         }
//          $cache->delete($cacheKey."_tags");
         
         $this->view()->articlesTags = $articlesTags ;
      }
      // laod all tags
      $this->view()->allTags = Articles_Model_Tags::getTagsByCategory($catsIds) ;
      
      // load other years
      $this->view()->artsYears = Articles_Model::getArticlesYears($catsIds, $this->category()->getRights()->isWritable());
      
      $this->checkDateFirstArticle();
      
      // odkaz zpět
      $this->link()->backInit();
      
      // načtení úvodního textu
      $this->view()->text = $this->loadText();
   }

   public function archiveDateController($year, $month)
   {
      $this->checkReadableRights();
      $artModel = new Articles_Model();
      $dateStart = new DateTime($year.'-'.$month.'-1 00:00:00');
      $this->view()->currentDate = $dateStart;
      $dateEnd = new DateTime($year.'-'.$month.'-'.$dateStart->format('t').' 23:59:59');
      
      $mWhereString = Articles_Model::COLUMN_ID_CATEGORY.' = :idc '
          . 'AND '.Articles_Model::COLUMN_ADD_TIME.' >= :dateStart '
          . 'AND '.Articles_Model::COLUMN_ADD_TIME.' <= :dateEnd';
      $mWhereBinds = array(
          'idc' => $this->category()->getId(),
          'dateStart' => $dateStart->format(DATE_ISO8601),
          'dateEnd' => $dateEnd->format(DATE_ISO8601),
          );
      
       if($this->category()->getRights()->isControll()){
         $artModel->setSelectAllLangs(true);
         $mWhereString .= ' AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL ';
         
      } else if($this->category()->getRights()->isWritable()){
         $mWhereString .= 
            // články který nejsou koncepty nebo je napsal uživatel
            " AND ("
            .'('.Articles_Model::COLUMN_CONCEPT.' = 0 OR '.Articles_Model::COLUMN_ID_USER.' = :idusr) '
            // články jsoupřidány po aktuálním času nebo je napsal uživatel
            .'AND ('.Articles_Model::COLUMN_ADD_TIME.' <= NOW() OR  '.Articles_Model::COLUMN_ID_USER.' = :idusr2)'
            // kategorie a vyplněný urlkey
            .'AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL '
            .")";
         $mWhereBinds['idusr'] = Auth::getUserId();
         $mWhereBinds['idusr2'] = Auth::getUserId();
      } else {
         $mWhereString .= 
            " AND ("
            .Articles_Model::COLUMN_CONCEPT.' = 0 '
            .'AND '.Articles_Model::COLUMN_ADD_TIME.' < NOW() '
            .'AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL '
            .")";
      }
      $artModel->order(array(Articles_Model::COLUMN_ADD_TIME => $this->getRequestParam('order','desc')));
      $artModel->where($mWhereString, $mWhereBinds);
      $artModel->joinFK(Articles_Model::COLUMN_ID_USER, array(
            Model_Users::COLUMN_USERNAME, 'usernameName' => Model_Users::COLUMN_NAME, 'usernameSurName' => Model_Users::COLUMN_SURNAME));
      $articles = $artModel->records();
      
      $cache = new Cache();
      $cacheKey = md5($mWhereString.serialize($mWhereBinds).Locales::getLang());
      if( ($articles = $cache->get($cacheKey)) == false ){
         $articles = $artModel->records();
         $cache->set($cacheKey, $articles);
      }
      $this->view()->articles = $articles;
      
      $this->checkDateFirstArticle();
      
      $this->link()->backInit();
   }
   
   protected function checkDateFirstArticle()
   {
      // rok prvního článku
      $modelArt = new Articles_Model();
      $firstRec = $modelArt
          ->where(Articles_Model::COLUMN_CONCEPT." = 0", array())
          ->order(array(Articles_Model::COLUMN_ADD_TIME => Model_ORM::ORDER_ASC))
          ->record();
      $this->view()->firstArticleDate = new DateTime($firstRec ? $firstRec->{Articles_Model::COLUMN_ADD_TIME} : null);
   }

   /**
    * @deprecated - není potřeba
    */
   public function contentController(){
      //        Kontrola práv
      $this->checkReadableRights();

      // načtení článků
      $artModel = new Articles_Model_List();
      $articles = $artModel->getList($this->category()->getId(),0,100,!$this->rights()->isWritable());
      $this->view()->articles = $articles;
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
      
      $this->checkDateFirstArticle();
   }

   public function showController($urlkey) {
      $this->checkReadableRights();

      $artM = new Articles_Model();

      $whereStr = null;
      $whereBind = array();
      
      if($this->rights()->isControll()){
          $whereStr = Articles_Model::COLUMN_URLKEY.' = :urlkey AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc';
          $whereBind = array('urlkey' => $urlkey, 'idc' => $this->category()->getId());
      } else if($this->rights()->isWritable()){
         $whereStr = Articles_Model::COLUMN_URLKEY.' = :urlkey AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc '
            .'AND ('.Articles_Model::COLUMN_ADD_TIME.' <= NOW() OR  '.Articles_Model::COLUMN_ID_USER.' = :idusr2)'
            .'AND ('.Articles_Model::COLUMN_CONCEPT.' = 0 OR '.Articles_Model::COLUMN_ID_USER.' = :idusr )';
         $whereBind = array('idc' => $this->category()->getId(),'urlkey' => $this->getRequest('urlkey'),
            'idusr' => Auth::getUserId(), 'idusr2' => Auth::getUserId());
      } else {
         $whereStr = Articles_Model::COLUMN_URLKEY.' = :urlkey AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc '
            .'AND ('.Articles_Model::COLUMN_CONCEPT.' = 0 ) '
            .'AND ('.Articles_Model::COLUMN_ADD_TIME.' <= NOW())';
         $whereBind = array('idc' => $this->category()->getId(),'urlkey' => $this->getRequest('urlkey'));
      }
      $artM->where($whereStr, $whereBind);

      
      $artM->joinFK(Articles_Model::COLUMN_ID_USER_LAST_EDIT, array(Model_Users::COLUMN_USERNAME), Model_ORM::JOIN_OUTER)
         ->joinFK(Articles_Model::COLUMN_ID_USER, array('usernameCreated' => Model_Users::COLUMN_USERNAME,
            'usernameName' => Model_Users::COLUMN_NAME, 'usernameSurName' => Model_Users::COLUMN_SURNAME ));
      $article = $artM->record();


      if($article == false) {
         // try mounted cats and redirect to article
         if( $this->category()->getParam(self::PARAM_MOUNTED_CATS, null) != null ){
            $externalCats = explode(';', $this->category()->getParam(self::PARAM_MOUNTED_CATS, "") );
            $eCatPl = array();
            foreach ($externalCats as $externalCatID) {
               $eCatPl[':pl_'.$externalCatID] = $externalCatID;
            }
            
            $whereStr = Articles_Model::COLUMN_URLKEY.' = :urlkey '
               .' AND '.Articles_Model::COLUMN_ID_CATEGORY." IN(".implode(',', array_keys($eCatPl)).")";
            $whereBind = array_merge( array('urlkey' => $urlkey) , $eCatPl );
            
            $artM->joinFK(Articles_Model::COLUMN_ID_CATEGORY, array(Model_Category::COLUMN_URLKEY));
            $externalArt = $artM->where($whereStr, $whereBind)
               ->record();
            
            if($externalArt != false AND !$externalArt->isNew()){
               $this->link(true)
                  ->category($externalArt->{Model_Category::COLUMN_URLKEY})
                  ->route('detail')
                  ->reload();
               die;// :-)
            }
            
         }

         throw new UnexpectedPageException();
      }

      if((string)$article->{Model_Users::COLUMN_USERNAME} == null){ // username po vytvoření
         $article->{Model_Users::COLUMN_USERNAME} = $article->usernameCreated;
      }

      $this->view()->article = $article;

      // přičtení zobrazení pokud není admin
      if($this->rights()->isControll() == false AND $article->{Articles_Model::COLUMN_ID_USER} != Auth::getUserId()){
         $article->{Articles_Model::COLUMN_SHOWED} = $article->{Articles_Model::COLUMN_SHOWED}+1;
         $artM->save($article);
      }

      if($this->category()->getRights()->isWritable() OR
              ($this->category()->getRights()->isWritable() AND
                      $this->view()->article->{Articles_Model::COLUMN_ID_USER} == Auth::getUserId())) {
         $deleteForm = new Form('article_', true);

         $feId = new Form_Element_Hidden('id');
         $feId->addValidation(new Form_Validator_IsNumber());
         $feId->setValues($this->view()->article->{Articles_Model::COLUMN_ID});
         $deleteForm->addElement($feId);

         $feSubmit = new Form_Element_Submit('delete', $this->tr('Smazat položku'));
         $deleteForm->addElement($feSubmit);

         if($deleteForm->isValid()) {
            $this->deleteArticle($deleteForm->id->getValues());
            $this->infoMsg()->addMessage($this->getOption('deleteMsg', $this->tr('Položka byla smazána')));
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
               $this->infoMsg()->addMessage($this->getOption('publicMsg', $this->tr('U položky byl zrušen příznak konceptu')));
               $this->link()->reload();
            }
            $this->view()->formPublic = $formPublic;
         }
      }

      // odkaz zpět
      $this->view()->linkBack = $this->link()->route();

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
      
      /* TAGY */
      $articlesTags = array();
      $modelTags = new Articles_Model_TagsConnection();
      $tagsRecs = $modelTags
         ->joinFK(Articles_Model_TagsConnection::COLUMN_ID_TAG)
         ->where(Articles_Model_TagsConnection::COLUMN_ID_ARTICLE." = :ida", array('ida' => $article->getPK() ) )
         ->order(array(Articles_Model_Tags::COLUMN_NAME => Model_ORM::ORDER_ASC ))
         ->records();

      $tags = array();
      $tagsIds = array();
      foreach ($tagsRecs as $tag) {
         $tagsIds[':id_'.$tag->{Articles_Model_TagsConnection::COLUMN_ID_TAG}] = $tag->{Articles_Model_TagsConnection::COLUMN_ID_TAG};
         $tags[] = $tag->{Articles_Model_Tags::COLUMN_NAME};
      }
      $this->view()->tags = $tags;

      // pokud jsou tagy načteme relevantní články
      if(!empty($tagsIds)){
         $modelTags = new Articles_Model_TagsConnection();
         $similar = $modelTags
            ->joinFK(Articles_Model_TagsConnection::COLUMN_ID_ARTICLE,
            array(Articles_Model::COLUMN_URLKEY, Articles_Model::COLUMN_NAME, Articles_Model::COLUMN_ADD_TIME))
            ->where(Articles_Model_TagsConnection::COLUMN_ID_TAG." IN (".implode(',', array_keys($tagsIds)).")"
               ." AND ".Articles_Model::COLUMN_ID_CATEGORY." = :idc"
               ." AND ".Articles_Model::COLUMN_CONCEPT." = 0"
               ." AND ".Articles_Model::COLUMN_ADD_TIME." <= NOW()"
               ." AND ".Articles_Model::COLUMN_ID." != :artId"
            , array_merge(array(
               'idc' => $this->category()->getId(),
               'artId' => $article->getPK()
            ), $tagsIds)
         )
            ->limit(0,5)
            ->order(array(Articles_Model_TagsConnection::COLUMN_ID_ARTICLE => Model_ORM::ORDER_DESC))
            ->groupBy(array(Articles_Model_TagsConnection::COLUMN_ID_ARTICLE))
            ->records();
         $this->view()->similar = $similar;
      }
      
      $modelUsers = new Model_Users();
      $this->view()->creator = $modelUsers->record($article->{Articles_Model::COLUMN_ID_USER});
      if($article->{Articles_Model::COLUMN_ID_USER} == $article->{Articles_Model::COLUMN_ID_USER_LAST_EDIT}){
         $this->view()->editor = $this->view()->creator;
      } else {
         $this->view()->editor = $modelUsers->record($article->{Articles_Model::COLUMN_ID_USER_LAST_EDIT});
      }
      
      if($article->{Articles_Model::COLUMN_ID_PHOTOGALLERY}){
         // images from connected photogalery
         $gallery = Articles_Model::getRecord($article->{Articles_Model::COLUMN_ID_PHOTOGALLERY});
         $this->view()->images = PhotoGalery_Model_Images::getImages($gallery->{Articles_Model::COLUMN_ID_CATEGORY}, $gallery->getPK());
         
         $cat = new Category((int)$gallery->{Articles_Model::COLUMN_ID_CATEGORY});
         $this->view()->imagesBaseDir = $gallery->getDataUrl();
         $this->view()->photoGalleryLink = $this->link(true)->category($cat->getUrlKey())->route('detail', 
             array('urlkey' => $gallery->{Articles_Model::COLUMN_URLKEY}));
      }
      
      $this->checkDateFirstArticle();
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
         if(isset($addForm->socNetPublish) && $addForm->socNetPublish->getValues() == true){
            $this->sendToSocialNetworks($art, $addForm->socNetMessage->getValues());
         }
         
         if(!$this->category()->getRights()->isControll()){
            $this->sendNotify($art);
         }
         
         $this->infoMsg()->addMessage($this->tr('Uloženo'));
         $this->link()->route($this->getOption('actionAfterAdd', 'detail'),
                 array('urlkey' => $art->{Articles_Model::COLUMN_URLKEY}))->rmParam()->reload();
      }

      $this->view()->form = $addForm;
      $this->view()->edit = false;
   }
   
   /**
    * controller pro úpravu novinky
    */
   public function editController() {
      $this->checkWritebleRights();

      // načtení dat
      $model = new Articles_Model();
      $article = $model->where(Articles_Model::COLUMN_URLKEY.' = :urlkey AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc',
            array('urlkey' => $this->getRequest('urlkey'), 'idc' => $this->category()->getId()))->record();
      
      if($article == false){
         throw new UnexpectedPageException();
      }

      if(!$this->rights()->isControll() && $article->{Articles_Model::COLUMN_ID_USER} != Auth::getUserId()){
         throw new ForbiddenAccessException();
      }

      $this->view()->article = $article;
      
      $editForm = $this->createForm($article);
      
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
         if(isset($editForm->socNetPublish) && $editForm->socNetPublish->getValues() == true){
            $this->sendToSocialNetworks($article, $editForm->socNetMessage->getValues());
         }
         $this->link()->route('detail',array('urlkey' => $article->{Articles_Model::COLUMN_URLKEY}))->reload();
      }
   }
   
   protected function sendToSocialNetworks($article, $message = null, $caption = null)
   {
      $scPublisher = new Component_SocialNetwork_Publisher();
      
      $link = (string)$this->link()->route('detail', array('urlkey' => $article->{Articles_Model::COLUMN_URLKEY}))->rmParam();
      
      $params = array(
         'name' => $article->{Articles_Model::COLUMN_NAME},
         'link' => $link,
         'description' => (string)$article->{Articles_Model::COLUMN_ANNOTATION} != null ? 
             $article->{Articles_Model::COLUMN_ANNOTATION} : vve_tpl_truncate($article->{Articles_Model::COLUMN_TEXT_CLEAR}, 300),
      );
         
      if($message != null ){
         $params['message'] = $message;
      }
      if($article->{Articles_Model::COLUMN_TITLE_IMAGE} != null){
         $params['picture'] = vve_tpl_art_title_image($article->{Articles_Model::COLUMN_TITLE_IMAGE});
      }
      
      $scPublisher->publishPost($params);
   }

   public function editPrivateController() {
      $this->checkWritebleRights(); // tady kontrola práv k článku ne tohle

      $modelArt = new Articles_Model();
      $article = $modelArt->where(Articles_Model::COLUMN_URLKEY.' = :urlkey AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc',
            array('urlkey' => $this->getRequest('urlkey'), 'idc' => $this->category()->getId()))->record();

      $form = new Form('art_priv_text_', true);

      $fGrpPrivate = $form->addGroup('privateZone', $this->tr('Privátní zóna'),
              $this->tr('Položky vyditelné pouze určitým uživatelům. Administrátorům jsou tyto informace vždy viditelné.'));

      $ePrivateUsers = new Form_Element_Select('privateUsers', $this->tr('Uživatelé'));
      $ePrivateUsers->setMultiple(true);

      $modelUsers = new Model_Users();
      $users = $modelUsers->usersForThisWeb()->records();
      
      foreach ($users as $user) {
         $ePrivateUsers->setOptions(
              array($user->{Model_Users::COLUMN_NAME} ." ".$user->{Model_Users::COLUMN_SURNAME}
              .' ('.$user->{Model_Users::COLUMN_USERNAME}.') - '.$user->{Model_Users::COLUMN_GROUP_LABEL}.' ('.$user->{Model_Users::COLUMN_GROUP_NAME}.')'
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

   public function editTextController() {
      $this->checkControllRights();
      $form = new Form('list_text_', true);

      $elemText = new Form_Element_TextArea('text', $this->_('Text'));
      $elemText->setLangs();
      $form->addElement($elemText);

      $elemS = new Form_Element_SaveCancel('save');
      $form->addElement($elemS);

      if($form->isSend() AND $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Úpravy textu byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      if($form->isValid()) {
         $textM = new Text_Model();
         
         $text = $this->loadText();
         $text->{Text_Model::COLUMN_TEXT} = $form->text->getValues(); 
         $text->{Text_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($form->text->getValues()); 
         $text->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId(); 
         
         $textM->save($text);

         $this->infoMsg()->addMessage($this->_('Úvodní text byl uložen'));
         $this->link()->route()->reload();
      }

      // načtení textu
      $text = $this->loadText();
      if($text != false) {
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $this->view()->form = $form;
   }
   
   private function loadText() {
      $textM = new Text_Model();
      $text = $textM->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc', array('idc' =>  $this->category()->getId()))->record();
      if($text != false){
         return $text;
      }
      return $textM->newRecord();
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
      
      if(isset($form->id_gallery)){
         $artRecord->{Articles_Model::COLUMN_ID_PHOTOGALLERY} = $form->id_gallery->getValues();
      }
      
      if($form->haveElement('creatorId')){
         $artRecord->{Articles_Model::COLUMN_ID_USER} = $form->creatorId->getValues();
      } else {
         $artRecord->{Articles_Model::COLUMN_ID_USER} = Auth::getUserId();
      }
      
      $artRecord->{Articles_Model::COLUMN_CONCEPT} = $form->concept->getValues();
      $artRecord->{Articles_Model::COLUMN_AUTHOR} = $form->creatorOther->getValues();
      $artRecord->{Articles_Model::COLUMN_PLACE} = $form->place->getValues();
      if(isset($form->priority)){
         $artRecord->{Articles_Model::COLUMN_PRIORITY} = $form->priority->getValues();
         if((int)$form->priority->getValues() != 0){
            $artRecord->{Articles_Model::COLUMN_PRIORITY_END_DATE} = $form->priorityEndDate->getValues();
         }
      }

      // add time
      if($artRecord->isNew()){
         $artRecord->{Articles_Model::COLUMN_ADD_TIME} = new DateTime();
      } 
      if(isset($form->created_date)){
         $artRecord->{Articles_Model::COLUMN_ADD_TIME} = new DateTime($form->created_date->getValues().' '.$form->created_time->getValues());
      } 


      $artRecord->{Articles_Model::COLUMN_ID_USER_LAST_EDIT} = Auth::getUserId();
      $artRecord->{Articles_Model::COLUMN_EDIT_TIME} = new DateTime();

      if(isset ($form->image) && !empty($form->image->getValues())){
         $img = $form->image->getValues();
         $artRecord->{Articles_Model::COLUMN_TITLE_IMAGE} = $img['name'];
      }
      
      /* SAVE ARTICLE */
      $lastId = $model->save($artRecord);
      
      /* TAGS */
      // smazání předchozích spojení
      $modelTagsArtConn = new Articles_Model_TagsConnection();
      $modelTagsArtConn
         ->where(Articles_Model_TagsConnection::COLUMN_ID_ARTICLE." = :ida", array('ida' => $artRecord->getPK()))
         ->delete();
      
      $tags = $form->tags->getValues();
      if(!empty($tags) && is_array($tags)){
         $tags = array_unique($tags);
         // projít tagy jestli existují
         $modelTags = new Articles_Model_Tags();
         foreach ($tags as $tag) {
            $tag = strtolower(htmlspecialchars( $tag ));
            // je tag v db?
            $tagRecord = $modelTags->where(Articles_Model_Tags::COLUMN_NAME." = :tagname", array('tagname' => $tag))->record();
            if(!$tagRecord){
               // tag není v db
               $tagRecord = $modelTags->newRecord();
               $tagRecord->{Articles_Model_Tags::COLUMN_NAME} = $tag;
               $modelTags->save($tagRecord);
            }
            
            // uložení spojení mezi tagem a článkem
            $tagArtConnRec = $modelTagsArtConn->newRecord();
            $tagArtConnRec->{Articles_Model_TagsConnection::COLUMN_ID_ARTICLE} = $artRecord->getPK();
            $tagArtConnRec->{Articles_Model_TagsConnection::COLUMN_ID_TAG} = $tagRecord->getPK();
            $modelTagsArtConn->save($tagArtConnRec);
         }
      }
      
      $this->infoMsg()->addMessage($this->tr('Uloženo'));
      return $lastId;
   }

   /**
    * Metoda vygeneruje url klíče
    * @param <type> $urlkeys
    * @param <type> $names
    * @return <type>
    */
   protected function createUrlKey($urlkeys, $names, $id = 0) {
      foreach ($urlkeys as $lang => $key) {
         if($key == null AND $names[$lang] == null) {
            $urlkeys[$lang] = null;
         } else if($key == null) {
            $urlkeys[$lang] = $names[$lang];
         } else {
            $urlkeys[$lang] = $key;
         }
         // kontrola unikátnosti
         $urlkeys[$lang] = $this->createUniqueUrlKey($urlkeys[$lang], $lang, $id);
      }
      return $urlkeys;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm(Model_ORM_Record $article = null) {
      $form = new Form('article_', true);

      $fGrpTexts = $form->addGroup('texts', $this->tr('Texty'));

      $iName = new Form_Element_Text('name', $this->tr('Nadpis'));
      $iName->setLangs();
      $iName->addValidation(New Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      $form->addElement($iName, $fGrpTexts);

      $iAnnot = new Form_Element_TextArea('annotation', $this->tr('Anotace'));
      $iAnnot->setSubLabel($this->tr('Krátký popis položky. Nejlépe tři věty.'));
      $iAnnot->setLangs();
      $form->addElement($iAnnot, $fGrpTexts);

      $iText = new Form_Element_TextArea('text', $this->tr('Text'));
      $iText->setLangs();
      if($this->allowEmptyText == false) {
         $iText->addValidation(New Form_Validator_NotEmpty(null, Locales::getDefaultLang(true)));
      }
      $form->addElement($iText, $fGrpTexts);
      
      $iTags = new Form_Element_Tags('tags', $this->tr('Štítky'));
      $iTags->setItemsUrl($this->link()->route('getTags'));
      // load available tags
      
      $form->addElement($iTags, $fGrpTexts);

      // připojení fotogalerie
      if(in_array($this->module()->getName(), array('articles', 'news')) && Face::getCurrent()->getParam('connectPhotogallery', 'articles')){
         $modelGalleries = new Articles_Model();
         
         $galeries = $modelGalleries
             ->joinFK(Articles_Model::COLUMN_ID_CATEGORY, array(Model_Category::COLUMN_NAME))
             ->where(Model_Category::COLUMN_MODULE." = :module", array('module' => 'photogalerymed'))
             ->order(Articles_Model::COLUMN_NAME)
             ->records();
         
         if(!empty($galeries)){
            $eGal = new Form_Element_Select('id_gallery', $this->tr('Připojení fotogalerie'));
            
            $eGal->addOption($this->tr('Bez propojení'), null);
            foreach ($galeries as $gal) {
               $eGal->addOption($gal->{Articles_Model::COLUMN_NAME}.' - ('.Utils_DateTime::fdate('%x', $gal->{Articles_Model::COLUMN_ADD_TIME}).')'
                   , (int)$gal->getPK(), (string)$gal->{Model_Category::COLUMN_NAME});
            }
            $form->addElement($eGal, $fGrpTexts);
         }
      }
      
      $fGrpParams = $form->addGroup('params', $this->tr('Parametry'));

      // $iUrlKey = new Form_Element_Text('urlkey', $this->tr('Url klíč'));
      $iUrlKey = new Form_Element_UrlKey('urlkey', $this->tr('Url klíč'));
      $iUrlKey->setAdvanced(true);
      $iUrlKey->setUpdateFromElement($iName)->setCheckingUrl($this->link()->route('checkUrlkey'));
      if($article != null){
         $iUrlKey->setCheckParam('id', (int)$article->{Articles_Model::COLUMN_ID})->setAutoUpdate(false);
      }
      $iUrlKey->setLangs();
      $iUrlKey->setSubLabel($this->tr('Pokud není url klíč zadán, je generován automaticky'));
      $form->addElement($iUrlKey, $fGrpParams);

      $iKeywords = new Form_Element_Text('metaKeywords', $this->tr('Klíčová slova'));
      $iKeywords->setSubLabel($this->tr('Pokud nejsou zadána, pokusí se generovat ze štítků.'));
      $iKeywords->setLangs();
      $form->addElement($iKeywords, $fGrpParams);

      $iDesc = new Form_Element_TextArea('metaDesc', $this->tr('Popisek'));
      $iDesc->setAdvanced(true);
      $iDesc->setLangs();
      $iDesc->setSubLabel($this->tr('Pokud není zadán pokusí se použít anotaci, jinak zůstne prázdný.'));
      $form->addElement($iDesc, $fGrpParams);
      
      $elemImage = new Form_Element_ImageSelector('image', $this->tr('Titulní obrázek'));
      $elemImage->setUploadDir(Utils_CMS::getTitleImagePath(false));
      $form->addElement($elemImage, $fGrpParams);
      
      $fGrpPublic = $form->addgroup('public', $this->tr('Parametry zveřejnění a vytvoření'));
      
      if($this->getRights()->isControll()){
         $ePriority = new Form_Element_Select('priority', $this->tr('Priorita'));
         $ePriority->setOptions(array(
               $this->tr('Nízká (-1)') => -1,
               $this->tr('Normální (0)') => 0,
               $this->tr('Vysoká (1)') => 1,
               $this->tr('Urgentní (2)') => 2,
               $this->tr('Okamžitá (3)') => 3,
               ));
         $ePriority->setValues(0);
         $ePriority->setAdvanced(true);
         $form->addElement($ePriority, $fGrpPublic);
      
         $ePriorityEndDate = new Form_Element_Text('priorityEndDate', $this->tr('Konec priority'));
         $ePriorityEndDate->addValidation(new Form_Validator_Date());
         $ePriorityEndDate->addFilter(new Form_Filter_DateTimeObj());
         $ePriorityEndDate->setSubLabel($this->tr('Do kdy bude položka označena prioritou'));
         $ePriorityEndDate->setAdvanced(true);
         $form->addElement($ePriorityEndDate, $fGrpPublic);
      }
      
      // pokud jsou práva pro kontrolu, přidám položku s uživateli, kterí mohou daný článek vytvořit
      if($this->category()->getRights()->isControll()){
         $eCreator = new Form_Element_Select('creatorId', $this->tr('Položka vytvořena uživatelem'));
         $modelUsers = new Model_Users();
         foreach ($modelUsers->records() as $user){
            $name = $user->{Model_Users::COLUMN_USERNAME};
            $name .= ' ('.$user->{Model_Users::COLUMN_NAME}.' '.$user->{Model_Users::COLUMN_SURNAME}.')';
            $eCreator->setOptions(array($name => $user->{Model_Users::COLUMN_ID}),true);
         }
         if($article == null){
            $eCreator->setValues(Auth::getUserId());
         }
         $eCreator->setAdvanced(true);
         $form->addElement($eCreator, $fGrpPublic);
      }
         
      $eCreatorOther = new Form_Element_Text('creatorOther', $this->tr('Autor'));
      $eCreatorOther->setSubLabel($this->tr('Autor položky, pokud není zařazen v systému.'));
//      $eCreatorOther->setAdvanced(true);
      $form->addElement($eCreatorOther, $fGrpPublic);
      
      $ePlace = new Form_Element_Text('place', $this->tr('Místo'));
      $ePlace->setSubLabel($this->tr('Např.: místo odkud článek pochází (Praha, Brno, Londýn, ...).'));
//      $ePlace->setAdvanced(true);
      $form->addElement($ePlace, $fGrpPublic);

      $iConcept = new Form_Element_Checkbox('concept', $this->tr('Koncept'));
      $iConcept->setSubLabel($this->tr('Pokud je položka koncept, je viditelná pouze autorovi a administrátorům.'));
      $iConcept->setValues(false);
      $form->addElement($iConcept, $fGrpPublic);

      if($this->getRights()->isControll()){
         $eCreatedDate = new Form_Element_Text('created_date', $this->tr('Datum vytvoření'));
         $eCreatedDate->setValues(vve_date("%x"));
         $eCreatedDate->setSubLabel($this->tr('Pokud bude datum v budoucnosti, dojde k zveřejnění až v toto datum.'));
         $eCreatedDate->addValidation(new Form_Validator_NotEmpty());
         $eCreatedDate->addValidation(new Form_Validator_Date());
//         $eCreatedDate->setAdvanced(true);
         $form->addElement($eCreatedDate, $fGrpPublic);

         $eCreatedTime = new Form_Element_Text('created_time', $this->tr('Čas vytvoření'));
         $eCreatedTime->setValues(vve_date("%H:%i"));
         $eCreatedTime->addValidation(new Form_Validator_NotEmpty());
         $eCreatedTime->addValidation(new Form_Validator_Time());
         $eCreatedTime->setAdvanced(true);
         $form->addElement($eCreatedTime, $fGrpPublic);
      }
      
      // doplnění id
      if($article != null){
         $iIdElem = new Form_Element_Hidden('art_id');
         $iIdElem->addValidation(new Form_Validator_IsNumber());
         $iIdElem->setValues($article->{Articles_Model::COLUMN_ID});
         $form->addElement($iIdElem, $fGrpPublic);
      }

      $iSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($iSubmit);
      
      $socialnetworks = new Component_SocialNetwork();
      if($socialnetworks->isPublishAvailable()){
         $fGrpSocNet = $form->addGroup('socialNetworks', $this->tr('Sociální sítě'));
         
         $elemSNPublish = new Form_Element_Checkbox('socNetPublish', $this->tr('Publikovat'));
         $elemSNPublish->setSubLabel($this->tr('Zveřejnit tuto položku na sociálních sítích.'));
         $form->addElement($elemSNPublish, $fGrpSocNet);
         
         $elemSNMessage = new Form_Element_Text('socNetMessage', $this->tr('Zpráva'));
         $form->addElement($elemSNMessage, $fGrpSocNet);
         
         /** 
          * @todo selektor, do kterých sítí publikovat 
          */
      }

      if($article instanceof Model_ORM_Record){
         $form->name->setValues($article->{Articles_Model::COLUMN_NAME});
         $form->text->setValues($article->{Articles_Model::COLUMN_TEXT});
         $form->metaKeywords->setValues($article->{Articles_Model::COLUMN_KEYWORDS});
         $form->metaDesc->setValues($article->{Articles_Model::COLUMN_DESCRIPTION});
         $form->annotation->setValues($article->{Articles_Model::COLUMN_ANNOTATION});
         $form->urlkey->setValues($article->{Articles_Model::COLUMN_URLKEY});
         if(isset($form->id_gallery)){
            $form->id_gallery->setValues($article->{Articles_Model::COLUMN_ID_PHOTOGALLERY});
         }
         if( isset($form->created_date) ){
            $addTime = new DateTime($article->{Articles_Model::COLUMN_ADD_TIME});
            $form->created_date->setValues(vve_date('%x',$addTime));
            $form->created_time->setValues(vve_date('%H:%i',$addTime));
         }
         if(isset($form->image)){
            $form->image->setValues($article->{Articles_Model::COLUMN_TITLE_IMAGE});
         }
         if(isset($form->creatorId)){
            $form->creatorId->setValues($article->{Articles_Model::COLUMN_ID_USER});
         }
         $form->creatorOther->setValues($article->{Articles_Model::COLUMN_AUTHOR});
         $form->place->setValues($article->{Articles_Model::COLUMN_PLACE});
         $form->concept->setValues($article->{Articles_Model::COLUMN_CONCEPT});
         if(isset($form->priority)){
            $form->priority->setValues($article->{Articles_Model::COLUMN_PRIORITY});
            if($article->{Articles_Model::COLUMN_PRIORITY} != 0){
               $form->priorityEndDate->setValues(
                     vve_date('%x', new DateTime($article->{Articles_Model::COLUMN_PRIORITY_END_DATE})));
            }
         }
          
         // TAGY
         $modelTagConnection = new Articles_Model_TagsConnection();
         $tags = $modelTagConnection
         ->joinFK(Articles_Model_TagsConnection::COLUMN_ID_TAG)
         ->where(Articles_Model_TagsConnection::COLUMN_ID_ARTICLE." = :ida",
               array('ida' => $article->getPK()) )
               ->records();
         if($tags){
            $tagsStr = array();
            foreach ($tags as $tag) {
               $tagsStr[] = $tag->{Articles_Model_Tags::COLUMN_NAME};
            }
            $form->tags->setValues(implode(',', $tagsStr));
         }
      }
      
      if($form->isSend() ){
         if($form->save->getValues() == false) {
            $this->link()->route( $article instanceof Model_ORM ? 'detail' : null)->reload();
         }
         if(isset($form->priorit) && $form->priority->getValues() != 0){
            $form->priorityEndDate->addValidation(new Form_Validator_NotEmpty(
               $this->tr('Pokud je priorita nastavena, musí být zadáno také datum ukončení této priority')));
         }
      }
      
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

   public function exportArticleController($urlkey, $output){
      $this->checkReadableRights();
      $model= new Articles_Model();
      $article = $model->where(Articles_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Articles_Model::COLUMN_URLKEY.' = :ukey',
         array('idc' => $this->category()->getId(), 'ukey' => $urlkey))->record();
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

   public function checkUrlkeyController()
   {
      $this->checkReadableRights();
      
      $this->view()->urlkey = $this->createUniqueUrlKey(
         $_POST['key'], 
         isset($_POST['lang']) ? $_POST['lang'] : Locales::getLang(),
         isset($_POST['id']) ? (int)$_POST['id'] : 0
         );
   }
   
   protected function createUniqueUrlKey($key, $lang, $id = 0)
   {
      if($key == null){
         return null;
      }
      
      $model = new Articles_Model();
      $step = 1;
      $key = vve_cr_url_key($key);
      $origPart = $key;
      
      $where = '('.$lang.')'.Articles_Model::COLUMN_URLKEY.' = :ukey AND '.Articles_Model::COLUMN_ID.' != :id';
      $keys = array('ukey' => $key, 'id' => (int)$id);// when is nul bad sql query is created
      
      while ($model->where($where, $keys)->count() != 0 ) {
         $keys['ukey'] = $origPart.'-'.$step;
         $step++;
      }
      
      return $keys['ukey'];
   }

   /**
    * Metoda pro vrácení tagů pro autocomplete
    */
   public function getTagsController() 
   {
      // get parametr 'term' s písmeny tagu
      $term = $this->getRequestParam('q');
      $modelTags = new Articles_Model_Tags();
      
      if($term != null ){
//         $modelTags->where(Articles_Model_Tags::COLUMN_NAME." LIKE :term", array('term' => $term.'%'));
      }
      
      $tags = $modelTags->records();
      $respond = array();
      if ($tags){
         foreach ($tags as $tag) {
            $respond[] = $tag->{Articles_Model_Tags::COLUMN_NAME};
         }
      }
      $this->view()->tags = $respond;
   }
   
   /**
    * Seznam tagů pro jqgrid
    */
   public function listTagsController() {
      $this->checkReadableRights();
      // objekt komponenty JGrid
      $jqGrid = new Component_JqGrid();
      $jqGrid->request()->setDefaultOrderField(Articles_Model_Tags::COLUMN_NAME);
      // search
      
      $model = new Articles_Model_Tags();

      $order = Model_ORM::ORDER_ASC;
      if($jqGrid->request()->order == 'desc'){
         $order = Model_ORM::ORDER_DESC;
      }
      
      switch ($jqGrid->request()->orderField) {
         case Articles_Model_Tags::COLUMN_ID:
            $model->order(array(Articles_Model_Tags::COLUMN_ID => $order));
            break;
         case 'tag_used':
            $model->order(array('tag_used' => $order));
            break;
         case Articles_Model_Tags::COLUMN_NAME:
         default:
            $model->order(array(Articles_Model_Tags::COLUMN_NAME => $order));
            break;
      }
      
      if ($jqGrid->request()->isSearch()) {
//         $count = $modelAddresBook->searchCount($jqGrid->request()->searchString(),
//            (int)$this->getRequestParam('idgrp', Mails_Model_Groups::GROUP_ID_ALL),
//            $jqGrid->request()->searchField(),$jqGrid->request()->searchType());
//         $jqGrid->respond()->setRecords($count);
//
//         $book = $modelAddresBook->search($jqGrid->request()->searchString(),
//            (int)$this->getRequestParam('idgrp', Mails_Model_Groups::GROUP_ID_ALL),
//            $jqGrid->request()->searchField(),$jqGrid->request()->searchType(),
//            ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage(),
//            $jqGrid->request()->rows, $jqGrid->request()->orderField, $jqGrid->request()->order);
      } else {
      // list
//         $groups = $this->getAllowedGroups($jqGrid);
      }
      
      $jqGrid->respond()->setRecords($model->count());
      
      $fromRow = ($jqGrid->request()->page - 1) * $jqGrid->respond()->getRecordsOnPage();
      
      $model
         ->columns(array('*', 'tag_used' => 'COUNT(ttagsc.'.Articles_Model_TagsConnection::COLUMN_ID_ARTICLE.')'))
         ->join(Articles_Model_Tags::COLUMN_ID, array('ttagsc' => "Articles_Model_TagsConnection"), Articles_Model_TagsConnection::COLUMN_ID_TAG, array())
         ->groupBy(array(Articles_Model_Tags::COLUMN_ID));
      
      $tags = $model->limit($fromRow, $jqGrid->request()->rows)->records(PDO::FETCH_OBJ);
      // out
      foreach ($tags as $tag) {
         array_push($jqGrid->respond()->rows, 
            array(
                  'id' => $tag->{Articles_Model_Tags::COLUMN_ID},
                  Articles_Model_Tags::COLUMN_ID => $tag->{Articles_Model_Tags::COLUMN_ID},
                  Articles_Model_Tags::COLUMN_NAME => $tag->{Articles_Model_Tags::COLUMN_NAME},
                  'tag_used' => $tag->tag_used
                     ));
      }
      $this->view()->respond = $jqGrid->respond();
   }
   
   /**
    * Editační stránky tagů
    */
   public function editTagsController()
   {
      $this->checkControllRights();
   } 
   
   /**
    * Obsluhe aditace JQGrid 
    */
   public function editTagController() 
   {
      $this->checkWritebleRights();
      $model = new Articles_Model_Tags();
      $this->view()->allOk = false;
      // část komponenty jqgrid pro formy
      $jqGridReq = new Component_JqGrid_FormRequest();
      $record = $model->newRecord();
      
      switch ($jqGridReq->getRequest()) {
         case Component_JqGrid_FormRequest::REQUEST_TYPE_ADD:
            $jqGridReq->id = null;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_EDIT:
            // kontrola položek
            if($jqGridReq->{Articles_Model_Tags::COLUMN_NAME} == null){
               $this->errMsg()->addMessage($this->tr('Nebyly zadány všechny povinné údaje'));
               return;
            }
            if($jqGridReq->id != null){
               $record = $model->record($jqGridReq->id);
            }
            
            $record->{Articles_Model_Tags::COLUMN_NAME} = strtolower($jqGridReq->{Articles_Model_Tags::COLUMN_NAME});
            
            $model->save($record);
            $this->infoMsg()->addMessage($this->tr('Štítek byl uložen'));
            break;
         case Component_JqGrid_FormRequest::REQUEST_TYPE_DELETE:
            $model = new Articles_Model_Tags();
            foreach ($jqGridReq->getIds() as $id) {
               $model->where(Articles_Model_Tags::COLUMN_ID." = :id", array('id' => $id))->delete();
            }
            $this->infoMsg()->addMessage($this->tr('Vybrané štítky byly smazány'));
            break;
         default:
            $this->errMsg()->addMessage($this->tr('Nepodporovaný typ operace'));
            break;
      }
      if ($this->errMsg()->isEmpty()) {
         $this->view()->allOk = true;
      }
   }
   
   /**
    * přesun článku
    */
   public function moveController(){
      $this->checkWritebleRights();
      $modelArt = new Articles_Model();      
      $modelCats = new Model_Category();
      
      $article = $modelArt
            ->where(Articles_Model::COLUMN_URLKEY.' = :urlkey AND '.Articles_Model::COLUMN_ID_CATEGORY.' = :idc',
               array('urlkey' => $this->getRequest('urlkey'), 'idc' => $this->category()->getId()))
            ->record();
      
      if(!$article){
         return false;
      }
      
      $form = new Form('article_move_');
      
      $eNewCat = new Form_Element_Select('newcat', $this->tr('Přesunout do'));
      $form->addElement($eNewCat);
      
      $eSubmit = new Form_Element_SaveCancel('save');
      // načtení kategorií
      $allowedCategories = array(
            "'articles'",
            "'articleswgal'",
            "'photogalerymed'",
            "'news'",
            );
      $cats = $modelCats
         ->where(Model_Category::COLUMN_MODULE." IN (".implode(',', $allowedCategories).")", array())
         ->order(array(Model_Category::COLUMN_NAME => Model_ORM::ORDER_ASC))
         ->records();
      
      foreach ($cats as $cat) {
         $eNewCat->setOptions(array(
               $cat->{Model_Category::COLUMN_NAME}.' - '.$cat->{Model_Category::COLUMN_ID} => $cat->{Model_Category::COLUMN_ID}), 
            true);
      }
      
      $form->addElement($eSubmit);

      if($form->isSend() && $form->save->getValues() == false){
         $this->link(true)
               ->route('detail')
               ->reload();
      }

      if($form->isValid()){
         $cat = new Category((int)$form->newcat->getValues());
         
         // změna id kategorie
         $article->{Articles_Model::COLUMN_ID_CATEGORY} = (int)$form->newcat->getValues();
         $modelArt->save($article);
         
         // přesun datového adresáře
         $dir = new FS_Dir($this->module()->getDataDir(false).$article->{Articles_Model::COLUMN_URLKEY}[Locales::getDefaultLang()]);
         if($dir->exist()){
            $dir->rename($cat->getModule()->getDataDir().$article->{Articles_Model::COLUMN_URLKEY}[Locales::getDefaultLang()]);
         }
         
         // přesměrování do nové kategorie
         $this->link(true)
            ->category($cat->getUrlKey())->route('detail', array('urlkey' => $article->{Articles_Model::COLUMN_URLKEY}))
            ->reload();         
      }
      
      $this->view()->article = $article;
      $this->view()->form = $form;
   }
   
   /**
    * Metoda odešle upozornění na přidaný článek
    * @param Model_ORM_Record $article
    */
   protected function sendNotify(Model_ORM_Record $article) 
   {
      $email = new Email(true);
      $email->setSubject( sprintf( $this->tr('Přidána nová položka do stránek %s'), VVE_WEB_NAME ) );
      $cnt = '<p>'
            .sprintf( $this->tr('Byla přidána nová položka do stránek %1$s do kategorie %2$s'), VVE_WEB_NAME, 
                  '<a href="'.$this->link(true).'">'.$this->category()->getName().'</a>' )
            ."</p>";
      
      $cnt .= '<p>'.sprintf( $this->tr('Autor: %1$s, datum přidání: %2$s'), Auth::getUserName(), vve_date("%x, %X") )."</p>";
      
      $cnt .= '<h1><a href="'.$this->link(true)->route('detail', array('urlkey' => $article->{Articles_Model::COLUMN_URLKEY})).'">'
              .htmlspecialchars($article->{Articles_Model::COLUMN_NAME})."</a></h1>";
      
      $cnt .= '<div style="margin-bottom: 10px;">'.$article->{Articles_Model::COLUMN_ANNOTATION}.'</div>';
      $cnt .= '<div style="margin-bottom: 10px;">'.$article->{Articles_Model::COLUMN_TEXT}.'</div>';
      
      $email->setContent( Email::getBaseHtmlMail($cnt) );
      
      $str = $this->category()->getParam(self::PARAM_NOTIFY_RECIPIENTS, null);
      
      if(empty($str)) {
         return;         
      }
      $ids = explode(';', $str);
      
      // maily adminů - z uživatelů
      $modelusers = new Model_Users();
      
      $notifyMails = array(); 
      foreach ($ids as $id) {
         $user = $modelusers->record($id);
         if($user->{Model_Users::COLUMN_NAME} != null){
            $notifyMails[$user->{Model_Users::COLUMN_MAIL}] = $user->{Model_Users::COLUMN_NAME}." ".$user->{Model_Users::COLUMN_SURNAME};
         } else {
            $notifyMails[] = $user->{Model_Users::COLUMN_MAIL};
         }
      }
      if(empty($notifyMails)){
         return;
      }
      
      $email->addAddress($notifyMails);
      $failures = array();
      $email->send($failures);
      // log failures
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

      $elemDisableList = new Form_Element_Checkbox('disableList', $this->tr('Vypnout úvodní seznam'));
      $elemDisableList->setAdvanced(true);
      $elemDisableList->setSubLabel($this->tr('Pokud je list vypnut, stránka je automaticky přesměrována na první položku. V detailu je pak načten seznam položek.'));
      if(isset($settings[self::PARAM_DISABLE_LIST])) {
         $elemDisableList->setValues($settings[self::PARAM_DISABLE_LIST]);
      }
      $form->addElement($elemDisableList, $fGrpView);

      $fGrpEditSet = $form->addGroup('editSettings', $this->tr('Úpravy a editace'));

      $elemEditorType = new Form_Element_Select('editor_type', $this->tr('Typ editoru'));
      $elemEditorType->setAdvanced(true);
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

      
//      $fGrpAdv = $form->addGroup('advanced', $this->tr('Připojení kateogriií')); 
      $elemMountCats = new Form_Element_Select('mountedCats', $this->tr('Připojit kategorie'));
      $elemMountCats->setAdvanced(true);
      $elemMountCats->setMultiple();
      
      $modelCats = new Model_Category();
      $cats = $modelCats
         ->where(Model_Category::COLUMN_MODULE." IN ('articles', 'news', 'articleswgal', 'photogalerymed')", array())->records();
      
      foreach ($cats as $cat) {
         if($cat->{Model_Category::COLUMN_ID} == $this->category()->getId()) continue;
         
         $elemMountCats->setOptions(array( 
            $cat->{Model_Category::COLUMN_NAME}." - (ID: ".$cat->{Model_Category::COLUMN_ID}.")" => $cat->{Model_Category::COLUMN_ID}),
            true);
      }
      if(isset($settings[self::PARAM_MOUNTED_CATS])) {
         $elemMountCats->setValues(explode(';', $settings[self::PARAM_MOUNTED_CATS]));
      }
      $form->addElement($elemMountCats, $fGrpView);
      
//      $fGrpNewPosts = $form->addGroup('notify', $this->tr('Nové příspěvky')); 
      $elemNotify = new Form_Element_Select('sendNotify', 'Odeslat upozornění při přidání položky');
      $elemNotify->setAdvanced(true);
      $elemNotify->setSubLabel($this->tr('Odešle zadaným uživatelům upozornění na nově přidanou položku <strong>obyčejným</strong> uživatelem'));
      // načtení uživatelů
      $modelUsers = new Model_Users();
      $users = $modelUsers->usersForThisWeb(true)->records(PDO::FETCH_OBJ);
      $usersIds = array();
      foreach ($users as $user) {
         if($user->{Model_Users::COLUMN_MAIL} != null){
            $usersIds[$user->{Model_Users::COLUMN_NAME} ." ".$user->{Model_Users::COLUMN_SURNAME}
              .' ('.$user->{Model_Users::COLUMN_USERNAME}.') - '.$user->{Model_Users::COLUMN_GROUP_LABEL}
              .' ('.$user->{Model_Users::COLUMN_GROUP_NAME}.')'] = $user->{Model_Users::COLUMN_ID};
         }
      }
      $elemNotify->setOptions($usersIds);
      $elemNotify->setMultiple();
      $elemNotify->html()->setAttrib('size', 4);
      if (isset($settings[self::PARAM_NOTIFY_RECIPIENTS])) {
         $elemNotify->setValues(explode(';', $settings[self::PARAM_NOTIFY_RECIPIENTS]));
      }

      $form->addElement($elemNotify, $fGrpEditSet);

      $fGrpEMail = $form->addGroup('emailLoad', $this->tr('Načítání z e-mailu'),
         $this->tr("Pokud je nastaven přístup k e-malové schránce, stránka automaticky načítá z tét schránky nové
         e-maily a vytváří z nich položky. Název e-mailové schránky nastavte tak, aby nebyla odhadnutelná
         (např. blog5685stranky@domena.cz) a držte ji v tajnosti. V opačném případě se Vám mohou zobrazovat nechtěné příspěvky."));

      $elemMailName = new Form_Element_Text('mailName', $this->tr('E-mail'));
      $elemMailName->setAdvanced(true);
      $elemMailName->addValidation(new Form_Validator_Email());
      $elemMailName->setValues(isset($settings[self::PARAM_MAIL_NAME]) ? $settings[self::PARAM_MAIL_NAME] : null);
      $form->addElement($elemMailName, $fGrpEMail);

      $elemMailPass = new Form_Element_Text('mailPass', $this->tr('Heslo schránky'));
      $elemMailPass->setAdvanced(true);
      $elemMailPass->setSubLabel($this->tr('Heslo je při úpravě zkryto'));
      $form->addElement($elemMailPass, $fGrpEMail);

      $elemMailServer = new Form_Element_Text('mailServer', $this->tr('Server schránky'));
      $elemMailServer->setAdvanced(true);
      $elemMailServer->setSubLabel($this->tr('Např.: imap.serve.cz (pro jiný port: imap.server.cz:995). Pokud není zadán, apliakce se pokusí server detekovat z e-mailu s předponou imap.'));
      $elemMailServer->setValues( isset($settings[self::PARAM_MAIL_SERVER]) ? $settings[self::PARAM_MAIL_SERVER] : null);
      $form->addElement($elemMailServer, $fGrpEMail);

      $elemMailSecKey = new Form_Element_Text('mailSecKey', $this->tr('Bezpečnostní řetězec'));
      $elemMailSecKey->setAdvanced(true);
      $elemMailSecKey->setSubLabel($this->tr('Nasatvte na náhodný řetězec. E-maily, které se mají načíst potom musí tento řetězec obsahovat, jinak budou odmítnuty.'));
      $elemMailSecKey->setValues( isset($settings[self::PARAM_MAIL_SECURE_KEY]) ? $settings[self::PARAM_MAIL_SECURE_KEY] : null);
      $form->addElement($elemMailSecKey, $fGrpEMail);

      
      $fGrpPrivate = $form->addGroup('privateZone', $this->tr('Privátní zóna'), $this->tr("Privátní zóna povoluje
         vložení textů, které jsou viditelné pouze vybraným uživatelům. U každé položky tak
         vznikne další textové okno s výběrem uživatelů majících přístup k těmto textům."));

      $elemAllowPrivateZone = new Form_Element_Checkbox('allow_private_zone',
              $this->tr('Povolit privátní zónu'));
      $elemAllowPrivateZone->setAdvanced(true);
      $form->addElement($elemAllowPrivateZone, $fGrpPrivate);
      if(isset($settings[self::PARAM_PRIVATE_ZONE])) {
         $form->allow_private_zone->setValues($settings[self::PARAM_PRIVATE_ZONE]);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scroll'] = (int)$form->scroll->getValues();
         $settings[self::PARAM_SORT] = $form->sort->getValues();
         $settings[self::PARAM_DISABLE_LIST] = $form->disableList->getValues();
//          $settings[self::PARAM_SHOW_CATS] = $form->showCats->getValues();
         $settings['discussion_allow'] = $form->discussion_allow->getValues();
         $settings['discussion_fcb'] = $form->discussion_fcb->getValues();
         $settings['discussion_not_public'] = $form->discussion_not_public->getValues();
         $settings['discussion_closed'] = $form->discussion_closed->getValues();
         $settings[self::PARAM_PRIVATE_ZONE] = (bool)$form->allow_private_zone->getValues();

         $mCats = $form->mountedCats->getValues() == null ? array() : $form->mountedCats->getValues();

         $settings[self::PARAM_MOUNTED_CATS] = is_array($mCats) ? implode(';', $mCats) : null;
         $settings[self::PARAM_NOTIFY_RECIPIENTS] = is_array($form->sendNotify->getValues()) ? implode(';', $form->sendNotify->getValues()) : null;

         $settings[self::PARAM_MAIL_NAME] = $form->mailName->getValues();
         if($form->mailPass->getValues() != null){
            $settings[self::PARAM_MAIL_PASSWORD] = $form->mailPass->getValues();
         }
         $settings[self::PARAM_MAIL_SERVER] = $form->mailServer->getValues();
         $settings[self::PARAM_MAIL_SECURE_KEY] = $form->mailSecKey->getValues();
      }
   }
   
   /* Autorun metody */
   
   public static function AutoRunDaily() 
   {
      $curDate = new DateTime();
      $curDate->setTime(0, 0);
      $isSomeEdit = false;
      $model = new Articles_Model();
      
      $model
         ->where(Articles_Model::COLUMN_PRIORITY.' != 0 '
            .'AND '.Articles_Model::COLUMN_PRIORITY_END_DATE.' < CURDATE()', array())
         ->update(array(
               Articles_Model::COLUMN_PRIORITY => 0,
               Articles_Model::COLUMN_PRIORITY_END_DATE => NULL,
               ));
   }

   public static function AutoRunHourly()
   {
      // načtou se kategorie s modulem articles
      $cats = self::_AutorunGetCategories();

      // procházení kategorií

      foreach($cats as $c){
         $category = new Category(null, false, $c);

         if($category->getParam(self::PARAM_MAIL_NAME) == null){
            continue;
         }

         $server = $category->getParam(self::PARAM_MAIL_SERVER);
         $email = $category->getParam(self::PARAM_MAIL_NAME);

         // odvození z e-mailu
         if($server == null){
            $server = 'imap.'.substr(strrchr($email, "@"), 1).":993";
         }
         // nemá předán port, použije se výchozí
         else if(!preg_match('/:[0-9]+/', $server)){
            $server .= ':993';
         }

         // připojení k mailu
         $imap = imap_open('{'.$server.'/imap/ssl/novalidate-cert}INBOX',
            $email, $category->getParam(self::PARAM_MAIL_PASSWORD));
         if (!$imap) {
            Log::msg(imap_last_error());
         }

         $info = imap_mailboxmsginfo($imap);

         $emails = imap_search($imap, 'UNSEEN');
         if(!$emails || empty($email)){
            continue;
         }
         foreach ($emails as $email_id) {
            try {
               if($category->getParam(self::PARAM_MAIL_SECURE_KEY) != null){
                  $cnt = imap_fetchbody($imap, $email_id, '1', FT_PEEK);
                  if(strpos($cnt, $category->getParam(self::PARAM_MAIL_SECURE_KEY)) === false){
                     continue;
                  }
               }
               $msg = self::_AutorunParseMessage($email_id, $imap);
               if($category->getParam(self::PARAM_MAIL_SECURE_KEY) != null){
                  $msg['plain'] = str_replace($category->getParam(self::PARAM_MAIL_SECURE_KEY), '', $msg['plain']);
                  $msg['html'] = str_replace($category->getParam(self::PARAM_MAIL_SECURE_KEY), '', $msg['html']);
               }
               self::_AutorunProcessMessage($category, $msg);

//               imap_delete($imap, $email_id, FT_UID);

            } catch (Exception $e) {
               Log::msg($e->getTraceAsString(), 'article-loadmail');
            }
         }
         // čištění
         imap_expunge($imap);

      }
   }

   protected static function _AutorunGetCategories()
   {
      return Model_Category::getCategoryListByModule(array('articles', 'news'));
   }

   protected static function _AutorunProcessMessage(Category $category, $msg)
   {
      // uložení článku
      $model = new Articles_Model();
      $article = $model->newRecord();

      $article->{Articles_Model::COLUMN_NAME} = $msg['subject'];
      $article->{Articles_Model::COLUMN_TEXT} = $msg['plain'];
      if($msg['html'] != null){
         Loader::loadExternalLib('htmlpurifier');

         $purifierConfig = HTMLPurifier_Config::createDefault();
         $purifierConfig->set('HTML.TidyLevel', 'heavy' );
         $purifierConfig->set('HTML.Allowed', 'p,b,strong,a[href|title],i,em,span,img[src|alt],div,address,h1,h2,h3,h4,h5,table,tr,td,th,hr,tbody,thead,tfoot');
         $purifierConfig->set('Cache.SerializerPath', AppCore::getAppCacheDir() );
         $purifier = new HTMLPurifier($purifierConfig);
         $article->{Articles_Model::COLUMN_TEXT} = $purifier->purify($msg['html']);
      }

      $article->{Articles_Model::COLUMN_ID_CATEGORY} = $category->getId();

      // zpracování titulního obrázku
      if(!empty($msg['attachments'])){
         $img = reset($msg['attachments']);

         $image = new File(AppCore::getAppCacheDir().$img);

         $image->move(AppCore::getAppDataDir().VVE_ARTICLE_TITLE_IMG_DIR);
//         $image->saveAs(AppCore::getAppDataDir().VVE_ARTICLE_TITLE_IMG_DIR,
//            $category->getParam('TITLE_IMAGE_WIDTH', VVE_ARTICLE_TITLE_IMG_W),
//            $category->getParam('TITLE_IMAGE_HWIGHT', VVE_ARTICLE_TITLE_IMG_H),
//            $category->getParam('TITLE_IMAGE_CROP', VVE_ARTICLE_TITLE_IMG_C));
         $article->{Articles_Model::COLUMN_TITLE_IMAGE} = $image->getName();
      }

      $article->save();

      return $article->getPK();
   }

   protected static function _AutorunParseMessage($msgUID, $connection)
   {
      $structure = imap_fetchstructure($connection, $msgUID);
      $msgInfo = imap_headerinfo($connection, $msgUID);
      $subjects = imap_mime_header_decode($msgInfo->subject);
      $subject = null;
      foreach($subjects as $subjectPart) {
         $subject .= $subjectPart->charset == 'default' ? $subjectPart->text : iconv($subjectPart->charset, 'utf-8', $subjectPart->text);
      }

      $retStruct = array(
         'subject' => $subject,
         'plain' => null,
         'html' => null,
         'attachments' => array(),
      );

      // Zajimaji nas pouze zpravy, ktere maji 2 a vice casti.
      // Tyto zpravy mohou obsahovat prilohy.
      if (isset($structure->parts))
      {
         // Vyhledani priloh v kazde casti zpravy.
         foreach ($structure->parts as $partNo => $part)
         {
            // je pole alternativní obsah?
            if ($part->subtype == 'ALTERNATIVE' && is_array($part->parts)) {
               foreach ($part->parts as $subPartNo => $subPart){
                  if($subPart->subtype == 'PLAIN'){
                     // Nacteni obsahu casti mailu.
                     $retStruct['plain'] = $part_content = self::_AutorunEncodeContent(
                        imap_fetchbody($connection, $msgUID, ($partNo+1).'.'.($subPartNo+1)), $subPart->encoding, $subPart->parameters);
                  } else if($subPart->subtype == 'HTML'){
                     $retStruct['html'] = $part_content = self::_AutorunEncodeContent(
                        imap_fetchbody($connection, $msgUID, ($partNo+1).'.'.($subPartNo+1)), $subPart->encoding, $subPart->parameters);
                  }
               }
            } else if ($part->subtype == 'PLAIN') {
               $retStruct['plain'] = $part_content = self::_AutorunEncodeContent(
                  imap_fetchbody($connection, $msgUID, ($partNo+1)), $part->encoding, $part->parameters);
            } else if ($part->subtype == 'HTML') {
               $retStruct['html'] = $part_content = self::_AutorunEncodeContent(
                  imap_fetchbody($connection, $msgUID, ($partNo+1)), $part->encoding, $part->parameters);
            } else if (isset($part->disposition) && $part->disposition == 'attachment' && $part->ifdparameters) {
               // Prohledani parametru. Zajima nas atribut s nazvem 'filename'.
               foreach ($part->dparameters as $part_param) {
                  // Tato cast obsahuje informaci o jmenu souboru v priloze.
                  $partName = mb_strtolower($part_param->attribute, 'utf-8');
                  if ($partName == 'filename') {
                     // Kontrola, jestli ma soubor pozadovanou priponu.
                     if (pathinfo(strtolower($part_param->value), PATHINFO_EXTENSION) == 'jpg')
                     {
                        // Nacteni obsahu casti mailu.
                        $part_content = self::_AutorunEncodeContent(
                           imap_fetchbody($connection, $msgUID, $partNo + 1), $part->encoding);
                        file_put_contents(AppCore::getAppCacheDir().$part_param->value, $part_content);
                        $retStruct['attachments'][] = $part_param->value;
                     }
                  }
               }
            }
         }
      }


      return $retStruct;
   }

   protected static function _AutorunEncodeContent($cnt, $encoding, $params = array())
   {
      switch ($encoding)
      {
         case 3:
            $cnt = base64_decode($cnt);
            break;
         case 4:
            $cnt = quoted_printable_decode($cnt);
            break;
      }
      // charset
      if(!empty($params)){
         foreach($params as $param) {
            if($param->attribute == 'charset'){
               $cnt = iconv($param->value, 'utf-8', $cnt);
            }
         }
      }

      return $cnt;
   }
}
