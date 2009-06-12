<?php
class NewsController extends Controller {
  /**
   * Sloupce u tabulky uživatelů
   * @var string
   */
   const COLUMN_USER_NAME = 'username';

  /**
   * Speciální imageinární sloupce
   * @var string
   */
   const NEWS_EDITABLE = 'editable';
   const NEWS_EDIT_LINK = 'editlink';
   const NEWS_SHOW_LINK = 'showlink';

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
   const FORM_INPUT_LABEL_PREFIX = 'label_';
   const FORM_INPUT_TEXT = 'text';
   const FORM_INPUT_TEXT_PREFIX = 'text_';

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
      $newsM = new NewsListModel($this->sys());
      //		Scrolovátka
      $scroll = new ScrollEplugin($this->sys());

      $scroll->setCountRecordsOnPage($this->module()->getParam(self::PARAM_NUM_NEWS, 10));
      $scroll->setCountAllRecords($newsM->getCountNews());
//      var_dump($scroll);
      //		Vybrání článků
      $this->view()->newsArray = $newsM->getSelectedListNews($scroll->getStartRecord(), $scroll->getCountRecords());
      $this->view()->EPLscroll = $scroll;
      

      //TODO předělat na objekt parametru z enginu
//      if(isset($_GET[self::GET_NUM_NEWS]) AND (is_numeric($_GET[self::GET_NUM_NEWS]) OR $_GET[self::GET_NUM_NEWS] == self::GET_ALL_NEWS)){
//         if(is_numeric($_GET[self::GET_NUM_NEWS])){
//            $scroll->setCountRecordsOnPage((int)$_GET[self::GET_NUM_NEWS]);
//         } else if($_GET[self::GET_NUM_NEWS] == self::GET_ALL_NEWS){
//            $scroll->setCountRecordsOnPage($listNews->getCountNews());
//         }
//      } else {
//         $scroll->setCountRecordsOnPage($this->getModule()->getParam(self::PARAM_NUM_NEWS, 10));
//      }
//      $scroll->setCountAllRecords($listNews->getCountNews());

      //		Vybrání novinek
//      $newsArray = $listNews->getSelectedListNews($scroll->getStartRecord(), $scroll->getCountRecords());
      //		Přidání linku pro editaci a jestli se dá editovat
//      if(!empty ($newsArray)){
//         foreach ($newsArray as $key => $news) {
//            if($news[NewsDetailModel::COLUMN_NEWS_ID_USER] == $this->getRights()->getAuth()->getUserId() OR $this->getRights()->isControll()){
//               $newsArray[$key][self::NEWS_EDITABLE] = true;
//               $newsArray[$key][self::NEWS_EDIT_LINK] = $this->getLink()
//               ->article($news[NewsDetailModel::COLUMN_NEWS_LABEL],$news[NewsDetailModel::COLUMN_NEWS_ID_NEW])
//               ->action($this->getAction()->edit());
//            } else {
//               $newsArray[$key][self::NEWS_EDITABLE] = false;
//            }
//            //			Link pro zobrazení
//            $newsArray[$key][self::NEWS_SHOW_LINK] = $this->getLink()
//            ->article($news[NewsDetailModel::COLUMN_NEWS_LABEL],
//               $news[NewsDetailModel::COLUMN_NEWS_ID_NEW]);
//         }
//      }

      //		Přenos do viewru
//      $this->container()->addEplugin('scroll',$scroll);

      //		Link pro přidání
//      if($this->getRights()->isWritable()){
//         $this->container()->addLink('add_new',$this->getLink()->action($this->getAction()->add()));
//      }

//      $this->container()->addData('news_list', $newsArray);

      //		linky pro zobrazení určitého počtu novinek
//      foreach ($this->getNumShowNews as $num) {
//         $numNewsArray[$num] = null;
//         if($listNews->getCountNews() >= $num){
//            //				$numNewsArray[$num] = $this->getLink()->params()->param(self::GET_NUM_NEWS, $num);
//            //				$this->container()->addLink('show_'.$num,$this->getLink()->param(self::GET_NUM_NEWS, $num));
//         };
//      }
      //		$this->container()->addLink('all_news',$this->getLink()->params()->param(self::GET_NUM_NEWS, self::GET_ALL_NEWS));

//      $this->container()->addData('num_news', $numNewsArray);
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
   public function addController(){
      $this->checkWritebleRights();

      $newsForm = new Form();
      $newsForm->setPrefix(self::FORM_PREFIX);

      $newsForm->crInputText(self::FORM_INPUT_LABEL, true, true)
      ->crTextArea(self::FORM_INPUT_TEXT, true, true)
      ->crSubmit(self::FORM_BUTTON_SEND);

      //        Pokud byl odeslán formulář
      if($newsForm->checkForm()){
         $newsDetail = new NewsDetailModel();
         if(!$newsDetail->saveNewNews($newsForm->getValue(self::FORM_INPUT_LABEL),
               $newsForm->getValue(self::FORM_INPUT_TEXT),
               $this->getRights()->getAuth()->getUserId())){
            throw new UnexpectedValueException(_m('Novinku se nepodařilo uložit, chyba při ukládání.'), 1);
         }
         $this->infoMsg()->addMessage(_m('Novinka byla uložena'));
         $this->getLink()->article()->action()->rmParam()->reload();
      }

      $this->view()->errorItems = $newsForm->getErrorItems();
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