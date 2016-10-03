<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class FAQ_Controller extends Controller {

   const PARAM_SCROLL = 'uq_scroll';

   public function mainController()
   {
      // kontrola mazání
      if ($this->rights()->isWritable()) {
         $this->checkDeleteQuestion();
      }

      // load questions
      $questionsModel = new FAQ_Model();
      $questionsModel->where(FAQ_Model::COLUMN_ID_CATEGORY." = :idc", array('idc' => $this->category()->getId()));

      // scroll komponenta
      $scrollComponent = null;
      if ($this->category()->getParam(self::PARAM_SCROLL, 10) != 0) {
         $scrollComponent = new Component_Scroll();
         $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $questionsModel->count());

         $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE, $this->category()->getParam(self::PARAM_SCROLL, 10));
      }

      if ($scrollComponent instanceof Component_Scroll) {
         $questionsModel->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage());
      }
//      $questionsModel->order(array(FAQ_Model::COLUMN_TIME_ADD => Model_ORM::ORDER_DESC));

      $this->view()->scrollComponent = $scrollComponent;
      $this->view()->questions = $questionsModel->records();
   }

   /* Kontrolery */

   public function addQuestionController()
   {
      $this->checkWritebleRights();
      $form = $this->createForm();
      if ($form->isSend() && !$form->send->getValues()) {
         $this->link()->route()->redirect();
      }

      if ($form->isValid()) {
         $this->processForm($form);
         $this->infoMsg()->addMessage($this->tr('Položka byla uložena'));
         $this->link()->route()->redirect();
      }

      $this->view()->form = $form;
   }

   public function editQuestionController($id)
   {
      $this->checkWritebleRights();
      $this->view()->question = $question = FAQ_Model::getRecord($id);

      if (!$question) {
         throw new InvalidArgumentException($this->tr('Požadovaná položka nebyla nalezena'));
      }

      $form = $this->createForm($question);

      if ($form->isSend() && !$form->send->getValues()) {
         $this->link()->route()->redirect();
      }

      if ($form->isValid()) {
         $this->processForm($form, $question);

         // mail uživateli a adminu
         $this->infoMsg()->addMessage($this->tr('Položka byla uložena.'));
         $this->link()->route()->redirect();
      }
      $this->view()->form = $form;
   }

   protected function createForm(Model_ORM_Record $question = null)
   {
      $form = new Form('editquestion');

      $elemQ = new Form_Element_TextArea('question', $this->tr('Dotaz'));
      $elemQ->setLangs();
      $elemQ->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemQ);

      $elemA = new Form_Element_TextArea('answer', $this->tr('Odpověď'));
      $elemA->setLangs();
      $elemA->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($elemA);

      $elemSend = new Form_Element_SaveCancel('send');
      $form->addElement($elemSend);

      if($question instanceof Model_ORM_Record){
         $form->question->setValues($question->{FAQ_Model::COLUMN_QUESTION});
         $form->answer->setValues($question->{FAQ_Model::COLUMN_ANSWER});
      }
      
      return $form;
   }

   protected function processForm(Form $form, Model_ORM_Record $question = null)
   {
      if ($question == null) {
         $question = FAQ_Model::getNewRecord();
         $question->{FAQ_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
      }

      $question->{FAQ_Model::COLUMN_QUESTION} = $form->question->getValues();
      $question->{FAQ_Model::COLUMN_ANSWER} = $form->answer->getValues();
      $question->save();
   }

   /* Podpůrné metody  */

   protected function checkDeleteQuestion()
   {
      $form = new Form('removequestion');

      $eId = new Form_Element_Hidden('id');
      $form->addElement($eId);

      $eDel = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $form->addElement($eDel);

      if ($form->isValid()) {
         $model = new FAQ_Model();
         $model->delete($form->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
         $this->link()->redirect();
      }

      $this->view()->formDelete = $form;
   }

   public function settings(&$settings, Form &$form)
   {
      $elemScroll = new Form_Element_Text('scroll', $this->tr('Počet položek na stránku'));
      $elemScroll->setSubLabel($this->tr('Výchozí: 10 položek. Pokud je zadána 0 budou vypsány všechny položky'));
      $elemScroll->setValues(10);
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll, Controller::SETTINGS_GROUP_VIEW);

      if (isset($settings[self::PARAM_SCROLL])) {
         $form->scroll->setValues($settings[self::PARAM_SCROLL]);
      }

      if ($form->isValid()) {
         $settings[self::PARAM_SCROLL] = (int) $form->scroll->getValues();
      }
   }

   /* Autorun metody */

   public static function AutoRunDaily()
   {
      
   }

   public static function AutoRunHourly()
   {
      
   }

   public static function AutoRunMonthly()
   {
      
   }

   public static function AutoRunYearly()
   {
      
   }

   public static function AutoRunWeekly()
   {
      
   }

}
