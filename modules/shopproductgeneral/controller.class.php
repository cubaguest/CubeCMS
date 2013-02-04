<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class ShopProductGeneral_Controller extends Shop_Product_Controller {
   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $this->loadProducts(
         $this->getRequestParam('num', $this->category()->getParam('scroll', 20)),
         $this->getRequestParam('sort'),
         $this->category()->getId());
      $this->createAddToCartForm();
   }

   /**
    * Kontroler pro editaci textu
    */
   public function editController() {
      $this->checkWritebleRights();
      $this->editProduct($this->getRequest('urlkey'));
      if($this->view()->product == false OR $this->view()->product == null){
         return false;
      }
   }
   /**
    * Kontroler pro editaci textu
    */
   public function addController() {
      $this->checkWritebleRights();
      
      $this->editProduct();
   }
   
   public function detailController()
   {
      //		Kontrola práv
      $this->checkReadableRights();
      $product = $this->loadCategoryProduct();
      
      if($product == false){
         return false;
      }

      if( VVE_SHOP_ALLOW_BUY_NOT_IN_STOCK ||
         ($product->{Shop_Model_Product::COLUMN_QUANTITY} > 0 && !VVE_SHOP_ALLOW_BUY_NOT_IN_STOCK )
         || !$product->{Shop_Model_Product::COLUMN_STOCK}){
         $this->createAddToCartForm($product->getPK(), true);
      }
      $this->processDeleteProduct($product);
      $this->processDuplicateProduct($product);
      $this->processChangeProductState($product);
      $this->view()->linkBack = $this->link()->route();

      $this->view()->product = $product;
   }

   public function editVariantsController()
   {
      $this->checkWritebleRights();
      $this->editProductVariants($this->getRequest('urlkey'));
      if($this->view()->product == false OR $this->view()->product == null){
         return false;
      }
   }

   /**
    * Kontroler pro editaci textu
    */
   public function settings(&$settings, Form &$form) {
      $fGrpView = $form->addGroup('view', $this->tr('Nastavení vzhledu'));

      $elemScroll = new Form_Element_Text('scroll', $this->tr('Počet položek na stránku'));
      $elemScroll->setSubLabel(sprintf($this->tr('Výchozí: %s položek. Pokud je zadána 0 budou vypsány všechny položky'),20));
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll, $fGrpView);

      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }
      
      if($form->isValid()) {
         $settings['scroll'] = (int)$form->scroll->getValues();
      }
   }

   public static function clearOnRemove(Category $category)
   {
      $model = new Shop_Model_Product();
      // delete product, combinations and variants
      $model->where(Shop_Model_Product::COLUMN_ID_CATEGORY." = :idc", array('idc' => $category->getId()))->delete();

   }
}

?>