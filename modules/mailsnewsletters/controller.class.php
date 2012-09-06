<?php

/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */
class MailsNewsletters_Controller extends Controller {
   const TEMPLATE_NAME = 'template.html';
   
   protected function init()
   {
      $this->checkControllRights();
      $this->module()->setDataDir('newsletters');
      
      if(!is_dir($this->module()->getDataDir())){
         @mkdir($this->module()->getDataDir());
      }
   }
   
   public function mainController()
   {
      $this->composeController();
   }
   
   public function composeController()
   {
      $form = new Form('newsletter_compose_');
      
      $grpCnt = $form->addGroup('contentgrp', $this->tr('Obsah newsleteru'));
      
      $eName = new Form_Element_Text('name', $this->tr('Název'));
      $eName->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($eName, $grpCnt);
      
      $eCnt = new Form_Element_TextArea('content', $this->tr('Obsah'));
      $eCnt->addValidation(new Form_Validator_NotEmpty());
      $form->addElement($eCnt, $grpCnt);
      
      $grpSend = $form->addGroup('sendgrp', $this->tr('Parametry odeslání'));
      
      $eActive = new Form_Element_Checkbox('active', $this->tr('Aktivní'));
      $form->addElement($eActive, $grpSend);
      
      $eSendDate = new Form_Element_Text('senddate', $this->tr('Datum odeslání'));
      $date = new DateTime();
      $eSendDate->setValues(vve_date('%x', $date->modify('+1 day')));
      $eSendDate->addValidation(new Form_Validator_NotEmpty());
      $eSendDate->addValidation(new Form_Validator_Date());
      $eSendDate->addFilter(new Form_Filter_DateTimeObj());
      $form->addElement($eSendDate, $grpSend);

      $eGroups = new Form_Element_Select('groups', $this->tr('Skupiny příjemců'));
      $eGroups->setMultiple(true);
      $modelGrp = new MailsAddressBook_Model_Groups();
      $grps = $modelGrp->records();
      foreach ($grps as $g) {
         $eGroups->setOptions(array($g->{MailsAddressBook_Model_Groups::COLUMN_NAME} => $g->{MailsAddressBook_Model_Groups::COLUMN_ID}), true);
      }
      $form->addElement($eGroups, $grpSend);


      $eTestMail = new Form_Element_Text('sendtestmail', $this->tr('Testovací e-mail'));
      $eTestMail->setValues(Auth::getUserMail());
      $form->addElement($eTestMail, $grpSend);

      $eButtons = new Form_Element_Multi_Submit('send');
      $eButtons->addElement(new Form_Element_Submit('cancel', $this->tr('Zrušit')));
      $eButtons->addElement(new Form_Element_Submit('save', $this->tr('Uložit')));
      $eButtons->addElement(new Form_Element_Submit('sendtest', $this->tr('Odeslat testovací')), 'sendtest'  );
      $form->addElement($eButtons);

      if($form->isSend()){
         $button = $form->send->getValues();
         if( $button == 'sendtest' ){
            $form->sendtestmail->addValidation(new Form_Validator_NotEmpty());
            $form->sendtestmail->addValidation(new Form_Validator_Email());
         }
      }

      if($form->isValid()){
         $button = $form->send->getValues();
          
         if($button == 'sendtest'){
            $this->sendTest($form->sendtestmail->getValues(), $form->name->getValues(), $form->content->getValues());
         } else if($button == 'save') {
            // save            
            $modelNewsletter = new MailsNewsletters_Model_Newsletter();
            
            if(true){ // nový
               $record = $modelNewsletter->newRecord();
            }
            
            $record->{MailsNewsletters_Model_Newsletter::COLUMN_ACTIVE} = $form->active->getValues();
            $record->{MailsNewsletters_Model_Newsletter::COLUMN_SUBJECT} = $form->name->getValues();
            $record->{MailsNewsletters_Model_Newsletter::COLUMN_CONTENT} = $form->content->getValues();
            $record->{MailsNewsletters_Model_Newsletter::COLUMN_DATE_SEND} = $form->senddate->getValues();
            $record->{MailsNewsletters_Model_Newsletter::COLUMN_GROUPS_IDS} = serialize($form->groups->getValues());
            $record->{MailsNewsletters_Model_Newsletter::COLUMN_ID_USER} = Auth::getUserId();
            
            $record->save();
            
            $this->infoMsg()->addMessage($this->tr('Newsletter byl uložen'));
            $this->link()->route('list')->reload();
//             $this->link()->reload();
         } else {
            $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
            $this->link(true)->reload;
         } 
            
      }
      
      $this->view()->form = $form;
   }

