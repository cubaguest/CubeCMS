<?php

class Contact_Controller extends Controller {

   const PARAM_MAP = 'map';
   const PARAM_MAP_URL_PARAMS = 'mapurlparams';
   const PARAM_MAP_POINTS = 'mappoints';
   const PARAM_MAP_TYPE = 'maptype';
   const PARAM_MAP_TYPE_IMAGE = 'image';
   const PARAM_MAP_TYPE_IFRAME = 'iframe';
   const PARAM_FORM = 'form';
   const PARAM_FORM_SUBJECTS = 'formsub';
   const PARAM_FORM_DIS_OWN_SUBJECT = 'formownsubdis';
   const PARAM_FORM_SEPARATE_NAME = 'formsepname';
   
   const PARAM_ADMIN_RECIPIENTS = 'admin_rec';
   const PARAM_OTHER_RECIPIENTS = 'other_rec';
   const TEXT_KEY_FOOTER = 'footer';

   /**
    * Kontroler pro zobrazení novinek
    */
   public function mainController()
   {
      //		Kontrola práv
      $this->checkReadableRights();

      // načtení textů
      $modelText = new Text_Model();
      $text = $modelText->where(Text_Model::COLUMN_ID_CATEGORY . ' = :idc AND ' . Text_Model::COLUMN_SUBKEY . ' = :subkey', array('idc' => $this->category()->getId(), 'subkey' => Text_Model::TEXT_MAIN_KEY))
          ->record();
      if ($text != false) {
         $this->view()->text = $text->{Text_Model::COLUMN_TEXT};
      }

      $formQuestion = new Form('contact_question_');

      if($this->category()->getParam(self::PARAM_FORM_SEPARATE_NAME, false) == true){
         $elemName = new Form_Element_Text('name', $this->tr('Jméno'));
         $elemName->addValidation(new Form_Validator_NotEmpty());
         $formQuestion->addElement($elemName);
         $elemSurName = new Form_Element_Text('surname', $this->tr('Přijmení'));
         $elemSurName->addValidation(new Form_Validator_NotEmpty());
         $formQuestion->addElement($elemSurName);
      } else {
         $elemName = new Form_Element_Text('name', $this->tr('Jméno a přijmení'));
         $elemName->addValidation(new Form_Validator_NotEmpty());
         $formQuestion->addElement($elemName);
      }

      $elemMail = new Form_Element_Text('mail', $this->tr('Váš e-mail'));
      $elemMail->addValidation(new Form_Validator_NotEmpty());
      $elemMail->addValidation(new Form_Validator_Email());
      $formQuestion->addElement($elemMail);
      
      $elemPhone = new Form_Element_Text('phone', $this->tr('Telefon'));
      $formQuestion->addElement($elemPhone);

      $subs = array();
      if ($this->category()->getParam(self::PARAM_FORM_SUBJECTS, null) != null) {
         $elemSubjectDef = new Form_Element_Select('subjectDef', $this->tr('Předmět'));
         $subs = explode(';', $this->category()->getParam(self::PARAM_FORM_SUBJECTS));
         if(!$this->category()->getParam(self::PARAM_FORM_DIS_OWN_SUBJECT, false)){
            $elemSubjectDef->setOptions(array($this->tr('< Vlastní předmět >') => 0), true);
         }
         
         foreach ($subs as $key => $sub) {
            $elemSubjectDef->setOptions(array(preg_replace('/<.*>/', '', $sub) => $key + 1), true); // +1 protože první je vlastní
         }
         $elemSubjectDef->setValues($this->getRequestParam('s', 0));
         $formQuestion->addElement($elemSubjectDef);
      }

      $elemSubject = new Form_Element_Text('subject', $formQuestion->haveElement('subjectDef') ? $this->tr('Vlastní předmět') : $this->tr('Předmět'));
      if ($this->category()->getParam(self::PARAM_FORM_SUBJECTS, null) == null) {
         $elemSubject->addValidation(new Form_Validator_NotEmpty());
      }
      $formQuestion->addElement($elemSubject);


      $elemText = new Form_Element_TextArea('text', $this->tr('Zpráva'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $formQuestion->addElement($elemText);

      if (!Auth::isLogin()) {
         $elemCaptcha = new Form_Element_Captcha('captcha', $this->tr('Ověření'));
         $formQuestion->addElement($elemCaptcha);
      }

      $elemSubmit = new Form_Element_Submit('send', $this->tr('Odeslat'));
      $formQuestion->addElement($elemSubmit);

      if (Auth::isLogin()) { // pokud je uživatel přihlášen doplníme jeho jméno
         $modelUsers = new Model_Users();
         $user = $modelUsers->record(Auth::getUserId());
         if(isset($formQuestion->surname)){
            $formQuestion->name->setValues($user->{Model_Users::COLUMN_NAME});
            $formQuestion->surname->setValues($user->{Model_Users::COLUMN_SURNAME});
         } else {
            $formQuestion->name->setValues($user->{Model_Users::COLUMN_NAME} . ' ' . $user->{Model_Users::COLUMN_SURNAME});
         }
         $formQuestion->mail->setValues($user->{Model_Users::COLUMN_MAIL});
      }

      $f = self::getShortForm($this->translator());
      if ($f->isSend() && !$f->isValid()) {
         $formQuestion->name->setValues($f->name->getValues());
         $formQuestion->mail->setValues($f->mail->getValues());
         $formQuestion->text->setValues($f->text->getValues());
         $formQuestion->phone->setValues($f->phone->getValues());
      }

      if ($formQuestion->haveElement('subjectDef') AND $formQuestion->isSend()
          AND $formQuestion->subjectDef->getValues() == 0 AND $formQuestion->subject->getValues() == null) {
         $formQuestion->subject->setError($this->tr('Musí být zadán předmět zprávy.'));
      }

      if ($formQuestion->isValid()) {
         $this->processMessage($formQuestion);
         $this->infoMsg()->addMessage($this->tr('Váš dotaz byl úspěšně odeslán. Co nejdříve Vám odpovíme.'));
         $this->link()->reload();
      }
      $this->processShortForm();

      $this->view()->formQuestion = $formQuestion;
   }

   /**
    * 
    * @return \Form
    */
   public static function getShortForm(Translator $tr)
   {
      $f = new Form('conShortForm');

      $eName = new Form_Element_Text('name', $tr->tr('Jméno'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $f->addElement($eName);

      $eEmail = new Form_Element_Text('mail', $tr->tr('Email'));
      $eEmail->addValidation(new Form_Validator_NotEmpty());
      $eEmail->addValidation(new Form_Validator_Email());
      $f->addElement($eEmail);
      
      $ePhone = new Form_Element_Text('phone', $tr->tr('Telefon'));
      $f->addElement($ePhone);

      $eMsg = new Form_Element_TextArea('text', $tr->tr('Text zprávy'));
      $eMsg->addValidation(new Form_Validator_NotEmpty());
      $f->addElement($eMsg);

      $eSubmit = new Form_Element_Submit('send', $tr->tr('Odeslat'));
      $f->addElement($eSubmit);

      return $f;
   }

   protected function processShortForm()
   {
      $f = self::getShortForm($this->translator());
      if ($f->isValid()) {
         $this->processMessage($f);
         $this->infoMsg()->addMessage($this->tr('Váš dotaz byl úspěšně odeslán. Co nejdříve Vám odpovíme.'));
         $this->link()->reload();
      }
   }

   protected function processMessage(Form $formQuestion)
   {
      $model = new Contact_Model_Questions();
      $subject = $this->tr('Dotaz ze stránek ') . CUBE_CMS_WEB_NAME;
      if (isset($formQuestion->subject)) {
         $subject = $formQuestion->subject->getValues();
      }

      $model->saveQuestion(
          isset($formQuestion->surname) 
            ? $formQuestion->name->getValues().' '.$formQuestion->surname->getValues() 
            : $formQuestion->name->getValues(), 
          $formQuestion->mail->getValues(), $subject, 
          $formQuestion->text->getValues(), 
          $formQuestion->phone->getValues());

      $adminMails = array();


      if ($formQuestion->haveElement('subjectDef') AND (int) $formQuestion->subjectDef->getValues() > 0) {
         $subs = explode(';', $this->category()->getParam(self::PARAM_FORM_SUBJECTS));
         $subject = preg_replace('/<.*>/', '', $subs[(int) $formQuestion->subjectDef->getValues() - 1]);
         if ($formQuestion->subject->getValues() != null) {
            $subject .= ': ' . $formQuestion->subject->getValues();
         }

         //vytažení emailu, pokud je
         $matches = array();
         if (preg_match('/<(.*)>/', $subs[(int) $formQuestion->subjectDef->getValues() - 1], $matches) !== 0) {
            $adminMails[] = $matches[1];
         }
      }

      $html = '<p>Byl odeslán kontaktní formulář ze stránek ' . VVE_WEB_NAME . '.</p>';
      $html .= '<table class="styled">';
      
      if(isset($formQuestion->surname)){
         $html .= '<tr>';
         $html .= '<th>Jméno</th>';
         $html .= '<td>' . $formQuestion->name->getValues() . '</td>';
         $html .= '</tr>';
         $html .= '<tr>';
         $html .= '<th>Přijmení</th>';
         $html .= '<td>' . $formQuestion->surname->getValues() . '</td>';
         $html .= '</tr>';
      } else {
         $html .= '<tr>';
         $html .= '<th>Jméno a přijmení</th>';
         $html .= '<td>' . $formQuestion->name->getValues() . '</td>';
         $html .= '</tr>';
      }
         
      
      $html .= '<tr>';
      $html .= '<th>E-mail</th>';
      $html .= '<td>' . $formQuestion->mail->getValues() . '</td>';
      $html .= '</tr>';
      $html .= '<tr>';
      $html .= '<th>Telefon</th>';
      $html .= '<td>' . $formQuestion->phone->getValues() . '</td>';
      $html .= '</tr>';
      $html .= '<tr>';
      $html .= '<th>Předmět</th>';
      $html .= '<td>' . $subject . '</td>';
      $html .= '</tr>';
      $html .= '</table>';
      $html .= '<br />';
      $html .= '<h2>Text</h2>';
      $html .= '<p>' . $formQuestion->text->getValues() . '</p>';



      // create email
      $email = new Email(true);
      $email->setSubject($subject);
      $email->setContent(Email::getBaseHtmlMail($html));

      $email->setFrom(array($formQuestion->mail->getValues() => $formQuestion->name->getValues()));
      //$mail->addAddress($formQuestion->mail->getValues()); // odesílat na odesílatele?

      if (empty($adminMails)) { // pokud je prázdný výtahneme nasatvené maily
         // maily adminů - předané
         $str = $this->category()->getParam(self::PARAM_OTHER_RECIPIENTS, null);
         if ($str != null)
            $adminMails = explode(';', $str);
         // maily adminů - z uživatelů
         $usersId = $this->category()->getParam(self::PARAM_ADMIN_RECIPIENTS, array());
         $modelusers = new Model_Users();
         foreach ($usersId as $id) {
            $user = $modelusers->record($id);
            $adminMails = array_merge($adminMails, explode(';', $user->{Model_Users::COLUMN_MAIL}));
         }
      }
      $email->addAddress($adminMails);
      try {
         $email->send();
      } catch (Exception $e) {
         new CoreErrors($e);
      }
   }

   /**
    * controller pro úpravu kontaktu
    */
   public function editController()
   {
      $this->checkWritebleRights();
      $modelText = new Text_Model();

      $formEdit = new Form('contact_edit');

      $elemText = new Form_Element_TextArea('text', $this->tr('Text kontaktu'));
      $elemText->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      $elemText->setLangs();

      // naplníme pokud je čím
      $textRecord = $modelText->where(Text_Model::COLUMN_ID_CATEGORY . ' = :idc AND ' . Text_Model::COLUMN_SUBKEY . ' = :subkey', array('idc' => $this->category()->getId(), 'subkey' => Text_Model::TEXT_MAIN_KEY))
          ->record();
      if ($textRecord != false) {
         $elemText->setValues($textRecord->{Text_Model::COLUMN_TEXT});
      }
      $formEdit->addElement($elemText);

      $elemTextPanel = new Form_Element_TextArea('textPanel', $this->tr('Text panelu'));
      $elemTextPanel->setLangs();
      $elemTextPanel->setSubLabel($this->tr('Pokud není vyplněn, zkusí se použít první odstavec typu adresa z hlavního textu.'));
      // naplníme pokud je čím
      $textRecordPanel = $modelText->where(Text_Model::COLUMN_ID_CATEGORY . ' = :idc AND ' . Text_Model::COLUMN_SUBKEY . ' = :subkey', array('idc' => $this->category()->getId(), 'subkey' => Text_Model::TEXT_PANEL_KEY))
          ->record();
      if ($textRecordPanel != false) {
         $elemTextPanel->setValues($textRecordPanel->{Text_Model::COLUMN_TEXT});
      }
      $formEdit->addElement($elemTextPanel);

      $textRecordFooter = false;
      if (Face::getParamStatic('footer', 'contact', false)) {
         $elemTextFooter = new Form_Element_TextArea('textFooter', $this->tr('Text zápatí'));
         $elemTextFooter->setLangs();
         $elemTextFooter->setSubLabel($this->tr('Pokud není vyplněn, zkusí se použít první odstavec typu adresa z hlavního textu.'));
         // naplníme pokud je čím
         $textRecordFooter = $modelText->where(Text_Model::COLUMN_ID_CATEGORY . ' = :idc AND ' . Text_Model::COLUMN_SUBKEY . ' = :subkey', array('idc' => $this->category()->getId(), 'subkey' => self::TEXT_KEY_FOOTER))
             ->record();
         if ($textRecordFooter != false) {
            $elemTextFooter->setValues($textRecordFooter->{Text_Model::COLUMN_TEXT});
         }
         $formEdit->addElement($elemTextFooter);
      }

      $submitButton = new Form_Element_SaveCancel('send');
      $formEdit->addElement($submitButton);

      if ($formEdit->isSend() AND $formEdit->send->getValues() == false) {
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }

      if ($formEdit->isValid()) {
         $matches = array();
         $texts = $formEdit->text->getValues();
         $textsPanels = $formEdit->textPanel->getValues();

         $textsFooter = false;
         if (isset($formEdit->textFooter)) {
            $textsFooter = $formEdit->textFooter->getValues();
         }

         if ($textRecord == false) {
            $textRecord = $modelText->newRecord();
            $textRecord->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
            $textRecord->{Text_Model::COLUMN_SUBKEY} = Text_Model::TEXT_MAIN_KEY;
         }
         $textRecord->{Text_Model::COLUMN_TEXT} = $texts;
         $textRecord->{Text_Model::COLUMN_TEXT_CLEAR} = vve_strip_tags($texts);
         $textRecord->{Text_Model::COLUMN_ID_USER_EDIT} = Auth::getUserId();
         $modelText->save($textRecord);

         $textsPSave = array();
         foreach ($texts as $lang => $text) {
            $matches = array();
            if ($textsPanels[$lang] != null) { // pokud je vyplněn panel
               $textsPSave[$lang] = $textsPanels[$lang];
            } else if (preg_match('/<address>(.*?)<\/address>/', $text, $matches) == 1) {
               $textsPSave[$lang] = $matches[1];
            }
         }
         if (!empty($textsPSave)) {
            if ($textRecordPanel == false) {
               $textRecordPanel = $modelText->newRecord();
               $textRecordPanel->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
               $textRecordPanel->{Text_Model::COLUMN_SUBKEY} = Text_Model::TEXT_PANEL_KEY;
            }
            $textRecordPanel->{Text_Model::COLUMN_TEXT} = $textsPSave;
            $textRecordPanel->{Text_Model::COLUMN_ID_USER_EDIT} = Auth::getUserId();
            $textRecordPanel->{Text_Model::COLUMN_TEXT_CLEAR} = Utils_Html::stripTags($textsPSave);
            $modelText->save($textRecordPanel);
         }

         if ($textsFooter) {
            $textsFooterSave = array();
            foreach ($texts as $lang => $text) {
               $matches = array();
               if ($textsFooter[$lang] != null) { // pokud je vyplněn panel
                  $textsFooterSave[$lang] = $textsFooter[$lang];
               } else if (preg_match('/<address>(.*?)<\/address>/', $text, $matches) == 1) {
                  $textsFooterSave[$lang] = $matches[1];
               }
            }
            if (!empty($textsFooterSave)) {
               if ($textRecordFooter == false) {
                  $textRecordFooter = $modelText->newRecord();
                  $textRecordFooter->{Text_Model::COLUMN_ID_CATEGORY} = $this->category()->getId();
                  $textRecordFooter->{Text_Model::COLUMN_SUBKEY} = self::TEXT_KEY_FOOTER;
               }
               $textRecordFooter->{Text_Model::COLUMN_TEXT} = $textsFooterSave;
               $textRecordFooter->{Text_Model::COLUMN_ID_USER_EDIT} = Auth::getUserId();
               $textRecordFooter->{Text_Model::COLUMN_TEXT_CLEAR} = Utils_Html::stripTags($textsFooterSave);
               $modelText->save($textRecordFooter);
            }
         }

         $this->infoMsg()->addMessage($this->tr('Text kontaktu byl uložen'));
         $this->link()->route()->reload();
      }

      $this->view()->formEdit = $formEdit;
   }

   /**
    * Metoda pro nastavení modulu
    */
   public function settings(&$settings, Form &$form)
   {
      $grpMap = $form->addGroup('map', 'Nastavení mapy', 'Nastavení typu, souřadnic
         a klíče pro použití mapy z Google Maps API.Dokumentace:<a
         href="http://code.google.com/intl/cs-CZ/apis/maps/documentation/staticmaps/"
         title="Dokumentace k obrázkové mapě">Obrázek</a>');

      $elemGGMapEnabled = new Form_Element_Checkbox('ggMapEnabled', 'Zapnout mapu');
      $elemGGMapEnabled->setValues(true);
      $form->addElement($elemGGMapEnabled, $grpMap);
      if (isset($settings[self::PARAM_MAP])) {
         $form->ggMapEnabled->setValues($settings[self::PARAM_MAP]);
      }

      $elemGGMapUrlPar = new Form_Element_Text('ggMapUrlParams', 'Parametry url');
      $elemGGMapUrlPar->setSubLabel('Obrázek: Parametry v url pro danou mapu (zvětšení, střed mapy, ...). např: center=Valašské+Meziříčí&zoom=15<br />
         Vložený rám: URL adresa mapy pro sdílení pomocí odkazu (z Google Maps). ');
      $form->addElement($elemGGMapUrlPar, $grpMap);
      if (isset($settings[self::PARAM_MAP_URL_PARAMS])) {
         $form->ggMapUrlParams->setValues($settings[self::PARAM_MAP_URL_PARAMS]);
      }

      $elemGGMapPoints = new Form_Element_Text('ggMapPoints', 'Souřadnice');
      $elemGGMapPoints->setSubLabel('Obrázek: část url s markery. např: markers=color:red|label:H|49.471847,17.969363<br />
         U vloženého rámu není nutné, protože informace o bodech jsou přímo v adrese mapy.');
      $form->addElement($elemGGMapPoints, $grpMap);
      if (isset($settings[self::PARAM_MAP_POINTS])) {
         $form->ggMapPoints->setValues($settings[self::PARAM_MAP_POINTS]);
      }

      $elemGGMypType = new Form_Element_Select('ggMapType', 'Typ mapy');
      $elemGGMypType->setOptions(array('Obrázek' => self::PARAM_MAP_TYPE_IMAGE, 'Vložený rám' => self::PARAM_MAP_TYPE_IFRAME));
      $elemGGMypType->setValues(self::PARAM_MAP_TYPE_IMAGE);
      $form->addElement($elemGGMypType, $grpMap);
      if (isset($settings[self::PARAM_MAP_TYPE])) {
         $form->ggMapType->setValues($settings[self::PARAM_MAP_TYPE]);
      }

      $grpForm = $form->addGroup('form', 'Nastavení formuláře', 'Nastavení formuláře pro dotazy.');

      $elemFormEnabled = new Form_Element_Checkbox('formEnabled', 'Zapnutí formuláře');
      $elemFormEnabled->setValues(true);
      $form->addElement($elemFormEnabled, $grpForm);
      if (isset($settings[self::PARAM_FORM])) {
         $form->formEnabled->setValues($settings[self::PARAM_FORM]);
      }
      
      $elemFormSepName = new Form_Element_Checkbox('formSepName', 'Oddělené jméno a příjmení');
      $elemFormSepName->setValues(false);
      $form->addElement($elemFormSepName, $grpForm);
      if (isset($settings[self::PARAM_FORM_SEPARATE_NAME])) {
         $form->formSepName->setValues($settings[self::PARAM_FORM_SEPARATE_NAME]);
      }
      

      $elemFormSubjects = new Form_Element_TextArea('formSub', 'Předměty zpráv');
      $elemFormSubjects->setSubLabel(htmlspecialchars('Předdefinované předměty zprávy, odělené středníkem. Pokud je za předmětem email ve špičatých závorkách, je odeslán dotaz na uvedenou adresu, např. dotaz na zboží<jmeno@email.cz> .'));
      $elemFormSubjects->html()->setAttrib('cols', 50)->setAttrib('rows', 5);
      $form->addElement($elemFormSubjects, $grpForm);
      if (isset($settings[self::PARAM_FORM_SUBJECTS])) {
         $form->formSub->setValues($settings[self::PARAM_FORM_SUBJECTS]);
      }

      $elemFormAllowOwn = new Form_Element_Checkbox('formDisabelOwnSubject', 'Zakázat vlastní předmět');
      $elemFormAllowOwn->setValues(false);
      $form->addElement($elemFormAllowOwn, $grpForm);
      if (isset($settings[self::PARAM_FORM_DIS_OWN_SUBJECT])) {
         $form->formDisabelOwnSubject->setValues($settings[self::PARAM_FORM_DIS_OWN_SUBJECT]);
      }
      

      $grpAdmin = $form->addGroup('admins', 'Nastavení příjemců', 'Nastavení příjemců odeslaných dotazů z kontaktního formuláře');

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
      $users = $modelUsers->usersForThisWeb(true)->records(PDO::FETCH_OBJ);
      $usersIds = array();
      foreach ($users as $user) {
         if ($user->{Model_Users::COLUMN_MAIL} != null) {
            $usersIds[$user->{Model_Users::COLUMN_NAME} . " " . $user->{Model_Users::COLUMN_SURNAME}
                . ' (' . $user->{Model_Users::COLUMN_USERNAME} . ':' . $user->{Model_Users::COLUMN_GROUP_NAME}
                . ') - ' . $user->{Model_Users::COLUMN_MAIL}] = $user->{Model_Users::COLUMN_ID};
         }
      }
      $elemAdmins->setOptions($usersIds);
      $elemAdmins->setMultiple();
      $elemAdmins->html()->setAttrib('size', 4);
      if (isset($settings[self::PARAM_ADMIN_RECIPIENTS])) {
         $elemAdmins->setValues($settings[self::PARAM_ADMIN_RECIPIENTS]);
      }

      $form->addElement($elemAdmins, $grpAdmin);


      // znovu protože mohl být už jednou validován bez těchto hodnot
      if ($form->isValid()) {
         $settings[self::PARAM_MAP] = $form->ggMapEnabled->getValues();
         $settings[self::PARAM_MAP_URL_PARAMS] = $form->ggMapUrlParams->getValues();
         $settings[self::PARAM_MAP_POINTS] = $form->ggMapPoints->getValues();
         $settings[self::PARAM_MAP_TYPE] = $form->ggMapType->getValues();

         $settings[self::PARAM_FORM] = $form->formEnabled->getValues();
         $settings[self::PARAM_FORM_SUBJECTS] = $form->formSub->getValues();
         $settings[self::PARAM_FORM_DIS_OWN_SUBJECT] = $form->formDisabelOwnSubject->getValues();
         $settings[self::PARAM_FORM_SEPARATE_NAME] = $form->formSepName->getValues();

         $settings[self::PARAM_ADMIN_RECIPIENTS] = $form->admins->getValues();
         $settings[self::PARAM_OTHER_RECIPIENTS] = $form->otherRec->getValues();
      }
   }

}
