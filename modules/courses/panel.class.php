<?php
class Courses_Panel extends Panel {
   const DEFAULT_NUM_COURSES = 3;
   const DEFAULT_TYPE = 'list';

   public function panelController() {
      $this->category()->getModule()->setDataDir(Courses_Controller::DATA_DIR);
   }

   public function panelView() {
      $coursesM = new Courses_Model_Courses();
      switch ($this->panelObj()->getParam('type', self::DEFAULT_TYPE)) {
         case 'random':
            break;
         case 'list':
         default:
            $this->template()->courses = $coursesM->getCoursesFromDate(new DateTime(), true, 0,
                    $this->panelObj()->getParam('num',self::DEFAULT_NUM_COURSES));
            $this->template()->addTplFile('panel.phtml');
            break;
      }
      $this->template()->courseImagesPath = $this->category()->getModule()->getDataDir(true);
   }

   public static function settingsController(&$settings,Form &$form) {
      $elemNum = new Form_Element_Text('num', 'Počet kurzů v panelu');
      $elemNum->setSubLabel('Výchozí: '.self::DEFAULT_NUM_COURSES.'');
      $elemNum->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemNum,'basic');

      if(isset($settings['num'])) {
         $form->num->setValues($settings['num']);
      }

      $elemType = new Form_Element_Select('type', 'Typ panelu');
      $types = array('Seznam' => 'list', 'Náhodný' => 'random');
      $elemType->setOptions($types);
      $elemType->setSubLabel('Výchozí: '.array_search(self::DEFAULT_TYPE, $types).'');
      $form->addElement($elemType,'basic');

      if(isset($settings['type'])) {
         $form->type->setValues($settings['type']);
      }

      if($form->isValid()) {
         $settings['num'] = $form->num->getValues();
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