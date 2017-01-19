<?php
class Partners_Panel extends Panel {
   const DEFAULT_NUM_PARTNERS = 3;
   const DEFAULT_TYPE = 'list';
   
   const LIST_TYPE_LIST = "list";
   const LIST_TYPE_RAND = "rand";

   public function panelController() {
//      $model = new Partners_Model();
//      $model
//         ->where(Partners_Model::COLUMN_ID_CATEGORY." = :idc AND ".Partners_Model::COLUMN_DISABLED." = 0", array('idc' => $this->category()->getId()));
//      
//      switch ($this->panelObj()->getParam('type', self::DEFAULT_TYPE)) {
//         case self::LIST_TYPE_RAND:
//            $model->order(array('RAND(NOW())' => Model_ORM::ORDER_ASC));
//            break;
//         case self::LIST_TYPE_LIST:
//         default:
//            $model->order(array(Partners_Model::COLUMN_ORDER => Model_ORM::ORDER_ASC));
//            break;
//      }
//      $this->template()->partners = $model->limit(0, $this->panelObj()->getParam('num',self::DEFAULT_NUM_PARTNERS))->records();
   }

   public function panelView() {
      $this->category()->getModule()->setDataDir(Partners_Controller::DATA_DIR);
      $this->template()->imagesDir = $this->category()->getModule()->getDataDir(true);
      $this->template()->addFile('tpl://panel.phtml');
   }

   protected function settings(&$settings,Form &$form) {
      $elemNum = new Form_Element_Text('num', $this->tr('Počet partnerů v panelu'));
      $elemNum->setSubLabel('Výchozí: '.self::DEFAULT_NUM_PARTNERS.'');
      $elemNum->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemNum,'basic');

      if(isset($settings['num'])) {
         $form->num->setValues($settings['num']);
      }

      $elemType = new Form_Element_Select('type', $this->tr('Typ zobrazení'));
      $types = array($this->tr('Podle pořadí') => self::LIST_TYPE_LIST, $this->tr('Náhodně') => self::LIST_TYPE_RAND);
      $elemType->setOptions($types);
      $elemType->setSubLabel(sprintf($this->tr('Výchozí řazení: %s '), array_search(self::DEFAULT_TYPE, $types) ) );
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