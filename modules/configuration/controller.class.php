<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class Configuration_Controller extends Controller {
   public function mainController() {
      $this->checkWritebleRights();
      // nastavení viewru
      $modelCfgLocal = new Model_Config();
      $modelCfgGlobal = new Model_ConfigGlobal();

      $eId = new Form_Element_Hidden('id');
      $eSubmit = new Form_Element_SubmitImage('send');
      $eSubmit->setImage('/images/icons/edit.png');
      
      $formEditGlobal = new Form('gl_edit_');
      $formEditGlobal->addElement(clone $eId);
      $formEditGlobal->addElement(clone $eSubmit);
      
      if($formEditGlobal->isValid()){
         $this->link()->route('editGlobal', array('id' => $formEditGlobal->id->getValues()))->reload();
      }
      
      $this->view()->formGlEdit = $formEditGlobal;
      
      $formCopyGlobal = new Form('gl_copy_', true);
      $formCopyGlobal->addElement(clone $eId);
      $formCopyGlobal->addElement(clone $eSubmit);
      
      if($formCopyGlobal->isValid()){
         $recordGl = $modelCfgGlobal->record($formCopyGlobal->id->getValues());
         // delete local config if exist
         $modelCfgLocal->where(Model_Config::COLUMN_KEY.' = :k', array('k' => $recordGl->{Model_Config::COLUMN_KEY}))->delete();
         
         $recordGl->{Model_Config::COLUMN_ID} = null;
         $modelCfgLocal->save($recordGl);
         
         $this->infoMsg()->addMessage($this->tr('Byla vytvořena lokální nastavení z globálního'));
         $this->link()->reload();
      }
      
      $this->view()->formGlCopy = $formCopyGlobal;
      
      
      $formEditLocal = new Form('loc_edit_');
      $formEditLocal->addElement(clone $eId);
      $formEditLocal->addElement(clone $eSubmit);
      
      if($formEditLocal->isValid()){
         $this->link()->route('edit', array('id' => $formEditLocal->id->getValues()))->reload();
      }
      
      $this->view()->formLocEdit = $formEditLocal;
      
      $formDelLocal = new Form('loc_del_', true);
      $formDelLocal->addElement(clone $eId);
      $formDelLocal->addElement(clone $eSubmit);
      
      if($formDelLocal->isValid()){
         $modelCfgLocal->delete($formDelLocal->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Lokální nastavení bylo odebráno'));
         $this->link()->reload();
      }
      
      $this->view()->formLocDelete = $formDelLocal;
      
      
      
      $modelCfgLocal->joinFK(Model_Config::COLUMN_ID_GROUP, array('gname' => Model_ConfigGroups::COLUMN_NAME, 'gdesc' => Model_ConfigGroups::COLUMN_DESC))
         ->where(Model_Config::COLUMN_PROTECTED.' != 1 AND '.Model_Config::COLUMN_ID_GROUP.' != 1', array())
         ->order(array(Model_Config::COLUMN_ID_GROUP, Model_Config::COLUMN_KEY));//, 'ISNULL('.Model_Config::COLUMN_LABEL.')'

      $configsRecordsLocal = $modelCfgLocal->records();
      

      $modelCfgGlobal->joinFK(Model_Config::COLUMN_ID_GROUP, array('gname' => Model_ConfigGroups::COLUMN_NAME, 'gdesc' => Model_ConfigGroups::COLUMN_DESC))
         ->where(Model_Config::COLUMN_PROTECTED.' != 1 AND '.Model_Config::COLUMN_ID_GROUP.' != 1', array())
         ->order(array(Model_Config::COLUMN_ID_GROUP, Model_Config::COLUMN_KEY));//, 'ISNULL('.Model_Config::COLUMN_LABEL.')'

      $configsRecordsGlobal = $modelCfgGlobal->records();
      
      
      // groups
      $options = array();
      $groups = array();
      foreach (array_merge($configsRecordsGlobal, $configsRecordsLocal) as $record) {
         if(!isset ($groups[$record->{Model_Config::COLUMN_ID_GROUP}])){
            $groups[$record->{Model_Config::COLUMN_ID_GROUP}] = array(
               'name' => $record->{Model_ConfigGroups::COLUMN_NAME},
               'desc' => $record->{Model_ConfigGroups::COLUMN_DESC},
            );
            $options[$record->{Model_Config::COLUMN_ID_GROUP}] = array();
         }
      }
      
      $this->configAssign($configsRecordsGlobal, $options , 'global');
      $this->configAssign($configsRecordsLocal, $options, 'local');

      $this->view()->groups = $groups;
      $this->view()->options = $options;
      $this->view()->allowGlobalEdit = true;
//      Debug::log($options);
   }
   
   private function configAssign($records, &$optArr, $key)
   {
      foreach ($records as $record) {
         if(!isset($optArr[$record->{Model_Config::COLUMN_ID_GROUP}][$record->{Model_Config::COLUMN_KEY}])){
            $obj = new Object();
            $obj->local = null;
            $obj->global = null;
            $obj->{Model_Config::COLUMN_KEY} = null;
            $obj->{Model_Config::COLUMN_LABEL} = null;
            
            $optArr[$record->{Model_Config::COLUMN_ID_GROUP}][$record->{Model_Config::COLUMN_KEY}] = $obj;
         }
         $opt = &$optArr[$record->{Model_Config::COLUMN_ID_GROUP}][$record->{Model_Config::COLUMN_KEY}];
         
         $opt->{$key} = $record;
         if($opt->{Model_Config::COLUMN_KEY} == null){
            $opt->{Model_Config::COLUMN_KEY} = $record->{Model_Config::COLUMN_KEY};
            $opt->{Model_Config::COLUMN_LABEL} = $record->{Model_Config::COLUMN_LABEL};
         }
         
      }
   }

   public function editController() {
      $this->eModel(new Model_Config());
   }
   
   public function editGlobalController() {
      $this->eModel(new Model_ConfigGlobal());
   }
   
   private function eModel($model) {
      $form = new Form('option', true);
      $opt = $model->record($this->getRequest('id'));

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

         $model->save($opt);
         $this->infoMsg()->addMessage($this->tr('Volba byla uložena'));
         $this->link()->route()->reload();
      }

      $this->view()->template()->form = $form;
      $this->view()->template()->option = $opt;
   }
}

?>