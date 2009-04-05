<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class TextAction extends Action {

   const ACTION_EDIT_TEXT_ABBR = 'at';

	protected function init() {
		$this->addAction(self::ACTION_EDIT_TEXT_ABBR, "edittext", _('uprava-textu'));
   }

	public function editText() {
		return $this->createAction(self::ACTION_EDIT_TEXT_ABBR);
	}
}
?>