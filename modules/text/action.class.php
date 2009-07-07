<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class Text_Action extends Action {

   const ACTION_EDIT_TEXT_ABBR = 'et';

	protected function init() {
		$this->addAction(self::ACTION_EDIT_TEXT_ABBR, "edittext", _('uprava-textu'));
   }

	public function editText() {
		return $this->createAction(self::ACTION_EDIT_TEXT_ABBR);
	}
}
?>