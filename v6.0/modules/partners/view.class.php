<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class PartnersView extends View {
	public function mainView() {
		if($this->getRights()->isWritable()){
//			$this->template()->addTpl("buttonAdd.tpl");
//
			$this->template()->addVar('EDITABLE', $this->getRights()->isWritable());
//
			$this->template()->addVar('LINK_TO_ADD_NAME', _('Přidat partnera'));
			$this->template()->addVar('LINK_TO_EDIT_NAME', _('Upravit partnera'));
			$this->template()->addVar('LINK_TO_REMOVE_NAME', _('Smazat partnera'));
         $this->template()->addVar('PARTNER_PRIORITY_LABEL', _('Priorita'));

//			$this->template()->addVar('LINK_TO_DELETE_SPONSORS_NAME', _('Smazat'));
			$this->template()->addVar('DELETE_CONFIRM_MESSAGE', _('Opravdu smazat partnera'));
//
			$this->template()->addJsPlugin(new SubmitForm());
//
		}
		$this->template()->addTpl("list.tpl");
		
		$this->template()->addCss('style.css');

      $jQuery = new JQuery();
      $this->template()->addJsPlugin($jQuery);
//		$this->template()->addVar('LINK_ADD_SPONSOR', $this->getModel()->linkAddSponsor);
//		$this->template()->addVar('DIR_TO_IMAGES', $this->getModel()->dirToImages);
//
//		$this->assignSponsorsLabels();
	}
	
	/**
	 * Metoda přiřadí názvy prvků v listu sponzorů
	 */
	private function assignSponsorsLabels() {
		$this->template()->addVar('NOT_ANY_PARTNER', _('Není uložen žádný sponzor'));

		$this->template()->addVar('PARTNER_NAME', _('Jméno partnera'));
		$this->template()->addVar('PARTNER_LABEL', _('Popis partnera'));
		$this->template()->addVar('PARTNER_URL_NAME', _('WWW stránky partnera'));
		$this->template()->addVar('PARTNER_LOGO_IMAGE', _('Logo (obrázek nebo flash) partnera'));
		$this->template()->addVar('PARTNER_PRIORITY', _('Priorita pratnera (celé číslo od 0 do 1000)'));
	}
	
	/**
	 * Viewer pro přidání sponzora
	 */
	public function addpartnerView() {
		$this->template()->addTpl('editPartner.tpl');
		$this->template()->setTplSubLabel(_('Přidání partnera'));
		
		$this->assignSponsorsLabels();
		
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));
		$this->template()->addVar('BUTTON_SEND', _('Uložit'));

      $tinyMce = new TinyMce();
      $tinyMce->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
		$this->template()->addJsPlugin($tinyMce);

      $jQuery = new JQuery();
      $jQuery->addWidgentTabs();
      $this->template()->addJsPlugin($jQuery);
	}
	
	/**
	 * Viewver pro zobrazení editace
	 */
	public function editpartnerView() {
		$this->template()->addTpl('editPartner.tpl');
//		$this->template()->addVar('SPONSOR_EDIT_ARRAY', $this->getModel()->sponsorArray);
//
//		$this->template()->addVar('SELECTED_ID_SPONSOR', $this->getModel()->idSponsor);
//		$this->template()->addVar('SPONSOR_IMAGE', $this->getModel()->sponsorImageFile);
//		$this->template()->addVar('SPONSOR_URL', $this->getModel()->sponsorUrl);
//		$this->template()->addVar('DIR_TO_IMAGES', $this->getModel()->dirToImages);
		$this->template()->addVar('EDIT_PARTNER', true);
		$this->template()->addVar('DELTE_IMAGE_LABEL', _('Smazat logo partnera'));
		$this->template()->addVar('BUTTON_BACK_NAME', _('Zpět na seznam'));
		
      $this->template()->setTplSubLabel(_("úprava partnera").':&nbsp;'.$this->container()->getData('PARTNER_NAME'));



		$this->assignSponsorsLabels();
		
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));

      $tinyMCE = new TinyMce();
      $tinyMCE->setTheme(TinyMce::TINY_THEME_ADVANCED_SIMPLE);
		$this->template()->addJsPlugin($tinyMCE);

      $jQuery = new JQuery();
      $jQuery->addWidgentTabs();
      $this->template()->addJsPlugin($jQuery);
	}
	
	
}

?>