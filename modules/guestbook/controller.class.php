<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class GuestBook_Controller extends Controller {
   const DEFAULT_MAX_TEXT_CHARS = 1000;
   const DEFAULT_MIN_TEXT_CHARS = 10;
   const DEFAULT_NUM_ON_PAGE = 15;

   const MIN_SEC_FOR_HUMAN = 10;
/**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      $model = new GuestBook_Model_Detail();

      $form = new Form('answer_');

      $elemNick = new Form_Element_Text('nick', $this->_('Přezdívka'));
      $elemNick->addValidation(new Form_Validator_NotEmpty());
      $elemNick->addValidation(new Form_Validator_MaxLength(100));
      $form->addElement($elemNick);

      $elemEmail = new Form_Element_Text('email', $this->_('E-mail'));
      $elemEmail->addValidation(new Form_Validator_NotEmpty());
      $elemEmail->addValidation(new Form_Validator_Email());
      $elemEmail->addValidation(new Form_Validator_MaxLength(50));
      $form->addElement($elemEmail);

      $elemWWW = new Form_Element_Text('www', $this->_('www'));
      $elemWWW->addValidation(new Form_Validator_Url());
      $elemWWW->addValidation(new Form_Validator_MaxLength(100));
      $elemWWW->addFilter(new Form_Filter_Url());
      $form->addElement($elemWWW);

      $elemText = new Form_Element_TextArea('text', $this->_('Text'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->addValidation(new Form_Validator_MinLength(
              $this->category()->getParam('mintextchars', self::DEFAULT_MIN_TEXT_CHARS)));
      $elemText->addValidation(new Form_Validator_MaxLength(
              $this->category()->getParam('maxtextchars', self::DEFAULT_MAX_TEXT_CHARS)));
      $elemText->addFilter(new Form_Filter_StripTags(array('span', 'strong', 'em', 'a', 'img')));
      $form->addElement($elemText);

      $elemCaptcha = new Form_Element_Hidden('captcha');
      $elemCaptcha->addValidation(new Form_Validator_NotEmpty());
      $elemCaptcha->setValues(0);
      $form->addElement($elemCaptcha);

      $elemSubmit = new Form_Element_Submit('send', $this->_('Odeslat'));
      $form->addElement($elemSubmit);

      if($form->isSend()){
         $this->view()->showFrom = true;
      }

      if($form->isValid()){
         if($form->isSend() AND $form->captcha->getValues() < self::MIN_SEC_FOR_HUMAN){
            $this->errMsg()->addMessage($this->_('Příliš rychlé odeslání příspěvku, pravděpodobně SPAM'));
         } else {
            $model->saveBook($this->category()->getId(), $form->email->getValues(),
                    $form->text->getValues(), $form->nick->getValues(),$form->www->getValues());
            $this->infoMsg()->addMessage($this->_('Příspěvek byl uložen'));
            $this->link()->reload();
         }
      }

      if($this->rights()->isWritable()){
         $delForm = new Form('guestbook_item_');

         $elemId = new Form_Element_Hidden('id');
         $elemId->addValidation(new Form_Validator_NotEmpty());
         $elemId->addValidation(new Form_Validator_IsNumber());
         $delForm->addElement($elemId);

         $elemSubDel = new Form_Element_SubmitImage('delete');
         $delForm->addElement($elemSubDel);

         if($delForm->isValid()){
            $model->deleteItem($delForm->id->getValues());
            $this->infoMsg()->addMessage($this->_('Položka byla smazána'));
            $this->link()->reload();
         }
      }

      // načtení příspěvků
      $scrollComponent = new Component_Scroll();
      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS,
              $model->getCount($this->category()->getId()));

      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              $this->category()->getParam('scroll', self::DEFAULT_NUM_ON_PAGE));

      $this->view()->books = $model->getList($this->category()->getId(),
              $scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage());
      $this->view()->scrollComp = $scrollComponent;

      $this->view()->form = $form;

   }

   public function exportFeedController(){
      $this->checkReadableRights();
      $this->view()->type = $this->getRequest('type', 'rss');
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings,Form &$form) {
      $form->addGroup('basic', 'Základní nasatvení');

      $elemScroll = new Form_Element_Text('scroll', 'Počet příspěvků na stránku');
      $elemScroll->setSubLabel('Výchozí: '.self::DEFAULT_NUM_ON_PAGE.' příspěvků');
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll,'basic');

      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }

      $elemMinChars = new Form_Element_Text('mintextchars', 'Minimální počet znaků textu');
      $elemMinChars->setSubLabel('Výchozí: '.self::DEFAULT_MIN_TEXT_CHARS.' znaků');
      $elemMinChars->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemMinChars,'basic');

      if(isset($settings['mintextchars'])) {
         $form->mintextchars->setValues($settings['mintextchars']);
      }

      $elemMaxChars = new Form_Element_Text('maxtextchars', 'Maximální počet znaků textu');
      $elemMaxChars->setSubLabel('Výchozí: '.self::DEFAULT_MAX_TEXT_CHARS.' znaků');
      $elemMaxChars->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemMaxChars,'basic');

      if(isset($settings['maxtextchars'])) {
         $form->maxtextchars->setValues($settings['maxtextchars']);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scroll'] = $form->scroll->getValues();
         $settings['maxtextchars'] = $form->maxtextchars->getValues();
         $settings['mintextchars'] = $form->mintextchars->getValues();
      }
   }
}

?>