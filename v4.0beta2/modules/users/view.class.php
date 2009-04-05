<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class UsersView extends View {
	public function mainView() {
		
		$this->template()->addTpl("header.tpl");
		$this->template()->addVar("USER_PAGE_NAME", _("Seznam uživatelů"));
		
		if($this->getRights()->isControll()){
			$this->template()->addTpl("buttonadd.tpl");

			$this->template()->addVar("LINK_TO_ADD", $this->getModel()->linkToAdd);
			$this->template()->addVar("LINK_TO_ADD_NAME", _("Přidat uživatele"));
			
			
		}
		
		$this->template()->addTpl("list.tpl");

		$this->template()->addVar("USERS_LIST_ARRAY", $this->getModel()->allUsersArray);
		
		//prvky pro řazení
		$this->template()->addVar("USERS_ORDER_LINKS", $this->getModel()->usersTableOrder);

		//prvky pro hledání
		$this->template()->addVar("USER_SEARCH_ARRAY", $this->getModel()->userSearchArray);
		
//		scrolovátka
		$this->template()->addTpl($this->getModel()->scroll->getTpl(), true);
		$this->getModel()->scroll->assignToTpl($this->template());
	
		$this->assignUsersListLabels();
		
		//		Zobrazení šablony EPluginu changes
		$this->template()->addTpl($this->getModel()->changes->getTpl(), true);
		$this->getModel()->changes->assignToTpl($this->template());
	}
	
	private function assignUsersListLabels() {
		$this->template()->addVar("USERS_ID", _("Id"));
		$this->template()->addVar("USERS_NAME", _("Jméno"));
		$this->template()->addVar("USERS_GROUPNAME", _("Skupina"));
		$this->template()->addVar("USERS_SURNAME", _("Přijmení"));
		$this->template()->addVar("USERS_USERNAME", _("Uživ. jméno"));
		$this->template()->addVar("USER_SHOW_DETAIL", _("Zobrazí detail uživatele"));
		$this->template()->addVar("USERS_SEARCH_BUTTON", _("Hledat"));
		$this->template()->addVar("USERS_RESET_BUTTON", _("Vymazat"));
	}
	
	/**
	 * Viewer pro zobrazení detailu
	 */
	public function showView() {
		$this->template()->addTpl("header.tpl");
		$this->template()->addVar("USER_PAGE_NAME", _("Detail uživatele"));
		
		$this->template()->addTpl("detail.tpl");
		
		$this->template()->addVar("USER_CONTROL", $this->getRights()->isControll());
		
		$this->template()->addVar("USER_DETAIL_ARRAY", $this->getModel()->userDetailArray);
		$this->template()->addVar("USER_EDIT_LINK", $this->getModel()->linkToEdit);
		$this->template()->addVar("BUTTON_BACK", $this->getModel()->linkToBack);
		
		$this->template()->addJsPlugin(new SubmitForm());
				
		$this->assignUserDetailLabels();
		
		//		Zobrazení šablony EPluginu changes
		$this->template()->addTpl($this->getModel()->changes->getTpl(), true);
		$this->getModel()->changes->assignToTpl($this->template());
		
	}
	
	private function assignUserDetailLabels() {
		$this->template()->addVar("USER_ID", _("Id"));
		$this->template()->addVar("USER_NAME", _("Jméno"));
		$this->template()->addVar("USER_SURNAME", _("Přijmení"));
		$this->template()->addVar("USER_USERNAME", _("Uživatelské jméno"));
		$this->template()->addVar("USER_MAIL", _("E-mail"));
		$this->template()->addVar("USER_GROUP", _("Skupina"));
		$this->template()->addVar("USER_NOTE", _("Poznámka"));
		$this->template()->addVar("USER_DETAIL", _("Detail"));
		$this->template()->addVar("USER_LABEL", _("Popis"));
		$this->template()->addVar("USER_NONE_RECORD", _("žádné"));
		$this->template()->addVar("USER_EDIT", _("Upravit"));
		$this->template()->addVar("USER_DELETE", _("Smazat"));
		$this->template()->addVar("BUTTON_BACK_NAME", _("Zpět"));
		$this->template()->addVar("BUTTON_DELETE_USER_MESSAGE", _("Opravdu smazat uživatele"));
		$this->template()->addVar("USER_PASSWORD", _("Heslo"));
		$this->template()->addVar("USER_PASSWORD2", _("Potvrzení hesla"));
		
	}
	
	public function editView() {
		$this->template()->addTpl("header.tpl");
		$this->template()->addVar("USER_PAGE_NAME", _("Úprava uživatele"));
		
		$this->template()->addTpl("edit.tpl");
		
		$this->template()->addVar("USER_DETAIL_ARRAY", $this->getModel()->userDetailArray);
		$this->template()->addVar("BUTTON_BACK", $this->getModel()->linkToBack);
		
		$this->assignUserDetailLabels();
		$this->assignEditFormLabels();
		
		$tmpArray = array();
		foreach ($this->getModel()->groupsArray as $key => $group) {
			$tmpArray[$group["id_group"]] = $group["label"];
		}
		$this->getModel()->groupsArray = $tmpArray;
		
		$this->template()->addVar("USER_GROUPS", $this->getModel()->groupsArray);
	}
	
	private function assignEditFormLabels() {
		
		$this->template()->addVar("USER_SEND", _("Uložit"));
		$this->template()->addVar("USER_RESET", _("Obnovit"));
	}
	
	public function addView() {
		$this->template()->addTpl("header.tpl");
		$this->template()->addVar("USER_PAGE_NAME", _("Přidání uživatele"));
		$this->template()->addTpl("edit.tpl");
		
		$this->assignUserDetailLabels();
		
		$tmpArray = array();
		foreach ($this->getModel()->groupsArray as $key => $group) {
			$tmpArray[$group["id_group"]] = $group["label"];
		}
		$this->getModel()->groupsArray = $tmpArray;
		
		$this->template()->addVar("USER_GROUPS", $this->getModel()->groupsArray);
		$this->template()->addVar("BUTTON_BACK", $this->getModel()->linkToBack);
		$this->assignEditFormLabels();
		
		
	}
	
}

?>