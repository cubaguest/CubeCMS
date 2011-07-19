<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Forum_Controller extends Controller {
   const PARAM_CAPCHA_SEC = 'c_s';


   const DEFAULT_NUM_ON_PAGE = 20;
   const MIN_SEC_FOR_HUMAN = 15;

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

         $elemSubDel = new Form_Element_SubmitImage('delete', $this->_('Smazat'));
         $delForm->addElement($elemSubDel);

         if($delForm->isValid()){
            $model->delete($delForm->id->getValues());
            $this->infoMsg()->addMessage($this->_('Téma a všechny jeho příspěvky bylo smazáno'));
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
//            .'(ISNULL(`'.Forum_Model_Posts::COLUMN_CENSORED.'`)  OR `'.Forum_Model_Posts::COLUMN_CENSORED.'` = 0 )', array('idc' => $this->category()->getId()));
//      }
      $model->columns(array('*','post_count' => 'COUNT(`'.Forum_Model_Posts::COLUMN_ID.'`)'))
         ->groupBy(array(Forum_Model_Topics::COLUMN_ID))
         ->join(Forum_Model_Topics::COLUMN_ID, 'Forum_Model_Posts', Forum_Model_Posts::COLUMN_ID_TOPIC, 
            array(Forum_Model_Posts::COLUMN_CREATED_BY, 
               'sort_date' => 'IFNULL(MAX(`'.Forum_Model_Posts::COLUMN_DATE_ADD.'`), `'.Forum_Model_Topics::COLUMN_DATE_ADD.'`)',
               'last_post_date' => 'MAX(`'.Forum_Model_Posts::COLUMN_DATE_ADD.'`)' ))
         ->order(array(
            'sort_date' => Model_ORM::ORDER_DESC,
            Forum_Model_Topics::COLUMN_DATE_ADD => Model_ORM::ORDER_DESC,
            ));
//      Debug::log($model->getSQLQuery());

      $this->view()->topics = $model->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage())->records();
      $this->view()->scrollComp = $scrollComponent;
      
      $this->editTopic();
   }
   
   public function addTopicController()
   {
      $this->checkWritebleRights();
      $this->editTopic();
   }
   
   public function addPostController()
   {
      $this->checkWritebleRights();
      $this->editPost($this->getRequest('id'));
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
   
   public function editPostController()
   {
      $this->checkWritebleRights();
      $modelP = new Forum_Model_Posts();
      $post = $modelP->record($this->getRequest('idp'));
      
      if($post != false && 
         ($this->rights()->isControll() 
         || $this->rights()->isWritable() && $post->{Forum_Model_Posts::COLUMN_ID_USER} == Auth::getUserId() )){
         $this->editPost($this->getRequest('id'), $post);
      } else {
         return false;
      }
      $this->view()->post = $post;
   }

   private function editTopic(Model_ORM_Record $record = null)
   {
      $form = new Form('topic_');

      $model = new Forum_Model_Topics();
      if($record == null){
         $record = $model->newRecord();
      }
      
      $elemName = new Form_Element_Text('name', $this->_('Téma'));
      $elemName->addValidation(new Form_Validator_NotEmpty());
      $elemName->addValidation(new Form_Validator_MaxLength(200));
      $form->addElement($elemName);

      $elemText = new Form_Element_TextArea('text', $this->_('Text'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->addValidation(new Form_Validator_MinLength(10));
      $elemText->addValidation(new Form_Validator_MaxLength(10000));
      $purifier = new Form_Filter_HTMLPurify();
      $purifier->setConfig('CSS.AllowedProperties', 'text-decoration');
      $purifier->setConfig('AutoFormat.RemoveSpansWithoutAttributes', true);
      $elemText->addFilter($purifier);
      $form->addElement($elemText);
      
      if($this->rights()->isControll()){
         $elemSolved = new Form_Element_Checkbox('solved', $this->tr('Vyřešeno'));
         $form->addElement($elemSolved);   
         $elemClosed = new Form_Element_Checkbox('closed', $this->tr('Zavřeno'));
         $form->addElement($elemClosed);   
      }
      
      $elemNick = new Form_Element_Text('author', $this->_('Autor'));
      $elemNick->addValidation(new Form_Validator_NotEmpty());
      $elemNick->addValidation(new Form_Validator_MaxLength(100));
      $form->addElement($elemNick);

      $elemEmail = new Form_Element_Text('email', $this->_('E-mail'));
      $elemEmail->addValidation(new Form_Validator_NotEmpty());
      $elemEmail->addValidation(new Form_Validator_Email());
      $elemEmail->addValidation(new Form_Validator_MaxLength(50));
      $form->addElement($elemEmail);

      if(!Auth::isLogin()){
         $elemCaptcha = new Form_Element_Captcha('captcha');
         $form->addElement($elemCaptcha);
      }
      
      $elemSubmit = new Form_Element_SaveCancel('send');
      $elemSubmit->setCancelConfirm(false);
      $form->addElement($elemSubmit);

      $capchaTime = $this->category()->getParam(self::PARAM_CAPCHA_SEC, self::MIN_SEC_FOR_HUMAN);
      // u přihlášených vypneme chapchu
      if(Auth::isLogin()){
         $form->author->setValues(Auth::getUserName());
         $form->email->setValues(Auth::getUserMail());
      } 
      
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
         
         $model->save($record);
         $this->infoMsg()->addMessage($this->_('Téma bylo uloženo'));
         if($record->isNew()){
            $this->link()->route()->reload();
         } else {
            $this->link()->route('showTopic')->reload();
         }
      }
      $this->view()->form = $form;
   }
   
   private function editPost($idTopic, Model_ORM_Record $post = null)
   {
      $form = new Form('post_');

      $model = new Forum_Model_Posts();
      $record = $model->newRecord();
      
      $elemName = new Form_Element_Text('name', $this->_('Předmět'));
//      $elemName->addValidation(new Form_Validator_NotEmpty());
      $elemName->addValidation(new Form_Validator_MaxLength(200));
      $form->addElement($elemName);

      $elemText = new Form_Element_TextArea('text', $this->_('Text'));
      $elemText->addValidation(new Form_Validator_NotEmpty());
      $elemText->addValidation(new Form_Validator_MinLength(10));
      $elemText->addValidation(new Form_Validator_MaxLength(10000));
      $purifier = new Form_Filter_HTMLPurify();
      $purifier->setConfig('CSS.AllowedProperties', 'text-decoration');
      $purifier->setConfig('AutoFormat.RemoveSpansWithoutAttributes', true);
      $elemText->addFilter($purifier);
      $form->addElement($elemText);
      
      $elemNick = new Form_Element_Text('author', $this->_('Autor'));
      $elemNick->addValidation(new Form_Validator_NotEmpty());
      $elemNick->addValidation(new Form_Validator_MaxLength(100));
      $form->addElement($elemNick);

      $elemEmail = new Form_Element_Text('email', $this->_('E-mail'));
      $elemEmail->addValidation(new Form_Validator_NotEmpty());
      $elemEmail->addValidation(new Form_Validator_Email());
      $elemEmail->addValidation(new Form_Validator_MaxLength(50));
      $form->addElement($elemEmail);
      
      if(!Auth::isLogin()){
         $elemCaptcha = new Form_Element_Captcha('captcha');
         $form->addElement($elemCaptcha);
      }

      $elemSubmit = new Form_Element_SaveCancel('send');
      $elemSubmit->setCancelConfirm(false);
      $form->addElement($elemSubmit);

      // u přihlášených vypneme chapchu
      if(Auth::isLogin() && $post == null){
         $form->author->setValues(Auth::getUserName());
         $form->email->setValues(Auth::getUserMail());
         $capchaTime = 0;
      }
      
      if($post != null){
         $record = $post;
         // add info to form
         $form->name->setValues($post->{Forum_Model_Posts::COLUMN_NAME});
         $form->text->setValues($post->{Forum_Model_Posts::COLUMN_TEXT});
         $form->author->setValues($post->{Forum_Model_Posts::COLUMN_CREATED_BY});
         $form->email->setValues($post->{Forum_Model_Posts::COLUMN_EMAIL});
      }

      if($form->isSend()){
         if($form->send->getValues() == false){
            $this->link()->route('showTopic')->reload();
            return;
         }
         $this->view()->showForm = true;
      }

      if($form->isValid()){
         $record->{Forum_Model_Posts::COLUMN_NAME} = $form->name->getValues();
         $record->{Forum_Model_Posts::COLUMN_CREATED_BY} = $form->author->getValues();
         $record->{Forum_Model_Posts::COLUMN_TEXT} = $form->text->getValues();
         $record->{Forum_Model_Posts::COLUMN_TEXT_CLEAR} = strip_tags($form->text->getValues());
         $record->{Forum_Model_Posts::COLUMN_EMAIL} = $form->email->getValues();
         if($record->isNew()){
            $record->{Forum_Model_Posts::COLUMN_ID_TOPIC} = $idTopic;
            if(Auth::isLogin()){
               $record->{Forum_Model_Posts::COLUMN_ID_USER} = Auth::getUserId();
            }
            if($this->category()->getRights()->isControll()){
               $record->{Forum_Model_Posts::COLUMN_CREATED_BY_MODERATOR} = true;
            }
            $record->{Forum_Model_Posts::COLUMN_IP} = $_SERVER['REMOTE_ADDR'];
            
         }
         
         $model->save($record);
         $this->infoMsg()->addMessage($this->_('Příspěvek byl uložen'));
         $this->link()->route('showTopic')->reload();
      }
      $this->view()->form = $form;
   }

   public function showTopicController()
   {
      $this->checkReadableRights();
      
      $modelT = new Forum_Model_Topics();
      $modelP = new Forum_Model_Posts();
      
      $topic = $modelT->record($this->getRequest('id'));
      if($topic == false){return false;}
      
      if($this->rights()->isControll()){
         $formDel = new Form('post_delete');
         $eId = new Form_Element_Hidden('id');
         $formDel->addElement($eId);
         
         $eDel = new Form_Element_SubmitImage('delete', $this->tr('Smazat'));
         $formDel->addElement($eDel);
         if($formDel->isValid()){
            $modelP->delete($formDel->id->getValues());
            $this->infoMsg()->addMessage($this->tr('Příspěvek byl smazán'));
            $this->link()->reload();
         }
         $this->view()->formPostDelete = $formDel;
         
         $formCensore = new Form('post_censore');
         $eId = new Form_Element_Hidden('id');
         $formCensore->addElement($eId);
         
         $eCensore = new Form_Element_SubmitImage('change', $this->tr('Změnit cenzůru'));
         $formCensore->addElement($eCensore);
         if($formCensore->isValid()){
            $post = $modelP->record($formCensore->id->getValues());
            
            if($post != false){
               $post->{Forum_Model_Posts::COLUMN_CENSORED} = (int)!(bool)$post->{Forum_Model_Posts::COLUMN_CENSORED};
               $modelP->save($post);
            }
            $this->infoMsg()->addMessage($this->tr('Cenzůra byla změněna'));
            $this->link()->reload();
         }
         $this->view()->formPostCensore = $formCensore;
         
         $formTopicDel = new Form('topic_delete');
         $eId = new Form_Element_Hidden('id');
         $eId->setValues($topic->{Forum_Model_Topics::COLUMN_ID});
         $formTopicDel->addElement($eId);
         
         $eDel = new Form_Element_SubmitImage('delete', $this->tr('Smazat'));
         $formTopicDel->addElement($eDel);
         if($formTopicDel->isValid()){
            $modelT->delete($formTopicDel->id->getValues());
            $this->infoMsg()->addMessage($this->tr('Téma bylo smazáno'));
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
         $modelP->where(Forum_Model_Posts::COLUMN_ID_TOPIC.' = :idt', array('idt' => $topic->{Forum_Model_Posts::COLUMN_ID_TOPIC}));
//      } else {
//         $modelP->where(Forum_Model_Posts::COLUMN_ID_TOPIC.' = :idt AND '.Forum_Model_Posts::COLUMN_CENSORED.' = 0', 
//            array('idt' => $topic->{Forum_Model_Posts::COLUMN_ID_TOPIC}));
//      }
      $modelP->order(array(Forum_Model_Posts::COLUMN_DATE_ADD => Model_ORM::ORDER_ASC));
      
      $scrollComponent = new Component_Scroll();
      $scrollComponent->setConfig('page_param', 'p');
      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $modelP->count());
      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              $this->category()->getParam('scroll', self::DEFAULT_NUM_ON_PAGE));

      $this->view()->topic = $topic;
      $this->view()->posts = $modelP->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage())->records();
      $this->view()->scrollComp = $scrollComponent;
   }

   public function rssTopicController()
   {
      $this->checkReadableRights();
      $modelT = new Forum_Model_Topics();
      $modelP = new Forum_Model_Posts();
      
      $topic = $modelT->record($this->getRequest('id'));
      if($topic == false){return false;}
      
      $modelP->where(Forum_Model_Posts::COLUMN_ID_TOPIC.' = :idt', array('idt' => $topic->{Forum_Model_Posts::COLUMN_ID_TOPIC}));
      $modelP->order(array(Forum_Model_Posts::COLUMN_DATE_ADD => Model_ORM::ORDER_ASC));
      
      $this->view()->topic = $topic;
      $this->view()->posts = $modelP->limit(0, VVE_FEED_NUM)->records();
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
      
      $elemScroll = new Form_Element_Text('scroll', $this->tr('Počet příspěvků na stránku'));
      $elemScroll->setSubLabel($this->tr(sprintf('Výchozí: %s příspěvků', self::DEFAULT_NUM_ON_PAGE)));
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll,'basic');

      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }

      $fGrpNewItem = $form->addGroup('newitem', $this->tr('Přidání příspěvku'));

