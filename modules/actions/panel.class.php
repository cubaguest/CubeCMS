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
         case 'past':
            $model = new Actions_Model;
            $actions = $model
               ->setPastOnly($this->category()->getId())
               ->order( array(Actions_Model::COLUMN_DATE_START => Model_ORM::ORDER_DESC) )
               ->limit(0, $this->panelObj()->getParam('num', self::DEFAULT_NUM_ACTIONS))
               ->records();
            $this->template()->addFile('tpl://actions:panel.phtml');
            
            $this->template()->actions = $actions;
            if ($this->template()->actions === false) return false;
            $this->template()->datadir = $this->category()->getModule()->getDataDir(true);
            $this->template()->count = $this->panelObj()->getParam('num', self::DEFAULT_NUM_ACTIONS);
            break;
         case 'list':
         case 'listfeatured':
            $model = new Actions_Model_List();
            $actions = $model->getFeaturedActions($this->category()->getId());
            $this->template()->addTplFile('panel.phtml', 'actions');
            $this->template()->actions = $actions->fetchAll();
            if ($this->template()->actions === false) return false;
            $this->template()->count = $this->panelObj()->getParam('num', self::DEFAULT_NUM_ACTIONS);
            break;
         case 'listactual':
         default:
            $model = new Actions_Model();
            $actions = $model
               ->actualOnly($this->category()->getId())
               /* Tady asi zakomponovat řazení podle deltadays nebo do modelu přidat metodu na sloupec */
               ->order(array(
                     Actions_Model::COLUMN_DATE_START => Model_ORM::ORDER_ASC,
                     Actions_Model::COLUMN_TIME => Model_ORM::ORDER_ASC,
                     ))
               ->records();
            $this->template()->addFile('tpl://actions:panel.phtml');
            if ($actions === false) return false;
            $this->template()->actions = $actions;
            $this->template()->count = $this->panelObj()->getParam('num', self::DEFAULT_NUM_ACTIONS);
            break;
      }
      $this->template()->rssLink = $this->link()->route('feed', array('type' => 'rss'));
	}

   protected function settings(&$settings, Form &$form) {
      $elemType = new Form_Element_Select('type', 'Typ panelu');
      $types = array(
            'Seznam aktuálních událostí' => 'listactual', 
            'Seznam nadcházejících událostí' => 'listfeatured', 
            'Aktuální událost' => 'actual', 
            'Nadcházející událost' => 'featured', 
            'Seznam uplynulých událostí' => 'past');
      $elemType->setOptions($types);
      $elemType->setSubLabel('Výchozí: '.array_search(self::DEFAULT_TYPE, $types).'');
      $form->addElement($elemType,'basic');

      if(isset($settings['type'])) {
         $form->type->setValues($settings['type']);
      }

      $elemNum = new Form_Element_Text('num', 'Počet událostí v seznamu');
      $elemNum->setSubLabel('Počet událostí při zapnutém stylu "Seznam".<br /> Výchozí: '.self::DEFAULT_NUM_ACTIONS.'');
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