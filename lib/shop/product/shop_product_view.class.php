<?php

/**
 * Třída Shop_Product_View
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id:  $ VVE 6.4 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
 */
class Shop_Product_View extends View {

   /**
    * Metoda vytvoří formulář produktu
    * @param type $product
    * @return Form 
    */
   protected function editProduct($edit = false)
   {
      $this->edit = $edit;
      $this->setTinyMCE($this->form->text, 'advanced');
      $this->setTinyMCE($this->form->textShort, 'simple');
      $this->template()->addFile("tpl://engine:shop/product_edit.phtml");
      Template_Module::setEdit(true);
   }
   
   /**
    * Metoda vytvoří formulář variant produktu
    * @param type $product
    * @return Form 
    */
   protected function editProductVariants()
   {
//      $this->addTinyMCE();
      $this->template()->addFile("tpl://engine:shop/product_edit_variants.phtml");
      Template_Module::setEdit(true);
   }
}
?>
