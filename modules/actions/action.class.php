<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class ActionsAction extends Action {
	

	const ACTION_ADD_ACTION_ABBR = 'ac';

    protected function init() {
		$this->addAction(self::ACTION_ADD_ACTION_ABBR, "addaction", _m('pridani-akce'));
    }


	public function addAction() {
		return $this->createAction(self::ACTION_ADD_ACTION_ABBR);
	}

}
?>