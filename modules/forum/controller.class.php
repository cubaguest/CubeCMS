<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Forum_Controller extends Controller {
   const PARAM_CAPCHA_SEC = 'c_s';
   const PARAM_NOTIFY_EMAILS = 'n_e';
   const PARAM_NOTIFY_USERS = 'n_u';
   const PARAM_ORDER_BY_DATE = 'obd';

   const DEFAULT_NUM_ON_PAGE = 20;
   const MIN_SEC_FOR_HUMAN = 15;

   const COOKIE_NAME = 'cube_cf';

   protected function init()
   {
      parent::init();
      $this->view()->captchaTime = self::MIN_SEC_FOR_HUMAN;
   }

   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      $model = new Forum_Model_Topics();

      if($this->rights()->isWritable()){
         $delForm = new Form('topic_delete_');

         $elemId = new Form_Element_Hidden('id');
         $elemId->addValidation(new Form_Validator_NotEmpty());
         $elemId->addValidation(new Form_Validator_IsNumber());
         $delForm->addElement($elemId);

         $elemSubDel = new Form_Element_SubmitImage('delete', $this->tr('Smazat'));
         $delForm->addElement($elemSubDel);

         if($delForm->isValid()){
            $model->delete($delForm->id->getValues());
            $this->infoMsg()->addMessage($this->tr('Téma a všechny jeho příspěvky bylo smazáno'));
            $this->link()->reload();
         }
         $this->view()->formDel = $delForm;
      }

      $scrollComponent = new Component_Scroll();

      // načtení příspěvků
      $model->where(Forum_Model_Topics::COLUMN_ID_CAT.' = :idc', array('idc' => $this->category()->getId()));

      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());
      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
         $this->category()->getParam('scrollT', self::DEFAULT_NUM_ON_PAGE));

