<?php
class Articles_Panel extends Panel {
   const DEFAULT_NUM_ARTICLES = 3;
   const DEFAULT_TYPE = 'list';

   public function panelController() {
   }

   public function panelView() {
      $artM = new Articles_Model_List();
      switch ($this->panelObj()->getParam('type', self::DEFAULT_TYPE)) {
         case 'top':
            $this->template()->topArticles = $artM->getListTop($this->category()->getId(), 0,
                    $this->panelObj()->getParam('num',self::DEFAULT_NUM_ARTICLES));
            $this->template()->addTplFile('panel_top.phtml');
            break;
         case 'list':
         default:
            $this->template()->newArticles = $artM->getList($this->category()->getId(), 0,
                    $this->panelObj()->getParam('num',self::DEFAULT_NUM_ARTICLES));
            $this->template()->addTplFile('panel.phtml');
            break;
      }
      $this->template()->rssLink = $this->link()->route('exportFeed', array('type' => 'rss'));

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