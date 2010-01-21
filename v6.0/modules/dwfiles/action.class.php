<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class DwfilesAction extends Action {

   const ACTION_ADD_FILE_ABBR = 'af';

	protected function init() {
		$this->addAction(self::ACTION_ADD_FILE_ABBR, "addfile", _('pridani-souboru'));
    }


	public function addFile() {
		return $this->createAction(self::ACTION_ADD_FILE_ABBR);
	}

}
?>