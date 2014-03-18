<?php
class Articles_Panel extends Panel {
   const DEFAULT_NUM_ARTICLES = 3;
   const DEFAULT_TYPE = 'list';
   const PARAM_TPL_PANEL = 'tplpanel';


   public function panelController() {
      $artM = new Articles_Model();
      $artM->where(Articles_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Articles_Model::COLUMN_CONCEPT.' = 0 AND '
         .Articles_Model::COLUMN_ADD_TIME.' <= NOW()  AND '.Articles_Model::COLUMN_URLKEY.' IS NOT NULL ',
         array('idc' => $this->category()->getId()));
      switch ($this->panelObj()->getParam('type', self::DEFAULT_TYPE)) {
         case 'top':
            $artM->order(array(Articles_Model::COLUMN_SHOWED => Model_ORM::ORDER_ASC));
            break;
         case 'rand':
            $artM->order(array('RAND(NOW())' => Model_ORM::ORDER_ASC));
            break;
         case 'list':
         default:
            $artM->order(array(Articles_Model::COLUMN_ADD_TIME => Model_ORM::ORDER_DESC));
            break;
      }
      $this->template()->articles = $artM->limit(0, $this->panelObj()->getParam('num',self::DEFAULT_NUM_ARTICLES))->records();
   }

   public function panelView() {
      $this->template()->addFile('tpl://'.$this->category()->getParam(self::PARAM_TPL_PANEL, 'panel.phtml'));
      $this->template()->rssLink = $this->link()->clear()->route().Url_Request::URL_FILE_RSS;
   }

   public static function settingsController(&$settings,Form &$form) {
      $elemNum = new Form_Element_Text('num', 'Počet článků v panelu');
      $elemNum->setSubLabel('Výchozí: '.self::DEFAULT_NUM_ARTICLES.'');
      $elemNum->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemNum,'basic');

      if(isset($settings['num'])) {
         $form->num->setValues($settings['num']);
      }

      $elemType = new Form_Element_Select('type', 'Řazení');
      $types = array('Podle data' => 'list', 'Podle počtu přečtění' => 'top', 'Náhodně' => 'rand');
      $elemType->setOptions($types);
      $elemType->setSubLabel('Výchozí: '.array_search(self::DEFAULT_TYPE, $types).'');
      $form->addElement($elemType,'basic');

      if(isset($settings['type'])) {
         $form->type->setValues($settings['type']);
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