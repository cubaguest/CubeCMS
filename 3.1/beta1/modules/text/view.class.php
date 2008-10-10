<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class TextView extends View {
	public function mainView() {
		
		if($this->getRights()->isWritable()){
			$this->template()->addTpl('editButton.tpl');
			
			$this->template()->addVar('WRITABLE', true);
			
			$this->template()->addVar('LINK_TO_EDIT_TEXT_NAME', _("Upravit"));
			$this->template()->addVar('LINK_TO_EDIT_TEXT',$this->container()->getLink('link_edit'));
			
			
		}
		
		$this->template()->addTpl("text.tpl");
		
		$this->template()->addVar('TEXT', $this->container()->getData('text'));
//		$this->template()->addVar('TEXT', $this->container()->text); //TODO prověřit přímý přístup k nedefinovaným proměným

	}
	/*EOF mainView*/
	
	public function editView() {
		
		$this->template()->addTpl("textedit.tpl");
		
		$this->template()->addVar('TEXT_EDIT_ARRAY', $this->container()->getData('text'));		
		
		$this->template()->addVar('TEXT_NAME', _('Text'));
		
		$this->template()->addVar('BUTTON_TEXT_SEND', _('Odeslat'));
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));
		
		$this->template()->setTplSubLabel(_('Úprava textu'));
		
		//NOTE soubory
		$this->template()->addTpl($this->container()->getEplugin('files')->getTpl(), true);
		$this->container()->getEplugin('files')->assignToTpl($this->template());

		//NOTE obrázky
		$this->template()->addTpl($this->container()->getEplugin('images')->getTpl(), true);
		$this->container()->getEplugin('images')->assignToTpl($this->template());
		
		$this->template()->addVar('TEXT_IN_DB', $this->container()->getData('indb'));
		$tinymce = new TinyMce();
		$this->template()->addJsPlugin($tinymce);
		
		$tabcontent = new TabContent();
		$this->template()->addJsPlugin($tabcontent);
	}
	/*EOF editView*/
	
}

?>