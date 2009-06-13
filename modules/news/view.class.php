<?php
class NewsView extends View {
   public function mainView() {
      if($this->rights()->isWritable()){
         $toolbox = new TplToolbox();
         $toolbox->addTool('add_article', $this->_m("Přidat"),
            $this->link()->action($this->sys()->action()->addNews()),
            $this->_m("Přidat novinku"), "text_add.png");
         $this->template()->toolbox = $toolbox;
      }

      $this->template()->addTplFile("list.phtml");
      $this->template()->addCssFile("style.css");

//      if($this->getRights()->isWritable()){
//         $this->template()->addTpl('addButton.tpl');
//         $this->template()->addVar('LINK_TO_ADD_NEWS_NAME', _m("Přidat novinku"));
//         $this->template()->addVar('LINK_TO_ADD_NEWS', $this->container()->getLink('add_new'));
//
//         // editační tlačítka
//         $jquery = new JQuery();
//         $this->template()->addJsPlugin($jquery);
//      }
//
//      $this->template()->addTpl("list.tpl");
//
//      $this->template()->addVar("NEWS_LIST_ARRAY", $this->container()->getData('news_list'));
//      $this->template()->addVar("NEWS_LIST_NAME", _m("Novinky"));
//      $this->template()->addCss("style.css");
//
//      //TODO korektní cestu
//      $this->template()->addTpl($this->container()->getEplugin('scroll')->getTpl(), true);
//      $this->container()->getEplugin('scroll')->assignToTpl($this->template());
//
//      $this->template()->addVar('NUM_NEWS', $this->container()->getData('num_news'));
//      $this->template()->addVar('NUM_NEWS_ALL', $this->container()->getLink('all_news'));
//      $this->template()->addVar('NUM_NEWS_ALL_NAME', _m('Vše'));
//      $this->template()->addVar('NUM_NEWS_SHOW', _m('Zobrazit novinku'));

   }

   public function showView(){
      if($this->getRights()->isWritable()){
         $this->template()->addTpl('editButtons.tpl');
         $this->template()->addVar('LINK_TO_ADD_NEWS_NAME', _m("Přidat novinku"));
         $this->template()->addVar('LINK_TO_ADD_NEWS', $this->container()->getLink('add_new'));

         $this->template()->addVar('LINK_TO_EDIT_NEWS_NAME', _m("Upravit"));
         $this->template()->addVar("NEWS_EDIT", $this->container()->getData('editable'));
         $this->template()->addVar('LINK_TO_EDIT_NEWS', $this->container()->getLink('edit_link'));

         $this->template()->addVar('LINK_TO_DELETE_NEWS_NAME', _m("Smazat"));
         $this->template()->addVar('DELETE_CONFIRM_MESSAGE', _m("Smazat novinku"));

         //			JSPlugin pro potvrzení mazání
         $submitForm = new SubmitForm();
         $this->template()->addJsPlugin($submitForm);

         // editační tlačítka
         $jquery = new JQuery();
         $this->template()->addJsPlugin($jquery);
      }

      $this->template()->addTpl("newDetail.tpl");
      $this->template()->addCss("style.css");

      $this->template()->addVar("NEWS_DETAIL", $this->container()->getData('new'));

      $this->template()->setTplSubLabel($this->container()->getData('new_name'));
      $this->template()->setSubTitle($this->container()->getData('new_name'), true);

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
   }

   /**
    * Viewer pro přidání novinky
    */
   public function addNewsView() {
      $this->template()->addTplFile('editNews.phtml');
      $this->template()->addCssFile("style.css");

//      $this->template()->setTplSubLabel(_m('Přidání novinky'));
//      $this->template()->setSubTitle(_m('Přidání novinky'), true);
//      $this->template()->addVar("ADD_NEWS_LABEL",_m('Přidání novinky'));

      //Tabulkové uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }

   /**
    * Metoda přiřadí popisky do šablony
    */
   private function assignLabels() {
      $this->template()->addVar('NEWS_LABEL_NAME', _m('Popis'));
      $this->template()->addVar('NEWS_TEXT_NAME', _m('Text'));

      $this->template()->addVar('BUTTON_RESET', _m('Obnovit'));
      $this->template()->addVar('BUTTON_SEND', _m('Uložit'));
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->template()->addTpl('editNews.tpl');
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel(_m("Úprava novinky").' - '.$this->container()->getData('NEWS_NAME'));
      $this->template()->setSubTitle(_m("Úprava novinky").' - '.$this->container()->getData('NEWS_NAME'), true);
      $this->template()->addVar("ADD_NEWS_LABEL",_m("Úprava novinky").' - '.$this->container()->getData('NEWS_NAME'));

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
      $this->assignLabels();
      //Taby - uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }
}

?>