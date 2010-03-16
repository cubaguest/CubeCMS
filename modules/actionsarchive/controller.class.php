<?php
class ActionsArchive_Controller extends ActionsList_Controller {
   const DEFAULT_NUM_ACTIONS = 5;
   const DEFAULT_TYPE = 1;

   const TYPE_PAST = 1;
   const TYPE_FEATURE = 2;
   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      $model = new Actions_Model_List();
      $scrollComponent = new Component_Scroll();

      $fromDate = new DateTime();
      $type = $this->category()->getParam('type', self::DEFAULT_TYPE);
      if($type == self::TYPE_PAST){
         $past = true;
         $fromDate->modify('-1 day');
         // posun na poslední stránku
         $scrollComponent->setConfig(Component_Scroll::CONFIG_START_PAGE, Component_Scroll::PAGE_LAST);
         $scrollComponent->setConfig(Component_Scroll::CONFIG_BACKWARD, true);
      } else {
         $past = false;
         $fromDate->modify('+1 day');
      }

      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS,
              $model->getCountActionsListByCatIds($fromDate, $this->category()->getParam('catsid', array(0)),$past));

      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              $this->category()->getParam('scroll', self::DEFAULT_NUM_ACTIONS));

      $actions = $model->getActionsListByCatIds($fromDate, $this->category()->getParam('catsid', array(0)),
              $scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage(),$past);

      $this->view()->actions = $actions;
      $this->view()->scrollComp = $scrollComponent;
      $this->view()->page = $scrollComponent->getCurPageNumber();

      // načtení textu
      $this->view()->text = $this->loadActionsText();
   }

   private function loadActionsText() {
      $textM = new Text_Model_Detail();
      $text = $textM->getText($this->category()->getId());
      return $text;
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

      $elemScroll = new Form_Element_Text('scroll', 'Počet akcí na stránku');
      $elemScroll->setSubLabel('Výchozí: '.self::DEFAULT_NUM_ACTIONS.'');
      $elemScroll->html()->setAttrib('size', '5');
      $form->addElement($elemScroll,'basic');
      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }

      $elemType = new Form_Element_Select('type', 'Zobrazit akce které');
      $types = array('Akce které již byly' => self::TYPE_PAST, 'Akce které teprve budou' => self::TYPE_FEATURE);
      $elemType->setOptions($types);
      $elemType->setSubLabel('Výchozí: '.array_search(self::DEFAULT_TYPE, $types).'');
      $form->addElement($elemType,'basic');
      if(isset($settings['type'])) {
         $form->type->setValues($settings['type']);
      }


      if($form->isValid()) {
         $settings['catsid'] = $form->catsid->getValues();
         $settings['scroll'] = $form->scroll->getValues();
         // protože je vždy hodnota
         if($form->type->getValues() != self::DEFAULT_TYPE){
            $settings['type'] = $form->type->getValues();
         } else {
            unset ($settings['type']);
         }
      }
   }
}
?>