<?php
class AdvEvents_Panel extends AdvEventsBase_Panel {
	
   protected $events = array();

   protected static $renderedIds = array();

   public function panelController()
   {
      parent::panelController();
      
      $from = new DateTime();
      $to = new DateTime();
      
      $showType = $this->panel()->getParam('show', 'week');
      
      $recommended = false;
      switch ($showType) {
         case "day":
            $to->modify('+1 day');
            break;
         case "rand":
         case "month":
            $to->modify('+1 month');
            $to->modify('+1 month');
            break;
         case "recommended":
            $recommended = true;
            $to->modify('+1 month');
            break;
         case "week":
         default:
            $to->modify('+1 week');
            break;
      }
      
      $this->events = AdvEventsBase_Model_Events::getEventsByDateRange($from, $to, array(
          'recommendedOnly' => $recommended,
          'limit' => 5,
          'ignoreEvents' => ($recommended ? array() : self::$renderedIds)
      ));
      if(!empty($this->events)){
         foreach ($this->events as $e) {
            self::$renderedIds[] = $e->getPK();
         }
      }
      
	}
	
	public function panelView() {
      parent::panelView();
      $this->template()->addFile('tpl://panel.phtml');
      $this->template()->events = $this->events;
	}
   
   protected function settings(&$settings,Form &$form) {
      $elemType = new Form_Element_Select('show', $this->tr('Zobrazit'));
      $types = array(
          $this->tr('Aktuální den') => 'day', 
          $this->tr('Aktuální týden') => 'week', 
          $this->tr('Aktuální měsíc') => 'month', 
          $this->tr('Aktuální doporučené') => 'recommended', 
          $this->tr('Náhodné') => 'rand'
          );
      $elemType->setOptions($types);
      $elemType->setValues('week');
      $elemType->setSubLabel($this->tr('Výchozí: týden'));
      $form->addElement($elemType,'basic');

      if(isset($settings['show'])) {
         $form->show->setValues($settings['show']);
      }

      if($form->isValid()) {
         $settings['show'] = $form->show->getValues();
      }
   }
}
