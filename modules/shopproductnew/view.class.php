<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ShopProductNew_View extends Shop_Product_View {
   public function mainView() 
   {
      $this->template()->addFile('tpl://shopproductgeneral:list.phtml');
   }
}
