<?php
class News_Controller extends Controller {
  /**
   * Sloupce u tabulky uživatelů
   * @var string
   */
   const COLUMN_USER_NAME = 'username';

  /**
   * Názvy formůlářových prvků
   * @var string
   */
   const FORM_PREFIX = 'news_';
   const FORM_BUTTON_SEND = 'send';
   const FORM_BUTTON_EDIT = 'edit';
   const FORM_BUTTON_DELETE = 'delete';
   const FORM_INPUT_ID = 'id';
   const FORM_INPUT_LABEL = 'label';
   const FORM_INPUT_TEXT = 'text';

  /**
   * Název $_GET s počtem zobrazených novinek
   * @var string
   */
   const GET_NUM_NEWS = 'numnews';

  /**
   * Proměná pro zobrazení všech novinek
   * @var string
   */
   const GET_ALL_NEWS = 'all';

   /**
    * Parametr s počtem novinek na stránku
    * @var string
    */
   const PARAM_NUM_NEWS = 'scroll';

  /**
   * Pole s počty zobrazených novinek
   * @var array
   */
   private $getNumShowNews = array(5,10,20,50);

  /**
   * Kontroler pro zobrazení novinek
   */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      //		Vytvoření modelu
      $newsM = new News_Model_List($this->sys());
      //		Scrolovátka
      $scroll = new Eplugin_Scroll($this->sys());

      $scroll->setCountRecordsOnPage($this->module()->getParam(self::PARAM_NUM_NEWS, 10));
      $scroll->setCountAllRecords($newsM->getCountNews());
      //		Vybrání článků
      $this->view()->newsArray = $newsM->getSelectedListNews($scroll->getStartRecord(), $scroll->getCountRecords());
      $this->view()->EPLscroll = $scroll;

   }

   public function showController(){
      $newsDetail = new News_Model_Detail($this->sys());
      $this->view()->new = $newsDetail->getNewsDetail($this->getArticle()->getArticle());
      if($this->view()->new == null){
         AppCore::setErrorPage();
      }

      //      obsluha Mazání novinky
      if($this->getRights()->isControll() OR
            $this->view()->new[News_Model_Detail::COLUMN_NEWS_ID_USER] 
            == $this->rights()->getAuth()->getUserId()) {
         $form = new Form(self::FORM_PREFIX);
         $form->crInputHidden(self::FORM_INPUT_ID, true, 'is_numeric')
         ->crSubmit(self::FORM_BUTTON_DELETE);
         if($form->checkForm()){
            $newDetail = new News_Model_Detail($this->sys());
            if(!$newDetail->deleteNews($form->getValue(self::FORM_INPUT_ID))){
               throw new UnexpectedValueException($this->_('Novinku se nepodařilo smazat, zřejmně špatně přenesené id'), 3);
            }
            $this->infoMsg()->addMessage($this->_('Novinka byla smazána'));
            $this->getLink()->article()->action()->rmParam()->reload();
         }
      }
   }

   /**
   * Kontroler pro přidání novinky
   */
   public function addNewsController(){
      $this->checkWritebleRights();

      $newsForm = new Form();
      $newsForm->setPrefix(self::FORM_PREFIX);
      $newsForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);
      
      //        Pokud byl odeslán formulář
      if($newsForm->checkForm()){
         $newsDetail = new News_Model_Detail($this->sys());
         if(!$newsDetail->saveNewNews($newsForm->getValue(self::FORM_INPUT_LABEL),
               $newsForm->getValue(self::FORM_INPUT_TEXT),
               $this->rights()->getAuth()->getUserId())){
            throw new UnexpectedValueException($this->_m('Novinku se nepodařilo uložit, chyba při ukládání.'), 1);
         }
         $this->infoMsg()->addMessage($this->_m('Novinka byla uložena'));
         $this->link()->article()->action()->rmParam()->reload();
      }

      $this->view()->errorItems = $newsForm->getErrorItems();
   }

  /**
   * controller pro úpravu novinky
   */
   public function editNewsController() {
      $this->checkWritebleRights();
      ob_flush();
      $newsForm = new Form(self::FORM_PREFIX);
      $newsForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true, Form::CODE_HTMLDECODE)
      ->crSubmit(self::FORM_BUTTON_SEND);
      //        Pokud byl odeslán formulář
      if($newsForm->checkForm()){
         $newsDetail = new News_Model_Detail($this->sys());
         if(!$newsDetail->saveEditNews($newsForm->getValue(self::FORM_INPUT_LABEL),
               $newsForm->getValue(self::FORM_INPUT_TEXT), $this->article())){
            throw new UnexpectedValueException($this->_('Novinku se nepodařilo uložit, chyba při ukládání.'), 2);
         }
         $this->infoMsg()->addMessage($this->_('Novinka byla uložena'));
         $this->getLink()->action()->reload();
      }
   }
}
?>