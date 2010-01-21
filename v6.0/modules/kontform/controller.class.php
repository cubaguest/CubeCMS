<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Kontform_Controller extends Controller {
/**
 * Názvy formulářových prvků
 * @var string
 */
   const FORM_PREFIX = 'kontform_';
   const FORM_PREFIX_MAILS = 'mail_';
   const FORM_BUTTON_SEND = 'send';
   const FORM_BUTTON_DELETE = 'delete';
   const FORM_NAME = 'name';
   const FORM_SURNAME = 'surname';
   const FORM_EMAIL = 'email';
   const FORM_QUESTION = 'question';
   const FORM_MAIL = 'mail';
   const FORM_MAIL_ID = 'id';

   /**
    * Kontroler pro zobrazení formuláře,
    * kterým dá zákazník kontakt na sebe a položí dotaz
    */
   public function mainController() {
   //		Kontrola práv
      $this->checkReadableRights();

      $form = new Form(self::FORM_PREFIX);
      $form->crInputText(self::FORM_NAME, True);
      //položka příjmení je nepovinná
      $form->crInputText(self::FORM_SURNAME);
      //tady u toho inputu to chce ještě dodělat validaci na mejlovou adresu:
      // validaci nastavíš typem validace. je to konstanta objektu FORM
      $form->crInputText(self::FORM_EMAIL, True, False, Form::VALIDATE_EMAIL);
      $form->crTextArea(self::FORM_QUESTION, True);
      $form->crSubmit(self::FORM_BUTTON_SEND);

      $kontform = new Kontform_Model_Detail($this->sys());

      //        Pokud byl formulář odeslán
      if($form->checkForm()) {
            if(!$kontform->saveKontform($form->getValue(self::FORM_NAME),
            $form->getValue(self::FORM_SURNAME), $form->getValue(self::FORM_EMAIL),
            $form->getValue(self::FORM_QUESTION))) {
               throw new UnexpectedValueException($this->_('Váš dotaz se nepodařilo odeslat'));
            }

            // odeslání emailu
            $mailObj = new Email();
            $mailObj->setSubject($this->_("Dotaz"));

            // Přidání textu
            $mailText = 'Byl odeslán dotaz ze stránek '.AppCore::sysConfig()->getOptionValue("web_name")
                .' '.Links::getMainWebDir()."\n\n"
                ."Přijmení, jméno: ".$form->getValue(self::FORM_SURNAME).", ".$form->getValue(self::FORM_NAME)."\n"
                ."E-mail:          ".$form->getValue(self::FORM_EMAIL)."\n"
                ."Dotaz:\n".$form->getValue(self::FORM_QUESTION)."\n\n"
                ."Odesláno: ".strftime("%x %X");

            $mailObj->setContent($mailText);

            // načtení emailů
            $modelMails = new Kontform_Model_Mails($this->sys());
            $mails = $modelMails->getListMails();
            foreach ($mails as $mail) {
               $mailObj->addAddress($mail[Kontform_Model_Mails::COLUMN_EMAIL]);
            }
            $mailObj->setFrom($form->getValue(self::FORM_EMAIL));
            $mailObj->sendMail();

         $this->infoMsg()->addMessage($this->_('Dotaz byl odeslán'));
         $this->getLink()->action()->reload();
      }
   }

   /**
    * Kontroler pro upravu emailu
    */
   public function editMailsController() {
      $this->checkWritebleRights();

      $addMailForm = new Form(self::FORM_PREFIX.self::FORM_PREFIX_MAILS);

      $addMailForm->crInputText(self::FORM_MAIL, true, false, Form::VALIDATE_EMAIL)
      ->crSubmit(self::FORM_BUTTON_SEND);

      if($addMailForm->checkForm()){
         $model = new Kontform_Model_Mails($this->sys());
         if(!$model->saveNewMail($addMailForm->getValue(self::FORM_MAIL))){
            throw new UnexpectedValueException($this->_("Chyba při ukládání emailu"), 1);
         }
         $this->infoMsg()->addMessage($this->_("Email byl uložen"));
         $this->link()->reload();
      }

      $deleteForm = new Form(self::FORM_PREFIX.self::FORM_PREFIX_MAILS);
      $deleteForm->crInputHidden(self::FORM_MAIL_ID, true, "is_numeric")
      ->crSubmit(self::FORM_BUTTON_DELETE);

      if($deleteForm->checkForm()){
         $model = new Kontform_Model_Mails($this->sys());
         if(!$model->deleteMail($deleteForm->getValue(self::FORM_MAIL_ID))){
            throw new UnexpectedValueException($this->_("Chyba při mazání emailu"), 2);
         }
         $this->infoMsg()->addMessage($this->_("Email byl smazán"));
         $this->link()->reload();
      }
   }
}

?>