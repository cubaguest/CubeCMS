<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class AdminEnviroment_Controller extends Controller {

   protected function init()
   {
      $this->checkControllRights();
   }
   
   public function mainController()
   {
      $form = new Form('enviroment');
      
      $grpNames = $form->addGroup('labels', $this->tr('Jména a popisky'));
      $grpEmails = $form->addGroup('emails', $this->tr('E-maily'));

      // Základní jména webu a podobně
      $eBaseName = new Form_Element_Text('webname', $this->tr('Název stránek'));
      $eBaseName->addValidation(new Form_Validator_NotEmpty());
      $eBaseName->addFilter(new Form_Filter_StripTags());
      $eBaseName->setValues(CUBE_CMS_WEB_NAME);
      $form->addElement($eBaseName, $grpNames);
      
      $eHPName = new Form_Element_Text('webhpname', $this->tr('Název titulní stránky'));
      $eHPName->addValidation(new Form_Validator_NotEmpty());
      $eHPName->addFilter(new Form_Filter_StripTags());
      $eHPName->setValues(CUBE_CMS_MAIN_PAGE_TITLE);
      $form->addElement($eHPName, $grpNames);
      
      $eCopy = new Form_Element_TextArea('webcopyright', $this->tr('Text v zápatí'));
      $eCopy->setValues(CUBE_CMS_WEB_COPYRIGHT);
      $eCopy->setSubLabel($this->tr('Řetězec "{Y}" je v zápatí nahrazen aktuálním rokem.'));
      $form->addElement($eCopy, $grpNames);
      
      $eLangs = new Form_Element_Select('langs', $this->tr('Zapnuté jazykové mutace'));
      $eLangPrimary = new Form_Element_Select('langprimary', $this->tr('Výchozí jazyk'));
      $eLangs->setMultiple(true);
      foreach (Locales::getSupportedLangs() as $code => $name) {
         $eLangs->addOption($name, $code);
         $eLangPrimary->addOption($name, $code);
      }
      $enabledLangs = explode(';', CUBE_CMS_APP_LANGS);
      $eLangs->setValues($enabledLangs);
      $form->addElement($eLangs, $grpNames);
      
      $eLangPrimary->setValues(CUBE_CMS_DEFAULT_APP_LANG);
      $form->addElement($eLangPrimary, $grpNames);
      
      
      // Emailové adresy pro odesílání
      $eNoReplayEmail = new Form_Element_Text('emailnoreplay', $this->tr('Adresa odesílaných emailů'));
      $eNoReplayEmail->setSubLabel($this->tr('Je využita v resgistrací, upozorněních a podobně. Typycky se používá adresa noreply@nazevdomeny.cz'));
      $eNoReplayEmail->addValidation(new Form_Validator_Email());
      $eNoReplayEmail->setValues(CUBE_CMS_NOREPLAY_MAIL);
      $form->addElement($eNoReplayEmail, $grpEmails);
      
      $eEmailSMTPServer = new Form_Element_Text('emailsmtpserver', $this->tr('Adresa SMTP serveru'));
      $eEmailSMTPServer->setAdvanced(true);
      $eEmailSMTPServer->setValues(CUBE_CMS_SMTP_SERVER == null ? 'localhost' : CUBE_CMS_SMTP_SERVER);
      $form->addElement($eEmailSMTPServer, $grpEmails);
      
      $eEmailSMTPServerPort = new Form_Element_Text('emailsmtpserverport', $this->tr('Port SMTP serveru'));
      $eEmailSMTPServerPort->setAdvanced(true);
      $eEmailSMTPServerPort->setValues(CUBE_CMS_SMTP_SERVER_PORT == null ? 25 : CUBE_CMS_SMTP_SERVER_PORT);
      $form->addElement($eEmailSMTPServerPort, $grpEmails);
      
      $eEmailSMTPUser = new Form_Element_Text('emailsmtpuser', $this->tr('Uživatel SMTP serveru'));
      $eEmailSMTPUser->setAdvanced(true);
      $eEmailSMTPUser->setValues(CUBE_CMS_SMTP_SERVER_USERNAME);
      $form->addElement($eEmailSMTPUser, $grpEmails);
      
      $eEmailSMTPPassowrd = new Form_Element_Text('emailsmtppassword', $this->tr('Heslo uživatele SMTP serveru'));
      $eEmailSMTPPassowrd->setAdvanced(true);
      $eEmailSMTPPassowrd->setValues(CUBE_CMS_SMTP_SERVER_PASSWORD);
      $form->addElement($eEmailSMTPPassowrd, $grpEmails);
      
      $eEmailSMTPConn = new Form_Element_Select('emailsmtpconn', $this->tr('Šifrování připojení'));
      $eEmailSMTPConn->setOptions(array(
          $this->tr('žádné') => null,
          $this->tr('SSL') => 'ssl',
          $this->tr('TLS') => 'tls',
      ));
      $eEmailSMTPConn->setSubLabel($this->tr('Toto nasatvení je propojeno s nasatvením portu. Pokud si nevíte rady, kontaktujte webmastera, nebo nechte prázdné.'));
      $eEmailSMTPConn->setAdvanced(true);
      $eEmailSMTPConn->setValues(CUBE_CMS_SMTP_SERVER_ENCRYPT);
      $form->addElement($eEmailSMTPConn, $grpEmails);
      
      
      if(function_exists('getFaceEnviromentItems')){
         $items = getFaceEnviromentItems();
         
         if(!empty($items)){
            $grpFace = $form->addGroup('faces', $this->tr('Nastavení vzhledu'));
            foreach ($items as $item) {
               $form->addElement($item, $grpFace);
            }
         }
      }
      
      
      $eSave = new Form_Element_Submit('save', $this->tr('Uložit'));
      $form->addElement($eSave);
      
      if($form->isValid()){
         Model_Config::setValue('WEB_NAME', $form->webname->getValues(), Model_Config::TYPE_STRING);
         Model_Config::setValue('MAIN_PAGE_TITLE', $form->webhpname->getValues(), Model_Config::TYPE_STRING);
         Model_Config::setValue('WEB_COPYRIGHT', $form->webcopyright->getValues(), Model_Config::TYPE_STRING);
         
         Model_Config::setValue('APP_LANGS', $form->langs->getValues(), Model_Config::TYPE_LIST_MULTI);
         Model_Config::setValue('DEFAULT_APP_LANG', $form->langprimary->getValues(), Model_Config::TYPE_LIST);
         
         Model_Config::setValue('NOREPLAY_MAIL', $form->emailnoreplay->getValues(), Model_Config::TYPE_STRING);
         Model_Config::setValue('SMTP_SERVER', $form->emailsmtpserver->getValues(), Model_Config::TYPE_STRING);
         Model_Config::setValue('SMTP_SERVER_PORT', $form->emailsmtpserverport->getValues(), Model_Config::TYPE_NUMBER);
         Model_Config::setValue('SMTP_SERVER_USERNAME', $form->emailsmtpuser->getValues(), Model_Config::TYPE_STRING);
         Model_Config::setValue('SMTP_SERVER_PASSWORD', $form->emailsmtppassword->getValues(), Model_Config::TYPE_STRING);
         Model_Config::setValue('SMTP_SERVER_ENCRYPT', $form->emailsmtpconn->getValues(), Model_Config::TYPE_STRING);
         
         
         
         
         if(function_exists('processFaceEnviroment')){
            processFaceEnviroment($form);
         }
         
         
         $this->infoMsg()->addMessage($this->tr('Nastavení bylo uloženo'));
         $this->link()->redirect();
      }
      
      
      $this->view()->form = $form;
   }
   
   
}
