<?php
class ShopProductGeneral_Panel extends Panel {
   const PARAM_NUM_PRODUCTS = "n";
   const PARAM_SHOW_ALL_CATS = 'sa';
   const PARAM_SHOW_ONLY_NEW = 'sn';

   public function panelController() {
      $modelProducts = new Shop_Model_Product();
      $modelProducts->setSelectAllLangs(false);

      $whereStr =
         Shop_Model_Product::COLUMN_ACTIVE.' = 1 '
               .'AND ('.Shop_Model_Product_Combinations::COLUMN_IS_DEFAULT.' = 1 OR '.Shop_Model_Product_Combinations::COLUMN_IS_DEFAULT.' IS NULL )'
               .'AND '.Shop_Model_Product::COLUMN_URLKEY.' IS NOT NULL';

      $whereBinds = array();

      if(!$this->panelObj()->getParam(self::PARAM_SHOW_ALL_CATS, false)){
         $whereStr .= ' AND '.Shop_Model_Product::COLUMN_ID_CATEGORY." = :idc";
         $whereBinds['idc'] = $this->category()->getId();
      }
      if($this->panelObj()->getParam(self::PARAM_SHOW_ONLY_NEW, false)){
         $whereStr .=' AND '.Shop_Model_Product::COLUMN_IS_NEW_TO_DATE.' >= CURDATE()';
      }

      $modelProducts
         ->joinFK(Shop_Model_Product::COLUMN_ID_CATEGORY, array(Model_Category::COLUMN_NAME, 'curlkey' => Model_Category::COLUMN_URLKEY))
         ->joinFK(Shop_Model_Product::COLUMN_ID_TAX)
         ->join(Shop_Model_Product::COLUMN_ID, 'Shop_Model_Product_Combinations', Shop_Model_Product_Combinations::COLUMN_ID_PRODUCT)
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

      $elemAllCats = new Form_Element_Checkbox('allCats', $this->tr('Ze všech kategorí'));
      $elemAllCats->setSubLabel($this->tr('Zobrazí produkty ze všech kategorií') );
      $form->addElement($elemAllCats,'basic');
      if(isset($settings[self::PARAM_SHOW_ALL_CATS])) {
         $form->allCats->setValues($settings[self::PARAM_SHOW_ALL_CATS]);
      }

      $elemNewsOnly = new Form_Element_Checkbox('newsOnly', $this->tr('Pouze novinky'));
      $elemNewsOnly->setSubLabel($this->tr('Zobrazí pouze nové produkty (novinky)') );
      $form->addElement($elemNewsOnly, 'basic');
      if(isset($settings[self::PARAM_SHOW_ONLY_NEW])) {
         $form->newsOnly->setValues($settings[self::PARAM_SHOW_ONLY_NEW]);
      }


      if($form->isValid()) {
         $settings[self::PARAM_NUM_PRODUCTS] = $form->num->getValues();
         $settings[self::PARAM_SHOW_ALL_CATS] = $form->allCats->getValues();
         $settings[self::PARAM_SHOW_ONLY_NEW] = $form->newsOnly->getValues();
      }
   }
}
?>