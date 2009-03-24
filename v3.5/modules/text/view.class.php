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
			
			// editační tlačítka
         $jquery = new JQuery();
         $this->template()->addJsPlugin($jquery);
		}
		$this->template()->addTpl("text.tpl");
		$this->template()->addVar('TEXT', $this->container()->getData('text'));
	}
	/*EOF mainView*/
	
	public function edittextView() {
		$this->template()->addTpl("textedit.tpl");
		$this->template()->addVar('TEXT_NAME', _('Text'));
		$this->template()->addVar('BUTTON_TEXT_SEND', _('Odeslat'));
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));
		$this->template()->setTplSubLabel(_('Úprava textu'));
		
		//NOTE soubory
		$this->template()->addTpl($this->container()->getEplugin('files')->getTpl(), true);
		$this->container()->getEplugin('files')->assignToTpl($this->template());

		//NOTE obrázky
		$eplImages = $this->container()->getEplugin('images');
		$this->template()->addTpl($eplImages->getTpl(), true);
		$eplImages->assignToTpl($this->template());
		
		$tinymce = new TinyMce();
//		$tinymce->setTheme(TinyMce::TINY_THEME_SIMPLE);
		$tinymce->setImagesList($eplImages->getImagesListLink(UserImagesEplugin::FILE_IMAGES_FORMAT_TINYMCE));
		$this->template()->addJsPlugin($tinymce);

      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);

      $this->template()->addVar('BUTTON_BACK_NAME', _('Zpět'));;
	}
	// EOF edittextView
}

?>