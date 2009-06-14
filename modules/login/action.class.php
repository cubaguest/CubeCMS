<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class Login_Action extends Action {
	

	const ACTION_CHANGE_PASSWD_ABBR = 'cp';

    protected function init() {
		$this->addAction(self::ACTION_CHANGE_PASSWD_ABBR, "changepasswd",
         $this->_m('zmena-hesla'));
    }


	public function changePasswd() {
		return $this->createAction(self::ACTION_CHANGE_PASSWD_ABBR);
	}
	
}
?>