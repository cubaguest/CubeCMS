<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class GuestBook_Controller extends Controller {
   const DEFAULT_MAX_TEXT_CHARS = 1000;
   const DEFAULT_MIN_TEXT_CHARS = 10;
   const DEFAULT_NUM_ON_PAGE = 15;

   const PARAM_WISIWIG_EDITOR = 'weditor';
   const PARAM_USE_CAPTCHA = 'uc';

   /**
 * Kontroler pro zobrazení textu
 */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();
      // pokud je v url parametr s ukážeme rovnou editor (přechod z panelu)
      if($this->getRequestParam('s')){
         $this->view()->showFrom = true;
      }

      $model = new GuestBook_Model();

      $form = new Form('answer_');

      $elemNick = new Form_Element_Text('nick', $this->tr('Přezdívka'));
      $elemNick->addValidation(new Form_Validator_NotEmpty());
      $elemNick->addValidation(new Form_Validator_MaxLength(100));
      $form->addElement($elemNick);

      $elemEmail = new Form_Element_Text('email', $this->tr('E-mail'));
      $elemEmail->addValidation(new Form_Validator_NotEmpty());
      $elemEmail->addValidation(new Form_Validator_Email());
      $elemEmail->addValidation(new Form_Validator_MaxLength(50));
      $form->addElement($elemEmail);

      $elemWWW = new Form_Element_Text('www', $this->tr('www'));
      $elemWWW->addValidation(new Form_Validator_Url());
      $elemWWW->addValidation(new Form_Validator_MaxLength(100));
      $elemWWW->addFilter(new Form_Filter_Url());
      $form->addElement($elemWWW);

      $elemText = new Form_Element_TextArea('text', $this->tr('Text'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->addValidation(new Form_Validator_MinLength(
              $this->category()->getParam('mintextchars', self::DEFAULT_MIN_TEXT_CHARS)));
      $elemText->addValidation(new Form_Validator_MaxLength(
              $this->category()->getParam('maxtextchars', self::DEFAULT_MAX_TEXT_CHARS)));
      $elemText->addFilter(new Form_Filter_HTMLPurify('p[style],span[style],a[href|title],strong,em,img[src|alt],br,ul,li,ol'));
      $form->addElement($elemText);

      if(!Auth::isLogin() && $this->category()->getParam(self::PARAM_USE_CAPTCHA, true)){
         $elemCaptcha = new Form_Element_Captcha('captcha');
         $form->addElement($elemCaptcha);
      }

      $elemSubmit = new Form_Element_Submit('send', $this->tr('Odeslat'));
      $form->addElement($elemSubmit);

      // u přihlášených vypneme chapchu
      if(Auth::isLogin()){
         $form->nick->setValues(Auth::getUserName());
         $form->email->setValues(Auth::getUserMail());
      }

      if($form->isSend()){
         $this->view()->showFrom = true;
      }

      if($form->isValid()){
         $newItem = $model->newRecord();
//         $newItem->{GuestBook_Model::COLUMN_DATE_ADD} = new DateTime();
         $newItem->{GuestBook_Model::COLUMN_ID_CAT} = $this->category()->getId();
         $newItem->{GuestBook_Model::COLUMN_NICK} = $form->nick->getValues();
         $newItem->{GuestBook_Model::COLUMN_WWW} = $form->www->getValues();
         $newItem->{GuestBook_Model::COLUMN_TEXT} = $form->text->getValues();
//          $newItem->{GuestBook_Model::COLUMN_TEXT_CLEAR} = strip_tags($form->text->getValues());
         $newItem->{GuestBook_Model::COLUMN_EMAIL} = $form->email->getValues();
         $newItem->{GuestBook_Model::COLUMN_IP} = $_SERVER['REMOTE_ADDR'];
         if(isset ($_SERVER['HTTP_USER_AGENT'])){
            $newItem->{GuestBook_Model::COLUMN_CLIENT} = $_SERVER['HTTP_USER_AGENT'];
         }
         $model->save($newItem);
         $this->infoMsg()->addMessage($this->tr('Příspěvek byl uložen'));
         $this->link()->param('s')->reload();
      }
      $this->view()->form = $form;

      if($this->rights()->isWritable()){
         $delForm = new Form('guestbook_item_');

         $elemId = new Form_Element_Hidden('id');
         $elemId->addValidation(new Form_Validator_NotEmpty());
         $elemId->addValidation(new Form_Validator_IsNumber());
         $delForm->addElement($elemId);

         $elemSubDel = new Form_Element_Submit('delete', $this->tr('Smazat'));
         $delForm->addElement($elemSubDel);

         if($delForm->isValid()){
            $model->delete($delForm->id->getValues());
            $this->infoMsg()->addMessage($this->tr('Položka byla smazána'));
            $this->link()->reload();
         }
         $this->view()->formDel = $delForm;
      }

      // načtení příspěvků
      $model->where(GuestBook_Model::COLUMN_ID_CAT.' = :idc AND '.GuestBook_Model::COLUMN_DELETED.' = 0',
         array('idc' => $this->category()->getId()))->order(array(GuestBook_Model::COLUMN_DATE_ADD => Model_ORM::ORDER_DESC));
      $scrollComponent = new Component_Scroll();
      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());
      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              $this->category()->getParam('scroll', self::DEFAULT_NUM_ON_PAGE));

      $this->view()->posts = $model->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage())->records();
      $this->view()->scrollComp = $scrollComponent;
   }

   /**
    * Metoda pro nastavení modulu
    */
   public function settings(&$settings, Form &$form) {
      $form->addGroup('basic');

      $elemScroll = new Form_Element_Text('scroll', $this->tr('Počet příspěvků na stránku'));
      $elemScroll->setSubLabel($this->tr(sprintf('Výchozí: %s příspěvků', self::DEFAULT_NUM_ON_PAGE)));
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll,'basic');

      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }

      $elemMinChars = new Form_Element_Text('mintextchars', $this->tr('Minimální počet znaků textu'));
      $elemMinChars->setSubLabel($this->tr(sprintf('Výchozí: %S znaků', self::DEFAULT_MIN_TEXT_CHARS)));
      $elemMinChars->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemMinChars,'basic');

      if(isset($settings['mintextchars'])) {
         $form->mintextchars->setValues($settings['mintextchars']);
      }

      $elemMaxChars = new Form_Element_Text('maxtextchars', $this->tr('Maximální počet znaků textu'));
      $elemMaxChars->setSubLabel($this->tr(sprintf('Výchozí: %s znaků', self::DEFAULT_MAX_TEXT_CHARS)));
      $elemMaxChars->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemMaxChars,'basic');

      if(isset($settings['maxtextchars'])) {
         $form->maxtextchars->setValues($settings['maxtextchars']);
      }

      $fGrpNewItem = $form->addGroup('newitem', $this->tr('Přidání příspěvku'));

      $elemWEditor = new Form_Element_Checkbox('weditor', $this->tr('Zapnout wisiwig editor'));
      $elemWEditor->setValues(true);
      $elemWEditor->setSubLabel($this->tr('Pokud je wisiwig editor vypnut, jsou automaticky odstraněnny všechny html tagy.'));
      $form->addElement($elemWEditor, $fGrpNewItem);
      if(isset($settings[self::PARAM_WISIWIG_EDITOR])) {
         $form->weditor->setValues($settings[self::PARAM_WISIWIG_EDITOR]);
      }
      
      $elemCaptcha = new Form_Element_Checkbox('captcha', $this->tr('Ověření uživatele'));
      $elemCaptcha->setValues(true);
      $elemCaptcha->setSubLabel($this->tr('Přidá do formuláře ověření pomocí opsání znaků.'));
      if(isset($settings[self::PARAM_USE_CAPTCHA])) {
         $elemCaptcha->setValues($settings[self::PARAM_USE_CAPTCHA]);
      }
      $form->addElement($elemCaptcha, $fGrpNewItem);

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scroll'] = $form->scroll->getValues();
         $settings['maxtextchars'] = $form->maxtextchars->getValues();
         $settings['mintextchars'] = $form->mintextchars->getValues();
         $settings[self::PARAM_WISIWIG_EDITOR] = $form->weditor->getValues();
         $settings[self::PARAM_USE_CAPTCHA] = $form->captcha->getValues();
      }
   }
}

?>