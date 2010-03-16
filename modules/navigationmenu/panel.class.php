<?php
class NavigationMenu_Panel extends Panel {
	const DEFAULT_TYPE = 'all';

	public function panelController() {
	}
	
	public function panelView() {
      $model = new NavigationMenu_Models_List();
      switch ($this->panelObj()->getParam('type', self::DEFAULT_TYPE)) {
         case 'pages':
            $this->template()->addTplFile('panel_pages.phtml');
            $this->template()->links = $model->getSubdomainsList();
            $this->template()->type = 'pages';
            break;
         case 'projects':
            $this->template()->addTplFile('panel_projects.phtml');
            $this->template()->links = $model->getProjectsList();
            $this->template()->type = 'projects';
            break;
         case 'groups':
            $this->template()->addTplFile('panel_groups.phtml');
            $this->template()->links = $model->getGroupsList();
            $this->template()->type = 'groups';
            break;
         case 'partners':
            $this->template()->addTplFile('panel_partners.phtml');
            $this->template()->links = $model->getPartnersList();
            $this->template()->type = 'partners';
            break;
         case 'all':
         default:
            $this->template()->addTplFile('panel.phtml');
            $this->template()->links = $model->getList();
            $this->template()->type = 'all';
            break;
      }
	}

   public static function settingsController(&$settings,Form &$form) {
      $elemType = new Form_Element_Select('type', 'Typ panelu');
      $types = array('Všechny' => 'all', 'Podstránky' => 'pages', 'Projekty' => 'projects',
         'Skupiny' => 'groups', 'Partneři' => 'partners');
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