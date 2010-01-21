<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class DwfilesView extends View {
	public function mainView() {
		if($this->getRights()->isWritable()){
			$this->template()->addTpl('addButton.tpl');
			$this->template()->addVar('LINK_TO_ADD_FILE_NAME', _("Přidat soubor"));
//
//			// editační tlačítka
         $jquery = new JQuery();
         $this->template()->addJsPlugin($jquery);
         $this->template()->addVar('EDITABLE', true);
         $this->template()->addVar('DELETE_DWFILE_CONFIRM_MESSAGE', _('Opravdu smazat soubor'));
         $this->template()->addVar('BUTTON_DELETE_FILE', _('Smazat soubor'));
         $this->template()->addJsPlugin(new SubmitForm());
		}
		$this->template()->addTpl("files.tpl");
      $this->template()->addCss('style.css');
		$this->template()->addVar('NOT_ANY_FILE',_('Žádný soubor nebyl uložen'));
		$this->template()->addVar('DOWNLOAD_THIS',_('Stáhnout'));
	}
	/*EOF mainView*/
	
	public function addfileView() {
		$this->template()->addTpl("addFile.tpl");
		$this->template()->addVar('FILE_LABEL_LABEL', _('Název'));
		$this->template()->addVar('FILE_LABEL', _('Soubor'));
		$this->template()->addVar('BUTTON_SEND', _('Odeslat'));
		$this->template()->addVar('BUTTON_RESET', _('Obnovit'));
		$this->template()->setTplSubLabel(_('Přidání souboru'));
//      $this->template()->setTplLabel(_('Přidání souboru'));
      $this->template()->setSubTitle(_('Přidání souboru'));
		
      $jquery = new JQuery();
      $jquery->addWidgentTabs();
      $this->template()->addJsPlugin($jquery);

      $this->template()->addVar('BUTTON_BACK_NAME', _('Zpět'));;
	}
}

?>