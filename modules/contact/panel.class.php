<?php
class Contact_Panel extends Panel {
   const TEXT_PANEL_KEY = 'panel';
	
   const PARAM_SHOW_FORM = 'pcsf';


   public function panelController() {
      if($this->panel()->getParam(self::PARAM_SHOW_FORM)){
         $this->template()->form = Contact_Controller::getShortForm($this->translator());
         $this->template()->form->setAction($this->link());
      } else {
         $textM = new Text_Model();
         $text = $textM->getText($this->category()->getId(),self::TEXT_PANEL_KEY);
         if($text != false){
            $this->template()->text = $text->{Text_Model::COLUMN_TEXT};
         } else {
            return false;
         }
      }
	}
	
	public function panelView() {
       if($this->panel()->getParam(self::PARAM_SHOW_FORM)){
         $this->template()->addTplFile("panel_form.phtml");
      } else if((string)$this->template()->text != null) {
         $this->template()->addTplFile("panel.phtml");
      }
	}
   
   protected function settings(&$settings,Form &$form) {
      $elemShowForm = new Form_Element_Checkbox('showForm', $this->tr('Zobrazit formulář místo textu'));
      $elemShowForm->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemShowForm,'basic');

      if(isset($settings[self::PARAM_SHOW_FORM])) {
         $form->showForm->setValues($settings[self::PARAM_SHOW_FORM]);
      }

      if($form->isValid()) {
         $settings[self::PARAM_SHOW_FORM] = $form->showForm->getValues();
      }
   }
}