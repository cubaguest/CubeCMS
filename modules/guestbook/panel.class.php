<?php
class GuestBook_Panel extends Panel {
   const DEFAULT_NUM_POSTS = 1;

   const PARAM_NUM_POSTS = 'num';
   const PARAM_SHOW_LEAVE_MESSAGE = 'go';

	public function panelController() {
      if($this->panelObj()->getParam(self::PARAM_NUM_POSTS, self::DEFAULT_NUM_POSTS) != 0){
         $model = new GuestBook_Model();

         $this->template()->lastPosts = $model->order(array(GuestBook_Model::COLUMN_DATE_ADD => Model_ORM::ORDER_DESC))
         ->where(GuestBook_Model::COLUMN_DELETED.' = 0 AND '.GuestBook_Model::COLUMN_ID_CAT.' = :idc', array('idc' => $this->category()->getId() ))
         ->limit(0, $this->panelObj()->getParam(self::PARAM_NUM_POSTS, self::DEFAULT_NUM_POSTS))
         ->records();
      }
      $this->template()->leaveMsg = $this->panelObj()->getParam(self::PARAM_SHOW_LEAVE_MESSAGE, true);
	}

	public function panelView() {
      $this->template()->addFile('tpl://panel.phtml');
	}

	public static function settingsController(&$settings,Form &$form) {
      $elemNum = new Form_Element_Text('num', 'Počet posledních příspěvků');
      $elemNum->setSubLabel('Výchozí: '.self::DEFAULT_NUM_POSTS .'. Pro vypnutí stačí zada 0.');
      $elemNum->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemNum,'basic');

      if(isset($settings[self::PARAM_NUM_POSTS])) {
         $form->num->setValues($settings['self::PARAM_NUM_POSTS']);
      }

      $elemLeaveMessage = new Form_Element_Checkbox('leavemsg', 'Zobrazit "zanechat vzkaz"');
      $form->addElement($elemLeaveMessage, 'basic');

      if(isset($settings[self::PARAM_SHOW_LEAVE_MESSAGE])) {
         $form->leavemsg->setValues((bool)$settings[self::PARAM_SHOW_LEAVE_MESSAGE]);
      }

      if($form->isValid()) {
         $settings[self::PARAM_NUM_POSTS] = $form->num->getValues();
         $settings[self::PARAM_SHOW_LEAVE_MESSAGE] = $form->leavemsg->getValues();
      }
   }
}
?>