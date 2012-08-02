<?php
class Text_Panel extends Panel {
   const TEXT_PANEL_KEY = 'panel';
   const PARAM_TPL_PANEL = 'tpl';


   public function panelController() {
      $textM = new Text_Model();
      $rec = $textM->setSelectAllLangs(false)
         ->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :sk',
         array('idc' => $this->category()->getId(), 'sk' => self::TEXT_PANEL_KEY))
         ->record();
      if($rec != false AND !$rec->isNew()){
         $this->template()->text = $rec;
      }
	}
	
	public function panelView() {
      if($this->template()->text != null){
         $this->template()->addFile('tpl://'.$this->panelObj()->getParam(self::PARAM_TPL_PANEL, "text:panel.phtml"));
      }
	}

   public function settings(&$settings,Form &$form) {
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