<?php

/**
 * Třída shop_cart_item
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
      $this->addTinyMCE();
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
      $this->addTinyMCE();
      $this->template()->addFile("tpl://engine:shop/product_edit_variants.phtml");
      Template_Module::setEdit(true);
   }
   
   private function addTinyMCE() {
      $this->form->text->html()->addClass("mceEditor");
      $this->form->textShort->html()->addClass("mceEditor");
      $this->tinyMCE = new Component_TinyMCE();
      $settings = new Component_TinyMCE_Settings_Advanced();
      $settings->setSetting('height', '300');
      $this->tinyMCE->setEditorSettings($settings);
      $this->tinyMCE->mainView();
   }
   
}
?>
