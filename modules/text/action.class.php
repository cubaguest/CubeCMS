<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class TextAction extends Action {
	/**
	 * Akce pro editaci textu
	 */
	public function editText() {
		$actionAbbr = 'ed';
		$this->addAction($actionAbbr, "edittext", _('uprava-textu'));
		return $this->createActionUrl($actionAbbr);
	}

}
?>