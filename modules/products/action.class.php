<?php
/**
 * Třída pro obsluhu akcí v modulu
 *
 */
class ProductsAction extends Action {
	

	const ACTION_ADD_PRODUCT_ABBR = 'ap';
	const ACTION_EDIT_PRODUCT_ABBR = 'ep';

    protected function init() {
		$this->addAction(self::ACTION_ADD_PRODUCT_ABBR, "addproduct", _m('pridani-produktu'));
		$this->addAction(self::ACTION_EDIT_PRODUCT_ABBR, "editproduct", _m('uprava-produktu'));
    }


	public function addProduct() {
		return $this->createAction(self::ACTION_ADD_PRODUCT_ABBR);
	}

	public function editProduct() {
		return $this->createAction(self::ACTION_EDIT_PRODUCT_ABBR);
	}

}
?>