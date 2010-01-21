<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class Contacts_Action extends Action {
   const ACTION_ADD_CONTACT_ABBR = 'ac';
	const ACTION_EDIT_CONTACT_ABBR = 'ec';

    protected function init() {
       $this->addAction(self::ACTION_ADD_CONTACT_ABBR, "addcontact", $this->_m('pridani-kontaktu'));
		$this->addAction(self::ACTION_EDIT_CONTACT_ABBR, "editcontact", $this->_m('uprava-kontaktu'));
    }


	public function addContact() {
		return $this->createAction(self::ACTION_ADD_CONTACT_ABBR);
	}

	public function editContact() {
		return $this->createAction(self::ACTION_EDIT_CONTACT_ABBR);
	}
}
?>