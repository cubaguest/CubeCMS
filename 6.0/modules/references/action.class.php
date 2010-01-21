<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class References_Action extends Action {
	const ACTION_ADD_REFER_ABBR = 'ar';
	const ACTION_EDIT_TEXT_REFER_ABBR = 'ert';
	const ACTION_EDIT_PHOTOS_REFER_ABBR = 'erp';

    protected function init() {
      $this->addAction(self::ACTION_ADD_REFER_ABBR, "addreference", $this->_('pridani-reference'));
      $this->addAction(self::ACTION_EDIT_TEXT_REFER_ABBR, "editreferencetext", $this->_('uprava-textu-reference'));
		$this->addAction(self::ACTION_EDIT_PHOTOS_REFER_ABBR, "editreferencephotos", $this->_('uprava-fotek-reference'));
    }


	public function addReference() {
		return $this->createAction(self::ACTION_ADD_REFER_ABBR);
	}

	public function editReferenceText() {
		return $this->createAction(self::ACTION_EDIT_TEXT_REFER_ABBR);
	}
	public function editReferencePhotos() {
		return $this->createAction(self::ACTION_EDIT_PHOTOS_REFER_ABBR);
	}
}
?>