<?php
class Text_Panel extends Panel {
   const TEXT_PANEL_KEY = 'panel';
   const PARAM_TPL_PANEL = 'tpl';


   public function panelController() {
	}
	
	public function panelView() {
      $textM = new Text_Model_Detail();
      $this->template()->text = $textM->getText($this->category()->getId(),self::TEXT_PANEL_KEY);
      if($this->template()->text == false) return false;
      $this->template()->addFile('tpl://'.$this->category()->getParam(self::PARAM_TPL_PANEL, "panel.phtml"));
	}

   public static function settingsController(&$settings,Form &$form) {
      // šablony
      $componentTpls = new Component_ViewTpl();
      $componentTpls->setConfig(Component_ViewTpl::PARAM_MODULE, $settings['_module']);

      $elemTplPanel = new Form_Element_Select('tplPanel', 'Šablona panelu');
      $elemTplPanel->setOptions(array_flip($componentTpls->getTpls('panel')));
      if(isset($settings[self::PARAM_TPL_PANEL])) {
         $elemTplPanel->setValues($settings[self::PARAM_TPL_PANEL]);
      }
      $form->addElement($elemTplPanel, 'basic');

      if($form->isValid()) {
         $settings[self::PARAM_TPL_PANEL] = $form->tplPanel->getValues();
      }
   }
}
?>