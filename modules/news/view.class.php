<?php
class NewsView extends View {
	public function mainView() {
		if($this->getRights()->isWritable()){
			$this->template()->addTpl('addNewsButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_NEWS_NAME', _("Přidat novinku"));
			$this->template()->addVar('LINK_TO_ADD_NEWS', $this->container()->getLink('add_new'));
		}
		
		$this->template()->addTpl("list.tpl");
		
		$this->template()->addVar("NEWS_LIST_ARRAY", $this->container()->getData('news_list'));
		$this->template()->addVar("NEWS_LIST_NAME", _("Novinky"));
		$this->template()->addCss("style.css");
		
		//TODO korektní cestu
		$this->template()->addTpl($this->container()->getEplugin('scroll')->getTpl(), true);
		$this->container()->getEplugin('scroll')->assignToTpl($this->template());

		$this->template()->addVar('NUM_NEWS', $this->container()->getData('num_news'));
		$this->template()->addVar('NUM_NEWS_ALL', $this->container()->getLink('all_news'));
		$this->template()->addVar('NUM_NEWS_ALL_NAME', _('Vše'));
		$this->template()->addVar('NUM_NEWS_SHOW', _('Zobrazit novinek'));
	}

	public function showView()
	{
		if($this->getRights()->isWritable()){
			$this->template()->addTpl('addNewsButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_NEWS_NAME', _("Přidat novinku"));
			$this->template()->addVar('LINK_TO_ADD_NEWS', $this->container()->getLink('add_new'));
			
			$this->template()->addVar('LINK_TO_EDIT_NEWS_NAME', _("Upravit"));
			$this->template()->addVar("NEWS_EDIT", $this->container()->getData('editable'));
			$this->template()->addVar('LINK_TO_EDIT_NEWS', $this->container()->getLink('edit_link'));
			
			$this->template()->addVar('LINK_TO_DELETE_NEWS_NAME', _("Smazat"));
			$this->template()->addVar('DELETE_CONFIRM_MESSAGE', _("Smazat novinku"));
			
//			JSPlugin pro potvrzení mazání
			$submitForm = new SubmitForm();
			$this->template()->addJsPlugin($submitForm);
		}
		
		$this->template()->addTpl("newDetail.tpl");
		$this->template()->addCss("style.css");
		
		$this->template()->addVar("NEWS_DETAIL", $this->container()->getData('new'));
		
		$this->template()->setTplSubLabel($this->container()->getData('new_name'));
		$this->template()->setSubTitle($this->container()->getData('new_name'), true);
		
		$this->assignButtonBack();
	}
	
	/**
	 * Viewer pro přidání novinky
	 */
	public function addView() {
		$this->template()->addTpl('editNews.tpl');
		$this->template()->addCss("style.css");
		$this->template()->addVar('NEWS_EDIT_ARRAY', $this->container()->getData('new_data'));

		$this->template()->setTplSubLabel(_('Přidání novinky'));
		$this->template()->setSubTitle(_('Přidání novinky'), true);
		
		$this->assignButtonBack();
		
		$this->assignLabels();
		
		$tabcontent = new TabContent();
		$this->template()->addJsPlugin($tabcontent);
	}
	
	/**
	 * Metoda přiřadí tlačítko zpět
	 *
	 */
	private function assignButtonBack() {
		$this->template()->addTpl("linkBack.tpl");
		$this->template()->addVar('LINK_TO_BACK', $this->container()->getLink('link_back'));
		$this->template()->addVar('LINK_TO_BACK_NAME', _('Zpět'));;
	}
	
	/**
	 * Metoda přiřadí popisky do šablony
	 */
	private function assignLabels() {
		$this->template()->addVar('NEWS_LABEL_NAME', _('Popis'));
		$this->template()->addVar('NEWS_TEXT_NAME', _('Text'));
		
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));
		$this->template()->addVar('BUTTON_SEND', _('Uložit'));
	}
	
	/**
	 * Viewer pro editaci novinky
	 */
	public function editView() {
		$this->template()->addTpl('editNews.tpl');
		$this->template()->addCss("style.css");
		$this->template()->addVar('NEWS_EDIT_ARRAY', $this->container()->getData('new_data'));
		
		$this->template()->setTplSubLabel(_("Úprava novinky").' - '.$this->container()->getData('news_label'));
		$this->template()->setSubTitle(_("Úprava novinky").' - '.$this->container()->getData('news_label'), true);
		
		$this->assignButtonBack();
		
		$this->assignLabels();
		
		$tabcontent = new TabContent();
		$this->template()->addJsPlugin($tabcontent);
	}
}

?>