//      if(!$this->rights()->isControll()){
//         $model->where(Forum_Model_Topics::COLUMN_ID_CAT.' = :idc AND '
//            .'(ISNULL(`'.Forum_Model_Messages::COLUMN_CENSORED.'`)  OR `'.Forum_Model_Messages::COLUMN_CENSORED.'` = 0 )', array('idc' => $this->category()->getId()));
//      }
      $model->columns(array('*','messages_count' => 'COUNT(`'.Forum_Model_Messages::COLUMN_ID.'`)'))
         ->groupBy(array(Forum_Model_Topics::COLUMN_ID))
         ->join(Forum_Model_Topics::COLUMN_ID, 'Forum_Model_Messages', Forum_Model_Messages::COLUMN_ID_TOPIC,
         array(Forum_Model_Messages::COLUMN_CREATED_BY,
            'sort_date' => 'IFNULL(MAX(`'.Forum_Model_Messages::COLUMN_DATE_ADD.'`), `'.Forum_Model_Topics::COLUMN_DATE_ADD.'`)',
            'last_message_date' => 'MAX(`'.Forum_Model_Messages::COLUMN_DATE_ADD.'`)' ))
         ->order(array(
         'sort_date' => Model_ORM::ORDER_DESC,
         Forum_Model_Topics::COLUMN_DATE_ADD => Model_ORM::ORDER_DESC,
      ));

      $this->view()->topics = $model->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage())->records();
      $this->view()->scrollComp = $scrollComponent;

      $this->editTopic();
   }

   public function addTopicController()
   {
      $this->checkWritebleRights();
      $this->editTopic();
   }

   public function addMessageController()
   {
      $this->checkWritebleRights();
      $model = new Forum_Model_Messages();
      $modelTopic = new Forum_Model_Topics();


      $pid = null;
      if($this->getRequestParam('msg', false)){
         $parentMsg = $model->record($this->getRequestParam('msg', null));
         $this->view()->parentMessage = $parentMsg;
         $pid = $parentMsg->{Forum_Model_Messages::COLUMN_ID};
      }

      $this->view()->topic = $modelTopic->record($this->getRequest('id'));
      $this->editMessage($this->getRequest('id'), null, $pid);
   }

   public function editTopicController()
   {
      $this->checkWritebleRights();
      $modelT = new Forum_Model_Topics();
      $topic = $modelT->record($this->getRequest('id'));
      if($topic != false &&
         ($this->rights()->isControll()
            || $this->rights()->isWritable() && $topic->{Forum_Model_Topics::COLUMN_ID_USER} == Auth::getUserId() )){
         $this->editTopic($topic);
      } else {
         return false;
      }
      $this->view()->topic = $topic;
   }

   public function editMessageController()
   {
      $this->checkWritebleRights();
      $model = new Forum_Model_Messages();
      $message = $model
         ->joinFK(Forum_Model_Messages::COLUMN_ID_TOPIC)
         ->record($this->getRequest('idm'));

      if($message != false &&
         ($this->rights()->isControll()
            || $this->rights()->isWritable() && $message->{Forum_Model_Messages::COLUMN_ID_USER} == Auth::getUserId() )){
         $this->editMessage($this->getRequest('id'), $message);
      } else {
         return false;
      }
      $this->view()->message = $message;
   }

   private function editTopic(Model_ORM_Record $record = null)
   {
      $form = new Form('topic_');

      $model = new Forum_Model_Topics();
      $modelAttachments = new Forum_Model_Attachments();
      if($record == null){
         $record = $model->newRecord();
      }

      $fGrpTopic = $form->addGroup('topic', $this->tr('Téma'));

      $elemName = new Form_Element_Text('name', $this->tr('Téma'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $elemName->addValidation(new Form_Validator_MaxLength(200));
      $form->addElement($elemName, $fGrpTopic);

      $elemText = new Form_Element_TextArea('text', $this->tr('Text'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->addValidation(new Form_Validator_MinLength(10));
      $elemText->addValidation(new Form_Validator_MaxLength(10000));
      $purifier = new Form_Filter_HTMLPurify();
      $purifier->setConfig('CSS.AllowedProperties', 'text-decoration');
      $purifier->setConfig('AutoFormat.RemoveSpansWithoutAttributes', true);
      $elemText->addFilter($purifier);
      $form->addElement($elemText, $fGrpTopic);

      if($this->rights()->isControll()){
         $elemSolved = new Form_Element_Checkbox('solved', $this->tr('Vyřešeno'));
         $form->addElement($elemSolved, $fGrpTopic);
         $elemClosed = new Form_Element_Checkbox('closed', $this->tr('Zavřeno'));
         $form->addElement($elemClosed, $fGrpTopic);
      }

      $fGrpAuthor = $form->addGroup('author', $this->tr('Autor'));

      $elemNick = new Form_Element_Text('author', $this->tr('Autor'));
      $elemNick->addValidation(new Form_Validator_NotEmpty());
      $elemNick->addValidation(new Form_Validator_MaxLength(100));
      $form->addElement($elemNick, $fGrpAuthor);

      $elemEmail = new Form_Element_Text('email', $this->tr('E-mail'));
      $elemEmail->addValidation(new Form_Validator_NotEmpty());
      $elemEmail->addValidation(new Form_Validator_Email());
      $elemEmail->addValidation(new Form_Validator_MaxLength(50));
      $form->addElement($elemEmail, $fGrpAuthor);

      if(!Auth::isLogin()){
         $elemCaptcha = new Form_Element_Captcha('captcha');
         $form->addElement($elemCaptcha, $fGrpAuthor);
//       $capchaTime = $this->category()->getParam(self::PARAM_CAPCHA_SEC, self::MIN_SEC_FOR_HUMAN);
      } else {
         $modelUsers = new Model_Users();
         $user = $modelUsers->record(Auth::getUserId());
         $form->author->setValues($user->{Model_Users::COLUMN_NAME}." ".$user->{Model_Users::COLUMN_SURNAME});
         $form->email->setValues(Auth::getUserMail());
      }

//       if($this->rights()->isControll()){
      $fGrpNotify = $form->addGroup('notification', $this->tr('Oznámení'));
      $elemNotifyEmail = new Form_Element_TextArea('notificationEMails', $this->tr('E-maily'));
      $elemNotifyEmail->setSubLabel($this->tr('E-mailové adresy oddělené středníkem, na které bude odesláno oznámení o novém příspěvku'));
      $form->addElement($elemNotifyEmail, $fGrpNotify);
//       }

      if(Auth::isLogin()){
         $fGrpAttachments = $form->addGroup('attachments', $this->tr('Přílohy'));

         if(!$record->isNew()){
            $attachments = $modelAttachments
               ->where(Forum_Model_Attachments::COLUMN_ID_TOPIC." = :idt AND ".Forum_Model_Attachments::COLUMN_ID_MESSAGE." = 0", array('idt' => $record->getPK()))
               ->records();

            if($attachments){
               $elemDelAtt = new Form_Element_Select('delete_attachment', $this->tr('Smazat přílohy'));
               $elemDelAtt->setMultiple(true);
               foreach ($attachments as $att) {
                  $elemDelAtt->setOptions(array($att->{Forum_Model_Attachments::COLUMN_FILENAME} => $att->{Forum_Model_Attachments::COLUMN_ID}), true);
               }
               $form->addElement($elemDelAtt, $fGrpAttachments);
            }
         }

         $elemAttachments = new Form_Element_File('attachments', $this->tr('Příloha'));
         $elemAttachments->addValidation(new Form_Validator_FileExtension(Form_Validator_FileExtension::ALL));
         $elemAttachments->setMultiple(true);
//          $elemAttachments->setSubLabel($this->tr('Soubory, které budou připojeny k příspěvku'));
         $form->addElement($elemAttachments, $fGrpAttachments);
      }

      $elemSubmit = new Form_Element_SaveCancel('send');
      $elemSubmit->setCancelConfirm(false);
      $form->addElement($elemSubmit);


      if(!$record->isNew()){
         // add info to form
         $form->name->setValues($record->{Forum_Model_Topics::COLUMN_NAME});
         $form->text->setValues($record->{Forum_Model_Topics::COLUMN_TEXT});
         $form->author->setValues($record->{Forum_Model_Topics::COLUMN_CREATED_BY});
         $form->email->setValues($record->{Forum_Model_Topics::COLUMN_EMAIL});
         if($form->haveElement('solved')){
            $form->solved->setValues($record->{Forum_Model_Topics::COLUMN_SOLVED});
         }
         if($form->haveElement('closed')){
            $form->closed->setValues($record->{Forum_Model_Topics::COLUMN_CLOSED});
         }
         if($form->haveElement('notificationEMails')){
            $form->notificationEMails->setValues($record->{Forum_Model_Topics::COLUMN_NOTIFICATION_EMAILS});
         }
      }

      if($form->isSend()){
         if($form->send->getValues() == false){
            if($record->isNew()){
               $this->link()->route()->reload();
            } else {
               $this->link()->route('showTopic')->reload();
            }
            return;
         }
         $this->view()->showForm = true;
      }

      if($form->isValid()){
         $record->{Forum_Model_Topics::COLUMN_NAME} = $form->name->getValues();
         $record->{Forum_Model_Topics::COLUMN_CREATED_BY} = $form->author->getValues();
         $record->{Forum_Model_Topics::COLUMN_TEXT} = $form->text->getValues();
         $record->{Forum_Model_Topics::COLUMN_TEXT_CLEAR} = strip_tags($form->text->getValues());
         $record->{Forum_Model_Topics::COLUMN_EMAIL} = $form->email->getValues();

         if($record->isNew()){
            $record->{Forum_Model_Topics::COLUMN_IP} = $_SERVER['REMOTE_ADDR'];
            $record->{Forum_Model_Topics::COLUMN_ID_CAT} = $this->category()->getId();
            if(Auth::isLogin()){
               $record->{Forum_Model_Topics::COLUMN_ID_USER} = Auth::getUserId();
            }

         }
         if($form->haveElement('solved')){
            $record->{Forum_Model_Topics::COLUMN_SOLVED} = $form->solved->getValues();
         }
         if($form->haveElement('closed')){
            $record->{Forum_Model_Topics::COLUMN_CLOSED} = $form->closed->getValues();
         }

         if($form->haveElement('notificationEMails')){ // čištění emailů a parsování
            $tmp = explode(';', $form->notificationEMails->getValues());
            $emails = array();
            foreach ($tmp as $token) {
               $email = filter_var(filter_var($token, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
               if ($email !== false) {
                  $emails[] = $email;
               }
            }
            $record->{Forum_Model_Topics::COLUMN_NOTIFICATION_EMAILS} = implode(';', $emails);
         }

         $isNewMessage = $record->isNew();

         $model->save($record);

         if($form->haveElement('delete_attachment') ){
            $delAtt = $form->delete_attachment->getValues();
            if(!empty($delAtt)){
               foreach ($delAtt as $aID) {
                  try {
                     $attRec = $modelAttachments->record($aID);
                     if(!$attRec){
                        continue;
                     }

                     $file = new File($attRec->{Forum_Model_Attachments::COLUMN_FILENAME}, $this->getTopicDataDir($record->getPK()));
                     $file->delete();

                     $modelAttachments->delete($attRec);
                  } catch (Exception $e) {
                     $this->infoMsg()->addMessage( sprintf(
                        $this->tr('Přílohu %s se nepodařilo smazat. Kontaktujte webmastera'),
                        $attRec->{Forum_Model_Attachments::COLUMN_FILENAME} ), true );
                  }
               }
            }
         }

         if($form->haveElement('attachments')){
            $files = $form->attachments->getValues();
            $dataDir = new FS_Dir($this->getTopicDataDir($record->getPK()));
            $dataDir->check();
            if(!empty($files)){
               foreach ($files as $f) {
                  if(empty($f)){
                     continue;
                  }
                  $file = new File($f);
                  $file->move($dataDir);

                  $fileRec = $modelAttachments->newRecord();
                  $fileRec->{Forum_Model_Attachments::COLUMN_ID_TOPIC} = $record->getPK();
                  $fileRec->{Forum_Model_Attachments::COLUMN_ID_USER} = Auth::getUserId();
                  $fileRec->{Forum_Model_Attachments::COLUMN_FILENAME} = $file->getName();
                  $modelAttachments->save($fileRec);
               }
            }
         }

         // send global notification
         if($isNewMessage){
            $this->sendNewTopicNotification($record);
         }

         $this->infoMsg()->addMessage($this->tr('Téma bylo uloženo'));
//         if($record->isNew()){
//            $this->link()->route()->reload();
//         } else {
         $this->link()->route('showTopic', array('id' => $record->getPK()))->reload();
//         }
      }
      $this->view()->form = $form;
   }

   private function editMessage($idTopic, Model_ORM_Record $message = null, $messageReactionId = false)
   {
      $form = new Form('message_');

      $model = new Forum_Model_Messages();
      $modelAttachments = new Forum_Model_Attachments();
      if($message == null){
         $message = $model->newRecord();
      }
      $elemName = new Form_Element_Text('name', $this->tr('Předmět'));
      $elemName->addValidation(new Form_Validator_MaxLength(200));
      $form->addElement($elemName);

      $elemText = new Form_Element_TextArea('text', $this->tr('Text'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->addValidation(new Form_Validator_MinLength(10));
      $elemText->addValidation(new Form_Validator_MaxLength(10000));
      $purifier = new Form_Filter_HTMLPurify();
      $purifier->setConfig('CSS.AllowedProperties', 'text-decoration');
      $purifier->setConfig('AutoFormat.RemoveSpansWithoutAttributes', true);
      $elemText->addFilter($purifier);
      $form->addElement($elemText);

      $elemNick = new Form_Element_Text('author', $this->tr('Autor'));
      $elemNick->addValidation(new Form_Validator_NotEmpty());
      $elemNick->addValidation(new Form_Validator_MaxLength(100));
      $form->addElement($elemNick);

      $elemEmail = new Form_Element_Text('email', $this->tr('E-mail'));
      $elemEmail->addValidation(new Form_Validator_NotEmpty());
      $elemEmail->addValidation(new Form_Validator_Email());
      $elemEmail->addValidation(new Form_Validator_MaxLength(50));
      $form->addElement($elemEmail);

      $elemSendNotify = new Form_Element_Checkbox('sendNotify', $this->tr('Odeslat upozornění'));
      $elemSendNotify->setSubLabel($this->tr('Při reakci na můj příspěvek odeslat upozornění na zadaný e-mail'));
      $form->addElement($elemSendNotify);

      if(!Auth::isLogin()){
         $elemCaptcha = new Form_Element_Captcha('captcha');
         $form->addElement($elemCaptcha);
      }

      if(Auth::isLogin()){
         $fGrpAttachments = $form->addGroup('attachments', $this->tr('Přílohy'));

         if(!$message->isNew()){
            $attachments = $modelAttachments
               ->where(Forum_Model_Attachments::COLUMN_ID_TOPIC." = :idt AND ".Forum_Model_Attachments::COLUMN_ID_MESSAGE." = :idm",
               array('idt' => $idTopic, 'idm' => $message->getPK() ))
               ->records();

            if($attachments){
               $elemDelAtt = new Form_Element_Select('delete_attachment', $this->tr('Smazat přílohy'));
               $elemDelAtt->setMultiple(true);
               foreach ($attachments as $att) {
                  $elemDelAtt->setOptions(array($att->{Forum_Model_Attachments::COLUMN_FILENAME} => $att->{Forum_Model_Attachments::COLUMN_ID}), true);
               }
               $form->addElement($elemDelAtt, $fGrpAttachments);
            }
         }

         $elemAttachments = new Form_Element_File('attachments', $this->tr('Příloha'));
         $elemAttachments->addValidation(new Form_Validator_FileExtension(Form_Validator_FileExtension::ALL));
         $elemAttachments->setMultiple(true);
         $form->addElement($elemAttachments, $fGrpAttachments);
      }

      if($messageReactionId != null){
         $eParentMsgId = new Form_Element_Hidden('parentMsgId');
         $eParentMsgId->setValues($messageReactionId);
         $form->addElement($eParentMsgId);
      }

      $elemSubmit = new Form_Element_SaveCancel('send');
      $elemSubmit->setCancelConfirm(false);
      $form->addElement($elemSubmit);

      // u přihlášených vypneme chapchu
      if(Auth::isLogin() && $message->isNew()){
         $modelUsers = new Model_Users();
         $user = $modelUsers->record(Auth::getUserId());
         $form->author->setValues($user->{Model_Users::COLUMN_NAME}." ".$user->{Model_Users::COLUMN_SURNAME});
         $form->email->setValues(Auth::getUserMail());
      }

      if(!$message->isNew()){
         // add info to form
         $form->name->setValues($message->{Forum_Model_Messages::COLUMN_NAME});
         $form->text->setValues($message->{Forum_Model_Messages::COLUMN_TEXT});
         $form->author->setValues($message->{Forum_Model_Messages::COLUMN_CREATED_BY});
         $form->email->setValues($message->{Forum_Model_Messages::COLUMN_EMAIL});
      }

      if($form->isSend()){
         if($form->send->getValues() == false){
            $this->link()->route('showTopic')->reload();
            return;
         }
         $this->view()->showForm = true;
      }

      if($form->isValid()){
         $message->{Forum_Model_Messages::COLUMN_NAME} = $form->name->getValues();
         $message->{Forum_Model_Messages::COLUMN_CREATED_BY} = $form->author->getValues();
         $message->{Forum_Model_Messages::COLUMN_TEXT} = $form->text->getValues();
         $message->{Forum_Model_Messages::COLUMN_TEXT_CLEAR} = strip_tags($form->text->getValues());
         $message->{Forum_Model_Messages::COLUMN_EMAIL} = $form->email->getValues();
         $message->{Forum_Model_Messages::COLUMN_SEND_NOTIFY} = $form->sendNotify->getValues();
         if($message->isNew()){
            $message->{Forum_Model_Messages::COLUMN_ID_TOPIC} = $idTopic;
            if(Auth::isLogin()){
               $message->{Forum_Model_Messages::COLUMN_ID_USER} = Auth::getUserId();
            }
            if($this->category()->getRights()->isControll()){
               $message->{Forum_Model_Messages::COLUMN_CREATED_BY_MODERATOR} = true;
            }
            $message->{Forum_Model_Messages::COLUMN_IP} = $_SERVER['REMOTE_ADDR'];
         }
         if(isset($form->parentMsgId)){
            $parentMsg = $model->record($form->parentMsgId->getValues());
            $message->{Forum_Model_Messages::COLUMN_ID_PARENT_MESSAGE} = $parentMsg->getPK();
            if($parentMsg->{Forum_Model_Messages::COLUMN_SEND_NOTIFY}){
               $this->sendMessageNotification($idTopic, $parentMsg, $message);
            }
         }
         $newMsg = $message->isNew();

         $model->save($message);

         if($form->haveElement('delete_attachment') ){
            $delAtt = $form->delete_attachment->getValues();
            if(!empty($delAtt)){
               foreach ($delAtt as $aID) {
                  try {
                     $attRec = $modelAttachments->record($aID);
                     if(!$attRec){
                        continue;
                     }
                     $file = new File($attRec->{Forum_Model_Attachments::COLUMN_FILENAME}, $this->getTopicDataDir($idTopic));
                     $file->delete();
                     $modelAttachments->delete($attRec);
                  } catch (Exception $e) {
                     $this->errMsg()->addMessage( sprintf(
                        $this->tr('Přílohu %s se nepodařilo smazat. Kontaktujte webmastera.'),
                        $attRec->{Forum_Model_Attachments::COLUMN_FILENAME} ) );
                  }
               }
            }
         }

         if($form->haveElement('attachments')){
            $files = $form->attachments->getValues();

            if($files != null){
               $dataDir = new FS_Dir($this->getTopicDataDir($idTopic));
               $dataDir->check();
               foreach ($files as $f) {
                  if(empty($f)){
                     continue;
                  }
                  $file = new File($f);
                  $file->move($dataDir);

                  $fileRec = $modelAttachments->newRecord();
                  $fileRec->{Forum_Model_Attachments::COLUMN_ID_TOPIC} = $idTopic;
                  $fileRec->{Forum_Model_Attachments::COLUMN_ID_MESSAGE} = $message->getPK();
                  $fileRec->{Forum_Model_Attachments::COLUMN_ID_USER} = Auth::getUserId();
                  $fileRec->{Forum_Model_Attachments::COLUMN_FILENAME} = $file->getName();
                  $modelAttachments->save($fileRec);
               }
            }
         }

         if($newMsg){
            $this->sendTopicNotification($idTopic, $message);
         }

         $this->infoMsg()->addMessage($this->tr('Příspěvek byl uložen'));
         if($messageReactionId){
            $anchor = 'message-'.$messageReactionId;
         } else {
            $anchor = 'message-'.$message->getPK();
         }
         $this->link()->route('showTopic')->rmParam('msg')->anchor($anchor)->reload();
      }
      $this->view()->form = $form;
   }

   public function showTopicController()
   {
      $this->checkReadableRights();

      $modelT = new Forum_Model_Topics();
      $modelP = new Forum_Model_Messages();
      $modelAttachments = new Forum_Model_Attachments();

      $topic = $modelT->record($this->getRequest('id'));
      if($topic == false){return false;}

      if($this->rights()->isControll()){
         $formDel = new Form('message_delete');
         $eId = new Form_Element_Hidden('id');
         $formDel->addElement($eId);

         $eDel = new Form_Element_SubmitImage('delete', $this->tr('Smazat'));
         $formDel->addElement($eDel);
         if($formDel->isValid()){
            $modelP->delete($formDel->id->getValues());

            // check user rights ?

            // delete all files
            $files = $modelAttachments
               ->where(Forum_Model_Attachments::COLUMN_ID_MESSAGE." = :idm", array('idm' => $formDel->id->getValues()))
               ->records();

            if($files){
               foreach ($files as $file) {
                  $f = new File($file->{Forum_Model_Attachments::COLUMN_FILENAME}, $this->getTopicDataDir($topic->{Forum_Model_Topics::COLUMN_ID} ));
                  $f->delete();
               }
               $modelAttachments->where(Forum_Model_Attachments::COLUMN_ID_MESSAGE." = :idm", array('idm' => $formDel->id->getValues()))->delete();
            }

            $this->infoMsg()->addMessage($this->tr('Příspěvek byl smazán'));
            $this->link()->reload();
         }
         $this->view()->formMessageDelete = $formDel;

         $formCensore = new Form('message_censore');
         $eId = new Form_Element_Hidden('id');
         $formCensore->addElement($eId);

         $eCensore = new Form_Element_SubmitImage('change', $this->tr('Změnit cenzůru'));
         $formCensore->addElement($eCensore);
         if($formCensore->isValid()){
            $message = $modelP->record($formCensore->id->getValues());

            if($message != false){
               $message->{Forum_Model_Messages::COLUMN_CENSORED} = (int)!(bool)$message->{Forum_Model_Messages::COLUMN_CENSORED};
               $modelP->save($message);
            }
            $this->infoMsg()->addMessage($this->tr('Cenzůra byla změněna'));
            $this->link()->reload();
         }
         $this->view()->formMessageCensore = $formCensore;

         $formTopicDel = new Form('topic_delete');
         $eId = new Form_Element_Hidden('id');
         $eId->setValues($topic->{Forum_Model_Topics::COLUMN_ID});
         $formTopicDel->addElement($eId);

         $eDel = new Form_Element_SubmitImage('delete', $this->tr('Smazat'));
         $formTopicDel->addElement($eDel);
         if($formTopicDel->isValid()){
            // delete all files
            $files = $modelAttachments
               ->where(Forum_Model_Attachments::COLUMN_ID_TOPIC." = :idt", array('idt' => $formTopicDel->id->getValues()))
               ->records();

            if($files){
               foreach ($files as $file) {
                  $f = new File($file->{Forum_Model_Attachments::COLUMN_FILENAME}, $this->getTopicDataDir($topic->{Forum_Model_Topics::COLUMN_ID} ));
                  $f->delete();
               }
            }

            $modelT->delete($formTopicDel->id->getValues());
            $this->infoMsg()->addMessage($this->tr('Téma i s příspěvky bylo smazáno'));
            $this->link()->route()->reload();
         }
         $this->view()->formTopicDelete = $formTopicDel;

      } else {
         // update pokud nemá právo kontroly
         $topic->{Forum_Model_Topics::COLUMN_VIEWS} = $topic->{Forum_Model_Topics::COLUMN_VIEWS}+1;
         $modelT->save($topic);
      }

      // načtení příspěvků
//      if($this->rights()->isControll()){
      $modelP->where(Forum_Model_Messages::COLUMN_ID_TOPIC.' = :idt', array('idt' => $topic->{Forum_Model_Messages::COLUMN_ID_TOPIC}));
//      } else {
//         $modelP->where(Forum_Model_Messages::COLUMN_ID_TOPIC.' = :idt AND '.Forum_Model_Messages::COLUMN_CENSORED.' = 0', 
//            array('idt' => $topic->{Forum_Model_Messages::COLUMN_ID_TOPIC}));
//      }
      $modelP->order(array(Forum_Model_Messages::COLUMN_ORDER => Model_ORM::ORDER_ASC));

      $messages = $modelP->records();

      $sorted = $this->messagesToArray($messages);
      if($this->category()->getParam(self::PARAM_ORDER_BY_DATE,'desc') == 'asc'){
         $sorted = array_reverse($sorted);
      }
      $this->view()->messagesSort = $sorted;
      
      $ids = isset($_COOKIE[self::COOKIE_NAME]) ? explode('|', $_COOKIE[self::COOKIE_NAME]) : array();
      foreach($messages as &$msg){
         $msg->voteEnabled = (!$this->rights()->isControll() && in_array($msg->getPK(), $ids)) ? false : true;
      }


      $this->view()->topic = $topic;
      $this->view()->linkBack = $this->link()->route()->rmParam('p_p');
//      $this->view()->messages = $modelP->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage())->records();
      $this->view()->messages = $messages;
//      $this->view()->scrollComp = $scrollComponent;

      /* attachments */
      $attTMP = $modelAttachments->where(Forum_Model_Attachments::COLUMN_ID_TOPIC." = :idt", array('idt' => $topic->getPK()))
         ->records();

      $attachments = array();
      foreach ($attTMP as $file) {
         $fid = $file->{Forum_Model_Attachments::COLUMN_ID_MESSAGE};
         if(!isset($attachments[$fid])){
            $attachments[$fid] = array();
         }
         $attachments[$fid][] = $file;
      }
      $this->view()->attachments = $attachments;
      $this->view()->attachmentsPath = $this->getTopicDataDir($topic->getPK(), true);
   }
   
   protected function messagesToArray($messages, $idParent = 0) {
      $result = array();
      foreach ($messages as $msg) {
         if($msg->{Forum_Model_Messages::COLUMN_ID_PARENT_MESSAGE} == $idParent ){
            $item = $msg;
            $item->childs = $this->messagesToArray($messages, $msg->getPK());
            $result[] = $item;
         } 
      }
      return count($result) > 0 ? $result : null;
   }

   public function rssTopicController()
   {
      $this->checkReadableRights();
      $modelT = new Forum_Model_Topics();
      $modelM = new Forum_Model_Messages();

      $topic = $modelT->record($this->getRequest('id'));
      if($topic == false){return false;}

      $modelM->where(Forum_Model_Messages::COLUMN_ID_TOPIC.' = :idt', array('idt' => $topic->{Forum_Model_Messages::COLUMN_ID_TOPIC}));
      $modelM->order(array(Forum_Model_Messages::COLUMN_DATE_ADD => Model_ORM::ORDER_ASC));

      $this->view()->topic = $topic;
      $this->view()->messages = $modelM->limit(0, VVE_FEED_NUM)->records();
   }

   public function sendTopicNotification($idTopic, $newMessage)
   {
      $model = new Forum_Model_Topics();
      $modelMessages = new Forum_Model_Messages();
      $topic = $model->columns(array(
         Forum_Model_Topics::COLUMN_NOTIFICATION_EMAILS, Forum_Model_Topics::COLUMN_EMAIL,
         Forum_Model_Topics::COLUMN_DATE_ADD, Forum_Model_Topics::COLUMN_NAME,
         Forum_Model_Topics::COLUMN_TEXT, Forum_Model_Topics::COLUMN_CREATED_BY,
      ))->record($idTopic);
      // pokud není téma nebo téma nemá nastavenu notifikaci, neodesílat
      if($topic == false || $topic->{Forum_Model_Topics::COLUMN_NOTIFICATION_EMAILS} == null){
         return;
      }
      $emails = Email::getEmailsFromString($topic->{Forum_Model_Topics::COLUMN_NOTIFICATION_EMAILS});
      // main notifications
      $userIds = $this->category()->getParam(self::PARAM_NOTIFY_USERS, null);
      if(!empty($userIds)){
         $modelUsers = new Model_Users();
         foreach ($userIds as $id) {
            $user = $modelUsers->record($id);
            if($user && $user->{Model_Users::COLUMN_MAIL} != null){
               $emails[] = $user->{Model_Users::COLUMN_MAIL};
            }
         }
      }
      $customMails = $this->category()->getParam(self::PARAM_NOTIFY_EMAILS, null);
      if($customMails != null){
         $emails = array_merge($emails, Email::getEmailsFromString($this->category()->getParam(self::PARAM_NOTIFY_EMAILS)));
      }
      $emails = array_filter($emails);
      if(empty ($emails)){
         return;
      }

      try {
         $mail = new Email(true);
         $mail->setSubject(sprintf('Fórum: nový příspěvek k tématu %s', $topic->{Forum_Model_Topics::COLUMN_NAME}));
         $mail->addAddress($emails);

         $tplFile = $this->module()->getLibDir().DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.'mail_tpl.html';
         if(!is_file($tplFile)){
            return;
         }
         // tady ještě test z faces asi
         $mailCnt = file_get_contents($tplFile) ; // mail content loaded from template

         // prepare messages
         $matches = array();
         if(preg_match('/{MESSAGE}(.*){\/MESSAGE}/s', $mailCnt, $matches) !== false){
            $messageTpl = $matches[1];
            $messagesStr = null;
            $messages = $modelMessages->where(Forum_Model_Messages::COLUMN_ID_TOPIC.' = :idt', array('idt' => $idTopic))
               ->limit(0, 100)
               ->order(array(Forum_Model_Messages::COLUMN_DATE_ADD => Model_ORM::ORDER_ASC))
               ->records();

            if(!empty ($messages)){
               foreach ($messages as $message) {
                  $messagesStr .= str_replace(array(
                     '{MESSAGE_NAME}',
                     '{MESSAGE_INFO}',
                     '{MESSAGE_TEXT}'
                  ), array(
                     $message->{Forum_Model_Messages::COLUMN_NAME},
                     $message->{Forum_Model_Messages::COLUMN_CREATED_BY}
                        .' ('.vve_date('%x %X', new DateTime($message->{Forum_Model_Messages::COLUMN_DATE_ADD})).')'
                        .' <'.$message->{Forum_Model_Messages::COLUMN_EMAIL}.'>',
                     $message->{Forum_Model_Messages::COLUMN_TEXT},
                  ), $messageTpl);

               }
            } else {
               $messagesStr = 'Není vložen žádný příspěvek';
            }
            $mailCnt = preg_replace('/{MESSAGE}(.*){\/MESSAGE}/s', $messagesStr, $mailCnt);
         }

         /* přepis hodnot */
         /* 
          * {POST_NEW_NAME} - nový příspěvek
          * {POST_NEW_LINK} - odkaz příspěvek
          * {POST_NEW_INFO} - odkaz příspěvek
          * {POST_NEW_TEXT} - odkaz příspěvek
          * {WEB_LINK} - odkaz na stránky
          * {TOPIC_LINK} - odkaz na téma ve fóru
          * {TOPIC_NAME} - název tématu
          * {TOPIC_TEXT} - text tématu
          * {TOPIC_INFO} - autor a čas tématu
          * 
          * {POSTS} - seznam odpovědí (asi takhle ???)
          */
         $search = array(
            // new msg
            '{MESSAGE_NEW_NAME}',
            '{MESSAGE_NEW_LINK}',
            '{MESSAGE_NEW_INFO}',
            '{MESSAGE_NEW_TEXT}',
            // topic
            '{TOPIC_NAME}',
            '{TOPIC_LINK}',
            '{TOPIC_INFO}',
            '{TOPIC_TEXT}',
            // ostatní
            '{WEB_NAME}',
            '{WEB_LINK}',
         );

         $replace = array(
            // new msg
            $newMessage->{Forum_Model_Messages::COLUMN_NAME},
            $this->link()->route('showTopic')->rmParam()->anchor('message-'.$newMessage->{Forum_Model_Messages::COLUMN_ID}),
            $newMessage->{Forum_Model_Messages::COLUMN_CREATED_BY}
               .' ('.  vve_date('%x %X', new DateTime()).')'
               .' <'.$newMessage->{Forum_Model_Messages::COLUMN_EMAIL}.'>',
            $newMessage->{Forum_Model_Messages::COLUMN_TEXT},
            // topic
            $topic->{Forum_Model_Topics::COLUMN_NAME},
            $this->link()->route('showTopic')->rmParam(),
            $topic->{Forum_Model_Topics::COLUMN_CREATED_BY}
               .' ('.  vve_date('%x %X', new DateTime($topic->{Forum_Model_Topics::COLUMN_DATE_ADD})).')'
               .' <'.$topic->{Forum_Model_Topics::COLUMN_EMAIL}.'>',
            $topic->{Forum_Model_Topics::COLUMN_TEXT},
            // ostatní
            VVE_WEB_NAME,
            $this->link()->clear(true),
         );

         $mailCnt = str_replace($search, $replace, $mailCnt);

//         Debug::log($mailCnt);
         $mail->setContent($mailCnt);
         $mail->batchSend();

      } catch (Swift_SwiftException $exc) {
         $this->log('Chyba při odesílání upozornění na nový příspěvek: '.$exc->getTraceAsString());
      }

   }

   public function sendNewTopicNotification($topic)
   {
      // pokud není téma nebo téma nemá nastavenu notifikaci, neodesílat
      if($topic == false){
         return;
      }
      $emails = Email::getEmailsFromString($topic->{Forum_Model_Topics::COLUMN_NOTIFICATION_EMAILS});
      // main notifications
      $userIds = $this->category()->getParam(self::PARAM_NOTIFY_USERS, null);
      if(!empty($userIds)){
         $modelUsers = new Model_Users();
         foreach ($userIds as $id) {
            $user = $modelUsers->record($id);
            if($user && $user->{Model_Users::COLUMN_MAIL} != null){
               $emails[] = $user->{Model_Users::COLUMN_MAIL};
            }
         }
      }
      $customMails = $this->category()->getParam(self::PARAM_NOTIFY_EMAILS, null);
      if($customMails != null){
         $emails = array_merge($emails, Email::getEmailsFromString($this->category()->getParam(self::PARAM_NOTIFY_EMAILS)));
      }
      $emails = array_filter($emails);

      if(empty ($emails)){
         return;
      }

      try {
         $mail = new Email(true);
         $mail->setSubject(sprintf('Fórum: bylo založeno nové téma %s', $topic->{Forum_Model_Topics::COLUMN_NAME}));
         $mail->addAddress(array_filter($emails));

         $tplFile = $this->module()->getLibDir().Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.'mail_topic_tpl.html';
         if(!is_file($tplFile)){
            return;
         }

         // tady ještě test z faces asi
         $mailCnt = file_get_contents($tplFile) ; // mail content loaded from template
         // prepare messages

         /* přepis hodnot */
         /*
          * {WEB_LINK} - odkaz na stránky
          * {TOPIC_LINK} - odkaz na téma ve fóru
          * {TOPIC_NAME} - název tématu
          * {TOPIC_TEXT} - text tématu
          * {TOPIC_INFO} - autor a čas tématu
          */
         $search = array(
            // topic
            '{TOPIC_NAME}',
            '{TOPIC_LINK}',
            '{TOPIC_INFO}',
            '{TOPIC_TEXT}',
            // ostatní
            '{WEB_NAME}',
            '{WEB_LINK}',
         );

         $replace = array(
            // topic
            $topic->{Forum_Model_Topics::COLUMN_NAME},
            $this->link()->route('showTopic', array('id' => $topic->getPK()))->rmParam(),
            $topic->{Forum_Model_Topics::COLUMN_CREATED_BY}
               .' ('.  vve_date('%x %X', new DateTime()).')'
               .' <'.$topic->{Forum_Model_Topics::COLUMN_EMAIL}.'>',
            $topic->{Forum_Model_Topics::COLUMN_TEXT},
            // ostatní
            VVE_WEB_NAME,
            $this->link()->clear(true),
         );

         $mailCnt = str_replace($search, $replace, $mailCnt);
         $mail->setContent($mailCnt);
         $mail->batchSend($failures);

      } catch (Swift_SwiftException $exc) {
         $this->log('Chyba při odesílání upozornění na nové téma: '.$exc->getTraceAsString());
      }
   }

   public function sendMessageNotification($idTopic, $parentMsg, $reaction)
   {
      if($parentMsg->{Forum_Model_Messages::COLUMN_EMAIL} == null){
         return;
      }
      $model = new Forum_Model_Topics();
      $modelMessages = new Forum_Model_Messages();
      $topic = $model->columns(array(
         Forum_Model_Topics::COLUMN_NOTIFICATION_EMAILS, Forum_Model_Topics::COLUMN_EMAIL,
         Forum_Model_Topics::COLUMN_DATE_ADD, Forum_Model_Topics::COLUMN_NAME,
         Forum_Model_Topics::COLUMN_TEXT, Forum_Model_Topics::COLUMN_CREATED_BY,
      ))->record($idTopic);
      // pokud není téma nebo téma nemá nastavenu notifikaci, neodesílat
      if($topic == false || $topic->{Forum_Model_Topics::COLUMN_NOTIFICATION_EMAILS} == null){
         return;
      }

      try {
         $mail = new Email(true);
         $mail->setSubject(sprintf('Fórum: reakce na Váš příspěvek k tématu %s', $topic->{Forum_Model_Topics::COLUMN_NAME}));
         $mail->addAddress($parentMsg->{Forum_Model_Messages::COLUMN_EMAIL});

         $tplFile = $this->module()->getLibDir().DIRECTORY_SEPARATOR.Template::TEMPLATES_DIR.DIRECTORY_SEPARATOR.'mail_reaction_tpl.html';
         if(!is_file($tplFile)){
            return;
         }
         // tady ještě test z faces asi
         $mailCnt = file_get_contents($tplFile) ; // mail content loaded from template

         /* přepis hodnot */
         /* 
          * {POST_NEW_NAME} - nový příspěvek
          * {POST_NEW_LINK} - odkaz příspěvek
          * {POST_NEW_INFO} - odkaz příspěvek
          * {POST_NEW_TEXT} - odkaz příspěvek
          * {WEB_LINK} - odkaz na stránky
          * {TOPIC_LINK} - odkaz na téma ve fóru
          * {TOPIC_NAME} - název tématu
          * {TOPIC_TEXT} - text tématu
          * {TOPIC_INFO} - autor a čas tématu
          * 
          * {POSTS} - seznam odpovědí (asi takhle ???)
          */
         $search = array(
            // msg
            '{MESSAGE_NAME}',
            '{MESSAGE_LINK}',
            '{MESSAGE_INFO}',
            '{MESSAGE_TEXT}',
            // topic
            '{TOPIC_NAME}',
            '{TOPIC_LINK}',
            '{TOPIC_INFO}',
            '{TOPIC_TEXT}',
            // reakce
            '{REACTION_NAME}',
            '{REACTION_LINK}',
            '{REACTION_INFO}',
            '{REACTION_TEXT}',
            // ostatní
            '{WEB_NAME}',
            '{WEB_LINK}',
            '{CANCEL_LINK}',
         );

         $linkClear = clone $this->link();
         $replace = array(
            // new msg
            $parentMsg->{Forum_Model_Messages::COLUMN_NAME},
            $this->link()->route('showTopic')->rmParam()->anchor('message-'.$parentMsg->{Forum_Model_Messages::COLUMN_ID}),
            $parentMsg->{Forum_Model_Messages::COLUMN_CREATED_BY}
               .' ('.  vve_date('%x %X', new DateTime($parentMsg->{Forum_Model_Messages::COLUMN_DATE_ADD})).')'
               .' <'.$parentMsg->{Forum_Model_Messages::COLUMN_EMAIL}.'>',
            $parentMsg->{Forum_Model_Messages::COLUMN_TEXT},
            // topic
            $topic->{Forum_Model_Topics::COLUMN_NAME},
            $this->link()->route('showTopic')->rmParam(),
            $topic->{Forum_Model_Topics::COLUMN_CREATED_BY}
               .' ('.  vve_date('%x %X', new DateTime($topic->{Forum_Model_Topics::COLUMN_DATE_ADD})).')'
               .' <'.$topic->{Forum_Model_Topics::COLUMN_EMAIL}.'>',
            $topic->{Forum_Model_Topics::COLUMN_TEXT},
            // reakce
            $reaction->{Forum_Model_Messages::COLUMN_NAME},
            $this->link()->route('showTopic')->rmParam()->anchor('message-'.$reaction->{Forum_Model_Messages::COLUMN_ID}),
            $reaction->{Forum_Model_Messages::COLUMN_CREATED_BY}
               .' ('.  vve_date('%x %X', new DateTime()).')'
               .' <'.$reaction->{Forum_Model_Messages::COLUMN_EMAIL}.'>',
            $reaction->{Forum_Model_Messages::COLUMN_TEXT},
            // ostatní
            VVE_WEB_NAME,
            $linkClear->clear(true),
            $this->link()->route('cancelMessageNotify', array('idm' => $parentMsg->{Forum_Model_Messages::COLUMN_ID})),
         );

         $mailCnt = str_replace($search, $replace, $mailCnt);

//         Debug::log($mailCnt);
         $mail->setContent($mailCnt);
         $mail->batchSend();

      } catch (Swift_SwiftException $exc) {
         $this->log('Chyba při odesílání upozornění na nový příspěvek: '.$exc->getTraceAsString());
      }
   }

   public function cancelMessageNotifyController()
   {
      $modelT = new Forum_Model_Topics();
      $modelM = new Forum_Model_Messages();

      $msg = $modelM->record($this->getRequest('idm'));

      if(!$msg){
         return false;
      }
      $topic = $modelT->record($msg->{Forum_Model_Messages::COLUMN_ID_TOPIC});
      $this->view()->message = $msg;
      $this->view()->topic = $topic;

      if($this->getRequestParam('confirm', false)){
         $msg->{Forum_Model_Messages::COLUMN_SEND_NOTIFY} = false;
         $modelM->save($msg);

         $this->infoMsg()->addMessage($this->tr('Upozornění na reakce bylo zrušeno'));
         $this->link()->rmParam('confirm')->reload();
      }

   }

   private function getTopicDataDir( $tid, $webPath = false){
      return $this->module()->getDataDir($webPath)."topic-".$tid.( $webPath ? "/" : DIRECTORY_SEPARATOR );
   }

   /**
    * Kontroler pro volbu u zpráv
    */
   public function voteMessageController()
   {
      if(!isset($_POST['id']) || !isset($_POST['action'])){
         $this->errMsg()->addMessage($this->tr('Chyba parametrů'));
         return;
      }

      $action = $_POST['action'];
      $model = new Forum_Model_Messages();
      $message = $model->record((int)$_POST['id']);

      if(!$message){
         throw new UnexpectedValueException($this->tr('Zpráva neexistuje'));
      }

      switch($action){
         case 'voteUp':
            $message->{Forum_Model_Messages::COLUMN_VOTE} = $message->{Forum_Model_Messages::COLUMN_VOTE} + 1;
            $message->save();
            break;
         case 'voteDown':
            $message->{Forum_Model_Messages::COLUMN_VOTE} = $message->{Forum_Model_Messages::COLUMN_VOTE}-1;
            $message->save();
            break;
         case 'voteSpam':
            $message->{Forum_Model_Messages::COLUMN_VOTE_SPAM} = $message->{Forum_Model_Messages::COLUMN_VOTE_SPAM}+1;
            $message->save();
            break;
      }

      $this->view()->votes = $message->{Forum_Model_Messages::COLUMN_VOTE};
      $this->view()->votesSpam = $message->{Forum_Model_Messages::COLUMN_VOTE_SPAM};
      $this->view()->idMessage = $message->getPK();

   }

   /**
    * Metoda pro nastavení modulu
    */
   public function settings(&$settings, Form &$form) {
      $form->addGroup('basic');

      $elemScrollT = new Form_Element_Text('scrollT', $this->tr('Počet témat na stránku'));
      $elemScrollT->setSubLabel($this->tr(sprintf('Výchozí: %s témat', self::DEFAULT_NUM_ON_PAGE)));
      $elemScrollT->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScrollT,'basic');

      if(isset($settings['scrollT'])) {
         $form->scrollT->setValues($settings['scrollT']);
      }
      
      $elemOrder = new Form_Element_Select('order', $this->tr('PoYad� koYenov�ch pY�spvko'));
      $elemOrder->setOptions(array(
          $this->tr('Od nejstara�ho pY�spvku') => 'asc',
          $this->tr('Od nejnovja�ho pY�spvku') => 'desc'
      ), false, false);
      $form->addElement($elemOrder, 'basic');
      if(isset($settings[self::PARAM_ORDER_BY_DATE])) {
         $form->order->setValues($settings[self::PARAM_ORDER_BY_DATE]);
      }

//      $elemScroll = new Form_Element_Text('scroll', $this->tr('Počet příspěvků na stránku'));
//      $elemScroll->setSubLabel($this->tr(sprintf('Výchozí: %s příspěvků', self::DEFAULT_NUM_ON_PAGE)));
//      $elemScroll->addValidation(new Form_Validator_IsNumber());
//      $form->addElement($elemScroll,'basic');
//
//      if(isset($settings['scroll'])) {
//         $form->scroll->setValues($settings['scroll']);
//      }

      $fGrpNewItem = $form->addGroup('newitem', $this->tr('Přidání příspěvku'));

      $elemCapchaSec = new Form_Element_Text('capchatime', $this->tr('Po kolika sekundách lze formulář odeslat'));
      $elemCapchaSec->setSubLabel($this->tr(sprintf('Výchozí: %s sekund (obrana proti spamu). Pokud je 0, kontrola je vypnuta.', self::MIN_SEC_FOR_HUMAN)));
      $elemCapchaSec->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemCapchaSec, $fGrpNewItem);
      if(isset($settings[self::PARAM_CAPCHA_SEC])) {
         $form->capchatime->setValues($settings[self::PARAM_CAPCHA_SEC]);
      }

      $grpNotify = $form->addGroup('notofocation', $this->tr('Nastavení oznamování'),
         $this->tr('Nastavení oznamování nových tématech a příspěvcích'));

      // maily správců
      $elemNotifyEmails = new Form_Element_TextArea('notifyEMails', $this->tr('Adresy pro oznámení'));
      $elemNotifyEmails->setSubLabel($this->tr('E-mailové adresy, kam chodí oznámení o nových tématech a příspěvcích oddělené středníkem.'));
      if (isset($settings[self::PARAM_NOTIFY_EMAILS])) {
         $elemNotifyEmails->setValues($settings[self::PARAM_NOTIFY_EMAILS]);
      }
      $form->addElement($elemNotifyEmails, $grpNotify);


      $elemNotifyUsers = new Form_Element_Select('notifyUsers', $this->tr('Uživatelé v systému'));
      $elemNotifyUsers->setSubLabel($this->tr('Uživatelé v systému, kterým chodí oznámení o nových tématech a odpovědích.'));
      // načtení uživatelů
      $modelUsers = new Model_Users();
      $users = $modelUsers->usersForThisWeb(true)->records(PDO::FETCH_OBJ);
      $usersIds = array();
      foreach ($users as $user) {
         if($user->{Model_Users::COLUMN_MAIL} != null){
            $usersIds[$user->{Model_Users::COLUMN_NAME} ." ".$user->{Model_Users::COLUMN_SURNAME}
               .' ('.$user->{Model_Users::COLUMN_USERNAME}.') - '.$user->{Model_Users::COLUMN_MAIL}
            ] = $user->{Model_Users::COLUMN_ID};
         }
      }
      $elemNotifyUsers->setOptions($usersIds);
      $elemNotifyUsers->setMultiple();
      $elemNotifyUsers->html()->setAttrib('size', 4);
      if (isset($settings[self::PARAM_NOTIFY_USERS])) {
         $elemNotifyUsers->setValues($settings[self::PARAM_NOTIFY_USERS]);
      }

      $form->addElement($elemNotifyUsers, $grpNotify);

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scrollT'] = $form->scrollT->getValues();
//         $settings['scroll'] = $form->scroll->getValues();
         $settings[self::PARAM_ORDER_BY_DATE]= $form->order->getValues();
         $settings[self::PARAM_CAPCHA_SEC] = (int)$form->capchatime->getValues();
         // oznámení
         $settings[self::PARAM_NOTIFY_EMAILS] = $form->notifyEMails->getValues();
         $settings[self::PARAM_NOTIFY_USERS] = $form->notifyUsers->getValues();
      }
   }

}
