<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class Orderform_Action extends Action {
   const ACTION_CONFIRM_ORDER = 'cfo';

    protected function init() {
      $this->addAction(self::ACTION_CONFIRM_ORDER, "confirmOrder", $this->_('potvrzeni-objednavky'));
    }


	public function confirmOrder() {
		return $this->createAction(self::ACTION_CONFIRM_ORDER);
	}
}
?>