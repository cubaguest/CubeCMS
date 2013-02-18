<?php
class ShopProductNew_Panel extends Panel {
   const PARAM_NUM_PRODUCTS = "n";

   public function panelController() {
      $modelProducts = new Shop_Model_Product();
      $modelProducts->setSelectAllLangs(false);

      $whereStr =
         Shop_Model_Product::COLUMN_ACTIVE.' = 1 '
               .'AND ('.Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT.' = 1 OR '.Shop_Model_ProductCombinations::COLUMN_IS_DEFAULT.' IS NULL )'
               .'AND '.Shop_Model_Product::COLUMN_URLKEY.' IS NOT NULL';

      $whereBinds = array();

      $modelProducts
         ->joinFK(Shop_Model_Product::COLUMN_ID_CATEGORY, array(Model_Category::COLUMN_NAME, 'curlkey' => Model_Category::COLUMN_URLKEY))
         ->joinFK(Shop_Model_Product::COLUMN_ID_TAX)
         ->join(Shop_Model_Product::COLUMN_ID, 'Shop_Model_ProductCombinations', Shop_Model_ProductCombinations::COLUMN_ID_PRODUCT)
         ->limit(0, $this->panelObj()->getParam(self::PARAM_NUM_PRODUCTS, 3))
         ->order(array('RAND(NOW())' => Model_ORM::ORDER_ASC))
         ->where($whereStr, $whereBinds)
         ->having('curlkey IS NOT NULL', array()); // aby nebyly produkty bez kategorie

      $this->template()->products = $modelProducts->records();
	}
	
	public function panelView() {
      
      $this->template()->addFile('tpl://panel.phtml');
	}

   protected function settings(&$settings,Form &$form) {
      $elemNum = new Form_Element_Text('num', $this->tr('Počet produktů v panelu'));
      $elemNum->setSubLabel('Výchozí: 3');
      $elemNum->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemNum, 'basic');

      if(isset($settings[self::PARAM_NUM_PRODUCTS])) {
         $form->num->setValues($settings[self::PARAM_NUM_PRODUCTS]);
      }

      if($form->isValid()) {
         $settings[self::PARAM_NUM_PRODUCTS] = $form->num->getValues();
      }
   }
}
?>