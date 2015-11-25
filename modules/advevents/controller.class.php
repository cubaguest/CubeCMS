<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class AdvEvents_Controller extends AdvEventsBase_Controller {

   const MAX_EVENTS = 10;
   const FILTER_GET_DATE_FROM = 'filter-datef';
   const FILTER_GET_DATE_TO = 'filter-datet';
   const FILTER_GET_CAT = 'filter-cat';
   const FILTER_GET_ORG = 'filter-org';
   const FILTER_GET_AREA = 'filter-area';
   const FILTER_GET_PLACE = 'filter-place';
   const FILTER_GET_FULLTEXT = 'filter-fulltext';
   const FILTER_GET_RECOMMENDED = 'filter-recommended';
   
   const PARAM_REDIRECT_TO_FILTER = 'aerf';

   public function mainController()
   {
      parent::mainController();

      if(!Template::isHomePage() && $this->category()->getParam(self::PARAM_REDIRECT_TO_FILTER, false)) {
         $this->link()->route('filter')->redirect();
      }
      
      $eventsLoaded = array();

      $params = array(
          'limit' => self::MAX_EVENTS,
          'offset' => $this->getRequestParam('offset', false)
      );

      // načtení akcí na 
      $begin = new DateTime();
      $begin->setTime(0, 0, 0);
      $end = new DateTime();
      $end->setTime(23, 59, 59);
      $this->view()->eventsToday = AdvEventsBase_Model_Events::getEventsByDateRange($begin, $end, $params);
      $this->view()->linkToday = $this->link()->route('filter')
          ->param('filter-datef', Utils_DateTime::fdate('%x', $begin))
          ->param('filter-datet', Utils_DateTime::fdate('%x', $end));

      // zítra
      if ($this->view()->eventsToday) {
         foreach ($this->view()->eventsToday as $e) {
            $eventsLoaded[] = $e->getPK();
         }
      }
      $params['ignoreEvents'] = $eventsLoaded;
      $begin->modify('+1 day');
      $end->modify('+1 day');
      $this->view()->eventsTomorow = AdvEventsBase_Model_Events::getEventsByDateRange($begin, $end, $params);
      $this->view()->linkTomorow = $this->link()->route('filter')
          ->param('filter-datef', Utils_DateTime::fdate('%x', $begin))
          ->param('filter-datet', Utils_DateTime::fdate('%x', $end));

      // tento týden
      if ($this->view()->eventsTomorow) {
         foreach ($this->view()->eventsTomorow as $e) {
            $eventsLoaded[] = $e->getPK();
         }
      }
      $params['ignoreEvents'] = $eventsLoaded;
      $begin->modify('+1 day');
      $end->modify('+8 day');
      $this->view()->eventsThisWeek = AdvEventsBase_Model_Events::getEventsByDateRange($begin, $end, $params);
      // odkaz na aktuální týden
      $date = new DateTime();
      $this->view()->linkThisWeek = $this->link()->route('filter')
          ->param('filter-datef', Utils_DateTime::fdate('%x', $date))
          ->param('filter-datet', Utils_DateTime::fdate('%x', clone $date->modify('+1 week')));


      // Aktuální měsíc
      if ($this->view()->eventsThisWeek) {
         foreach ($this->view()->eventsThisWeek as $e) {
            $eventsLoaded[] = $e->getPK();
         }
      }
      $params['ignoreEvents'] = $eventsLoaded;
      $begin->modify('+8 day');
      $end->modify('+1 month');
      $this->view()->eventsThisMonth = AdvEventsBase_Model_Events::getEventsByDateRange($begin, $end, $params);
      // odkaz na aktuální měsíc
      $date = new DateTime();
      $this->view()->linkThisMonth = $this->link()->route('filter')
          ->param('filter-datef', Utils_DateTime::fdate('%x', $date))
          ->param('filter-datet', Utils_DateTime::fdate('%x', clone $date->modify('+1 month')));
   }

   public function filterController()
   {
      // parametry filtru
      $begin = new DateTime($this->getRequestParam(self::FILTER_GET_DATE_FROM));
      $begin->setTime(0, 0, 0); // tohle nevím, asi odebrat
      $dateEndTmp = new DateTime();
      $dateEndTmp->modify('+1 month');
      $end = new DateTime($this->getRequestParam(self::FILTER_GET_DATE_TO, $dateEndTmp->format('Y-m-d')));
      $end->setTime(23, 59, 59);

      $this->view()->events = AdvEventsBase_Model_Events::getEventsByDateRange($begin, $end, array(
              'idPlace' =>
              $this->getRequestParam(self::FILTER_GET_PLACE, false) == null ? false : $this->getRequestParam(self::FILTER_GET_PLACE, false),
              'idLocation' =>
              $this->getRequestParam(self::FILTER_GET_AREA, false) == null ? false : $this->getRequestParam(self::FILTER_GET_AREA, false),
              'idOrganizer' => $this->getRequestParam(self::FILTER_GET_ORG, false) == null ? false : $this->getRequestParam(self::FILTER_GET_ORG, false),
              'idCategory' => $this->getRequestParam(self::FILTER_GET_CAT, false) == null ? false : $this->getRequestParam(self::FILTER_GET_CAT, false),
              'fulltext' => $this->getRequestParam(self::FILTER_GET_FULLTEXT, false) == null ? false : $this->getRequestParam(self::FILTER_GET_FULLTEXT, false),
              'recommendedOnly' => $this->getRequestParam(self::FILTER_GET_RECOMMENDED, false) == null ? false : $this->getRequestParam(self::FILTER_GET_RECOMMENDED, false),
              'limit' => $this->getRequestParam('limit', 10),
              'offset' => $this->getRequestParam('offset', 0),
      ));

      $this->view()->categories = AdvEventsBase_Model_Categories::getCategories(10000);
      $this->view()->places = AdvEventsBase_Model_Places::getPlaces(10000);
      $this->view()->areas = AdvEventsBase_Model_Locations::getAllRecords();
      $this->view()->organizers = AdvEventsBase_Model_Organizers::getOrganizers(10000);

      $date = new DateTime();
      $this->view()->eventsRecommended = AdvEventsBase_Model_Events::getEventsByDateRange(
              clone $date, $date->modify('+1 month'), array(
              'recommendedOnly' => true,
              'limit' => 10,
      ));

      $this->view()->dateFrom = $begin;
      $this->view()->dateTo = $end;
   }

   public function detailController($id)
   {
      $this->view()->event = AdvEventsBase_Model_Events::getEvent($id);

      if (!$this->view()->event) {
         throw new UnexpectedPageException();
      }

      $begin = new DateTime();
      $begin->setTime(0, 0, 0);
      $end = new DateTime();
      $end->modify('+1 month');
      $end->setTime(23, 59, 59);
      $this->view()->events = null;

      $otherLink = $this->link()->route('filter')
          ->param('filter-datef', Utils_DateTime::fdate('%x', $begin))
          ->param('filter-datet', Utils_DateTime::fdate('%x', $end));
      // první zkus akce ve stejné kategorii
      if ($this->view()->event->{AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY} != 0) {
         $this->view()->events = AdvEventsBase_Model_Events::getEventsByDateRange($begin, $end, array(
                 'idCategory' => $this->view()->event->{AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY},
                 'limit' => 10
         ));
         $this->view()->otherEventsName = $this->tr('Podobné akce');
         $otherLink->param('filter-cat', $this->view()->event->{AdvEventsBase_Model_Events::COLUMN_ID_CATEGORY});
      }


      // potom od poředatele
      if (empty($this->view()->events)) {
         $this->view()->events = AdvEventsBase_Model_Events::getEventsByDateRange($begin, $end, array(
                 'idOrganizer' => $this->view()->event->{AdvEventsBase_Model_Events::COLUMN_ID_ORGANIZER},
                 'limit' => 10
         ));
         $this->view()->otherEventsName = $this->tr('Od pořadatele');
         $otherLink->param('filter-org', $this->view()->event->{AdvEventsBase_Model_Events::COLUMN_ID_ORGANIZER});
      }

      // potom v doporučené
      if (empty($this->view()->events)) {
         $this->view()->events = AdvEventsBase_Model_Events::getEventsByDateRange($begin, $end, array(
                 'limit' => 10,
                 'recommendedOnly' => true
         ));
         $this->view()->otherEventsName = $this->tr('Doporučujeme');
//         $otherLink->param('filter-org', $this->view()->event->{AdvEventsBase_Model_Events::COLUMN_ID_ORGANIZER});
      }

      // potom v aktuální den
      if (empty($this->view()->events)) {
         $this->view()->events = AdvEventsBase_Model_Events::getEventsByDateRange($begin, $end, array(
                 'limit' => 10,
         ));
         $this->view()->otherEventsName = $this->tr('V nejbližší době také');
      }
      $this->view()->otherLink = $otherLink;
   }

   /* AJAX */

   public function listAjaxController($year, $month)
   {
      $this->view()->year = $year;
      $this->view()->month = $month;
      $outArray = array();

      $from = new DateTime();
      $from->setDate($year, $month, 1);
      $from->setTime(0, 0, 0);
      $to = new DateTime($from->format('Y-m-t'));
      $to->setTime(23, 59, 59);

      $eventsTmp = AdvEventsBase_Model_Events::getEventsByDateRange($from, $to);

      // Vytvoření pole se dny
      $tmpDate = clone $from;
      $tmpEndDate = clone $to;

      do {
         $outArray[$tmpDate->format('Y-m-d')] = array(
             'names' => array(),
             'ids' => array(),
             'count' => 0,
             'recommended' => false,
             'url' => (string) $this->link()->route('filter')->rmParam()
                 ->param(self::FILTER_GET_DATE_FROM, Utils_DateTime::fdate("%x", $tmpDate))
                 ->param(self::FILTER_GET_DATE_TO, Utils_DateTime::fdate("%x", $tmpDate))
                 ->file(null)
         );
      } while ($tmpDate->modify('+1 day') <= $tmpEndDate);

      foreach ($eventsTmp as $e) {
         // co když je event na více dní?
         $times = $e->getTimesArray();
         $recommended = $e->{AdvEventsBase_Model_Events::COLUMN_RECOMMENDED};
         // vyhoď prázdné časy
         if (empty($times)) {
            continue;
         }
         foreach ($times as $time) {
            if ($time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END} == null) {
               $dateBegin = new DateTime($time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN});
               if (isset($outArray[$dateBegin->format('Y-m-d')]) && !isset($outArray[$dateBegin->format('Y-m-d')]['ids'][$e->getPK()])) {
                  array_push($outArray[$dateBegin->format('Y-m-d')]['names'], $e->{AdvEventsBase_Model_Events::COLUMN_NAME});
                  $outArray[$dateBegin->format('Y-m-d')]['ids'][$e->getPK()] = true;
                  $outArray[$dateBegin->format('Y-m-d')]['count'] ++;
                  $outArray[$dateBegin->format('Y-m-d')]['recommended'] = $recommended;
               }
            } else {
               // časy na více dní se doplňují do všech dnů, kdy jsou
               $dateBegin = new DateTime($time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_BEGIN});
               $dateEnd = new DateTime($time->{AdvEventsBase_Model_EventsTimes::COLUMN_DATE_END});
               do {
                  if (isset($outArray[$dateBegin->format('Y-m-d')]) && !isset($outArray[$dateBegin->format('Y-m-d')]['ids'][$e->getPK()])) {
                     array_push($outArray[$dateBegin->format('Y-m-d')]['names'], $e->{AdvEventsBase_Model_Events::COLUMN_NAME});
                     $outArray[$dateBegin->format('Y-m-d')]['ids'][$e->getPK()] = true;
                     $outArray[$dateBegin->format('Y-m-d')]['count'] ++;
                     $outArray[$dateBegin->format('Y-m-d')]['recommended'] = $recommended;
                  }
               } while ($dateBegin->modify('+1 day') <= $dateEnd);
            }
         }
      }

      $outWeeks = array();

      $startWeekNo = clone $from;
      $startWeekNo->modify('-1 week');
      $endWeekNo = clone $startWeekNo;
      $endWeekNo->modify('+6 weeks');

      $date = new DateTime($startWeekNo->format('Y') . '-' . $startWeekNo->format('m') . '-' . $startWeekNo->format('d'));
      $date->modify('-' . ($date->format('w') - 1) . ' days');
      $this->view()->weeksNom = array();
      do {
         $outWeeks[(int) $date->format('W')] = (string) $this->link()->route('filter')
                 ->param(self::FILTER_GET_DATE_FROM, Utils_DateTime::fdate("%x", $date))
                 ->param(self::FILTER_GET_DATE_TO, Utils_DateTime::fdate("%x", $date->modify('+6 days')))
                 ->file(null);
         $date->modify('+1 day'); // posunutí na další začínající den
      } while ($date <= $endWeekNo);

//      var_dump($outWeeks, $startWeekNo, $endWeekNo);

      $this->view()->monthUrl = (string) $this->link()->route('filter')
              ->param(self::FILTER_GET_DATE_FROM, Utils_DateTime::fdate("%x", $from))
              ->param(self::FILTER_GET_DATE_TO, Utils_DateTime::fdate("%x", $to))
              ->file(null);
      $this->view()->weeks = $outWeeks;
      $this->view()->events = $outArray;

//      header('Content-Type: text/html');
//      flush();
//      die;
   }

   public function settings(&$settings, Form &$form)
   {
      parent::settings($settings, $form);
      
      $eRedirect = new Form_Element_Checkbox('redirect', $this->tr('Přesměrovat rovnou na filtraci'));
      if(isset($settings[self::PARAM_REDIRECT_TO_FILTER])) {
         $eRedirect->setValues($settings[self::PARAM_REDIRECT_TO_FILTER]);
      }
      $form->addElement($eRedirect, self::SETTINGS_GROUP_VIEW);
      
      
      if($form->isValid()){
         $settings[self::PARAM_REDIRECT_TO_FILTER] = $form->redirect->getValues();
      }
      
   }

}
