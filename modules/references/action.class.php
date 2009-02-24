<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class ReferencesAction extends Action {
	

	const ACTION_EDIT_REF_ABBR = 'er';

    protected function init() {
		$this->addAction(self::ACTION_EDIT_REF_ABBR, "editref", _('uprava-reference'));
    }

	public function editReference() {
		return $this->createAction(self::ACTION_EDIT_REF_ABBR);
	}

}
?>