   public function listController() 
   {
//       $this->AutoRunDaily();
   }
   
   public function tplsController() 
   {
      $this->view()->export = $this->getRequestParam('export');
      $model = new MailsNewsletters_Model_Templates();
      
      $formDelete = new Form('form_delete_');
      $eId = new Form_Element_Hidden('id');
      $formDelete->addElement($eId);
      
      $eDelete = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $formDelete->addElement($eDelete);
      
      if($formDelete->isValid()){
         $tpl = $model->record($formDelete->id->getValues());
         $tpl->{MailsNewsletters_Model_Templates::COLUMN_DELETED} = true;
         $tpl->save();
         
         $this->infoMsg()->addMessage($this->tr('Šablona byla smazána'));
         $this->link()->reload();
      }
      $this->view()->formDelete = $formDelete;
      
      $this->view()->templates = $model->records(PDO::FETCH_OBJ);
      
   }
   
   protected function getTemplateDir($id, $onlyName = false) 
   {
      
      return $onlyName ? 'template-'.$id : $this->category()->getModule()->getDataDir().'template-'.$id.DIRECTORY_SEPARATOR;
   } 
   
   public function tplAddController() 
   {
      $model = new MailsNewsletters_Model_Templates();

      $tempTplName = "newsletter-".Auth::getUserId()."-tmp";
      
      $form = $this->createTplEditForm();
      $dir = $this->module()->getDataDir().$tempTplName;
      if(!is_dir($dir)){
         @mkdir($dir, 0777, true);
      }
      
      $this->view()->dataDir = "/newsletters/";
      $this->view()->newsletterDataDir = "/newsletters/".$tempTplName;
      
      
      if($form->isValid()){
         $model = new MailsNewsletters_Model_Templates();
         // vytvoření nové šablony v db
         $record = $model->newRecord();
         $record->{MailsNewsletters_Model_Templates::COLUMN_NAME} = $form->name->getValues();
         $id = $record->save();
         
         // přejmenování adresáře na nový podle id šablony
         $newDirName = $this->getTemplateDir($id, true);
         $newDir = $this->getTemplateDir($id);
         if(!rename($dir, $newDir)){
            throw new UnexpectedValueException($this->tr('Složku šablony nelze vytvořit. Kontaktujte webmastera.'));
         }
         
         // přejmenování všech cest v šabloně na nový adresář
         $cnt = $form->content->getValues();
         $cnt = str_replace('/'.$tempTplName."/", "/".$newDirName."/", $cnt);
         // uložení html šablony do adresáře
         file_put_contents($newDir.DIRECTORY_SEPARATOR.self::TEMPLATE_NAME, $cnt);
         
         $this->infoMsg()->addMessage($this->tr('Šablona byla uložena'));
         $this->link()->route('tpls')->reload();
      }
      
      $this->view()->form = $form;
   }
   
   public function tplEditController() 
   {
      $model = new MailsNewsletters_Model_Templates();
      $tpl = $model->record($this->getRequest('id', 0));
      
      if(!$tpl){
         return false;
      }
      
      $form = $this->createTplEditForm($tpl);
      
      $this->view()->dataDir = "/newsletters/";
      $this->view()->newsletterDataDir = "/newsletters/".$this->getTemplateDir($tpl->{MailsNewsletters_Model_Templates::COLUMN_ID}, true);
      
      
      if($form->isValid()){
         $model = new MailsNewsletters_Model_Templates();
         // vytvoření nové šablony v db
         $tpl->{MailsNewsletters_Model_Templates::COLUMN_NAME} = $form->name->getValues();
         $tpl->save();
         
         // uložení html šablony do adresáře
         file_put_contents($this->getTemplateDir($tpl->{MailsNewsletters_Model_Templates::COLUMN_ID})
            .self::TEMPLATE_NAME, $form->content->getValues());
         
         $this->infoMsg()->addMessage($this->tr('Šablona byla uložena'));
         $this->link()->route('tpls')->reload();
      }
      $this->view()->template = $tpl;
      $this->view()->form = $form;
   }
   
