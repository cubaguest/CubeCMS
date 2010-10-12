<?php
class News_Panel extends Articles_Panel {
   const DEFAULT_NUM_ARTICLES = 3;

   public static function settingsController(&$settings,Form &$form) {
      $elemNum = new Form_Element_Text('num', 'Počet novinek v seznamu');
      $elemNum->setSubLabel('Výchozí: '.self::DEFAULT_NUM_ARTICLES.'');
      $elemNum->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemNum,'basic');

      if(isset($settings['num'])) {
         $form->num->setValues($settings['num']);
      }
      if($form->isValid()) {
         $settings['num'] = $form->num->getValues();
      }
   }
}
?>