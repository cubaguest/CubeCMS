<?php
class Bands_Panel extends Panel {
//   const DEFAULT_NUM_ARTICLES = 3;
   const DEFAULT_TYPE = 'randband';

   public function panelController() {
   }

   public function panelView() {
      $model = new Bands_Model();
      switch ($this->panelObj()->getParam('type', self::DEFAULT_TYPE)) {
         case 'randband':
            $this->template()->band = $model->getRandomBand();
            $this->template()->addTplFile('panel_band.phtml');
            break;
         case 'randclip':
         default:
            $this->template()->band = $model->getRandomCLip();
            if($this->template()->band == null) return false;
            $this->template()->addTplFile('panel_clip.phtml');
            break;
      }
   }

   public static function settingsController(&$settings,Form &$form) {
      $elemType = new Form_Element_Select('type', 'Typ panelu');
      $types = array('Náhodná skupina' => 'randband', 'Náhodný klip' => 'randclip');
      $elemType->setOptions($types);
      $elemType->setSubLabel('Výchozí: '.array_search(self::DEFAULT_TYPE, $types).'');
      $form->addElement($elemType,'basic');

      if(isset($settings['type'])) {
         $form->type->setValues($settings['type']);
      }

      if($form->isValid()) {
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