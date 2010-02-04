<?php
class ActionsList_Controller extends Controller {
   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      // uložení datumu do session pokud existuje - kvuli návratu
      // odkaz zpět
      $this->link()->backInit();

      $timeSpace = $this->category()->getModule()->getParam('time', "1_M");
      $arr = explode("_", $timeSpace);

      $startTime = mktime(0, 0, 0, $this->getRequest('month', date("n")),
              $this->getRequest('day', date("j")), $this->getRequest('year', date("Y")));

      $dateStart = new DateTime(date('Y-m-d',$startTime));
      $dateEnd = new DateTime(date('Y-m-d',$startTime));
      switch (strtolower($arr[1])) {
         case 'y':
            $dateStart->modify("+".$arr[0]." year");
            $dateEnd->modify("-".$arr[0]." year");
            $linkNextLabel = sprintf($this->ngettext('+ %s year','+ %s years',(int)$arr[0], 'actions'), $arr[0]);
            $linkBackLabel = sprintf($this->ngettext('- %s year','+ %s years',(int)$arr[0], 'actions'), $arr[0]);
            break;
         case 'm':
            $dateStart->modify("+".$arr[0]." month");
            $dateEnd->modify("-".$arr[0]." month");
            $linkNextLabel = sprintf($this->ngettext('+ %s month','+ %s months',(int)$arr[0], 'actions'), $arr[0]);
            $linkBackLabel = sprintf($this->ngettext('- %s month','+ %s months',(int)$arr[0], 'actions'), $arr[0]);
            break;
         case 'd':
         default:
            $dateStart->modify("+".$arr[0]." day");
            $dateEnd->modify("-".$arr[0]." day");
            $linkNextLabel = sprintf($this->ngettext('+ %s day','+ %s days',(int)$arr[0], 'actions'), $arr[0]);
            $linkBackLabel = sprintf($this->ngettext('- %s day','+ %s days',(int)$arr[0], 'actions'), $arr[0]);
            break;
      }
      $timeNext = $dateStart->format("U");
      $timePrev = $dateEnd->format("U");

      $ids = explode(',', $this->category()->getModule()->getParam('catids', '0'));

      $acM = new Actions_Model_List();
      $actions = $acM->getActionsByCatIds($startTime, $timeNext,$ids,
              !$this->getRights()->isWritable());

      $this->view()->actions = $actions;
      $this->view()->dateFrom = $startTime;
      $this->view()->dateTo = $timeNext;

      // link další
      $this->view()->linkNext = $this->link()->route('normaldate',
              array('day' => date('j', $timeNext) , 'month' => date('n', $timeNext),
              'year' => date('Y', $timeNext)));
      $this->view()->linkNextLabel = $linkNextLabel;
      // link předchozí
      $this->view()->linkBack = $this->link()->route('normaldate',
              array('day' => date('j', $timePrev) , 'month' => date('n', $timePrev),
              'year' => date('Y', $timePrev)));
      $this->view()->linkBackLabel = $linkBackLabel;
   }

   // RSS
   public function exportController() {
      $this->checkReadableRights();
      $this->view()->type = $this->getRequest('type', 'rss');

      $ids = explode(',', $this->category()->getModule()->getParam('catids', '0'));
      
      $acM = new Actions_Model_List();
      $actions = $acM->getActionsByAddedByCatIds($ids, VVE_FEED_NUM);
      $this->view()->actions = $actions;
   }

   public function listCatAddController(){
      $this->checkWritebleRights();
      $catM = new Model_Category();

      $ids = explode(',', $this->category()->getModule()->getParam('catids', '0'));
      $catsArr = array();
      foreach ($ids as $idc){
          $cat = $catM->getCategoryById($idc);
          array_push($catsArr, array('name' => $cat->{Model_Category::COLUMN_CAT_LABEL},
                  'url' => $this->link()->category($cat->{Model_Category::COLUMN_URLKEY})->route('add')));
      }
      $this->view()->categories = $catsArr;

   }

//   public function featuredListController() {
//      $model = new Actions_Model_List();
//
//      $this->view()->actions = $model->getFeaturedActions($this->category()->getId());
//      if($this->view()->action === false) return false;
//   }
//
//   public function currentActController() {
//      $model = new Actions_Model_Detail();
//      $this->view()->action = $model->getCurrentAction($this->category()->getId());
////      if($this->view()->action === false) return false;
//   }
}
?>