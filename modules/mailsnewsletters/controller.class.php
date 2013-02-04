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

      $modelNewsletter = new MailsNewsletters_Model_Newsletter();
      
      $tempDirName = "newsletter-".Auth::getUserId()."-tmp";
      $this->view()->newsletterDataDir = "/newsletters/".$tempDirName;
      if(!is_dir($this->module()->getDataDir().$tempDirName)){
         mkdir($this->module()->getDataDir().$tempDirName);
      }
      
      if($this->getRequestParam('idn', null) != null){
         $record = $modelNewsletter->record($this->getRequestParam('idn'));
         $form->name->setValues($record->{MailsNewsletters_Model_Newsletter::COLUMN_SUBJECT});
         $form->content->setValues($record->{MailsNewsletters_Model_Newsletter::COLUMN_CONTENT});
         $sendDate = new DateTime($record->{MailsNewsletters_Model_Newsletter::COLUMN_DATE_SEND});
         $form->senddate->setValues(vve_date('%x', $sendDate));
         $form->groups->setValues( unserialize( $record->{MailsNewsletters_Model_Newsletter::COLUMN_GROUPS_IDS}) );
         $form->active->setValues($record->{MailsNewsletters_Model_Newsletter::COLUMN_ACTIVE});
         $this->view()->newsletterDataDir = "/newsletters/newsletter-".$record->{MailsNewsletters_Model_Newsletter::COLUMN_ID};
      }
      
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
            $cnt = $form->content->getValues();
            if( isset($record) ){ // nový
                $cnt .= self::createAlternateLink($record->{MailsNewsletters_Model_Newsletter::COLUMN_ID});
            }
            $idgrps = null;
            if($form->groups->getValues() != null){
               $idgrps = implode('|', $form->groups->getValues());
            }
            $cnt .= self::createUnscribeText($form->sendtestmail->getValues(), $idgrps);
            $this->sendTest($form->sendtestmail->getValues(), $form->name->getValues(), $cnt);
         } else if($button == 'save') {
            // save            
            if( !isset($record) ){ // nový
               $record = $modelNewsletter->newRecord();
            }
            
            $record->{MailsNewsletters_Model_Newsletter::COLUMN_ACTIVE} = $form->active->getValues();
            $record->{MailsNewsletters_Model_Newsletter::COLUMN_SUBJECT} = $form->name->getValues();
            $record->{MailsNewsletters_Model_Newsletter::COLUMN_DATE_SEND} = $form->senddate->getValues();
            $record->{MailsNewsletters_Model_Newsletter::COLUMN_GROUPS_IDS} = serialize($form->groups->getValues());
            $record->{MailsNewsletters_Model_Newsletter::COLUMN_ID_USER} = Auth::getUserId();
            $record->save();
            $idn = $record->{MailsNewsletters_Model_Newsletter::COLUMN_ID};
            $cnt = $form->content->getValues();
            
            // create new data dir for newsletter and move files
            $newDirName = 'newsletter-'.$idn;
            if (!is_dir($this->module()->getDataDir().$newDirName)) {
               mkdir($this->module()->getDataDir().$newDirName);
               rename($this->module()->getDataDir().$tempDirName, $this->module()->getDataDir().$newDirName);
               $cnt = str_replace('/'.$tempDirName.'/', '/'.$newDirName.'/', $cnt );
            }
            
            $record->{MailsNewsletters_Model_Newsletter::COLUMN_CONTENT} = $cnt;
            $record->save();

            $modelAB = new MailsAddressBook_Model_Addressbook();
            $modelQueue = new MailsNewsletters_Model_Queue();
            // odstranění mailů z fronty
            $modelQueue->where(MailsNewsletters_Model_Queue::COLUMN_ID_NEWSLETTER." = :idn", array('idn' => $idn))->delete();
            if($record->{MailsNewsletters_Model_Newsletter::COLUMN_ACTIVE} == true){
               // uložení mailů do fronty
               $idgrps = $form->groups->getValues();
               // načtení emailů ze skupiny
               $grpIDSPL = array();
               foreach ($idgrps as $id) {
                  $grpIDSPL[':pl_'.$id] = $id;
               }
               $mWhereString = MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP.' IN ('.implode(',',array_keys($grpIDSPL)).')';
               $mWhereBinds = $grpIDSPL;
               $mails = $modelAB->where($mWhereString, $mWhereBinds)->records();

               foreach ($mails as $mail){
                  $qRec = $modelQueue->newRecord();
                  $qRec->{MailsNewsletters_Model_Queue::COLUMN_ID_NEWSLETTER} = $idn;
                  $qRec->{MailsNewsletters_Model_Queue::COLUMN_MAIL} = $mail->{MailsAddressBook_Model_Addressbook::COLUMN_MAIL};
                  $qRec->{MailsNewsletters_Model_Queue::COLUMN_NAME} = $mail->{MailsAddressBook_Model_Addressbook::COLUMN_NAME};
                  $qRec->{MailsNewsletters_Model_Queue::COLUMN_SURNAME} = $mail->{MailsAddressBook_Model_Addressbook::COLUMN_SURNAME};
                  $qRec->{MailsNewsletters_Model_Queue::COLUMN_DATE_SEND} = $form->senddate->getValues();
                  $qRec->save();
               }
               $this->infoMsg()->addMessage($this->tr('Newsletter byl uložen, aktivován a e-maily byly zařazeny do fronty odesílání. K odeslání dojde během zadaného dne.'));
            } else {
               $this->infoMsg()->addMessage($this->tr('Newsletter byl uložen a vyřazen z fronty pokud v ní byl.'));
            }
            $this->link()->route('list')->param('idn')->reload();
//             $this->link()->param('idn', $idn)->reload();
         } else {
            $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
            if($this->getRequestParam('idn') != null){
               $this->link()->route('list')->param('idn')->reload();
            }
            $this->link(true)->reload();
         } 
            
      }
      
      $this->view()->form = $form;
   }

   public function listController()
   {
      $model = new MailsNewsletters_Model_Newsletter();
      
      $formDelete = new Form('newsletter_delete_');
      $eId = new Form_Element_Hidden('id');
      $formDelete->addElement($eId);
      $eSubmit = new Form_Element_Submit('delete', $this->tr('Smazat'));
      $formDelete->addElement($eSubmit);
      
      if($formDelete->isValid()){
         $n = $model->record($formDelete->id->getValues());
         if($n){
            $modelQ = new MailsNewsletters_Model_Queue();
            $modelQ
               ->where(MailsNewsletters_Model_Queue::COLUMN_ID_NEWSLETTER." = :idn", array('idn' => $n->{MailsNewsletters_Model_Newsletter::COLUMN_ID}) )
               ->delete();
            
            $n->{MailsNewsletters_Model_Newsletter::COLUMN_DELETED} = 1;
            $n->save();
            $this->infoMsg()->addMessage($this->tr('Newsletter byl smazán'));
            $this->link()->reload();
         } else {
            $this->errMsg()->addMessage($this->tr('Newsletter neexistuje'));
         }
      }
      
      $this->view()->formDelete = $formDelete;
      
      $formStatus = new Form('newsletter_status_');
      $eId = new Form_Element_Hidden('id');
      $formStatus->addElement($eId);
      $eSubmit = new Form_Element_Submit('change', $this->tr('Změnit stav'));
      $formStatus->addElement($eSubmit);
      
      if($formStatus->isValid()){
         $n = $model->record($formStatus->id->getValues());
         if($n){
            $n->{MailsNewsletters_Model_Newsletter::COLUMN_ACTIVE} = !$n->{MailsNewsletters_Model_Newsletter::COLUMN_ACTIVE};
            $n->save();
         }
         $this->link()->reload();
      }
      
      $this->view()->formStatus = $formStatus;
      
      // výběr newsletterů
      $model = new MailsNewsletters_Model_Newsletter();
      $model->where(MailsNewsletters_Model_Newsletter::COLUMN_DELETED." = 0", array());
      
      $scrollComponent = new Component_Scroll();
      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE, 20);
      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());
      $this->view()->scrollComp = $scrollComponent;

      $modelQ = new MailsNewsletters_Model_Queue();
      $this->view()->newsletters = $model
         ->columns(array('*', 'mails' => '(SELECT COUNT(*) FROM '.$modelQ->getTableName()
            .' WHERE '.MailsNewsletters_Model_Queue::COLUMN_ID_NEWSLETTER.' = '.$model->getTableShortName().'.'.MailsNewsletters_Model_Newsletter::COLUMN_ID.')'))
         ->order(array(MailsNewsletters_Model_Newsletter::COLUMN_DATE_SEND => Model_ORM::ORDER_DESC))
         ->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage())
         ->records();
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
      
      $this->view()->templates = $model->where(MailsNewsletters_Model_Templates::COLUMN_DELETED." = 0", array())->records(PDO::FETCH_OBJ);
      
   }
   
   protected function getTemplateDir($id, $onlyName = false, $webAddress = false) 
   {
      
      return $onlyName ? 'template-'.$id : $this->category()->getModule()->getDataDir($webAddress).'template-'.$id
               . ( $webAddress ? "/" : DIRECTORY_SEPARATOR );
   } 
   
   protected function getNewsletterDir($id, $onlyName = false, $webAddress = false) 
   {
      
      return $onlyName ? 'newsletter-'.$id : $this->category()->getModule()->getDataDir($webAddress).'newsletter-'.$id
               . ( $webAddress ? "/" : DIRECTORY_SEPARATOR );
   } 
   
   public function tplAddController() 
   {
      $model = new MailsNewsletters_Model_Templates();

      $tempTplName = "tpl-".Auth::getUserId()."-tmp";
      
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
   }
   
   public function newsletterPreviewController() 
   {
      $model = new MailsNewsletters_Model_Newsletter();
      $newsletter = $model->record($this->getRequest('id', 0));
      
      if(!$newsletter){
         return false;
      }
      $this->view()->template = $newsletter->{MailsNewsletters_Model_Newsletter::COLUMN_CONTENT};
   }
   
   public function replacementsController() 
   {
      $this->view()->variables = array(
            '{WEB_NAME}' => $this->tr('Název stránek'),
            '{WEB_LINK}' => $this->tr('Odkaz na stránky'),
            '{UNSCRIBE}' => $this->tr('Odkaz s textem pro odhlášení odběru'),
            '{UNSCRIBE_LINK}' => $this->tr('Odkaz pro odhlášení odběru'),
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
               
                  /**
                   * @todo if sometime using PHP Simple HTML DOM Parser replace wit this function
                   */ 
//                   $html = str_get_html($rawHtml); // what you have done
//                   foreach ($html->find("img") as $element) {
//                      if(strpos($element->src, 'http') != 0){
//                         $element->src = $element->src;
//                      }
//                   }
                  
//                   echo $html;
                  $rawHtml = preg_replace('/src="(?!http)([^"]+)"/i', 'src="'.$this->getTemplateDir($id, false, true).'/\1"', $rawHtml);
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
         $this->link()->route('tpls')->reload();
      }
      
      $this->view()->form = $formUpload;
   }
   
   public function sendTest($recipient, $subject, $content)
   {
      $um = new Model_Users();
      $user = $um->record(Auth::getUserId());
      $mailObj = self::createMail($subject, $content);
      self::sendMail($mailObj, $recipient, $user->{Model_Users::COLUMN_NAME}." ".$user->{Model_Users::COLUMN_SURNAME});
      $this->infoMsg()->addMessage($this->tr('Testovací newsletter byl odeslán.'), false);
   }
   
   /**
    * Metoda vytvoří objekt emailu
    * @param string $cnt
    * @param string $subject
    */
   protected static function createMail($subject, $cnt) 
   {
      $mailObj = new Email(true);
      $mailObj->setSubject($subject);
      $mailObj->setContent('<html><body>' .$cnt .'</body></html>');
      return $mailObj;       
   }

   /**
    * Metoda vytvoí text pro alternativní zobrazení
    * @param $idn
    * @return string
    */
   protected static function createAlternateLink($idn, $mail = null, $source = 'mail', $idGroups = null){
      $linkPreview = new Url_Link_ModuleStatic(true);
      $linkPreview
         ->module('mailsnewsletters')
         ->action('showNewsletter')
         ->param('newsletter', $idn)
         ->param('source', $source);
      if($mail != null){
         $linkPreview->param('mail', $mail);
      }
      if($idGroups != null){
         $linkPreview->param('idg', $idGroups);
      }
      $cnt = '<div style="text-align: center; margin: 5px auto; font-size: 9px;">';

      foreach (Locales::getAppLangs() as $lang){
         switch ($lang) {
            case 'en':
               $cnt .= 'E-mail not displaying correctly? Click <a href="'.$linkPreview.'" style="font-size: 9px;">here</a>. ';
               break;
            case 'cs':
            default:
               $cnt .= 'Nezobrazuje se Vám e-mail korektně? Klikněte <a href="'.$linkPreview.'" style="font-size: 9px;">zde</a>. ';
               break;
         }
      }
      $cnt .= "</div>";
      return $cnt;
   }

   /**
    * Metoda vytvoí text pro odhlášení
    * @param $idn
    * @return string
    */
   protected static function createUnscribeText($mail, $idGroups = null){
      $cnt = null;
      $unscribeLinkObj = new Url_Link_ModuleStatic(true);
      $unscribeLinkObj
         ->module('mailsnewsletters')
         ->action('unscribe')
         ->param('mail', $mail);
      if($idGroups != null){
         $unscribeLinkObj->param('idg', $idGroups);
      }
      $cnt = '<div style="text-align: center; margin: 5px auto; font-size: 9px;">';

      foreach (Locales::getAppLangs() as $lang){
         switch ($lang) {
            case 'en':
               $cnt .= 'To unsubscribe, click <a href="'.$unscribeLinkObj.'" style="font-size: 9px;">here</a>. ';
               break;
            case 'cs':
            default:
               $cnt .= 'Pro odhlášení odběru klikněte <a href="'.$unscribeLinkObj.'" style="font-size: 9px;">zde</a>. ';
               break;
         }
      }
      $cnt .= "</div>";
      return $cnt;
   }

   /**
    * Metoda provede nahrazení proměných a odeslání emailu 
    * @param Email $emailObj
    * @param string $mail
    * @param string $name
    */
   protected static function sendMail(Email $emailObj, $mail, $name = null) 
   {
      $tr = new Translator_Module('mailsnewsletters');
      $unscribeLinkObj = new Url_Link_ModuleStatic(true);

      $emailObj->setReplacements( array(
            // complex
            '{WEB_LINK}' => '<a href="'.Url_Request::getBaseWebDir().'" title="{WEB_NAME}">{WEB_NAME}</a>',
            '{UNSCRIBE}' => '<a href="{UNSCRIBE_LINK}">'.$tr->tr('odhlášení odběru').'</a>',
            // base
            '{WEB_NAME}' => VVE_WEB_NAME,
            '{NAME}' => $name,
            '{MAIL}' => $mail,
            '{UNSCRIBE_LINK}' => (string)$unscribeLinkObj->module('mailsnewsletters')->action('unscribe')->param('mail', $mail),
         ), false);
      $emailObj->setAddress($mail, $name);
      $failures = array();
      $emailObj->send($failures);
      return $failures;
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
         $model = new MailsAddressBook_Model_Addressbook();

         // remove from model
         if(isset($_GET['idg'])){
            $idgs = explode('|', $_GET['idg']);
            $idgStatement = array();
            foreach($idgs as $idg){
               $idgStatement[':idgrp_'.(int)$idg] = (int)$idg;
            }

            $model->where(MailsAddressBook_Model_Addressbook::COLUMN_MAIL." = :mail"
               ." AND ".MailsAddressBook_Model_Addressbook::COLUMN_ID_GRP." IN ( ".implode(',', array_keys($idgStatement) )." )",
               array_merge(array('mail' => $data->mail), $idgStatement ))
               ->delete();
         } else {
            $model->where(MailsAddressBook_Model_Addressbook::COLUMN_MAIL." = :mail", array('mail' => $data->mail) )
               ->delete();
         }
      } else {
         AppCore::getUserErrors()->addMessage($tr->tr('Nebyla zadána korektní e-mailová adresa'));
      }
      return $data;
   }

   /**
    * Kontroler pro zobrazení newsletter
    */
   public static function showNewsletterController()
   {
      $tr = new Translator_Module('mailsnewsletter');
      $data = new Object();
      $data->cnt = null;
      $data->title = null;

      if(!isset($_GET['newsletter'])){
         return $data;
      }

      $model = new MailsNewsletters_Model_Newsletter();

      $newsletter = $model->record((int)$_GET['newsletter']);

      if(!$newsletter){
         return $data;
      }
      $newsletter->{MailsNewsletters_Model_Newsletter::COLUMN_VIEWED}
         = $newsletter->{MailsNewsletters_Model_Newsletter::COLUMN_VIEWED} + 1;
      $newsletter->save();

      $data->title = $newsletter->{MailsNewsletters_Model_Newsletter::COLUMN_SUBJECT};
      $data->cnt = $newsletter->{MailsNewsletters_Model_Newsletter::COLUMN_CONTENT};
      if(isset($_GET['mail'])){
         $data->cnt .= self::createUnscribeText($_GET['mail'], isset($_GET['idg']) ? $_GET['idg'] : null ); // XSS ??
      }

      // get mail from addressbook

      $replacements = array(
         // complex
         '{WEB_LINK}' => '<a href="'.Url_Request::getBaseWebDir().'" title="{WEB_NAME}">{WEB_NAME}</a>',
         '{UNSCRIBE}' => '<a href="{UNSCRIBE_LINK}">'.$tr->tr('odhlášení odběru').'</a>',
         // base
         '{WEB_NAME}' => VVE_WEB_NAME,
         '{NAME}' => null,
         '{MAIL}' => null,
         '{UNSCRIBE_LINK}' => null,
      );
      $data->cnt = str_replace( array_keys($replacements), array_values($replacements), $data->cnt);

      return $data;
   }

   /* Autorun metody */
   public static function AutoRunHourly()
   {
      // nastavení maximálního time limitu scriptu
      set_time_limit(0);
      
      $tr = new Translator_Module('mailsnewsletters');
      $model = new MailsNewsletters_Model_Newsletter();
      $modelQueue = new MailsNewsletters_Model_Queue();
   
      $mails = $modelQueue
         ->where(MailsNewsletters_Model_Queue::COLUMN_DATE_SEND.' <= CURDATE()', array())
         ->order( MailsNewsletters_Model_Queue::COLUMN_ID_NEWSLETTER )
         ->records(PDO::FETCH_OBJ);
      
      if(!$mails){
         return;
      }

      $mailObj = null;
      $curIdN = 0;
      foreach ($mails as $mail) {
         if($curIdN != $mail->{MailsNewsletters_Model_Queue::COLUMN_ID_NEWSLETTER}){
            $curIdN = $mail->{MailsNewsletters_Model_Queue::COLUMN_ID_NEWSLETTER};
            // pokud se nejdná o stejný newsletter, načteme jej
            $newsLetter = $model->record($curIdN);
            // přiřazení skupin u kterých se bude odhlašovat
            $idgs = null;
            if($newsLetter->{MailsNewsletters_Model_Newsletter::COLUMN_GROUPS_IDS} != null){
               $idgs = implode('|', unserialize($newsLetter->{MailsNewsletters_Model_Newsletter::COLUMN_GROUPS_IDS}));
            }
         }

         $cnt = $newsLetter->{MailsNewsletters_Model_Newsletter::COLUMN_CONTENT};
         $cnt .= self::createAlternateLink(
            $newsLetter->{MailsNewsletters_Model_Newsletter::COLUMN_ID},
            $mail->{MailsNewsletters_Model_Queue::COLUMN_MAIL}, 'mail', $idgs );
         $cnt .= self::createUnscribeText(
            $mail->{MailsNewsletters_Model_Queue::COLUMN_MAIL}, $idgs);
         $mailObj = self::createMail($newsLetter->{MailsNewsletters_Model_Newsletter::COLUMN_SUBJECT}, $cnt);

         $name = null;
         if($mail->{MailsNewsletters_Model_Queue::COLUMN_NAME} != null){
            $name = $mail->{MailsNewsletters_Model_Queue::COLUMN_NAME}." ".$mail->{MailsNewsletters_Model_Queue::COLUMN_SURNAME};
         }
         
         // odstranění mailu s frony je méně náročnější než jeho odeslání, proto dříve
         try {
            self::sendMail($mailObj, $mail->{MailsNewsletters_Model_Queue::COLUMN_MAIL}, $name);
            $modelQueue->delete($mail->{MailsNewsletters_Model_Queue::COLUMN_ID});
            file_put_contents(AppCore::getAppCacheDir()."newsletter.log", "SEND:".$mail->{MailsNewsletters_Model_Queue::COLUMN_MAIL}."\n\n", FILE_APPEND);
         } catch (Exception $e) {
            // log
            file_put_contents(AppCore::getAppCacheDir()."newsletter.log", "ERROR:".$e->getTraceAsString()."\n\n", FILE_APPEND);
         }
      }
   }
}
?>
