<?php
/** 
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ShopProductList_View extends Shop_Product_View {
	public function mainView()
   {
//      $this->template()->addFile('tpl://shopproductgeneral:list.phtml');
      $this->template()->addFile('tpl://main.phtml');
   }
}
