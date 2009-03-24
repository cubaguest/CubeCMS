<?php
class ReferencesView extends View {
   public function mainView() {
      if($this->getRights()->isWritable()){
         $this->template()->addTpl('addButton.tpl');
         $this->template()->addVar('LINK_TO_ADD_REFERENCE_NAME', _("Přidat referenci"));
         $this->template()->addVar('LINK_TO_EDIT_REFERENCE_NAME', _("Upravit referenci"));
         $this->template()->addVar('DELETE_REFERENCE_CONFIRM_MESSAGE', _("Smazast referenci"));
         $this->template()->addVar('LINK_TO_DELETE_REFERENCE_NAME', _("Smazast referenci"));
         $this->template()->addVar('EDITABLE', true);

         //			JSPlugin pro potvrzení mazání
         $submitForm = new SubmitForm();
         $this->template()->addJsPlugin($submitForm);
      }
//
      $this->template()->addTpl("list.tpl");
//
//      $this->template()->addVar("NEWS_LIST_ARRAY", $this->container()->getData('news_list'));
//      $this->template()->addVar("NEWS_LIST_NAME", _("Novinky"));
      $this->template()->addCss("style.css");
//
//      //TODO korektní cestu
//      $this->template()->addTpl($this->container()->getEplugin('scroll')->getTpl(), true);
//      $this->container()->getEplugin('scroll')->assignToTpl($this->template());
//
//      $this->template()->addVar('NUM_NEWS', $this->container()->getData('num_news'));
//      $this->template()->addVar('NUM_NEWS_ALL', $this->container()->getLink('all_news'));
//      $this->template()->addVar('NUM_NEWS_ALL_NAME', _('Vše'));
//      $this->template()->addVar('NUM_NEWS_SHOW', _('Zobrazit novinek'));
      $jQuery = new JQuery();
      $this->template()->addJsPlugin($jQuery);
      $this->template()->addJsPlugin(new LightBox());
   }

//   public function showview()
//   {
//      if($this->getRights()->isWritable()){
//         $this->template()->addTpl('editButtons.tpl');
//         $this->template()->addVar('LINK_TO_ADD_NEWS_NAME', _("Přidat novinku"));
//         $this->template()->addVar('LINK_TO_ADD_NEWS', $this->container()->getLink('add_new'));
//
//         $this->template()->addVar('LINK_TO_EDIT_NEWS_NAME', _("Upravit"));
//         $this->template()->addVar("NEWS_EDIT", $this->container()->getData('editable'));
//         $this->template()->addVar('LINK_TO_EDIT_NEWS', $this->container()->getLink('edit_link'));
//
//         $this->template()->addVar('LINK_TO_DELETE_NEWS_NAME', _("Smazat"));
//         $this->template()->addVar('DELETE_CONFIRM_MESSAGE', _("Smazat novinku"));
//
//         //			JSPlugin pro potvrzení mazání
//         $submitForm = new SubmitForm();
//         $this->template()->addJsPlugin($submitForm);
//
//         // editační tlačítka
//         $jquery = new JQuery();
//         $this->template()->addJsPlugin($jquery);
//      }
//
//      $this->template()->addTpl("newDetail.tpl");
//      $this->template()->addCss("style.css");
//
//      $this->template()->addVar("NEWS_DETAIL", $this->container()->getData('new'));
//
//      $this->template()->setTplSubLabel($this->container()->getData('new_name'));
//      $this->template()->setSubTitle($this->container()->getData('new_name'), true);
//
//      $this->template()->addVar('BUTTON_BACK_NAME', _('Zpět na seznam'));
//   }

   /**
    * Viewer pro přidání novinky
    */
   public function addView() {
      $this->template()->addTpl('editReference.tpl');
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel(_('Přidání reference'));
      $this->template()->setSubTitle(_('Přidání reference'), true);
      $this->template()->addVar("ADD_REFERENCE_LABEL",_('Přidání reference'));

      $this->template()->addVar('BUTTON_BACK_NAME', _('Zpět'));
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
      $this->template()->addVar('REFERENCE_LABEL_NAME', _('Název'));
      $this->template()->addVar('REFERENCE_TEXT_NAME', _('Popis'));
      $this->template()->addVar('REFERENCE_IMAGE_LABEL', _('Obrázek'));

      $this->template()->addVar('BUTTON_RESET', _('Obnovit'));
      $this->template()->addVar('BUTTON_SEND', _('Uložit'));
   }

   /**
    * Viewer pro editaci novinky
    */
   public function editView() {
      $this->template()->addTpl('editReference.tpl');
      $this->template()->addCss("style.css");

      $this->template()->setTplSubLabel(_("Úprava reference").' - '.$this->container()->getData('REFERENCE_NAME'));
      $this->template()->setSubTitle(_("Úprava reference").' - '.$this->container()->getData('REFERENCE_NAME'), true);
      $this->template()->addVar("ADD_REFERENCE_LABEL",_("Úprava reference").' - '.$this->container()->getData('REFERENCE_NAME'));

      $this->template()->addVar('BUTTON_BACK_NAME', _('Zpět na seznam'));
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
}

?>