<?php

class Navigation_Panel extends Panel {

   const PARAM_DEEP = 'navdeep';
   
   protected $events = false;
   
   public function panelController()
   {
      $menu = Category_Structure::getStructure();
      if ($menu != false) {
         $this->ignoreCats = explode(';', $this->category()->getParam(Navigation_Controller::PARAM_IGNORE_IDS, null));
         $newMenu = Navigation_Controller::recursive(
            $menu->getCategory($this->category()->getId()), 
            $this->ignoreCats,
            $this->panel()->getParam(self::PARAM_DEEP, 1)
            );
         $this->template()->structure = $newMenu;
      }
   }

   public function panelView()
   {
      $this->template()->addFile('tpl://panel.phtml');
   }
   
   protected function settings(&$settings, Form &$form)
   {
      $elemType = new Form_Element_Text(self::PARAM_DEEP, $this->tr('Hloubka'));
      $elemType->setValues(1);
      $elemType->setSubLabel($this->tr('Výchozí: 1'));
      $form->addElement($elemType);
      if(isset($settings[self::PARAM_DEEP])) {
         $form->{self::PARAM_DEEP}->setValues($settings[self::PARAM_DEEP]);
      }
      if($form->isValid()) {
         $settings[self::PARAM_DEEP] = $form->{self::PARAM_DEEP}->getValues();
      }
   }
}