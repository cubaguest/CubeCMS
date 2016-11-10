<?php

class Events_Panel extends Panel {

   
   const P_RANGE = 'r';
   const P_TYPE = 't';
   const P_COUNT = 'eve_count';
   
   const P_TYPE_CURRENT = 't_curr';
   const P_TYPE_RANDE = 't_range';
   
   const DEFAULT_TYPE = self::P_TYPE_CURRENT;
   
   protected $events = false;
   
   protected $panelType = self::DEFAULT_TYPE;

   public function panelController()
   {
      $this->panelType = $this->panelObj()->getParam(self::P_TYPE, self::DEFAULT_TYPE);
      if ($this->panelType == self::DEFAULT_TYPE) {
         $this->events = Events_Model::getCurrentEventsinCats($this->category()->getId(), $this->panelObj()->getParam(self::P_COUNT, 10));
      }
   }

   public function panelView()
   {
      if ($this->panelType == self::DEFAULT_TYPE) {
         $this->template()->events = $this->events;
         $this->template()->addFile('tpl://events:panel_actual_list.phtml');
      }
   }
   
   protected function settings(&$settings, Form &$form)
   {
      $elemType = new Form_Element_Select('type', 'Typ panelu');
      $types = array('Seznam nadcházejících událostí' => self::P_TYPE_CURRENT,
          'Aktuální události' => 'actual', 'Nadcházející události' => 'featured', 'Uplynulé události' => 'past');
      $elemType->setOptions($types);
      $elemType->setSubLabel('Výchozí: '.array_search(self::DEFAULT_TYPE, $types).'');
      $form->addElement($elemType,'basic');

      if(isset($settings[self::P_TYPE])) {
         $form->type->setValues($settings[self::P_TYPE]);
      }

      $elemNum = new Form_Element_Text('num', 'Počet událostí v seznamu');
      $elemNum->setSubLabel('Počet událostí při zapnutém stylu "Seznam".<br /> Výchozí: 10');
      $elemNum->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemNum,'basic');

      if(isset($settings[self::P_COUNT])) {
         $form->num->setValues($settings[self::P_COUNT]);
      }

      if($form->isValid()) {
         $settings[self::P_COUNT] = $form->num->getValues();
         // protože je vždy hodnota
         $settings[self::P_TYPE] = $form->type->getValues();
      }
   }
}