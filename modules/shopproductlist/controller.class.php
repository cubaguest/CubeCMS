<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class ShopProductList_Controller extends Shop_Product_Controller {

   public function init()
   {
       parent::init();
   }

   public function mainController()
   {
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
         ->join(Shop_Model_Product::COLUMN_ID, 'Shop_Model_Product_Combinations', Shop_Model_Product_Combinations::COLUMN_ID_PRODUCT);

      $struct = Category_Structure::getStructure(Category_Structure::ALL)->getCategory($this->category()->getId());
      $cats = $this->catsToArrayForForm($struct);
      

      $model->joinFK(Shop_Model_Product::COLUMN_ID_CATEGORY);
      $model->where( ($this->category()->getRights()->isWritable() ? null : Shop_Model_Product::COLUMN_ACTIVE.' = 1 AND ')
            .'('.Shop_Model_Product_Combinations::COLUMN_IS_DEFAULT.' = 1 OR '.Shop_Model_Product_Combinations::COLUMN_IS_DEFAULT.' IS NULL )'
            .'AND '.Shop_Model_Product::COLUMN_URLKEY.' IS NOT NULL AND '.Shop_Model_Product::COLUMN_ID_CATEGORY.' IN ('.$model->getWhereINPlaceholders($cats).')',
         $model->getWhereINValues($cats)
      );

      // řazení
      $sortTypes = self::getSortTypes();
      $model->order(array(Shop_Model_Product::COLUMN_NAME => Model_ORM::ORDER_ASC));
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

   private function catsToArrayForForm($categories)
   {
      $ret = array();
      // pokud je hlavní kategorie
      if ($categories->getLevel() != 0) {
         $ret[] = $categories->getCatObj()->getId();
      } 
      if (!$categories->isEmpty()) {
         foreach ($categories as $cat) {
            $ret = array_merge($ret, $this->catsToArrayForForm($cat));
         }
      }
      return $ret;
   }
   
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
