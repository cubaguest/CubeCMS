<?php
class ActionsList_Controller extends Actions_Controller {
   const PARAM_CATEGORY_IDS = 'catsid';

   protected function getEvents($dateStart, $dateEnd)
   {
      return Actions_Model::getActions(
             $this->category()->getModule()->getParam(self::PARAM_CATEGORY_IDS, array()), // idc
             $dateStart, $dateEnd, // rozsah
             !Auth::isAdmin() // restrikca uživatele
            );
   }
   
   public function featuredListController() {
      $model = new Actions_Model();

      $to = new DateTime();
      $to->modify('+1 year');
      
      $this->view()->actions = $model->getActions($this->category()->getParam(self::PARAM_CATEGORY_IDS), new DateTime(), $to);
      if($this->view()->action === false) return false;
   }
   
   public function currentActController() {
      $this->view()->action = Actions_Model::getCurrentAction($this->category()->getParam(self::PARAM_CATEGORY_IDS), (int)$this->getRequestParam('from', 0));
//      if($this->view()->action === false) return false;
   }
   
   public function settings(&$settings,Form &$form) 
   {
      parent::settings($settings, $form);
      $modules = array('actions', 'actionswgal');
      $results = array();
      foreach ($modules as $module) {
         $cats = Model_Category::getCategoryListByModule($module);
         foreach($cats as $cat) {
            $results[(string)$cat->{Model_Category::COLUMN_CAT_LABEL}.'(ID: '.(string)$cat->getPK().')'] = $cat->{Model_Category::COLUMN_CAT_ID};
         }
      }

      $elemSelectedCategories = new Form_Element_Select('catsid', 'Kategorie ze kterých se má výbírat');
      $elemSelectedCategories->setOptions($results);
      $elemSelectedCategories->setMultiple();
      $form->addElement($elemSelectedCategories,'basic');
      
      if(isset($settings[self::PARAM_CATEGORY_IDS])) {
         $form->catsid->setValues($settings[self::PARAM_CATEGORY_IDS]);
      }

      // odebrání elementů týkajících se obrázků
      $form->removeElement('img_width');
      $form->removeElement('img_height');

      if($form->isValid()) {
         $settings[self::PARAM_CATEGORY_IDS] = $form->catsid->getValues();
      }
   }
}