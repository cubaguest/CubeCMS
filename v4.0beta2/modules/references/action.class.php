<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class ReferencesAction extends Action {
	

	const ACTION_EDIT_REF_ABBR = 'er';
	const ACTION_EDIT_OTHER_REF_ABBR = 'eor';

    protected function init() {
		$this->addAction(self::ACTION_EDIT_REF_ABBR, "editref", _('uprava-reference'));
		$this->addAction(self::ACTION_EDIT_OTHER_REF_ABBR, "editotherref", _('uprava-ostatnich-referenci'));
    }

	public function editReference() {
		return $this->createAction(self::ACTION_EDIT_REF_ABBR);
	}

   public function editOtherReference() {
		return $this->createAction(self::ACTION_EDIT_OTHER_REF_ABBR);
	}

}
?>