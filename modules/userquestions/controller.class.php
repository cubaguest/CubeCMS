<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class UserQuestions_Controller extends Controller {

   const PARAM_NEED_APPROVE = 'uq_na';
   const PARAM_ADMIN_RECIPIENTS = 'uq_admin_rec';
   const PARAM_OTHER_RECIPIENTS = 'uq_other_rec';
   const PARAM_SCROLL = 'uq_scroll';
   
   public function mainController()
   {
      // kontrola mazání
      if($this->rights()->isWritable()){
         $this->checkDeleteQuestion();
         $this->checkConfirmQuestion();
      }
      
      if($this->getRequestParam('id', false) && $this->getRequestParam('key', false) && $this->getRequestParam('action', false) ){
         
         $model = new UserQuestions_Model();
         
         $question = $model->where(UserQuestions_Model::COLUMN_SECURE_KEY." = :key AND ".UserQuestions_Model::COLUMN_ID." = :id", 
             array('key' => $this->getRequestParam('key'), 'id' => $this->getRequestParam('id')))->record();
             
         if(!$question){
            throw new InvalidArgumentException($this->tr('Požadovaná položka neexistuje.'));
         }
         
         switch ($this->getRequestParam('action')) {
            case 'approve':
               $question->{UserQuestions_Model::COLUMN_APPROVED} = 1;
               $question->{UserQuestions_Model::COLUMN_APPROVED_SEND} = 1;
               $question->save();
               $this->sendUserConfirmMail($question);
               $this->infoMsg()->addMessage($this->tr('Položka byla schválena a zveřejněna'));
               break;
            
            case 'remove':
               $model->delete($question);
               $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
               break;
            
            default :
               throw new InvalidArgumentException($this->tr('Nepodporovaná akce nad položkou.'));
         }
         
         
         $this->link()->clear()->redirect();
      }
         
      // load questions
      $questionsModel = new UserQuestions_Model();
      
      if($this->category()->getParam(self::PARAM_NEED_APPROVE, true) && !$this->rights()->isWritable()){
         $questionsModel->where(UserQuestions_Model::COLUMN_APPROVED.' = 1', array());
      }
      
      // scroll komponenta
         
      $scrollComponent = null;
      if($this->category()->getParam(self::PARAM_SCROLL, 10) != 0){
         $scrollComponent = new Component_Scroll();
         $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $questionsModel->count());

         $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              $this->category()->getParam(self::PARAM_SCROLL, 10));
      }
      
      if($scrollComponent instanceof Component_Scroll){
         $questionsModel->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage());
      }
      $questionsModel->order(array(UserQuestions_Model::COLUMN_TIME_ADD => Model_ORM::ORDER_DESC));
      
      $this->view()->scrollComponent = $scrollComponent;
      $this->view()->questions = $questionsModel->records();
   }
   
   /* Kontrolery */
   public function addQuestionController()
   {
      $form = new Form('addquestion');
      
      $elemName = new Form_Element_Text('name', $this->tr('Vaše jméno'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $elemName->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemName);
      
      $elemEmail = new Form_Element_Text('email', $this->tr('Váš e-mail'));
//      $elemEmail->addValidation(new Form_Validator_NotEmpty());
      $elemEmail->addValidation(new Form_Validator_Email());
      $elemEmail->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemEmail);
      
      $elemQ = new Form_Element_TextArea('question', $this->tr('Dotaz'));
      $elemQ->addValidation(new Form_Validator_NotEmpty());
      $elemQ->addFilter(new Form_Filter_StripTags());
      $form->addElement($elemQ);
      
      if(!$this->category()->getRights()->isWritable()){
         $elemC = new Form_Element_Captcha('captcha', $this->tr('Kontrola'));
         $form->addElement($elemC);
      }
      
      $elemSend = new Form_Element_SaveCancel('send');
      $form->addElement($elemSend);
      
      if($form->isSend() && !$form->send->getValues()){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         
         $question = UserQuestions_Model::getNewRecord();
         $question->{UserQuestions_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
         $question->{UserQuestions_Model::COLUMN_NAME} = $form->name->getValues();
         $question->{UserQuestions_Model::COLUMN_EMAIL} = $form->email->getValues();
         $question->{UserQuestions_Model::COLUMN_QUESTION} = $form->question->getValues();
         // od správců jsou automaticky schváleny
         if($this->category()->getRights()->isWritable()){
            $question->{UserQuestions_Model::COLUMN_APPROVED} = 1;
            $question->{UserQuestions_Model::COLUMN_APPROVED_SEND} = 1;
         } else {
            $question->{UserQuestions_Model::COLUMN_APPROVED} = !$this->category()->getParam(self::PARAM_NEED_APPROVE, true);
            $question->{UserQuestions_Model::COLUMN_APPROVED_SEND} = !$this->category()->getParam(self::PARAM_NEED_APPROVE, true);
         }
         
         $question->save();

         // nedát tyhle dotazy radši do parametrů nebo tak něco?
         if($this->category()->getParam(self::PARAM_NEED_APPROVE, true)){
            $this->infoMsg()->addMessage($this->tr('Položka byla uložena. Jakmile ji potvrdíme, dáme Vám vědět. Děkujeme.'));
         } else {
            $this->infoMsg()->addMessage($this->tr('Položka byla uložena. Děkujeme.'));
         }
         
         // mail uživateli a adminu, pokud nneí přihlášen
         if(!$this->category()->getRights()->isWritable()){
            if($question->{UserQuestions_Model::COLUMN_EMAIL} != null){
               $this->sendUserMail($question);
            }
            $this->sendAdminMail($question);
         }
         
         $this->link()->route()->redirect();
      }
      
      
      
      $this->view()->form = $form;
   }
   
   public function editQuestionController($id)
   {
      $this->checkWritebleRights();
      $this->view()->question = $question = UserQuestions_Model::getRecord($id);
      
      if(!$question){
         throw new InvalidArgumentException($this->tr('Požadovaná položka nebyla nalezena'));
      }
      
      $form = new Form('editquestion');
      
      $elemName = new Form_Element_Text('name', $this->tr('Jméno'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $elemName->addFilter(new Form_Filter_StripTags());
      $elemName->setValues($question->{UserQuestions_Model::COLUMN_NAME});
      $form->addElement($elemName);
      
      $elemEmail = new Form_Element_Text('email', $this->tr('E-mail'));
//      $elemEmail->addValidation(new Form_Validator_NotEmpty());
      $elemEmail->addValidation(new Form_Validator_Email());
      $elemEmail->addFilter(new Form_Filter_StripTags());
      $elemEmail->setValues($question->{UserQuestions_Model::COLUMN_EMAIL});
      $form->addElement($elemEmail);
      
      $elemQ = new Form_Element_TextArea('question', $this->tr('Dotaz'));
      $elemQ->addValidation(new Form_Validator_NotEmpty());
      $elemQ->addFilter(new Form_Filter_StripTags());
      $elemQ->setValues($question->{UserQuestions_Model::COLUMN_QUESTION});
      $form->addElement($elemQ);
      
      $elemA = new Form_Element_TextArea('answer', $this->tr('Odpověď'));
      $elemA->setValues($question->{UserQuestions_Model::COLUMN_ANSWER});
      $form->addElement($elemA);
      
      $elemApprove = new Form_Element_Checkbox('approve', $this->tr('Schválená položka'));
      $elemApprove->setValues($question->{UserQuestions_Model::COLUMN_APPROVED});
      $form->addElement($elemApprove);
      
      $elemSend = new Form_Element_SaveCancel('send');
      $form->addElement($elemSend);
      
      if($form->isSend() && !$form->send->getValues()){
         $this->link()->route()->redirect();
      }
      
      if($form->isValid()){
         if($this->category()->getParam(self::PARAM_NEED_APPROVE, true)
             && $form->approve->getValues() 
             && $question->{UserQuestions_Model::COLUMN_APPROVED_SEND} == 0){
            if($question->{UserQuestions_Model::COLUMN_EMAIL} != null){
               $this->sendUserConfirmMail($question);
            }    
            $question->{UserQuestions_Model::COLUMN_APPROVED_SEND} = 1;
         }
         
         $question->{UserQuestions_Model::COLUMN_NAME} = $form->name->getValues();
         $question->{UserQuestions_Model::COLUMN_EMAIL} = $form->email->getValues();
         $question->{UserQuestions_Model::COLUMN_QUESTION} = $form->question->getValues();
         $question->{UserQuestions_Model::COLUMN_ANSWER} = $form->answer->getValues();
         $question->{UserQuestions_Model::COLUMN_APPROVED} = $form->approve->getValues();
         $question->save();

         
         // mail uživateli a adminu
         $this->infoMsg()->addMessage($this->tr('Položka byla uložena.'));
         
         $this->link()->route()->redirect();
      }
      
      $this->view()->form = $form;
      
   }
   
   
   
   /* Podpůrné metody  */
   protected function checkDeleteQuestion()
   {
      $form = new Form('removequestion');
      
      $eId = new Form_Element_Hidden('id');
      $form->addElement($eId);
      
      $eDel = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $form->addElement($eDel);
      
      if($form->isValid()){
         $model = new UserQuestions_Model();
         $model->delete($form->id->getValues());
         $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
         $this->link()->redirect();
      }
      
      $this->view()->formDelete = $form;
   }
   
   protected function checkConfirmQuestion()
   {
      $form = new Form('approvequestion');
      
      $eId = new Form_Element_Hidden('id');
      $form->addElement($eId);
      
      $eApprove = new Form_Element_Submit('approve', $this->tr('Schválit'));
      $form->addElement($eApprove);
      
      if($form->isValid()){
         $question = UserQuestions_Model::getRecord($form->id->getValues());
         
         if(!$question->{UserQuestions_Model::COLUMN_APPROVED_SEND}){
            $this->sendUserConfirmMail($question);
         }
         $question->{UserQuestions_Model::COLUMN_ID_USER_APPROVED} = Auth::getUserId();
         $question->{UserQuestions_Model::COLUMN_APPROVED} = 1;
         $question->{UserQuestions_Model::COLUMN_APPROVED_SEND} = 1;
         $question->save();
         
         $this->infoMsg()->addMessage($this->tr('Položka byla schválena'));
         $this->link()->redirect();
      }
      
      $this->view()->formApprove = $form;
   }
   
   protected function sendUserMail(Model_ORM_Record $question)
   {
      // obsah mailu načítat z šablony pro daný jazyk
      $cntTpl = new Template_Module($this->link(), $this->category());
      $tplFile = 'mails/newquestion_user_'.Locales::getLang().'.phtml';
      $tplFileGlobal = 'mails/newquestion_user.phtml';
      
      if($cntTpl->existTpl($tplFile, 'userquestions')){
         $cntTpl->addFile('tpl://userquestions:'.$tplFile);
      } else if($cntTpl->existTpl($tplFileGlobal, 'userquestions')) {
         $cntTpl->addFile('tpl://userquestions:'.$tplFileGlobal);
      } else {
         return;
      }
      
      $cntTpl->needApprove = $this->category()->getParam(self::PARAM_NEED_APPROVE, true);
      $cntTpl->question = $question;
      
      $mail = new Email(true);
      $mail->setSubject( sprintf( $this->tr('Přidání příspěvku na stránkách %s'), CUBE_CMS_WEB_NAME ) );
      $mail->setContent(Email::getBaseHtmlMail((string)$cntTpl));
      
      $mail->addAddress($question->{UserQuestions_Model::COLUMN_EMAIL});
      $mail->send();
   }
   
   protected function sendUserConfirmMail(Model_ORM_Record $question)
   {
      // obsah mailu načítat z šablony pro daný jazyk
      $cntTpl = new Template_Module($this->link(), $this->category());
      $tplFile = 'mails/user_confirm_'.Locales::getLang().'.phtml';
      $tplFileGlobal = 'mails/user_confirm.phtml';
      
      if($cntTpl->existTpl($tplFile, 'userquestions')){
         $cntTpl->addFile('tpl://userquestions:'.$tplFile);
      } else if($cntTpl->existTpl($tplFileGlobal, 'userquestions')) {
         $cntTpl->addFile('tpl://userquestions:'.$tplFileGlobal);
      } else {
         return;
      }
      
      $cntTpl->needApprove = $this->category()->getParam(self::PARAM_NEED_APPROVE, true);
      $cntTpl->question = $question;
      
      $mail = new Email(true);
      $mail->setSubject( sprintf( $this->tr('Schválení příspěvku na stránkách %s'), CUBE_CMS_WEB_NAME ) );
      $mail->setContent(Email::getBaseHtmlMail((string)$cntTpl));
      
      $mail->addAddress($question->{UserQuestions_Model::COLUMN_EMAIL});
      $mail->send();
   }
   
   protected function sendAdminMail(Model_ORM_Record $question)
   {
      // obsah mailu načítat z šablony pro daný jazyk
      
      $cntTpl = new Template_Module($this->link(), $this->category());
      $tplFile = 'mails/newquestion_admin_'.Locales::getLang().'.phtml';
      $tplFileGlobal = 'mails/newquestion_admin.phtml';
      
      if($cntTpl->existTpl($tplFile, 'userquestions')){
         $cntTpl->addFile('tpl://userquestions:'.$tplFile);
      } else if($cntTpl->existTpl($tplFileGlobal, 'userquestions')) {
         $cntTpl->addFile('tpl://userquestions:'.$tplFileGlobal);
      } else {
         return;
      }
      
      $cntTpl->needApprove = $this->category()->getParam(self::PARAM_NEED_APPROVE, true);
      $cntTpl->question = $question;
      $cntTpl->linkApprove = $this->link()->clear()
          ->param('action', 'approve')
          ->param('key', $question->{UserQuestions_Model::COLUMN_SECURE_KEY})
          ->param('id', $question->getPK());
      $cntTpl->linkRemove = $this->link()->clear()
          ->param('action', 'remove')
          ->param('key', $question->{UserQuestions_Model::COLUMN_SECURE_KEY})
          ->param('id', $question->getPK());
      
      $mail = new Email(true);
      $mail->setSubject( sprintf( $this->tr('Přidána nová položka do stránek %s'), CUBE_CMS_WEB_NAME ) );
      if($question->{UserQuestions_Model::COLUMN_EMAIL} != null){
         $mail->setFrom(array($question->{UserQuestions_Model::COLUMN_EMAIL} => $question->{UserQuestions_Model::COLUMN_NAME}));      
      }
      
      $mail->setContent(Email::getBaseHtmlMail((string)$cntTpl));
      
      $adminMails = array();
      // maily adminů - předané
      $str = $this->category()->getParam(self::PARAM_OTHER_RECIPIENTS, null);
      if ($str != null) {
         $adminMails = explode(';', $str);
      }
      // maily adminů - z uživatelů
      $mails = Model_Users::getUsersMails($this->category()->getParam(self::PARAM_ADMIN_RECIPIENTS, array()));
      if(!empty($mails)){
         $mail->addAddress(array_flip($mails));
         $mail->send();
      }
   }
   
   
   
   public function settings(&$settings, Form &$form)
   {
      $elemNedApprove = new Form_Element_Checkbox('approve', $this->tr('Před zveřejněním dotazu potrdit'));
      $form->addElement($elemNedApprove);
      
      if(isset($settings[self::PARAM_NEED_APPROVE])){
         $form->approve->setValues($settings[self::PARAM_NEED_APPROVE]);
      }
      
      $elemScroll = new Form_Element_Text('scroll', $this->tr('Počet položek na stránku'));
      $elemScroll->setSubLabel($this->tr('Výchozí: 10 položek. Pokud je zadána 0 budou vypsány všechny položky'));
      $elemScroll->setValues(10);
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll, Controller::SETTINGS_GROUP_VIEW);

      if(isset($settings[self::PARAM_SCROLL])) {
         $form->scroll->setValues($settings[self::PARAM_SCROLL]);
      }
      
      
      $grpAdmin = $form->addGroup('admins', 'Nastavení e-mailů', 'Nastavení příjemců odeslaných dotazů');

      // maily správců
      $elemEamilRec = new Form_Element_TextArea('otherRec', 'Adresy správců');
      $elemEamilRec->setSubLabel('E-mailové adresy správců, kterým chodí dotazy.
Může jich být více a jsou odděleny středníkem. Místo tohoto boxu
lze využít následující výběr již existujících uživatelů.');
      $form->addElement($elemEamilRec, $grpAdmin);

      if (isset($settings[self::PARAM_OTHER_RECIPIENTS])) {
         $form->otherRec->setValues($settings[self::PARAM_OTHER_RECIPIENTS]);
      }

      $elemAdmins = new Form_Element_Select('admins', 'Adresy uživatelů v systému');
      // načtení uživatelů
      $users = Model_Users::getUsersWithMails();
      $elemAdmins->setOptions($users);
      $elemAdmins->setMultiple();
      $elemAdmins->html()->setAttrib('size', 4);
      if (isset($settings[self::PARAM_ADMIN_RECIPIENTS])) {
         $elemAdmins->setValues($settings[self::PARAM_ADMIN_RECIPIENTS]);
      }

      $form->addElement($elemAdmins, $grpAdmin);
      
      
      if($form->isValid()){
         $settings[self::PARAM_NEED_APPROVE] = $form->approve->getValues();
         $settings[self::PARAM_ADMIN_RECIPIENTS] = $form->admins->getValues();
         $settings[self::PARAM_OTHER_RECIPIENTS] = $form->otherRec->getValues();
         $settings[self::PARAM_SCROLL] = (int)$form->scroll->getValues();
      }
         
      
   }


   /* Autorun metody */
   public static function AutoRunDaily()
   {}
   public static function AutoRunHourly()
   {}
   public static function AutoRunMonthly()
   {}
   public static function AutoRunYearly()
   {}
   public static function AutoRunWeekly()
   {}
}
