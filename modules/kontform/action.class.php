<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class Kontform_Action extends Action {

   const ACTION_EDIT_TEXT_ABBR = 'at';

	protected function init() {
		$this->addAction(self::ACTION_EDIT_TEXT_ABBR, "kontform", _('kontaktni-formular'));
    }


	public function editText() {
		return $this->createAction(self::ACTION_EDIT_TEXT_ABBR);
	}

}
?>