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
      $formDel = new Form('tpl_del_');

      $elemId = new Form_Element_Hidden('id');
      $elemId->addValidation(new Form_Validator_IsNumber());
      $formDel->addElement($elemId);

      $elemSubmitDel = new Form_Element_SubmitImage('save');
      $formDel->addElement($elemSubmitDel);

      if($formDel->isValid()){
         $model->deleteTemplate($formDel->id->getValues());
         $this->infoMsg()->addMessage($this->_('Šablona byla smazána'));
         $this->link()->reload();
      }

      $this->view()->templates = $model->getTemplates();
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

      if($addForm->isValid()) {
         $model = new Templates_Model();
         $model->saveTemplate($addForm->name->getValues(), $addForm->desc->getValues(), $addForm->content->getValues(),
                 $addForm->type->getValues());

         $this->infoMsg()->addMessage($this->_('Uloženo'));
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

      $tpl = $model->getTemplate($this->getRequest('id'));

      if($tpl == false) return false;

      $form->name->setValues($tpl->{Templates_Model::COLUMN_NAME});
      $form->content->setValues($tpl->{Templates_Model::COLUMN_CONTENT});
      $form->type->setValues($tpl->{Templates_Model::COLUMN_TYPE});
      $form->desc->setValues($tpl->{Templates_Model::COLUMN_DESC});

      if($form->isValid()) {
         $model->saveTemplate($form->name->getValues(), $form->desc->getValues(), $form->content->getValues(),
                 $form->type->getValues(), $this->getRequest('id'));

         $this->infoMsg()->addMessage($this->_('Uloženo'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
      $this->view()->edit = true;
   }

   /**
    * Metoda  vytvoří element formuláře
    * @return Form
    */
   protected function createForm() {
      $form = new Form('template_');

      $iName = new Form_Element_Text('name', $this->_('Název'));
      $iName->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iName);

      $iDesc = new Form_Element_TextArea('desc', $this->_('Popis'));
      $form->addElement($iDesc);

      $iCnt = new Form_Element_TextArea('content', $this->_('Obsah'));
      $iCnt->addValidation(New Form_Validator_NotEmpty());
      $form->addElement($iCnt);

      $t = array();
      foreach (Templates_Model::$tplTypes as $type) {
         $t[$type] = $type;
      }

      $iType = new Form_Element_Select('type', $this->_('Typ šablony'));
      $iType->setOptions($t);
      $form->addElement($iType);

      $iSubmit = new Form_Element_Submit('save', $this->_('Uložit'));
      $form->addElement($iSubmit);

      return $form;
   }

   public static function templateController(){
      $id = (int)$_GET[self::GET_PARAM_ID]; // ověřit jestli to nemá být stripslashes

      $model = new Templates_Model();
      Templates_View::$tpl = $model->getTemplate($id);

      if(Templates_View::$tpl == false) return false;
   }
}
?>