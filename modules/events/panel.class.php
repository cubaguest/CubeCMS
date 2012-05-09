<?php

class Events_Panel extends Panel {

   const DEFAULT_TYPE = 'actual-list';
   
   const P_RANGE = 'r';
   const P_TYPE = 't';
   
   protected $events = false;
   
   protected $panelType = self::DEFAULT_TYPE;

   public function panelController()
   {
      $this->panelType = $this->panelObj()->getParam(self::P_TYPE, self::DEFAULT_TYPE);
      
      if ($this->panelType == self::DEFAULT_TYPE) {
         $dateFrom = $dateTo = new DateTime(date("Y-m-d"));
         $this->events = $this->getSortedEvents($dateFrom, $dateTo);
      }
   }

   public function panelView()
   {
      if ($this->panelType == self::DEFAULT_TYPE) {
         $this->template()->events = $this->events;
         $this->template()->addFile('tpl://events:panel_actual_list.phtml');
      }
   }

   protected function getSortedEvents($dateFrom, $dateTo)
   {
      $model = new Events_Model();
      $modelWhere = Events_Model_Categories::COL_ID_CATEGORY . " = :idc AND " . Events_Model::COL_PUBLIC . " = 1";
      $modelBindValues = array('idc' => $this->category()->getId());
      
      // model settings
      $modelWhere .= " AND (" . Events_Model::COL_DATE_FROM . " BETWEEN :dateStart1 AND :dateEnd1 "
         . " OR " . Events_Model::COL_DATE_TO . " BETWEEN :dateStart2 AND :dateEnd2 "
         . " OR ( " . Events_Model::COL_DATE_FROM . " < :dateStart3 AND " . Events_Model::COL_DATE_TO . " > :dateEnd3 )"
         .")";
      $modelBindValues['dateStart1'] = $modelBindValues['dateStart2'] = $modelBindValues['dateStart3'] = $dateFrom;
      $modelBindValues['dateEnd1'] = $modelBindValues['dateEnd2'] = $modelBindValues['dateEnd3'] = $dateTo;
      
      $events = $model
         ->joinFK(Events_Model::COL_ID_EVE_CATEGORY)
         ->order(array(
            Events_Model_Categories::COL_NAME => Model_ORM::ORDER_ASC,
            Events_Model::COL_DATE_FROM => Model_ORM::ORDER_ASC,
            Events_Model::COL_TIME_FROM => Model_ORM::ORDER_ASC,
         ))
         ->where($modelWhere, $modelBindValues)
         ->records();
      
      $eventsSorted = array();
      if (!empty($events)) {
         foreach ($events as $event) {
            $cId = $event->{Events_Model_Categories::COL_ID};
            if (!isset($eventsSorted[$cId])) {
               $eventsSorted[$cId] = array('cat' => $event, 'events' => array());
            }
            $eventsSorted[$cId]['events'][] = $event;
         }
      }
      return $eventsSorted;
   }
   
   protected function settings(&$settings, Form &$form)
   {
//      $elemType = new Form_Element_Select('type', 'Typ panelu');
//      $types = array('Seznam nadcházejících událostí' => 'list', 'Aktuální události' => 'actual', 'Nadcházející události' => 'featured', 'Uplynulé události' => 'past');
//      $elemType->setOptions($types);
//      $elemType->setSubLabel('Výchozí: '.array_search(self::DEFAULT_TYPE, $types).'');
//      $form->addElement($elemType,'basic');
//
//      if(isset($settings['type'])) {
//         $form->type->setValues($settings['type']);
//      }
//
//      $elemNum = new Form_Element_Text('num', 'Počet událostí v seznamu');
//      $elemNum->setSubLabel('Počet událostí při zapnutém stylu "Seznam".<br /> Výchozí: '.self::DEFAULT_NUM_ACTIONS.'');
//      $elemNum->addValidation(new Form_Validator_IsNumber());
//      $form->addElement($elemNum,'basic');
//
//      if(isset($settings['num'])) {
//         $form->num->setValues($settings['num']);
//      }
//
//      if($form->isValid()) {
//         $settings['num'] = $form->num->getValues();
//         // protože je vždy hodnota
//         if($form->type->getValues() != self::DEFAULT_TYPE){
//            $settings['type'] = $form->type->getValues();
//         } else {
//            unset ($settings['type']);
//         }
//      }
   }

}
?>