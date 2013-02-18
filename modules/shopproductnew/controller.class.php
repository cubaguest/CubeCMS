<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class ShopProductNew_Controller extends Shop_Product_Controller {
   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $this->loadProducts(
         $this->getRequestParam('num', $this->category()->getParam('scroll', 20)),
         $this->getRequestParam('sort'));
      $this->createAddToCartForm();
   }

   protected function loadProducts($productsOnPage = 0, $sort = 'p', $idCategory = 0, $joinCategory = false)
   {
      $model = new Shop_Model_Product();
      $model->setSelectAllLangs(false);
      $model
         ->joinFK(Shop_Model_Product::COLUMN_ID_TAX)
         ->join(Shop_Model_Product::COLUMN_ID, 'Shop_Model_ProductCombinations', Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT);

//      if($joinCategory){
         $model->joinFK(Shop_Model_Product::COLUMN_ID_CATEGORY);
//      }

      $model->where( ($this->category()->getRights()->isWritable() ? null : Shop_Model_Product::COLUMN_ACTIVE.' = 1 AND ')
            .'('.Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT.' = 1 OR '.Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT.' IS NULL )'
            .'AND '.Shop_Model_Product::COLUMN_URLKEY.' IS NOT NULL '
            .'AND '.Shop_Model_Product::COLUMN_IS_NEW_TO_DATE.' >= CURDATE() ',
         array()
      );

      // řazení
      $sortTypes = self::getSortTypes();
      if(isset ($sortTypes[$sort])){
         $model->order(array($sortTypes[$sort]['column'] => $sortTypes[$sort]['sort']));
      }

      $scrollComponent = null;
      if($productsOnPage != 0){
         $scrollComponent = new Component_Scroll();
         $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());
         $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE, $productsOnPage);
      }

      if($scrollComponent instanceof Component_Scroll){
         $model->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage());
      }
      $model->groupBy(Shop_Model_Product::COLUMN_ID);
      $this->view()->scrollComp = $scrollComponent;
      $this->view()->products = $model->records();
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
