<?php
class ArticlesController extends Controller {
   /**
    * Název parametru s počtem článků na stránce
    */
   const PARAM_NUM_ARTICLES_ON_PAGE = 'scroll';

   /**
    * Název parametru jestli články může editovat každy nebo pouze vlastník
    */
   const PARAM_EDIT_ONLY_OWNER = 'editonlyowner';

   /**
    * Parametry s typem editoru
    */
   const PARAM_EDITOR_THEME = 'theme';

   /**
    * Parametr jestli se používají soubory
    */
   const PARAM_FILES = 'files';

  /**
   * Speciální imageinární sloupce
   * @var string
   */
   const ARTICLE_EDITABLE = 'editable';
   const ARTICLE_EDIT_LINK = 'editlink';
   const ARTICLE_SHOW_LINK = 'showlink';

  /**
   * Názvy formůlářových prvků
   * @var string
   */
   const FORM_PREFIX = 'article_';
   const FORM_BUTTON_SEND = 'send';
   const FORM_BUTTON_EDIT = 'edit';
   const FORM_BUTTON_DELETE = 'delete';
   const FORM_INPUT_ID = 'id';
   const FORM_INPUT_LABEL = 'label';
   const FORM_INPUT_TEXT = 'text';

  /**
   * Kontroler pro zobrazení novinek
   */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      //		Vytvoření modelu
      $articleModel = $this->createModel("ArticlesListModel");
      //		Scrolovátka
      $scroll = new ScrollEplugin($this->sys());
      $scroll->setCountRecordsOnPage($this->module()->getParam(self::PARAM_NUM_ARTICLES_ON_PAGE, 10));
      $scroll->setCountAllRecords($articleModel->getCountArticles());
//      var_dump($scroll);
      //		Vybrání článků
      $this->view()->articlesArray = $articleModel->getSelectedListArticles($scroll->getStartRecord(), $scroll->getCountRecords());
      $this->view()->EPLscroll = $scroll;

      //		Přidání linku pro editaci a jestli se dá editovat
//      if(!empty ($articlesArray)){
//         foreach ($articlesArray as $key => $article) {
//            //			Link pro zobrazení
//            $articlesArray[$key][self::ARTICLE_SHOW_LINK] = $this->getLink()
//            ->article($article[ArticleDetailModel::COLUMN_ARTICLE_LABEL],
//               $article[ArticleDetailModel::COLUMN_ARTICLE_ID]);
//         }
//      }

      //		Přenos do viewru
//      $this->container()->addEplugin('scroll',$scroll);

