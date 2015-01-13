<?php
class Templates_Controller extends Controller {
   const GET_PARAM_ID = 'id';
   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkWritebleRights();
      $model = new Templates_Model();
      $formDel = new Form('tpl_del_', true);

      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber());
      $formDel->addElement($elemId);

      $elemSubmitDel = new Form_Element_SubmitImage('save');
      $formDel->addElement($elemSubmitDel);

      if($formDel->isValid()){
         $model->deleteTemplate($formDel->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Šablona byla smazána'));
         $this->link()->reload();
      }

      $this->view()->templates = $model->records();
   }

   public function showController() {
      $this->checkWritebleRights();


   }

   /**
    * Kontroler pro přidání šablony
    */
   public function addController() {
      $this->checkWritebleRights();
      $addForm = $this->createForm();

      if($addForm->isSend() AND $addForm->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($addForm->isValid()) {
         $model = new Templates_Model();
         $tpl = $model->newRecord();
         $tpl->{Templates_Model::COLUMN_NAME} = $addForm->name->getValues();
         $tpl->{Templates_Model::COLUMN_DESC} = $addForm->desc->getValues();
         $tpl->{Templates_Model::COLUMN_CONTENT} = $addForm->content->getValues();
         $tpl->{Templates_Model::COLUMN_TYPE} = $addForm->type->getValues();

         $model->save($tpl);

         $this->infoMsg()->addMessage($this->tr('Uloženo'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $addForm;
      $this->view()->edit = false;
   }

   /**
    * controller pro úpravu novinky
    */
   public function editController() {
      $this->checkWritebleRights();
      $model = new Templates_Model();

      $form = $this->createForm();

      $tpl = $model->where($this->getRequest('id'))->record();

      if($tpl == false) return false;

      $form->name->setValues($tpl->{Templates_Model::COLUMN_NAME});
      $form->content->setValues($tpl->{Templates_Model::COLUMN_CONTENT});
      $form->type->setValues($tpl->{Templates_Model::COLUMN_TYPE});
      $form->desc->setValues($tpl->{Templates_Model::COLUMN_DESC});

      if($form->isSend() AND $form->send->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($form->isValid()) {
         $tpl->{Templates_Model::COLUMN_NAME} = $form->name->getValues();
         $tpl->{Templates_Model::COLUMN_DESC} = $form->desc->getValues();
         $tpl->{Templates_Model::COLUMN_CONTENT} = $form->content->getValues();
         $tpl->{Templates_Model::COLUMN_TYPE} = $form->type->getValues();

         $model->save($tpl);

         $this->infoMsg()->addMessage($this->tr('Uloženo'));
         if($form->goback->getValues() == true){
            $this->link()->route()->reload();
         } else {
            $this->link()->reload();
         }
      }

      $this->view()->form = $form;
      $this->view()->edit = true;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm() {
      $form = new Form('template_', true);

      $iName = new Form_Element_Text('name', $this->tr('Název'));
      $iName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iName);

      $iDesc = new Form_Element_TextArea('desc', $this->tr('Popis'));
      $form->addElement($iDesc);

      $iCnt = new Form_Element_TextArea('content', $this->tr('Obsah'));
      $iCnt->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iCnt);

      $t = array();
      foreach (Templates_Model::$tplTypes as $type) {
         $t[$type] = $type;
      }

      $iType = new Form_Element_Select('type', $this->tr('Typ šablony'));
      $iType->setOptions($t);
      $form->addElement($iType);

      $redirElem = new Form_Element_Checkbox('goback', $this->tr('Přejít zpět na seznam'));
      $form->addElement($redirElem);

      $submitButton = new Form_Element_SaveCancel('send');
      $form->addElement($submitButton);

      return $form;
   }

   public function previewController() {
      $this->checkControllRights();

      $model = new Templates_Model();

      $tplObj = $model->where($this->getRequest('id'))->record();

      if($tplObj == false) return false;


      $this->view()->tpl = $tplObj;
      // odkaz zpět
      $this->view()->linkBack = $this->link()->route();
   }


   public static function templateController(){
      $id = (int)$_GET[self::GET_PARAM_ID]; // ověřit jestli to nemá být stripslashes
      $model = new Templates_Model();
      Templates_View::$tpl = $model->where($id)->record();

      if(Templates_View::$tpl == false) return false;
   }
}