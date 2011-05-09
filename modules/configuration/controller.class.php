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
      $modelCfg = new Model_Config();

      $modelCfg->joinFK(Model_Config::COLUMN_ID_GROUP, array('gname' => Model_ConfigGroups::COLUMN_NAME, 'gdesc' => Model_ConfigGroups::COLUMN_DESC))
         ->where(Model_Config::COLUMN_PROTECTED.' != 1 AND '.Model_Config::COLUMN_ID_GROUP.' != 1', array())
         ->order(array(Model_Config::COLUMN_ID_GROUP, Model_Config::COLUMN_KEY));//, 'ISNULL('.Model_Config::COLUMN_LABEL.')'

      $records = $modelCfg->records();
      $sorted = array();
      foreach ($records as $record) {
         if(!isset ($sorted[$record->{Model_Config::COLUMN_ID_GROUP}])){
            $sorted[$record->{Model_Config::COLUMN_ID_GROUP}] = array(
               'name' => $record->{Model_ConfigGroups::COLUMN_NAME},
               'desc' => $record->{Model_ConfigGroups::COLUMN_DESC},
               'options' => array(),
            );
         }
         array_push($sorted[$record->{Model_Config::COLUMN_ID_GROUP}]['options'], $record);
      }

      $this->view()->list = $sorted;
   }

   public function editController() {
      $form = new Form('option');

      $m = new Model_Config();
      $opt = $m->record($this->getRequest('id'));

      if($opt == false){
         return false;
      }
      switch ($opt->{Model_Config::COLUMN_TYPE}) {
         case 'number':
            $elem = new Form_Element_Text('value', $this->tr('Hodnota'));
            $elem->addValidation(new Form_Validator_IsNumber());
            $elem->setValues($opt->{Model_Config::COLUMN_VALUE});
            break;
         case 'bool':
            $elem = new Form_Element_Radio('value', $this->tr('Hodnota'));
            $elem->setOptions(array('true' => 'true', 'false' => 'false'));
            $elem->setValues($opt->{Model_Config::COLUMN_VALUE});
            break;
         case 'list':
            $elem = new Form_Element_Select('value', $this->tr('Hodnota'));
            $arrExpl = explode(';', $opt->{Model_Config::COLUMN_VALUES});
            $arr = array();
            foreach ($arrExpl as $o) {
               $arr[$o] = $o;
            }
            $elem->setOptions($arr);
            $elem->setValues($opt->{Model_Config::COLUMN_VALUE});
            break;
         case 'listmulti':
            $elem = new Form_Element_Select('value', $this->tr('Hodnota'));
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
            $elem = new Form_Element_TextArea('value', $this->tr('Hodnota'));
            $elem->setValues($opt->{Model_Config::COLUMN_VALUE});
            break;
      }
      $form->addElement($elem);

      $submitButton = new Form_Element_SaveCancel('send');
      $form->addElement($submitButton);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($form->isValid()){
         if($form->value instanceof Form_Element_TextArea
            OR $form->value instanceof Form_Element_Text
            OR $form->value instanceof Form_Element_Radio){
            $opt->{Model_Config::COLUMN_VALUE} = $form->value->getValues();
         } else if($form->value instanceof Form_Element_Select){
            $vals = $form->value->getValues();
            if(is_array($vals)){
               $opt->{Model_Config::COLUMN_VALUE} = implode(';', $vals);
            } else {
               $opt->{Model_Config::COLUMN_VALUE} = $vals;
            }
         }

         $m->save($opt);
         $this->infoMsg()->addMessage($this->tr('Volba byla uložena'));
         $this->link()->route()->reload();

      }

      $this->view()->template()->form = $form;
      $this->view()->template()->option = $opt;
   }
}

?>