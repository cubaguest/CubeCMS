<?php
class NewsView extends View {
	public function mainView() {
		if($this->getRights()->isWritable()){
			$this->template()->addTpl('addNewsButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_NEWS_NAME', _("Přidat novinku"));
			$this->template()->addVar('LINK_TO_ADD_NEWS', $this->getModel()->linkToAdd);
			
			$this->template()->addVar('LINK_TO_EDIT_NEWS_NAME', _("Upravit"));
			$this->template()->addVar('LINK_TO_DELETE_NEWS_NAME', _("Smazat"));
			$this->template()->addVar('DELETE_CONFIRM_MESSAGE', _("Smazat novinku"));
			
//			JSPlugin pro potvrzení mazání
			$submitForm = new SubmitForm();
			$this->template()->addJsPlugin($submitForm);
		}
		
		$this->template()->addTpl("list.tpl");
//		$this->template()->addTpl("scroll.tpl");
//		$this->template()->addCss("pokus.css");
//		$this->template()->addJS("pokus.js");
//		
		$this->template()->addVar("NEWS_LIST_ARRAY", $this->getModel()->allNewsArray);
		$this->template()->addVar("NEWS_LIST_NAME", _("Novinky"));
		$this->template()->addCss("style.css");
		
		//TODO korektní cestu
		$this->template()->addTpl($this->getModel()->scroll->getTpl(), true);
		$this->getModel()->scroll->assignToTpl($this->template());
//		$this->getModel()->scroll->getTpl();

		$this->template()->addVar('NEWS_TEXT_LANGUAGE', _('Jazyk'));
		$this->template()->addVar('NEWS_TEXT_AUTHOR', _('Autor'));
		$this->template()->addVar('NEWS_TEXT_LABEL', _('Popis'));
		$this->template()->addVar('NEWS_TEXT_NAME', _('Text'));
		
		//JS Plugins
//		$tinymce = new TinyMce();
//		$this->template()->addJsPlugin($tinymce);
		
//		echo "<pre>";
//		print_r($tinymce);
//		echo "</pre>";
		
	}

	public function showView()
	{
		;
	}
	
	/**
	 * Viewer pro přidání novinky
	 */
	public function addView() {
		$this->template()->addTpl('editNews.tpl');
		$this->template()->addCss("style.css");
		$this->template()->addVar('NEWS_EDIT_ARRAY', $this->getModel()->newsArray);

		$this->template()->setTplSubLabel(_('Přidání novinky'));
		
//		$this->template()->addVar('PAGE_NAME', _('Přidání novinky'));
		$this->template()->addVar('LINK_TO_BACK', $this->getModel()->linkToBack);
		$this->template()->addVar('LINK_TO_BACK_NAME', _('Zpět'));
		
		$this->assignLabels();
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
		$this->template()->addVar('NEWS_EDIT_ARRAY', $this->getModel()->newsArray);
		
		$this->template()->addVar('SELECTED_ID_NEWS', $this->getModel()->idNews);
		$this->template()->addVar('LINK_TO_BACK', $this->getModel()->linkToBack);
		$this->template()->addVar('LINK_TO_BACK_NAME', _('Zpět'));
		
		$this->template()->setTplSubLabel(_("Úprava novinky").' - '.$this->getModel()->newsDefaultLabel);
		
		$this->assignLabels();
	}
	
	
//	public function testView() {
//		echo "pokus test";
//	}
	
	
}

?>