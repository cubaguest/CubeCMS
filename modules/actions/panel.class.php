<?php
class Actions_Panel extends Panel {
   const DEFAULT_TYPE = 'list';
   const DEFAULT_NUM_ACTIONS = 3;

	public function panelController() {
	}
	
	public function panelView() {
      switch ($this->panelObj()->getParam('type', self::DEFAULT_TYPE)) {
         case 'actual':
            $model = new Actions_Model_Detail();
            $actions = $model->getCurrentAction($this->category()->getId());
            $this->template()->addTplFile('panel_actual.phtml', 'actions');
            if ($actions === false) return false;
            $this->template()->action = $actions;
            $this->template()->datadir = $this->category()->getModule()->getDataDir(true)
                    .$this->template()->action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getLang()].URL_SEPARATOR;
            break;
         case 'featured':
            $model = new Actions_Model_List();
            $actions = $model->getFeaturedActions($this->category()->getId());
            $this->template()->addTplFile('panel_actual.phtml', 'actions');
//            $actions->fetch(); // posun o jeden dopředu
            $this->template()->action = $actions->fetch();
            if ($this->template()->action === false) return false;
            $this->template()->datadir = $this->category()->getModule()->getDataDir(true)
                    .$this->template()->action[Actions_Model_Detail::COLUMN_URLKEY][Locales::getLang()].URL_SEPARATOR;
            break;
         case 'list':
         default:
            $model = new Actions_Model_List();
            $actions = $model->getFeaturedActions($this->category()->getId());
            $this->template()->addTplFile('panel.phtml', 'actions');
            $this->template()->actions = $actions->fetchAll();
            if ($this->template()->action === false) return false;
            $this->template()->count = $this->panelObj()->getParam('num', self::DEFAULT_NUM_ACTIONS);
            break;
      }
      $this->template()->rssLink = $this->link()->route('export', array('type' => 'rss'));
	}

   public static function settingsController(&$settings,Form &$form) {
      $elemType = new Form_Element_Select('type', 'Typ panelu');
      $types = array('Seznam' => 'list', 'Aktuální akce' => 'actual', 'Nadcházející akce' => 'featured');
      $elemType->setOptions($types);
      $elemType->setSubLabel('Výchozí: '.array_search(self::DEFAULT_TYPE, $types).'');
      $form->addElement($elemType,'basic');

      if(isset($settings['type'])) {
         $form->type->setValues($settings['type']);
      }

      $elemNum = new Form_Element_Text('num', 'Počet akcí v seznamu');
      $elemNum->setSubLabel('Počet akcí při zapnutém stylu "Seznam".<br /> Výchozí: '.self::DEFAULT_NUM_ACTIONS.'');
      $elemNum->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemNum,'basic');

      if(isset($settings['num'])) {
         $form->num->setValues($settings['num']);
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