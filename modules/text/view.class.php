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

	}
	
	public function editView() {
		
//		$this->template()->addTpl("textedit.tpl");
		
//		$this->template()->addVar('TEXT_EDIT_ARRAY', $this->getModel()->textEdit);		
		
//		$this->template()->addVar('TEXT_NAME', _('Text'));
		
//		$this->template()->addVar('BUTTON_TEXT_SEND', _('Odeslat'));
//		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));
		
//		$this->template()->setTplSubLabel(_('Úprava textu'));
		
		//NOTE soubory
//		$this->template()->addTpl($this->getModel()->files->getTpl(), true);
//		$this->getModel()->files->assignToTpl($this->template());

		//NOTE obrázky
//		$this->template()->addTpl($this->getModel()->images->getTpl(), true);
//		$this->getModel()->images->assignToTpl($this->template());
		
//		$this->template()->addVar('TEXT_IN_DB', $this->getModel()->inDb);
//		$tinymce = new TinyMce();
//		$this->template()->addJsPlugin($tinymce);
	}
	
}

?>