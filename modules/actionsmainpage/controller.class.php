<?php
class ActionsMainPage_Controller extends Controller {
      const DEFAULT_NUM_ACTIONS = 2;
   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      // uložení datumu do session pokud existuje - kvuli návratu
      // odkaz zpět
      $ids = $this->category()->getParam('catsid', array(0));
      $currentDateO = new DateTime();
      $dateNext = clone $currentDateO;
      $dateNext->modify('+3 month');
      $acM = new Actions_Model_List();
      $actions = $acM->getActionsByCatIds($currentDateO, $dateNext,$ids,
              !$this->getRights()->isWritable());

      $this->view()->count = $this->category()->getParam('count', self::DEFAULT_NUM_ACTIONS);
      $this->view()->actions = $actions;
   }

   public static function settingsController(&$settings,Form &$form) {
      $catM = new Model_Category();
      $modules = array('actions', 'actionswgal');
      $results = array();
      foreach ($modules as $module) {
         $cats = $catM->getCategoryListByModule($module);
         while($cat = $cats->fetch()) {
            $results[(string)$cat->{Model_Category::COLUMN_CAT_LABEL}] = $cat->{Model_Category::COLUMN_CAT_ID};
         }
      }

      $elemSelectedCategories = new Form_Element_Select('catsid', 'Kategorie ze kterých se má výbírat');
      $elemSelectedCategories->setOptions($results);
      $elemSelectedCategories->setMultiple();
      $form->addElement($elemSelectedCategories,'basic');

      if(isset($settings['catsid'])) {
         $form->catsid->setValues($settings['catsid']);
      }

      $elemCount = new Form_Element_Text('count', 'Počet zobrazených akcí');
      $elemCount->setSubLabel('Výchozí: '.self::DEFAULT_NUM_ACTIONS.' akcí');
      $elemCount->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemCount,'basic');

      if(isset($settings['count'])) {
         $form->count->setValues($settings['count']);
      }

      if($form->isValid()) {
         $settings['catsid'] = $form->catsid->getValues();
         $settings['count'] = $form->count->getValues();
      }
   }
}
?>