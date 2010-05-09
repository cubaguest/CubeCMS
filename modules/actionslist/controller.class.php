<?php
class ActionsList_Controller extends Controller {
   const PARAM_CATEGORY_IDS = 'catsid';
   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      // uložení datumu do session pokud existuje - kvuli návratu
      // odkaz zpět
      $this->link()->backInit();

      if($this->getRequest('month') != null) {
         $currentDateO = new DateTime($this->getRequest('year', date("Y")).'-'
                         .$this->getRequest('month', date("m")).'-'
                         .$this->getRequest('day', date("d")));
      } else {
         $currentDateO = new DateTime();
      }

      $dateNext = clone $currentDateO;
      $datePrev = clone $currentDateO;
      $time = $this->category()->getParam('time', 1);
      switch (strtolower($this->category()->getParam('type', 'month'))) {
         case 'year':
            $dateNext->modify("+".$time." year");
            $datePrev->modify("-".$time." year");
            $linkNextLabel = sprintf($this->ngettext('+ %s year','+ %s years',(int)$time, 'actions'), $time);
            $linkBackLabel = sprintf($this->ngettext('- %s year','+ %s years',(int)$time, 'actions'), $time);
            break;
         case 'month':
            $dateNext->modify("+".$time." month");
            $datePrev->modify("-".$time." month");
            $linkNextLabel = sprintf($this->ngettext('+ %s month','+ %s months',(int)$time, 'actions'), $time);
            $linkBackLabel = sprintf($this->ngettext('- %s month','+ %s months',(int)$time, 'actions'), $time);
            break;
         case 'day':
         default:
            $dateNext->modify("+".$time." day");
            $datePrev->modify("-".$time." day");
            $linkNextLabel = sprintf($this->ngettext('+ %s day','+ %s days',(int)$time, 'actions'), $time);
            $linkBackLabel = sprintf($this->ngettext('- %s day','+ %s days',(int)$time, 'actions'), $time);
            break;
      }
      $acM = new Actions_Model_List();
      $actions = $acM->getActionsByCatIds($currentDateO, $dateNext,
              $this->category()->getModule()->getParam(self::PARAM_CATEGORY_IDS, array(0)),
              !$this->getRights()->isWritable());

      $this->view()->actions = $actions;
      $this->view()->dateFrom = $currentDateO;
      $this->view()->dateTo = $dateNext;

      // link další
      $this->view()->linkNext = $this->link()->route('normaldate',
              array('day' => $dateNext->format('j') , 'month' => $dateNext->format('n'),
              'year' => $dateNext->format('Y')));
      $this->view()->linkNextLabel = $linkNextLabel;
      // link předchozí
      $this->view()->linkBack = $this->link()->route('normaldate',
              array('day' => $datePrev->format('j') , 'month' => $datePrev->format('n'),
              'year' => $datePrev->format('Y')));
      $this->view()->linkBackLabel = $linkBackLabel;

      // načtení textu
      $this->view()->text = $this->loadActionsText();
   }

   public function showController(){
      return false;
   }

   public function editLabelController() {
      $this->checkControllRights();
      $form = new Form('modlabel');

      $elemText = new Form_Element_TextArea('text', $this->_('Popis'));
      $elemText->setLangs();
      $form->addElement($elemText);

      $elemS = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($elemS);

      if($form->isValid()) {
         $textM = new Text_Model_Detail();
         $textM->saveText($form->text->getValues(), null, $this->category()->getId());

         $this->infoMsg()->addMessage($this->_('Úvodní text byl uložen'));
         $this->link()->route()->reload();
      }

      // načtení textu
      $text = $this->loadActionsText();
      if($text != false) {
         $form->text->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $this->view()->form = $form;
   }

   private function loadActionsText() {
      $textM = new Text_Model_Detail();
      $text = $textM->getText($this->category()->getId());
      return $text;
   }

   // RSS
   public function exportController() {
      $this->checkReadableRights();
      $this->view()->type = $this->getRequest('type', 'rss');

      $ids = explode(',', $this->category()->getModule()->getParam(self::PARAM_CATEGORY_IDS, '0'));

      $acM = new Actions_Model_List();
      $actions = $acM->getActionsByAddedByCatIds($ids, VVE_FEED_NUM);
      $this->view()->actions = $actions;
   }

   public function listCatAddController() {
      $this->checkWritebleRights();
      $catM = new Model_Category();

      $ids =  $this->category()->getParam(self::PARAM_CATEGORY_IDS, null);
      $catsArr = array();
      foreach ($ids as $idc) {
         $cat = $catM->getCategoryById($idc);
         array_push($catsArr, array('name' => $cat->{Model_Category::COLUMN_CAT_LABEL},
                 'url' => $this->link()->category($cat->{Model_Category::COLUMN_URLKEY})->route('add')));
      }
      $this->view()->categories = $catsArr;

   }

   public function featuredListController() {
      $model = new Actions_Model_List();
      $ids = $this->category()->getModule()->getParam(self::PARAM_CATEGORY_IDS, array(0));
      $toDate = new DateTime();
      $toDate->modify("+6 month");
      $this->view()->actions = $model->getActionsByCatIds(new DateTime(), $toDate, $ids);
      if($this->view()->action === false) return false;
   }

   public function currentActController() {
      $model = new Actions_Model_List();
      $ids = $this->category()->getModule()->getParam(self::PARAM_CATEGORY_IDS, array(0));
      $toTime = new DateTime();
      $toTime->modify('+1 month');
      $actions = $model->getActionsByCatIds(new DateTime(), $toTime, $ids);
      $this->view()->action = $actions->fetch();
   }

   public static function settingsController(&$settings,Form &$form) {
      Actions_Controller::settingsController($settings, $form);
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
?>