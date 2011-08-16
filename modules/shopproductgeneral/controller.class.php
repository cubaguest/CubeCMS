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
      $this->loadProducts($this->getRequestParam('num', $this->category()->getParam('scroll', 20)), $this->getRequestParam('sort'));
      $this->createAddToCartForm();
   }

   /**
    * Kontroler pro editaci textu
    */
   public function editController() {
      $this->checkWritebleRights();
      $this->editProduct($this->getRequest('urlkey'));
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
      $this->loadProduct();
      $this->deleteProduct();
      $this->createAddToCartForm();
      $this->view()->linkBack = $this->link()->route();
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
}

?>