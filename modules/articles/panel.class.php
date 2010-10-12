<?php
class Articles_Panel extends Panel {
   const DEFAULT_NUM_ARTICLES = 3;
   const DEFAULT_TYPE = 'list';

   public function panelController() {
   }

   public function panelView() {
      $artM = new Articles_Model();
      switch ($this->panelObj()->getParam('type', self::DEFAULT_TYPE)) {
         case 'top':
            $artM->order(array(Articles_Model::COLUMN_SHOWED => Model_ORM::ORDER_ASC));
            $this->template()->addTplFile('panel_top.phtml');
            break;
         case 'list':
         default:
            $artM->order(array(Articles_Model::COLUMN_ADD_TIME => Model_ORM::ORDER_DESC));
            $this->template()->addTplFile('panel.phtml');
            break;
      }
      $this->template()->articles = $artM->where(Articles_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Articles_Model::COLUMN_PUBLIC.' = :pub',
         array('idc' => $this->category()->getId(), 'pub' => true))
         ->limit(0, $this->panelObj()->getParam('num',self::DEFAULT_NUM_ARTICLES))->records();
      $this->template()->rssLink = $this->link()->clear()->route().Url_Request::URL_FILE_RSS;
   }

   public static function settingsController(&$settings,Form &$form) {
      $elemNum = new Form_Element_Text('num', 'Počet článků v panelu');
      $elemNum->setSubLabel('Výchozí: '.self::DEFAULT_NUM_ARTICLES.'');
      $elemNum->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemNum,'basic');

      if(isset($settings['num'])) {
         $form->num->setValues($settings['num']);
      }

      $elemType = new Form_Element_Select('type', 'Typ panelu');
      $types = array('Seznam' => 'list', 'Seznam - Nejčtenější' => 'top');
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