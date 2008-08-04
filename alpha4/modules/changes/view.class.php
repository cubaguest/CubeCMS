<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ChangesView extends View {
	public function mainView() {
		$this->template()->addTpl("list.tpl");
//		$this->template()->addTpl("scroll.tpl");
//		$this->template()->addCss("pokus.css");
//		$this->template()->addJS("pokus.js");
//		
		$this->template()->addVar("CHANGES_LIST_ARRAY", $this->getModel()->allChangesArray);
		
		//prvky pro řazení
		$this->template()->addVar("CHANGES_ORDER_LINKS", $this->getModel()->changesTableOrder);

		//prvky pro hledání
		$this->template()->addVar("CHANGE_SEARCH_ARRAY", $this->getModel()->changeSearchArray);
		
//		scrolovátka
		$this->template()->addTpl($this->getModel()->scroll->getTpl(), true);
		$this->getModel()->scroll->assignToTpl($this->template());
		
		$this->assignChangesListLabels();
	}
	
	private function assignChangesListLabels() {
		$this->template()->addVar("CHANGES_NAME_OF_LIST", _("Seznam změn"));
		$this->template()->addVar("CHANGES_NAME", _("Jméno"));
		$this->template()->addVar("CHANGES_SURNAME", _("Přijmení"));
		$this->template()->addVar("CHANGES_USERNAME", _("Uživ. jméno"));
		$this->template()->addVar("CHANGES_TIME", _("Čas"));
		$this->template()->addVar("CHANGES_LABEL", _("Popis"));
		$this->template()->addVar("CHANGES_SEARCH_BUTTON", _("Hledat"));
		$this->template()->addVar("CHANGES_RESET_BUTTON", _("Vymazat"));
	}
	
	/**
	 * Viewer pro zobrazení detailu
	 */
	public function showView() {
		
	}
	
	public function editView() {
		
	}
}

?>