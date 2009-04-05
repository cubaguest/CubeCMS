<?php
class ReferencesView extends View {
   public function mainView() {
      if($this->getRights()->isWritable()){
         $this->template()->addTpl('addButton.tpl');
         $this->template()->addVar('LINK_TO_ADD_REFERENCE_NAME', _m("Přidat referenci"));
         $this->template()->addVar('LINK_TO_EDIT_REFERENCE_NAME', _m("Upravit referenci"));
         $this->template()->addVar('DELETE_REFERENCE_CONFIRM_MESSAGE', _m("Smazast referenci"));
         $this->template()->addVar('LINK_TO_DELETE_REFERENCE_NAME', _m("Smazast referenci"));
         $this->template()->addVar('LINK_TO_EDIT_OTHER_REFERENCE_NAME', _m("Upravit ostatní reference"));
         $this->template()->addVar('EDITABLE', true);

         //			JSPlugin pro potvrzení mazání
         $submitForm = new SubmitForm();
         $this->template()->addJsPlugin($submitForm);
      }
//
      $this->template()->addTpl("list.tpl");
//      $this->template()->addTpl("otherRef.tpl");
      $this->template()->addVar("OTHER_REFERENCES_NAME", _m('Ostatní reference'));

      $this->template()->addCss("style.css");
      $jQuery = new JQuery();
      $this->template()->addJsPlugin($jQuery);
      $this->template()->addJsPlugin(new LightBox());
   }

   /**
    * Viewer pro přidání novinky
    */
   public function addView() {
      $this->template()->addTpl('editReference.tpl');
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel(_m('Přidání reference'));
      $this->template()->setSubTitle(_m('Přidání reference'), true);
      $this->template()->addVar("ADD_REFERENCE_LABEL",_m('Přidání reference'));

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět'));
      $this->assignLabels();

//      tinyMCE
      $tinyMce = new TinyMce();
      $tinyMce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinyMce);

      //Tabulkové uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }

   /**
    * Metoda přiřadí popisky do šablony
    */
   private function assignLabels() {
      $this->template()->addVar('REFERENCE_LABEL_NAME', _m('Název'));
      $this->template()->addVar('REFERENCE_TEXT_NAME', _m('Popis'));
      $this->template()->addVar('REFERENCE_OTEHR_TEXT_NAME', _m('Text'));
      $this->template()->addVar('REFERENCE_IMAGE_LABEL', _m('Obrázek'));

      $this->template()->addVar('BUTTON_RESET', _m('Obnovit'));
      $this->template()->addVar('BUTTON_SEND', _m('Uložit'));
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->template()->addTpl('editReference.tpl');
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel(_m("Úprava reference").' - '.$this->container()->getData('REFERENCE_NAME'));
      $this->template()->setSubTitle(_m("Úprava reference").' - '.$this->container()->getData('REFERENCE_NAME'), true);
      $this->template()->addVar("ADD_REFERENCE_LABEL",_m("Úprava reference").' - '.$this->container()->getData('REFERENCE_NAME'));

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
      $this->assignLabels();

      //      tinyMCE
      $tinyMce = new TinyMce();
      $tinyMce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinyMce);

      //Taby - uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }
   
   public function editotherrefView() {
      $this->template()->addTpl('editOtherRef.tpl');
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel(_m("Úprava ostatních referencí"));
      $this->template()->setSubTitle(_m("Úprava ostatních referencí").' - '.$this->container()->getData('REFERENCE_NAME'), true);
      $this->template()->addVar("EDIT_OTHER_REFERENCE_LABEL",_m("Úprava ostatních referencí"));

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět na seznam'));
      $this->assignLabels();

      //      tinyMCE
      $tinyMce = new TinyMce();
      $tinyMce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinyMce);

      //Taby - uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);;
   }
}
?>