//      $elemWEditor = new Form_Element_Checkbox('weditor', $this->tr('Zapnout wisiwig editor'));
//      $elemWEditor->setValues(true);
//      $elemWEditor->setSubLabel($this->tr('Pokud je wisiwig editor vypnut, jsou automaticky odstraněnny všechny html tagy.'));
//      $form->addElement($elemWEditor, $fGrpNewItem);
//      if(isset($settings[self::PARAM_WISIWIG_EDITOR])) {
//         $form->weditor->setValues($settings[self::PARAM_WISIWIG_EDITOR]);
//      }

      $elemCapchaSec = new Form_Element_Text('capchatime', $this->tr('Po kolika sekundách lze formulář odeslat'));
      $elemCapchaSec->setSubLabel($this->tr(sprintf('Výchozí: %s sekund (obrana proti spamu). Pokud je 0, kontrola je vypnuta.', self::MIN_SEC_FOR_HUMAN)));
      $elemCapchaSec->addValidation(new Form_Validator_IsNumber(null, Form_Validator_IsNumber::TYPE_INT));
      $form->addElement($elemCapchaSec, $fGrpNewItem);
      if(isset($settings[self::PARAM_CAPCHA_SEC])) {
         $form->capchatime->setValues($settings[self::PARAM_CAPCHA_SEC]);
      }

      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scrollT'] = $form->scrollT->getValues();
         $settings['scroll'] = $form->scroll->getValues();
//         $settings['maxtextchars'] = $form->maxtextchars->getValues();
//         $settings['mintextchars'] = $form->mintextchars->getValues();
//         $settings[self::PARAM_WISIWIG_EDITOR] = $form->weditor->getValues();
         $settings[self::PARAM_CAPCHA_SEC] = (int)$form->capchatime->getValues();
      }
   }
}

?>