      //		Link pro přidání
//      if($this->getRights()->isWritable()){
//         $this->container()->addLink('LINK_ADD_ARTICLE',$this->getLink()->action($this->getAction()->addArticle()));
//      }
      // předání dat
//      $this->container()->addData('ARTICLE_LIST_ARRAY', $articlesArray);
   }

   public function showController(){
//      $articleDetail = new ArticleDetailModel();
//      $articleArr = $articleDetail->getArticleDetailSelLang($this->getArticle()->getArticle());

      //      obsluha Mazání novinky
//      if(($this->getRights()->isWritable() AND $articleDetail->getIdUser()
//            == $this->getRights()->getAuth()->getUserId()) OR
//         $this->getRights()->isControll()){
//
//      }

//      $this->container()->addData('ARTICLE', $articleArr);
//      $this->container()->addData('ARTICLE_LABEL', $articleArr[ArticleDetailModel::COLUMN_ARTICLE_LABEL]);

      if($this->getRights()->isControll() OR
         ($this->getRights()->isWritable() AND $this->getModule()->getParam(self::PARAM_EDIT_ONLY_OWNER, true) == false) OR
         ($this->getRights()->isWritable() AND $this->getModule()->getParam(self::PARAM_EDIT_ONLY_OWNER, true) == true AND
            $articleDetail->getIdUser() == $this->getRights()->getAuth()->getUserId())){

         $form = new Form(self::FORM_PREFIX);
         $form->crInputHidden(self::FORM_INPUT_ID, true, 'is_numeric')
         ->crSubmit(self::FORM_BUTTON_DELETE);

         if($form->checkForm()){
            $files = new UserFilesEplugin($this->getRights());
            $files->deleteAllFiles($articleArr[ArticleDetailModel::COLUMN_ARTICLE_ID]);
            $images = new UserImagesEplugin($this->getRights());
            $images->deleteAllImages($articleArr[ArticleDetailModel::COLUMN_ARTICLE_ID]);
            if(!$articleDetail->deleteArticle($form->getValue(self::FORM_INPUT_ID))){
               throw new UnexpectedValueException(_m('Článek se nepodařilo smazat, zřejmně špatně přenesené id'), 3);
            }
            $this->infoMsg()->addMessage(_m('Článek byl smazán'));
            $this->getLink()->article()->action()->rmParam()->reload();
         }

//         $this->container()->addLink('EDIT_LINK', $this->getLink()->action($this->getAction()->editArticle()));
//         $this->container()->addData('EDITABLE', true);
      }

//      if($this->getRights()->isWritable()){
//         $this->container()->addLink('ADD_LINK',$this->getLink()->action($this->getAction()->addArticle())->article());
//         $this->container()->addData('WRITABLE', true);
//      }
//      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
   }

   /**
   * Kontroler pro přidání novinky
   */
   public function addarticleController(){
      $this->checkWritebleRights();
      if($this->getModule()->getParam(self::PARAM_FILES, true)){
         // Uživatelské soubory
         $files = new UserFilesEplugin($this->getRights());
         $files->setIdArticle($this->getRights()->getAuth()->getUserId()*(-1));
         $this->container()->addEplugin('files', $files);
      }

      $articleForm = new Form();
      $articleForm->setPrefix(self::FORM_PREFIX);

      $articleForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //        Pokud byl odeslán formulář
      if($articleForm->checkForm()){
         $articleDetail = new ArticleDetailModel();
         if(!$articleDetail->saveNewArticle($articleForm->getValue(self::FORM_INPUT_LABEL),
               $articleForm->getValue(self::FORM_INPUT_TEXT),
               $this->getRights()->getAuth()->getUserId())){
            throw new UnexpectedValueException(_m('Článek se nepodařilo uložit, chyba při ukládání.'), 1);
         }
         if(isset ($files)){
            $files->renameIdArticle($this->getRights()->getAuth()->getUserId()*(-1),
               $articleDetail->getLastInsertedId());
         }
         $this->infoMsg()->addMessage(_m('Článek byl uložen'));
         $this->getLink()->article()->action()->rmParam()->reload();
      }

      $this->container()->addData('ARTICLE_DATA', $articleForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $articleForm->getErrorItems());
      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
   }

  /**
   * controller pro úpravu novinky
   */
   public function editArticleController() {
      $this->checkWritebleRights();

      $ardicleEditForm = new Form(self::FORM_PREFIX);

      $ardicleEditForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //      Načtení hodnot prvků
      $articleModel = new ArticleDetailModel();
      $articleModel->getArticleDetailAllLangs($this->getArticle());
      //      Nastavení hodnot prvků
      $ardicleEditForm->setValue(self::FORM_INPUT_LABEL, $articleModel->getLabelsLangs());
      $ardicleEditForm->setValue(self::FORM_INPUT_TEXT, $articleModel->getTextsLangs());
      $label = $articleModel->getLabelsLangs();
      
      $this->container()->addData('ARTICLE_NAME', $label[Locale::getLang()]);

      if($this->getModule()->getParam(self::PARAM_FILES, true)){
         // Uživatelské soubory
         $files = new UserFilesEplugin($this->getRights());
         $files->setIdArticle($articleModel->getId());
         $this->container()->addEplugin('files', $files);
      }

      //        Pokud byl odeslán formulář
      if($ardicleEditForm->checkForm()){
         if(!$articleModel->saveEditArticle($ardicleEditForm->getValue(self::FORM_INPUT_LABEL),
               $ardicleEditForm->getValue(self::FORM_INPUT_TEXT), $this->getArticle())){
            throw new UnexpectedValueException(_m('Článek se nepodařilo uložit, chyba při ukládání.'), 2);
         }
         $this->infoMsg()->addMessage(_m('Článek byl uložen'));
         $this->getLink()->action()->reload();
      }

      //    Data do šablony
      $this->container()->addData('ARTICLE_DATA', $ardicleEditForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $ardicleEditForm->getErrorItems());

      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->action());
   }
}
?>