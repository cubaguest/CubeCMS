<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class GuestBook_Controller extends Controller {
   const DEFAULT_MAX_TEXT_CHARS = 1000;
   const DEFAULT_MIN_TEXT_CHARS = 10;
   const DEFAULT_NUM_ON_PAGE = 15;

   const MIN_SEC_FOR_HUMAN = 10;

   const PARAM_CAPCHA_SEC = 'capchasec';
   const PARAM_WISIWIG_EDITOR = 'weditor';


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
      $elemText->addFilter(new Form_Filter_HTMLPurify('p[style],span[style],a[href|title],strong,em,img[src|alt],br'));
      $form->addElement($elemText);

      $elemCaptcha = new Form_Element_Hidden('captcha');
      $elemCaptcha->addValidation(new Form_Validator_NotEmpty());
      $elemCaptcha->setValues(0);
      $form->addElement($elemCaptcha);

      $elemSubmit = new Form_Element_Submit('send', $this->_('Odeslat'));
      $form->addElement($elemSubmit);

      $capchaTime = $this->category()->getParam(self::PARAM_CAPCHA_SEC, self::MIN_SEC_FOR_HUMAN);
      // u přihlášených vypneme chapchu
      if(Auth::isLogin()){
         $form->nick->setValues(Auth::getUserName());
         $form->email->setValues(Auth::getUserMail());
         $capchaTime = 0;
      }

      if($form->isSend()){
         $this->view()->showFrom = true;
         if($form->captcha->getValues() < $capchaTime){
            $elemCaptcha->setError($this->_('Příliš rychlé odeslání příspěvku, pravděpodobně SPAM!'));
            $this->log('Guestbook SPAM from IP: '.$_SERVER['REMOTE_ADDR']);
         }
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
         $this->infoMsg()->addMessage($this->_('Příspěvek byl uložen'));
         $this->link()->param('s')->reload();
      }
      $this->view()->form = $form;

      if($this->rights()->isWritable()){
         $delForm = new Form('guestbook_item_');

         $elemId = new Form_Element_Hidden('id');
         $elemId->addValidation(new Form_Validator_NotEmpty());
         $elemId->addValidation(new Form_Validator_IsNumber());
         $delForm->addElement($elemId);

         $elemSubDel = new Form_Element_Submit('delete', $this->_('Smazat'));
         $delForm->addElement($elemSubDel);

         if($delForm->isValid()){
            $model->delete($delForm->id->getValues());
            $this->infoMsg()->addMessage($this->_('Položka byla smazána'));
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
      $this->view()->capchaTime = $capchaTime;
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

      $elemCapchaSec = new Form_Element_Text('capchatime', $this->tr('Po kolika sekundách lze formulář odeslat'));
      $elemCapchaSec->setSubLabel($this->tr(sprintf('Výchozí: %s sekund (obrana proti spamu). Pokud je 0, kontrola je vypnuta.', self::MIN_SEC_FOR_HUMAN)));
      $elemCapchaSec->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemCapchaSec, $fGrpNewItem);
      if(isset($settings[self::PARAM_CAPCHA_SEC])) {
         $form->capchatime->setValues($settings[self::PARAM_CAPCHA_SEC]);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scroll'] = $form->scroll->getValues();
         $settings['maxtextchars'] = $form->maxtextchars->getValues();
         $settings['mintextchars'] = $form->mintextchars->getValues();
         $settings[self::PARAM_WISIWIG_EDITOR] = $form->weditor->getValues();
         $settings[self::PARAM_CAPCHA_SEC] = (int)$form->capchatime->getValues();
      }
   }
}

?>