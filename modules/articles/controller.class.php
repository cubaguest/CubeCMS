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
      $this->checkReadableRights();

      $articleDetail = new Articles_Model_Detail($this->sys());
      $this->view()->article = $articleDetail->getArticleDetailSelLang($this->sys()->article()->getArticle());

      if($this->rights()->isControll() OR
         ($this->rights()->isWritable() AND $this->module()->getParam(self::PARAM_EDIT_ONLY_OWNER, true) == false) OR
         ($this->rights()->isWritable() AND $this->module()->getParam(self::PARAM_EDIT_ONLY_OWNER, true) == true AND
            $articleDetail->getIdUser() == $this->rights()->getAuth()->getUserId())){

         $form = new Form(self::FORM_PREFIX);
         $form->crInputHidden(self::FORM_INPUT_ID, true, 'is_numeric')
         ->crSubmit(self::FORM_BUTTON_DELETE);

         if($form->checkForm()){
            $files = new Eplugin_UserFiles($this->sys());
            $files->deleteAllFiles($this->view()->article[Articles_Model_Detail::COLUMN_ARTICLE_ID]);
            if(!$articleDetail->deleteArticle($form->getValue(self::FORM_INPUT_ID))){
               throw new UnexpectedValueException($this->_m('Článek se nepodařilo smazat, zřejmně špatně přenesené id'), 3);
            }
            $this->infoMsg()->addMessage($this->_m('Článek byl smazán'));
            $this->link()->article()->action()->rmParam()->reload();
         }
      }
   }

   /**
   * Kontroler pro přidání novinky
   */
   public function addArticleController(){
      $this->checkWritebleRights();
      if($this->module()->getParam(self::PARAM_FILES, true)){
         // Uživatelské soubory
         $files = new Eplugin_UserFiles($this->sys());
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
         $articleDetail = new Articles_Model_Detail($this->sys());
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

      if($this->module()->getParam(self::PARAM_FILES, true)){
         // Uživatelské soubory
         $files = new Eplugin_UserFiles($this->sys());
         $this->view()->EPLfiles = $files;
      }

      //        Pokud byl odeslán formulář
      if($ardicleEditForm->checkForm()){
         $articleModel = new Articles_Model_Detail($this->sys());
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