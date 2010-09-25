<?php
class Contact_Controller extends Controller {
   const PARAM_MAP = 'map';
   const PARAM_MAP_URL_PARAMS = 'mapurlparams';
   const PARAM_MAP_POINTS = 'mappoints';
   const PARAM_MAP_TYPE = 'maptype';
   const PARAM_MAP_TYPE_IMAGE = 'image';
   const PARAM_MAP_TYPE_IFRAME = 'iframe';
   const PARAM_FORM = 'form';
   const PARAM_ADMIN_RECIPIENTS = 'admin_rec';
   const PARAM_OTHER_RECIPIENTS = 'other_rec';

   const TEXT_KEY_MAIN = 'main';
   const TEXT_KEY_PANEL = 'panel';

   /**
   * Kontroler pro zobrazení novinek
   */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      // načtení textů
      $modelText = new Text_Model();
      $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_MAIN);
      if($text != false){
         $this->view()->text = $text->{Text_Model::COLUMN_TEXT};
      }

      $formQuestion = new Form('contact_question_');

      $elemName = new Form_Element_Text('name', $this->_('Jméno a přijmení'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $formQuestion->addElement($elemName);

      $elemMail = new Form_Element_Text('mail', $this->_('Váš e-mail'));
      $elemMail->addValidation(new Form_Validator_NotEmpty());
      $elemMail->addValidation(new Form_Validator_Email());
      $formQuestion->addElement($elemMail);

      $elemSubject = new Form_Element_Text('subject', $this->_('Předmět'));
      $elemSubject->addValidation(new Form_Validator_NotEmpty());
      $formQuestion->addElement($elemSubject);

      $elemText = new Form_Element_TextArea('text', $this->_('Text'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $formQuestion->addElement($elemText);

      $elemSubmit = new Form_Element_Submit('send', $this->_('Odeslat'));
      $formQuestion->addElement($elemSubmit);

      if($formQuestion->isValid()){
         $model = new Contact_Model_Questions();
         $model->saveQuestion($formQuestion->name->getValues(), $formQuestion->mail->getValues(), 
             $formQuestion->subject->getValues(), $formQuestion->text->getValues());

         // odeslání emailu
         $mail = new Email();
         $mail->setSubject($formQuestion->subject->getValues());
         $mail->setContent($formQuestion->text->getValues());
         $mail->addAddress($formQuestion->mail->getValues());
         // maily adminů
         $str = $this->category()->getParam(self::PARAM_OTHER_RECIPIENTS, null);
         $mails = array();
         if($str != null) $mails = explode(';', $str);
         $usersId = $this->category()->getParam(self::PARAM_ADMIN_RECIPIENTS, array());

         $modelusers = new Model_Users();

         foreach ($usersId as $id) {
            $user = $modelusers->getUserById($id);
            $mails = array_merge($mails, explode(';', $user->{Model_Users::COLUMN_MAIL}));
         }

         $mail->addAddress($mails);
         $mail->sendMail();

         $this->infoMsg()->addMessage($this->_('Váš dotaz byl úspěšně odeslán. Co nejdříve Vám odpovíme.'));
         $this->link()->reload();
      }

      $this->view()->formQuestion = $formQuestion;
   }

  /**
   * controller pro úpravu kontaktu
   */
   public function editController() {
      $this->checkWritebleRights();
      $modelText = new Text_Model();

      $formEdit = new Form('contact_edit');

      $elemText = new Form_Element_TextArea('text', $this->_('Text kontaktu'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->setLangs();

      // naplníme pokud je čím
      $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_MAIN);
      if($text != false){
         $elemText->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $formEdit->addElement($elemText);

      $elemTextPanel = new Form_Element_TextArea('textPanel', $this->_('Text panelu'));
      $elemTextPanel->setLangs();
      $elemTextPanel->setSubLabel($this->_('Pokud není vyplněn, zkusí se použít první odstavec typu adresa z hlavního textu.'));
      // naplníme pokud je čím
      $text = $modelText->getText($this->category()->getId(), self::TEXT_KEY_PANEL);
      if($text != false){
         $elemTextPanel->setValues($text->{Text_Model_Detail::COLUMN_TEXT});
      }
      $formEdit->addElement($elemTextPanel);

      $submitButton = new Form_Element_SaveCancel('send');
      $formEdit->addElement($submitButton);

      if($formEdit->isSend() AND $formEdit->send->getValues() == false){
         $this->infoMsg()->addMessage($this->_('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if($formEdit->isValid()){
         $modelText->saveText($formEdit->text->getValues(), null, $this->category()->getId(), self::TEXT_KEY_MAIN);
         $matches = array();
            $texts = $formEdit->text->getValues();
            $textsPanels = $formEdit->textPanel->getValues();
            $textsPSave = array();
            foreach ($texts as $lang => $text) {
               $matches = array();
               if($textsPanels[$lang] != null){ // pokud je vyplněn panel
                  $textsPSave[$lang] = $textsPanels[$lang];
               } else if(preg_match('/<address>(.*?)<\/address>/',$text, $matches) == 1){
                  $textsPSave[$lang] = $matches[1];
               }
            }
            if(!empty ($textsPSave)){
               $modelText->saveText($textsPSave, null, $this->category()->getId(), self::TEXT_KEY_PANEL);
            }
         $this->infoMsg()->addMessage($this->_('Text kontaktu byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->formEdit = $formEdit;
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings,Form &$form) {
      $grpMap = $form->addGroup('map', 'Nastavení mapy', 'Nastavení typu, souřadnic
         a klíče pro použití mapy z Google Maps API.Dokumentace:<a
         href="http://code.google.com/intl/cs-CZ/apis/maps/documentation/staticmaps/"
         title="Dokumentace k obrázkové mapě">Obrázek</a>');

      $elemGGMapEnabled = new Form_Element_Checkbox('ggMapEnabled', 'Zapnout mapu');
      $elemGGMapEnabled->setValues(true);
      $form->addElement($elemGGMapEnabled,$grpMap);
      if(isset($settings[self::PARAM_MAP])) {
         $form->ggMapEnabled->setValues($settings[self::PARAM_MAP]);
      }

      $elemGGMapUrlPar = new Form_Element_Text('ggMapUrlParams', 'Parametry url');
      $elemGGMapUrlPar->setSubLabel('Obrázek: Parametry v url pro danou mapu (zvětšení, střed mapy, ...). např: center=Valašské+Meziříčí&zoom=15<br />
         Vložený rám: URL adresa vnitřku rámu, tedy vše za "http://maps.google.com/maps?q=" (vytažená z Google Maps). ');
      $form->addElement($elemGGMapUrlPar,$grpMap);
      if(isset($settings[self::PARAM_MAP_URL_PARAMS])) {
         $form->ggMapUrlParams->setValues($settings[self::PARAM_MAP_URL_PARAMS]);
      }
      
      $elemGGMapPoints = new Form_Element_Text('ggMapPoints', 'Souřadnice');
      $elemGGMapPoints->setSubLabel('Obrázek: část url s markery. např: markers=color:red|label:H|49.471847,17.969363<br />
         U vloženého rámu není nutné, protože informace o bodech jsou přímo v adrese mapy.');
      $form->addElement($elemGGMapPoints,$grpMap);
      if(isset($settings[self::PARAM_MAP_POINTS])) {
         $form->ggMapPoints->setValues($settings[self::PARAM_MAP_POINTS]);
      }

      $elemGGMypType = new Form_Element_Select('ggMapType', 'Typ mapy');
      $elemGGMypType->setOptions(array('Obrázek'=>  self::PARAM_MAP_TYPE_IMAGE, 'Vložený rám'=>  self::PARAM_MAP_TYPE_IFRAME));
      $elemGGMypType->setValues(self::PARAM_MAP_TYPE_IMAGE);
      $form->addElement($elemGGMypType, $grpMap);
      if(isset($settings[self::PARAM_MAP_TYPE])) {
         $form->ggMapType->setValues($settings[self::PARAM_MAP_TYPE]);
      }

      $grpForm = $form->addGroup('form', 'Nastavení formuláře', 'Nastavení formuláře pro dotazy.');

      $elemFormEnabled = new Form_Element_Checkbox('formEnabled', 'Zapnutí formuláře');
      $elemFormEnabled->setValues(true);
      $form->addElement($elemFormEnabled, $grpForm);
      if(isset($settings[self::PARAM_FORM])) {
         $form->formEnabled->setValues($settings[self::PARAM_FORM]);
      }

      $grpAdmin = $form->addGroup('admins', 'Nastavení příjemců',
              'Nastavení příjemců odeslaných dotazů z kontaktního formuláře');

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
      $modelUsers = new Model_Users();
      $users = $modelUsers->getUsersList();
      $usersIds = array();
      foreach ($users as $user) {
         $usersIds[$user[Model_Users::COLUMN_USERNAME]] = $user[Model_Users::COLUMN_ID];
      }
      $elemAdmins->setOptions($usersIds);
      $elemAdmins->setMultiple();
      $elemAdmins->html()->setAttrib('size', 4);
      if (isset($settings[self::PARAM_ADMIN_RECIPIENTS])) {
         $elemAdmins->setValues($settings[self::PARAM_ADMIN_RECIPIENTS]);
      }

      $form->addElement($elemAdmins, $grpAdmin);


      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings[self::PARAM_MAP] = $form->ggMapEnabled->getValues();
         $settings[self::PARAM_MAP_URL_PARAMS] = $form->ggMapUrlParams->getValues();
         $settings[self::PARAM_MAP_POINTS] = $form->ggMapPoints->getValues();
         $settings[self::PARAM_MAP_TYPE] = $form->ggMapType->getValues();

         $settings[self::PARAM_FORM] = $form->formEnabled->getValues();

         $settings[self::PARAM_ADMIN_RECIPIENTS] = $form->admins->getValues();
         $settings[self::PARAM_OTHER_RECIPIENTS] = $form->otherRec->getValues();
      }
   }
}
?>