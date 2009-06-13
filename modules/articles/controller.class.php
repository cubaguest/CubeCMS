<?php
class Articles_Controller extends Controller {
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
         ($this->getRights()->isWritable() AND $this->module()->getParam(self::PARAM_EDIT_ONLY_OWNER, true) == false) OR
         ($this->getRights()->isWritable() AND $this->module()->getParam(self::PARAM_EDIT_ONLY_OWNER, true) == true AND
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
               throw new UnexpectedValueException($this->_m('Článek se nepodařilo smazat, zřejmně špatně přenesené id'), 3);
            }
            $this->infoMsg()->addMessage($this->_m('Článek byl smazán'));
            $this->link()->article()->action()->rmParam()->reload();
         }

//         $this->container()->addLink('EDIT_LINK', $this->link()->action($this->getAction()->editArticle()));
//         $this->container()->addData('EDITABLE', true);
      }

//      if($this->getRights()->isWritable()){
//         $this->container()->addLink('ADD_LINK',$this->link()->action($this->getAction()->addArticle())->article());
//         $this->container()->addData('WRITABLE', true);
//      }
//      $this->container()->addLink('BUTTON_BACK', $this->link()->article()->action());
   }

   /**
   * Kontroler pro přidání novinky
   */
   public function addarticleController(){
      $this->checkWritebleRights();
      if($this->module()->getParam(self::PARAM_FILES, true)){
         // Uživatelské soubory
         $files = new UserFilesEplugin($this->sys());
         $files->setIdArticle($this->rights()->getAuth()->getUserId()*(-1));
         $this->view()->EPLfiles = $files;
      }

      $articleForm = new Form();
      $articleForm->setPrefix(self::FORM_PREFIX);

      $articleForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //        Pokud byl odeslán formulář
      if($articleForm->checkForm()){
         $articleDetail = $this->createModel("ArticleDetailModel");
         if(!$articleDetail->saveNewArticle($articleForm->getValue(self::FORM_INPUT_LABEL),
               $articleForm->getValue(self::FORM_INPUT_TEXT),
               $this->rights()->getAuth()->getUserId())){
            throw new UnexpectedValueException($this->_m('Článek se nepodařilo uložit, chyba při ukládání.'), 1);
         }
         if(isset ($files)){
            $files->renameIdArticle($this->rights()->getAuth()->getUserId()*(-1),
               $articleDetail->getLastInsertedId());
         }
         $this->infoMsg()->addMessage($this->_m('Článek byl uložen'));
         $this->link()->article()->action()->rmParam()->reload();
      }

      $this->view()->errorItems = $articleForm->getErrorItems();
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
//      $articleModel = new ArticleDetailModel($this->sys());
//      $articleModel->getArticleDetailAllLangs($this->getArticle());
      //      Nastavení hodnot prvků
//      $ardicleEditForm->setValue(self::FORM_INPUT_LABEL, $articleModel->getLabelsLangs());
//      $ardicleEditForm->setValue(self::FORM_INPUT_TEXT, $articleModel->getTextsLangs());
//      $label = $articleModel->getLabelsLangs();
      
//      $this->container()->addData('ARTICLE_NAME', $label[Locale::getLang()]);

      if($this->module()->getParam(self::PARAM_FILES, true)){
         // Uživatelské soubory
//         $files = new UserFilesEplugin($this->getRights());
//         $files->setIdArticle($articleModel->getId());
//         $this->container()->addEplugin('files', $files);
         $files = new UserFilesEplugin($this->sys());
         $this->view()->EPLfiles = $files;
      }

      //        Pokud byl odeslán formulář
      if($ardicleEditForm->checkForm()){
         $articleModel = new ArticleDetailModel($this->sys());
         if(!$articleModel->saveEditArticle($ardicleEditForm->getValue(self::FORM_INPUT_LABEL),
               $ardicleEditForm->getValue(self::FORM_INPUT_TEXT), $this->getArticle())){
            throw new UnexpectedValueException($this->_m('Článek se nepodařilo uložit, chyba při ukládání.'), 2);
         }
         $this->infoMsg()->addMessage($this->_m('Článek byl uložen'));
         $this->link()->action()->reload();
      }

      //    Data do šablony
      $this->view()->errorItems = $ardicleEditForm->getErrorItems();
   }
}
?>