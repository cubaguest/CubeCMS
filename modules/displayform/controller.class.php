<?php
/**
 * Kontroler pro obsluhu fotogalerie
 *
 * Jedná se o jednoúrovňovou fotogalerii s textem
 *
 * @copyright  	Copyright (c) 2009 Jakub Matas
 * @version    	$Id: $ VVE 6.0.0 $Revision: $
 * @author 		$Author: $ $Date:$
 *              $LastChangedBy: $ $LastChangedDate: $
 */

class DisplayForm_Controller extends Controller {
   const PARAM_FORM_ID = 'idf';

   /**
    * Kontroler pro zobrazení fotogalerii
    */
   public function mainController() 
   {
      //		Kontrola práv
      $this->checkReadableRights();
      $this->view()->dFormTpl = Forms_Controller::dynamicForm($this->category()->getParam(self::PARAM_FORM_ID, 0));

      $model = new Text_Model();
      $this->view()->text = $model->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :subkey',
         array('idc' => $this->category()->getId(), 'subkey' => Text_Controller::TEXT_MAIN_KEY))
         ->record();
   }

   public function edittextController() 
   {
      $this->checkWritebleRights();

      $form = new Form("text_", true);
      $textarea = new Form_Element_TextArea('text', $this->tr("Text"));
      $textarea->setLangs();
      $form->addElement($textarea);

      $model = new Text_Model();
      $text = $model->where(Text_Model::COLUMN_ID_CATEGORY.' = :idc AND '.Text_Model::COLUMN_SUBKEY.' = :subkey',
         array('idc' => $this->category()->getId(), 'subkey' => Text_Controller::TEXT_MAIN_KEY))
         ->record();
      if($text != false) {
         $form->text->setValues($text->{Text_Model::COLUMN_TEXT});
      }

      $submit = new Form_Element_SaveCancel('send');
      $form->addElement($submit);

      if($form->isSend() AND $form->send->getValues() == false){
         $this->link()->route()->reload();
      }

      if($form->isValid()) {
         try {
            if($text == false){
               $text = $model->newRecord();
               $text->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId(); 
               $text->{Text_Model::COLUMN_SUBKEY} = Text_Controller::TEXT_MAIN_KEY; 
            }
            $text->{Text_Model::COLUMN_TEXT} = $form->text->getValues(); 
            $text->{Text_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($form->text->getValues()); 
            $model->save($text);
            $this->infoMsg()->addMessage($this->tr('Text byl uložen'));
            $this->link()->route()->reload();
         } catch (PDOException $e) {
            new CoreErrors($e);
         }
      }

      // view
      $this->view()->template()->form = $form;
   }

   public function settings(&$settings,Form &$form) 
   {
      $fGrpViewSet = $form->addGroup('view', $this->tr('Nastavení vzhledu'));
      $fGrpFormSet = $form->addGroup('form', $this->tr('Nastavení formuláře'));

      $elemIDForm = new Form_Element_Select('form_id', $this->tr('Formulář'));
      $formModel = new Forms_Model();
      $forms = $formModel->records();
      foreach ($forms as $f) {
         $elemIDForm->setOptions(array($f->{Forms_Model::COLUMN_NAME}." (ID: ".$f->{Forms_Model::COLUMN_ID}.")" => $f->{Forms_Model::COLUMN_ID}), true);
      }
      if(isset($settings[self::PARAM_FORM_ID])) {
         $elemIDForm->setValues($settings[self::PARAM_FORM_ID]);
      }

      $form->addElement($elemIDForm, $fGrpFormSet);

      if($form->isValid()){
         $settings[self::PARAM_FORM_ID] = $form->form_id->getValues();
      }
   }
}
?>