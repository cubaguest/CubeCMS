<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Configuration_Controller extends Controller {
   private $categoriesArray = array();

   public function mainController() {
      $this->checkWritebleRights();
      // nastavení viewru
      $this->view()->template()->addTplFile('list.phtml');
   }

   public function showController() {
   }

   public function editController() {
      $form = new Form('option');

      $m = new Model_Config();
      $opt = $m->getOption($this->getRequest('id'));

      switch ($opt->{Model_Config::COLUMN_TYPE}) {
         case 'number':
            $elem = new Form_Element_Text('value', $this->_('Hodnota'));
            $elem->addValidation(new Form_Validator_IsNumber());
            $elem->setValues($opt->{Model_Config::COLUMN_VALUE});
            break;
         case 'bool':
            $elem = new Form_Element_Radio('value', $this->_('Hodnota'));
            $elem->setOptions(array('true' => 'true', 'false' => 'false'));
            $elem->setValues($opt->{Model_Config::COLUMN_VALUE});
            break;
         case 'list':
            $elem = new Form_Element_Select('value', $this->_('Hodnota'));
            $arrExpl = explode(';', $opt->{Model_Config::COLUMN_VALUES});
            $arr = array();
            foreach ($arrExpl as $o) {
               $arr[$o] = $o;
            }
            $elem->setOptions($arr);
            $elem->setValues($opt->{Model_Config::COLUMN_VALUE});
            break;
         case 'listmulti':
            $elem = new Form_Element_Select('value', $this->_('Hodnota'));
            $arrExpl = explode(';', $opt->{Model_Config::COLUMN_VALUES});
            $arr = array();
            foreach ($arrExpl as $o) {
               $arr[$o] = $o;
            }

            $arrExplValue = explode(';', $opt->{Model_Config::COLUMN_VALUE});
            $arrVal = array();
            foreach ($arrExplValue as $o) {
               $arrVal[$o] = $o;
            }
            $elem->setOptions($arr);
            $elem->setValues($arrVal);
            $elem->setMultiple();
            break;
         case 'string':
         default:
            $elem = new Form_Element_TextArea('value', $this->_('Hodnota'));
            $elem->setValues($opt->{Model_Config::COLUMN_VALUE});
            break;
      }
      $form->addElement($elem);
      
      $submitButton = new Form_Element_SaveCancel('send');
      $form->addElement($submitButton);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->infoMsg()->addMessage($this->_('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($form->isValid()){
         $saveValue = null;
         if($form->value instanceof Form_Element_TextArea 
            OR $form->value instanceof Form_Element_Text
            OR $form->value instanceof Form_Element_Radio){
            $saveValue = $form->value->getValues();
         } else if($form->value instanceof Form_Element_Select){
            $vals = $form->value->getValues();
            if(is_array($vals)){
               $saveValue = implode(';', $vals);
            } else {
               $saveValue = $vals;
            }
         }

         if($saveValue !== null){
            $m->saveCfg($opt->{Model_Config::COLUMN_KEY}, $saveValue);
            $this->infoMsg()->addMessage($this->_('Volba byla uložena'));
            $this->link()->route()->reload();
         }

      }

      $this->view()->template()->form = $form;
      $this->view()->template()->option = $opt;
      $this->view()->template()->addTplFile('edit.phtml');
   }

//   public function addController() {
//      $form = new Form('option');
//
//      $m = new Model_Config();
//      $opt = $m->getOption($this->getRequest('id'));
//
//      var_dump($opt);
//
//      $this->view()->template()->form = $form;
//      $this->view()->template()->edit = false;
//      $this->view()->template()->addTplFile('edit.phtml');
//   }

}

?>