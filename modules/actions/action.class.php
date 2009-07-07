<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class Actions_Action extends Action {
	

	const ACTION_ADD_ACTION_ABBR = 'aac';
	const ACTION_EDIT_ACTION_ABBR = 'eac';

    protected function init() {
		$this->addAction(self::ACTION_ADD_ACTION_ABBR, "addnewaction", $this->_('pridani-akce'));
		$this->addAction(self::ACTION_EDIT_ACTION_ABBR, "editaction", $this->_('uprava-akce'));
    }


	public function addNewAction() {
		return $this->createAction(self::ACTION_ADD_ACTION_ABBR);
	}
	public function editAction() {
		return $this->createAction(self::ACTION_EDIT_ACTION_ABBR);
	}

}
?>