   private function createTplEditForm(Model_ORM_Record $tpl = null) 
   {
      $f = new Form('tpl_create_');
      
      $ename = new Form_Element_Text('name', $this->tr('Název'));
      $ename->addValidation(new Form_Validator_NotEmpty());
//       $ename->addFilter(new Form_Filter_HTMLSpecialChars());
      $f->addElement($ename);
      
      $eCnt = new Form_Element_TextArea('content', $this->tr('Šablona'));
      $eCnt->addValidation(new Form_Validator_NotEmpty());
      $f->addElement($eCnt);
      
      $eS = new Form_Element_SaveCancel('save');
      $f->addElement($eS);
      
      if($f->isSend() && $f->save->getValues() == false){
         $this->link()->route('tpls')->reload();
      }
      
      if($tpl != null){
         $f->name->setValues($tpl->{MailsNewsletters_Model_Templates::COLUMN_NAME});
         $f->content->setValues(file_get_contents($this->getTemplateDir($tpl->{MailsNewsletters_Model_Templates::COLUMN_ID}).self::TEMPLATE_NAME));
      }
      
      return $f;
   }
   
   public function tplPreviewController() 
   {
      $model = new MailsNewsletters_Model_Templates();
      $tpl = $model->record($this->getRequest('id', 0));
      
      if(!$tpl){
         return false;
      }
      
      $this->view()->template = file_get_contents($this->getTemplateDir($tpl->{MailsNewsletters_Model_Templates::COLUMN_ID}).self::TEMPLATE_NAME);
//       echo $this->view()->template; 
   }
   
   public function replacementsController() 
   {
      $this->view()->variables = array(
            '{WEB_NAME}' => $this->tr('Název stránek'),
            '{WEB_LINK}' => $this->tr('Odkaz na stránky'),
            '{UNSCRIBE}' => $this->tr('Odkaz pro odhlášení odběru'),
            '{NAME}' => $this->tr('Jméno příjemce'),
            '{MAIL}' => $this->tr('Odesílaný e-mail'),
      );
//       echo json_encode($this->view()->template()->getTemplateVars());
   } 
   
   public function tplUploadController() 
   {
      $formUpload = new Form('tpl_upload_');

      $fgrpB = $formUpload->addGroup('base', $this->tr('Základní informace'));
      
      $ename = new Form_Element_Text('name', $this->tr('Název'));
      $ename->addValidation(new Form_Validator_NotEmpty());
//       $ename->addFilter(new Form_Filter_HTMLSpecialChars());
      $formUpload->addElement($ename, $fgrpB);
      
      $fgrpF = $formUpload->addGroup('base', $this->tr('Základní informace'));
      $eFiles = new Form_Element_File('files', $this->tr('Soubor'));
      $eFiles->addValidation(new Form_Validator_NotEmpty());
      $eFiles->addValidation(new Form_Validator_FileExtension(Form_Validator_FileExtension::WEB|Form_Validator_FileExtension::IMG));
      $eFiles->setDimensional();
      $formUpload->addElement($eFiles, $fgrpF);
      
      $eS = new Form_Element_SaveCancel('save');
      $formUpload->addElement($eS);
      
      if($formUpload->isSend() && $formUpload->save->getValues() == false){
         $this->link()->route('tpls')->reload();
      }
      
      if($formUpload->isValid()){
         $files = $formUpload->files->getValues();
         $model = new MailsNewsletters_Model_Templates();
//          Debug::log($files);
         
         // vatvoření db záznamu a uložení
         $record = $model->newRecord();
         $record->{MailsNewsletters_Model_Templates::COLUMN_NAME} = $formUpload->name->getValues();
         $id = $record->save();
         
         // vytvoření adresáře podle id
         $dir = $this->getTemplateDir($id);
         if(!mkdir($dir, 0777, true)){
            throw UnexpectedValueException($this->tr('Šablonu nelze uložit. Kontaktujte webmastera'));
         }
         
         // nahrání povolených souborů do adresáře
         $htmlFile = null;
         foreach ($files as $file) {
            try {
               $fObj = new File($file);               
               if($file['extension'] == 'html'){
                  // načtení obsahu html souboru
                  $rawHtml = file_get_contents($fObj);
                  $rawHtml = mb_convert_encoding($rawHtml, 'utf-8', 
                        mb_detect_encoding($rawHtml, mb_list_encodings() ));
               
                  // uložení html souboru na povolený název šablony
                  file_put_contents($dir.self::TEMPLATE_NAME, $rawHtml);

               } elseif(in_array($file['extension'], array('jpg', 'jpeg', 'gif', 'png'))) {
                  $fObj->move($dir);
               }
            } catch (Exception $e) {
               new CoreErrors($e);
               $this->log($e->getTraceAsString());
            }
         }
         $this->infoMsg()->addMessage($this->tr('Šablona byla nahrána'));
//          $this->link()->route('tpls')->reload();
      }
      
      $this->view()->form = $formUpload;
   }
   
