<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class SponsorsView extends View {
	public function mainView() {
		if($this->getRights()->isWritable()){
			$this->template()->addTpl("buttonAdd.tpl");
			
			$this->template()->addVar('EDITABLE', $this->getRights()->isWritable());

			$this->template()->addVar('LINK_TO_EDIT_SPONSORS_NAME', _('Upravit'));
			$this->template()->addVar('LINK_TO_DELETE_SPONSORS_NAME', _('Smazat'));
			$this->template()->addVar('DELETE_CONFIRM_MESSAGE', _('Opravdu smazat sponzora'));
			
			$this->template()->addJsPlugin(new SubmitForm());
			
		}
		$this->template()->addTpl("list.tpl");
		
		$this->template()->addCss('style.css');
		
		$this->template()->addVar('SPONSORS_ARRAY', $this->getModel()->allSponsorsArray);
		$this->template()->addVar('LINK_ADD_SPONSOR', $this->getModel()->linkAddSponsor);
		$this->template()->addVar('DIR_TO_IMAGES', $this->getModel()->dirToImages);
		
		$this->assignSponsorsLabels();
	}
	
	/**
	 * Metoda přiřadí názvy prvků v listu sponzorů
	 */
	private function assignSponsorsLabels() {
		$this->template()->addVar('NOT_ANY_SPONSOR', _('Není uložen žádný sponzor'));
		$this->template()->addVar('LINK_ADD_SPONSOR_NAME', _('Přidat sponzora'));

		$this->template()->addVar('SPONSOR_NAME', _('Jméno sponzora'));
		$this->template()->addVar('SPONSOR_LABEL', _('Popis sponzora'));
		$this->template()->addVar('SPONSOR_URL_NAME', _('WWW stránky sponzora'));
		$this->template()->addVar('SPONSOR_LOGO_IMAGE', _('Logo sponzora'));
	}
	
	/**
	 * Viewer pro přidání sponzora
	 */
	public function addView() {
		$this->template()->addTpl('editSponsor.tpl');
		$this->template()->setTplSubLabel(_('Přidání sponzora'));
		
		$this->template()->addVar('SPONSOR_EDIT_ARRAY', $this->getModel()->sponsorArray);
		$this->template()->addVar('SELECTED_ID_SPONSOR', $this->getModel()->selectedId);
		
		
		$this->assignSponsorsLabels();
		
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));
		
		$this->template()->addJsPlugin(new TinyMce());
	}
	
	/**
	 * Viewver pro zobrazení editace
	 */
	public function editView() {
		$this->template()->addTpl('editSponsor.tpl');
		$this->template()->addVar('SPONSOR_EDIT_ARRAY', $this->getModel()->sponsorArray);
		
		$this->template()->addVar('SELECTED_ID_SPONSOR', $this->getModel()->idSponsor);
		$this->template()->addVar('SPONSOR_IMAGE', $this->getModel()->sponsorImageFile);
		$this->template()->addVar('SPONSOR_URL', $this->getModel()->sponsorUrl);
		$this->template()->addVar('DIR_TO_IMAGES', $this->getModel()->dirToImages);
		
		$this->template()->setTplSubLabel(_("Úprava spozora").' - '.$this->getModel()->sponsorDefaultName);

		$this->assignSponsorsLabels();
		
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));
		
		$this->template()->addJsPlugin(new TinyMce());
	}
	
	
}

?>