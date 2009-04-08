<?php
class ContactsView extends View {
   public function mainView() {
      if($this->getRights()->isWritable()){
         $this->template()->addTpl('addButton.tpl');
         $this->template()->addVar('LINK_TO_ADD_CONTACT_NAME', _m("Přidat kontakt"));
         $this->template()->addVar('LINK_TO_EDIT_CONTACT_NAME', _m("Upravit kontakt"));
         $this->template()->addVar('DELETE_CONTACT_CONFIRM_MESSAGE', _m("Smazat kontakt"));
         $this->template()->addVar('LINK_TO_DELETE_CONTACT_NAME', _m("Smazast kontakt"));
         $this->template()->addVar('EDITABLE', true);

         //			JSPlugin pro potvrzení mazání
         $submitForm = new SubmitForm();
         $this->template()->addJsPlugin($submitForm);
      }
//
      $this->template()->addTpl("list.tpl");

      $this->template()->addCss("style.css");
      $jQuery = new JQuery();
      $this->template()->addJsPlugin($jQuery);
      $this->template()->addJsPlugin(new LightBox());
   }

   /**
    * Viewer pro přidání novinky
    */
   public function addView() {
      $this->template()->addTpl('editContact.tpl');
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel(_m('Přidání kontaktu'));
      $this->template()->setSubTitle(_m('Přidání kontaktu'), true);
      $this->template()->addVar("ADD_CONTACT_LABEL",_m('Přidání kontaktu'));

      $this->template()->addVar('BUTTON_BACK_NAME', _m('Zpět'));
      $this->assignLabels();

//      tinyMCE
      $tinyMce = new TinyMce();
      $tinyMce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
      $this->template()->addJsPlugin($tinyMce);

      // seřazení oblastí pro smarty
      $this->seradAreas();

      //Tabulkové uspořádání
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);
   }

   private function seradAreas() {
      $areas = $this->container()->getData('AREAS');
      $newAreas = $cities = array();
      $idArea = null;
      foreach ($areas as $area) {
         if(!isset ($newAreas[$area[ContactModel::COLUMN_AREA_NAME]])){
            $newAreas[$area[ContactModel::COLUMN_AREA_NAME]] = array();
         }
         $newAreas[$area[ContactModel::COLUMN_AREA_NAME]][$area[ContactModel::COLUMN_ID_CITY]]
            = $area[ContactModel::COLUMN_CITY_NAME];
      }
      $this->template()->addVar('AREAS', $newAreas);
   }

   /**
    * Metoda přiřadí popisky do šablony
    */
   private function assignLabels() {
      $this->template()->addVar('CONTACT_LABEL_NAME', _m('Název'));
      $this->template()->addVar('CONTACT_TEXT_NAME', _m('Popis'));
      $this->template()->addVar('CONTACT_OTEHR_TEXT_NAME', _m('Text'));
      $this->template()->addVar('CONTACT_IMAGE_LABEL', _m('Obrázek'));
      $this->template()->addVar('CONTACT_CITY_LABEL', _m('Město'));

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