   public function sendTest($recipient, $subject, $content)
   {
      $um = new Model_Users();
      $user = $um->record(Auth::getUserId());

      self::sendMails($subject, $content, array( array(
               'mail' => $recipient, 
               'name' => $user->{Model_Users::COLUMN_NAME}." ".$user->{Model_Users::COLUMN_SURNAME}) 
      ));
      $this->infoMsg()->addMessage($this->tr('Testovací newsletter byl odeslán.'), false);
   }
   
   /**
    * Metoda vytvoí objek e-mailu
    * @param string $cnt
    * @param string $subject
    * @param string $mails
    */
   protected static function sendMails($subject, $cnt, $mails) 
   {
      $tr = new Translator_Module('mailsnewsletters');
      $mailObj = new Email(true);
      $mailObj->setSubject($subject);
      $mailObj->setContent('<html><body>' .$cnt .'</body></html>');
         
      $decorators = array();
      foreach ($mails as $mail) {
         $unscribeLinkObj = new Url_Link_ModuleStatic();
         $unscribeLink = (string)$unscribeLinkObj->module('mailsnewsletters')->action('unscribe')
            ->param('mail', $mail['mail']);
            
         $decorators[ $mail['mail'] ] = array(
               '{WEB_LINK}' => '<a href="'.Url_Request::getBaseWebDir().'" title="{WEB_NAME}">{WEB_NAME}</a>',
               '{UNSCRIBE}' => '<a href="{UNSCRIBE_LINK}">'.$tr->tr('Odhlášení odběru').'</a>',
               // base
               '{WEB_NAME}' => VVE_WEB_NAME,
               '{NAME}' => $mail['name'],
               '{MAIL}' => $mail['mail'],
               '{UNSCRIBE_LINK}' => $unscribeLink,
               );
         $mailObj->addAddress($mail['mail'], $mail['name']);
      }
         
      $mailObj->setRecipientReplacements($decorators);
      $failures = array();
      $mailObj->batchSend($failures);
   }
   
   /**
    * Kontroler pro odhlášení newsletteru
    */
   public static function unscribeController() 
   {
      $tr = new Translator_Module('mailsnewsletter');
      $data = new Object();
      if(isset($_GET['mail'])){
         $data->mail = $_GET['mail'];
      } else {
         AppCore::getUserErrors()->addMessage($tr->tr('Nebyla zadána korektní e-mailová adresa'));
      }
      return $data;
   }

   /* Autorun metody */
   public static function AutoRunDaily()
   {
      $tr = new Translator_Module('mailsnewsletters');
      $model = new MailsNewsletters_Model_Newsletter();
      $modelAB = new MailsAddressBook_Model_Addressbook();
   
      $newsletters = $model->where(MailsNewsletters_Model_Newsletter::COLUMN_ACTIVE.' = 1 '
            .'AND '.MailsNewsletters_Model_Newsletter::COLUMN_DATE_SEND.' = CURDATE()', array())->records();
      if(!$newsletters){
         return;
      }
      
      foreach ($newsletters as $newsletter) {
//          Debug::log('Odesílam: '.$newsletter->{MailsNewsletters_Model_Newsletter::COLUMN_SUBJECT});
         
         $idgrps = unserialize($newsletter->{MailsNewsletters_Model_Newsletter::COLUMN_GROUPS_IDS});
         // načtení emailů ze skupiny
         $grpIDSPL = array();
         foreach ($idgrps as $id) {
            $grpIDSPL[':pl_'.$id] = $id;
         }
         $mWhereString = MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP.' IN ('.implode(',',array_keys($grpIDSPL)).')';
         $mWhereBinds = $grpIDSPL;
         
         $mails = $modelAB->where($mWhereString, $mWhereBinds)->records();
         
         if (!$mails) {
            continue;
         }
         
         $mailsForSend = array();
         foreach ($mails as $mail) {
            $name = null;
            if($mail->{MailsAddressBook_Model_Addressbook::COLUMN_NAME} != null){
               $name = $mail->{MailsAddressBook_Model_Addressbook::COLUMN_NAME}." ".$mail->{MailsAddressBook_Model_Addressbook::COLUMN_SURNAME};
            }
            $mailsForSend[] = array('mail' => $mail->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL}, 'name' => $name);
         }
         self::sendMails($newsletter->{MailsNewsletters_Model_Newsletter::COLUMN_SUBJECT}, 
                          $newsletter->{MailsNewsletters_Model_Newsletter::COLUMN_CONTENT}, 
                          $mailsForSend);
      }
   }
}
?>
