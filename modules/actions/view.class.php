<?php
class ActionsView extends View {
   public function mainView() {
      if($this->getRights()->isWritable()){
         $this->template()->addTpl('addButton.tpl');
         $this->template()->addVar('LINK_TO_ADD_ACTION_NAME', _m("Přidat akci"));
         $this->template()->addVar('LINK_TO_ADD_ACTION', $this->container()->getLink('add_action'));
         // editační tlačítka
         $jquery = new JQuery();
         $this->template()->addJsPlugin($jquery);
      }

      $this->template()->addTpl("list.tpl");

      $this->template()->addVar("ACTION_LIST_NAME", _m("Akce"));
      $this->template()->addCss("style.css");

      //TODO korektní cestu
      $this->template()->addTpl($this->container()->getEplugin('scroll'));
   }

   public function showView(){
      if($this->getRights()->isWritable()){
         $this->template()->addTpl('editButtons.tpl');
         $this->template()->addVar('LINK_TO_ADD_ACTION_NAME', _m("Přidat akci"));
         $this->template()->addVar('LINK_TO_EDIT_ACTION_NAME', _m("Upravit"));

         $this->template()->addVar('LINK_TO_DELETE_ACTION_NAME', _m("Smazat"));
         $this->template()->addVar('DELETE_CONFIRM_MESSAGE', _m("Smazat akci"));

         //			JSPlugin pro potvrzení mazání
         $submitForm = new SubmitForm();
         $this->template()->addJsPlugin($submitForm);

         // editační tlačítka
         $jquery = new JQuery();
         $this->template()->addJsPlugin($jquery);
      }

      $this->template()->addTpl("actionDetail.tpl");
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel($this->container()->getData('NEW_NAME'));
      $this->template()->setSubTitle($this->container()->getData('NEW_NAME'), true);

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
   }

   /**
    * Viewer pro přidání novinky
    */
   public function addNActionView() {
      $this->template()->addTpl('editAction.tpl');
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel(_m('Přidání akce'));
      $this->template()->setSubTitle(_m('Přidání akce'), true);
      $this->template()->addVar("ADD_ACTION_LABEL",_m('Přidání akce'));

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
      $this->assignLabels();

      //Tabulkové uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));

      $tinyMce = new TinyMce();
      $tinyMce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinyMce);
   }

   /**
    * Metoda přiřadí popisky do šablony
    */
   private function assignLabels() {
      $this->template()->addVar('ACTION_LABEL_NAME', _m('Název'));
      $this->template()->addVar('ACTION_TEXT_NAME', _m('Text'));
      //      $this->template()->addVar('SHOW_DATE_START', _m('Zobrazit od'));
      //      $this->template()->addVar('SHOW_DATE_STOP', _m('Zobrazit do'));
      $this->template()->addVar('ACTION_IMAGE', _m('Obrázek akce'));

      $this->template()->addVar('BUTTON_RESET', _m('Obnovit'));
      $this->template()->addVar('BUTTON_SEND', _m('Uložit'));
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->template()->addTpl('editAction.tpl');
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel(_m("Úprava akce").' - '.$this->container()->getData('ACTION_NAME'));
      $this->template()->setSubTitle(_m("Úprava akce").' - '.$this->container()->getData('ACTION_NAME'), true);
      $this->template()->addVar("ADD_NEWS_LABEL",_m("Úprava akce").' - '.$this->container()->getData('ACTION_NAME'));

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
      $this->template()->addVar('EDITING', true);
      $this->template()->addVar('DELETE_IMAGE', _m('Smazat obrázek'));
      $this->assignLabels();
      //Taby - uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);

      $tinyMce = new TinyMce();
      $tinyMce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinyMce);
   }
}

?>