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
    * Parametr jestli se používají obrázky
    */
   const PARAM_IMAGES = 'images';

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
      $articleModel = new ArticlesListModel();
      //		Scrolovátka
      $scroll = new ScrollEplugin();
      $scroll->setCountRecordsOnPage($this->getModule()->getParam(self::PARAM_NUM_ARTICLES_ON_PAGE, 10));
      $scroll->setCountAllRecords($articleModel->getCountArticles());

      //		Vybrání článků
      $articlesArray = $articleModel->getSelectedListArticles($scroll->getStartRecord(), $scroll->getCountRecords());

      //		Přidání linku pro editaci a jestli se dá editovat
      if(!empty ($articlesArray)){
         foreach ($articlesArray as $key => $article) {
            //			Link pro zobrazení
            $articlesArray[$key][self::ARTICLE_SHOW_LINK] = $this->getLink()
            ->article($article[NewsDetailModel::COLUMN_NEWS_LABEL],
               $article[NewsDetailModel::COLUMN_NEWS_ID_NEW]);
         }
      }

      //		Přenos do viewru
      $this->container()->addEplugin('scroll',$scroll);

      //		Link pro přidání
      if($this->getRights()->isWritable()){
         $this->container()->addLink('LINK_ADD_ARTICLE',$this->getLink()->action($this->getAction()->addArticle()));
      }
      // předání dat
      $this->container()->addData('ARTICLE_LIST_ARRAY', $articlesArray);
   }

   public function showController(){
      $newsDetail = new NewsDetailModel();
      $new = $newsDetail->getNewsDetailSelLang($this->getArticle()->getArticle());

      //      obsluha Mazání novinky
      if(($this->getRights()->isWritable() AND $newsDetail->getIdUser()
            == $this->getRights()->getAuth()->getUserId()) OR
         $this->getRights()->isControll()){
         $form = new Form(self::FORM_PREFIX);

         $form->crInputHidden(self::FORM_INPUT_ID, true, 'is_numeric')
         ->crSubmit(self::FORM_BUTTON_DELETE);

         if($form->checkForm()){
            $newDetail = new NewsDetailModel();

            if($newDetail->deleteNews($form->getValue(self::FORM_INPUT_ID),
                  $this->getRights()->getAuth()->getUserId())){
               throw new UnexpectedValueException(_m('Novinku se nepodařilo smazat, zřejmně špatně přenesené id'), 3);
            }
            $this->infoMsg()->addMessage(_m('Novinka byla smazána'));
            $this->getLink()->article()->action()->rmParam()->reload();
         }
      }

      $this->container()->addData('new', $new);
      $this->container()->addData('new_name', $new[NewsDetailModel::COLUMN_NEWS_LABEL]);

      if($this->getRights()->isControll() OR $new[NewsDetailModel::COLUMN_NEWS_ID_USER]
         == $this->getRights()->getAuth()->getUserId()){
         $this->container()->addLink('edit_link', $this->getLink()->action($this->getAction()->edit()));
         $this->container()->addData('editable', true);
         $this->container()->addLink('add_new',$this->getLink()->action($this->getAction()->add())->article());
      }
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
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

      if($this->getModule()->getParam(self::PARAM_IMAGES, true)){
         //	Uživatelské obrázky
         $images = new UserImagesEplugin($this->getRights());
         $images->setIdArticle($this->getRights()->getAuth()->getUserId()*(-1));
         $this->container()->addEplugin('images', $images);
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
   public function editController() {
      $this->checkWritebleRights();

      $newsForm = new Form();
      $newsForm->setPrefix(self::FORM_PREFIX);

      $newsForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //      Načtení hodnot prvků
      $newsModel = new NewsDetailModel();
      $newsModel->getNewsDetailAllLangs($this->getArticle());
      //      Nastavení hodnot prvků
      $newsForm->setValue(self::FORM_INPUT_LABEL, $newsModel->getLabelsLangs());
      $newsForm->setValue(self::FORM_INPUT_TEXT, $newsModel->getTextsLangs());

      $label = $newsModel->getLabelsLangs();
      $this->container()->addData('NEWS_NAME', $label[Locale::getLang()]);

      //        Pokud byl odeslán formulář
      if($newsForm->checkForm()){
         $newsDetail = new NewsDetailModel();
         if(!$newsDetail->saveEditNews($newsForm->getValue(self::FORM_INPUT_LABEL),
               $newsForm->getValue(self::FORM_INPUT_TEXT), $this->getArticle())){
            throw new UnexpectedValueException(_m('Novinku se nepodařilo uložit, chyba při ukládání.'), 2);
         }
         $this->infoMsg()->addMessage(_m('Novinka byla uložena'));
         $this->getLink()->action()->reload();
      }

      //    Data do šablony
      $this->container()->addData('NEWS_DATA', $newsForm->getValues());
      $this->container()->addData('ERROR_ITEMS', $newsForm->getErrorItems());

      //		Odkaz zpět
      $this->container()->addLink('BUTTON_BACK', $this->getLink()->article()->action());
   }

  /**
   * metoda pro mazání novinky
   */
   private function deleteNews() {

   }
}
?>