<?php
class AdminCustomMenu_Panel extends Panel {
   const MENU_ID = 'mid';

   public function panelController() 
   {
      $this->template()->menu = AdminCustomMenu_Model_Items::getMenu($id);
   }

   public function panelView() {
      $this->template()->addFile($this->getTemplate());
      $this->template()->rssLink = $this->link()->clear()->route().Url_Request::URL_FILE_RSS;
   }

   protected function settings(&$settings,Form &$form) {
      $elemNum = new Form_Element_Select(self::MENU_ID, $this->tr('Menu'));
      $elemNum->addValidation(new Form_Validator_IsNumber());
      
      $menus = AdminCustomMenu_Model_Items::COLUMN_NAME;
      
      $form->addElement($elemNum,'basic');

      if(isset($settings[self::MENU_ID])) {
         $form->{self::MENU_ID}->setValues($settings[self::MENU_ID]);
      }

      if($form->isValid()) {
         $settings[self::MENU_ID] = $form->{self::MENU_ID}->getValues();
      }
   }
}