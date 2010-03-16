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

   // RSS
//   public function exportController() {
//      $this->checkReadableRights();
//      $this->view()->type = $this->getRequest('type', 'rss');
//
//      $ids = explode(',', $this->category()->getModule()->getParam('catids', '0'));
//
//      $acM = new Actions_Model_List();
//      $actions = $acM->getActionsByAddedByCatIds($ids, VVE_FEED_NUM);
//      $this->view()->actions = $actions;
//   }
//
//   public function listCatAddController(){
//      $this->checkWritebleRights();
//      $catM = new Model_Category();
//
//      $ids = explode(',', $this->category()->getModule()->getParam('catids', '0'));
//      $catsArr = array();
//      foreach ($ids as $idc){
//          $cat = $catM->getCategoryById($idc);
//          array_push($catsArr, array('name' => $cat->{Model_Category::COLUMN_CAT_LABEL},
//                  'url' => $this->link()->category($cat->{Model_Category::COLUMN_URLKEY})->route('add')));
//      }
//      $this->view()->categories = $catsArr;
//
//   }
//
//   public function featuredListController() {
//      $model = new Actions_Model_List();
//      $ids = explode(',', $this->category()->getModule()->getParam('catids', '0'));
//      $toDate = new DateTime();
//      $toDate->modify("+6 month");
//      $this->view()->actions = $model->getActionsByCatIds(new DateTime(), $toDate, $ids);
//      if($this->view()->action === false) return false;
//   }
//
//   public function currentActController() {
//      $model = new Actions_Model_List();
//      $ids = explode(',', $this->category()->getModule()->getParam('catids', '0'));
//      $actions = $model->getActionsByCatIds(time(), time()+3600*24*30, $ids);
//
//      $this->view()->action = $actions->fetch();
////      if($this->view()->action === false) return false;
//   }
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