<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class NewsLetter_Controller extends Controller {
   const PARAM_ID_MAIL_GRP = 'mgrpid';

   const FORM_SUBMIT_PREFIX = 'regmail_';
   const FORM_EMAIL = 'mail';
   const FORM_SUBMIT = 'send';
   
   public function mainController() {
      $this->checkReadableRights();
      $newMailForm = $this->createRegMailForm();
      $model = new MailsAddressBook_Model_Addressbook();

      if($newMailForm->isSend()){
         // kontrola jestli tam už mail není
         if($model->where(MailsAddressBook_Model_Addressbook::COLUMN_MAIL." = :mail", 
               array('mail' => $newMailForm->mail->getValues()) )->count() > 0) {
            $newMailForm->mail->setError($this->tr('Váš e-mail je již registrován. Pokud Vám přesto nechodí e-maily, kontaktujte nás!'));
         }         
      }
      
      if($newMailForm->isValid()) {
         $rec = $model->newRecord();
         $rec->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL} = $newMailForm->mail->getValues();
         $rec->{MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP} = $this->category()->getParam(self::PARAM_ID_MAIL_GRP, 0);
         $rec->save();
         
         $this->infoMsg()->addMessage( sprintf( $this->tr('Váš e-mail %s byl registrován. Děkujeme.'), $newMailForm->mail->getValues() ) );
         $this->link()->route()->reload();
      }

      $textModel = new Text_Model();
      $this->view()->text = $textModel->getText($this->category()->getId());

      $this->view()->newMailForm = $newMailForm;
   }

   public static function createRegMailForm(){
      $newMailForm = new Form(self::FORM_SUBMIT_PREFIX);
      $tr = new Translator_Module('newsletter');
      $grp = $newMailForm->addGroup('mailreg', $tr->tr('Registrace e-mailu'));
      
      $elemMail = new Form_Element_Text(self::FORM_EMAIL, $tr->tr('E-mail'));
      $elemMail->addValidation(new Form_Validator_NotEmpty());
      $elemMail->addValidation(new Form_Validator_Email());
      $newMailForm->addElement($elemMail, $grp);

      $elemSend = new Form_Element_Submit(self::FORM_SUBMIT, $tr->tr('Registrovat'));
      $newMailForm->addElement($elemSend, $grp);
      return $newMailForm;
   }

   public function editTextController() {
      $this->checkWritebleRights();
      $textModel = new Text_Model();

      $form = new Form('edittext_');
      $text = $textModel->getText($this->category()->getId());
      if(!$text){
         $text = $textModel->newRecord();
         $text->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
      }

      $elemLabel = new Form_Element_Text('label', $this->tr('Nadpis'));
      $elemLabel->addFilter(new Form_Filter_HTMLSpecialChars());
      $elemLabel->setLangs();
      if($text != false) {
         $elemLabel->setValues($text->{Text_Model::COLUMN_LABEL});
      }
      $form->addElement($elemLabel);

      $elemText = new Form_Element_TextArea('text',$this->tr('úvodní text'));
      $elemText->setLangs();
      if($text != false) {
         $elemText->setValues($text->{Text_Model::COLUMN_TEXT});
      }
      $form->addElement($elemText);

      $elemSubmit = new Form_Element_SaveCancel('save');
      $form->addElement($elemSubmit);

      if($form->isSend() && $form->save->getValues() == false){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      if($form->isValid()) {
         $text->{Text_Model::COLUMN_TEXT} = $form->text->getValues(); 
         $text->{Text_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($form->text->getValues()); 
         $text->{Text_Model::COLUMN_LABEL} = $form->label->getValues(); 
         $text->save();         

         $this->infoMsg()->addMessage($this->tr('Text byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
   }

   public function unregistrationMailController() {
      $this->checkReadableRights();
      $model = new MailsAddressBook_Model_Addressbook();
      $unregMailForm = new Form('unregmail_');

      $elemMail = new Form_Element_Text('mail', $this->tr('E-mail'));
      $elemMail->addValidation(new Form_Validator_NotEmpty());
      $elemMail->addValidation(new Form_Validator_Email());
      $unregMailForm->addElement($elemMail);

      $elemSend = new Form_Element_SaveCancel('send', array($this->tr('Zrušit registraci'), $this->tr('Zpět') ) );
      $elemSend->setCancelConfirm(false);
      $unregMailForm->addElement($elemSend);

      if($unregMailForm->isSend()) {
         $this->link()->route();
      }
      
      if($unregMailForm->isValid()) {
         $model->where(MailsAddressBook_Model_Addressbook::COLUMN_MAIL." = :mail", array('mail' => $unregMailForm->mail->getValues() ))->delete();
         $this->infoMsg()->addMessage(sprintf($this->tr('E-mailové adrese %s byla zrušena registrace'), $unregMailForm->mail->getValues()));
         $this->link()->reload();
      }
      // odregistrace přes url adresu
      $email = $this->getRequestParam('mail', null);
      if($email !== null) {
         $model->where(MailsAddressBook_Model_Addressbook::COLUMN_MAIL." = :mail", array('mail' => $unregMailForm->mail->getValues() ))->delete();
         $this->infoMsg()->addMessage(sprintf($this->tr('E-mailové adrese %s byla zrušena registrace'), $email));
         $this->link()->rmParam()->reload();
      }
      $this->view()->unregMailForm = $unregMailForm;
   }
   
   /**
    * Metoda pro nastavení modulu
    */
   public function settings(&$settings,Form &$form) {
      $form->addGroup('mailgrp', $this->tr('Nastavení registrace'));
      $grpMails = $form->addGroup('mails', $this->tr('Ukládání e-mailů'));
      
      $eSelectGroup = new Form_Element_Select('groupid', $this->tr('Mailová skupina'));
      $eSelectGroup->setSubLabel($this->tr('Nové e-mailové adresy jsou automaticky zařazeny do této skupiny pro odesílání newsletterů.'));
      
      $m = new MailsAddressBook_Model_Groups();
      foreach ($m->records() as $group) {
         $eSelectGroup->setOptions(array($group->{MailsAddressBook_Model_Groups::COLUMN_NAME} => $group->{MailsAddressBook_Model_Groups::COLUMN_ID}), true);
      }
      if(isset($settings[self::PARAM_ID_MAIL_GRP])){
         $eSelectGroup->setValues($settings[self::PARAM_ID_MAIL_GRP]);
      }
      $form->addElement($eSelectGroup, $grpMails);
      
      if($form->isValid()){
         $settings[self::PARAM_ID_MAIL_GRP] = $form->groupid->getValues();
      }
   }

}
