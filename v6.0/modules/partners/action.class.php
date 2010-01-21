<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class PartnersAction extends Action {
	

	const ACTION_ADD_PARTNER_ABBR = 'ap';
	const ACTION_EDIT_PARTNER_ABBR = 'ep';

    protected function init() {
		$this->addAction(self::ACTION_ADD_PARTNER_ABBR, "addpartner", _('pridani-partnera'));
		$this->addAction(self::ACTION_EDIT_PARTNER_ABBR, "editpartner", _('uprava-partnera'));
    }


	public function addPartner() {
		return $this->createAction(self::ACTION_ADD_PARTNER_ABBR);
	}
	
	public function editPartner() {
		return $this->createAction(self::ACTION_EDIT_PARTNER_ABBR);
	}

}
?>