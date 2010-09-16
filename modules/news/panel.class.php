<?php
class News_Panel extends Panel {
   const DEFAULT_NUM = 3;
	public function panelController() {
	}

	public function panelView() {
      $artM = new Articles_Model_List();
      $this->template()->newArticles = $artM->getList($this->category()->getId(),0,
              $this->panelObj()->getParam('num', self::DEFAULT_NUM));
      $this->template()->rssLink = $this->link()->route('feed', array('type' => 'rss'));
      $this->template()->addTplFile("panel.phtml");
	}

   public static function settingsController(&$settings,Form &$form) {
      $elemNum = new Form_Element_Text('num', 'Počet novinek v seznamu');
      $elemNum->setSubLabel('Výchozí: '.self::DEFAULT_NUM.'');
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