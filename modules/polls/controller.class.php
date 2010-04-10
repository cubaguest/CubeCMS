<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 */

class Polls_Controller extends Controller {
   const DEFAULT_POLLS_ON_PAGE = 5;
   const DEFAULT_COOKIE_DAYS = 30;

   /**
    * Kontroler pro zobrazení textu
    */
   public function mainController() {
      //		Kontrola práv
      $this->checkReadableRights();

      $model = new Polls_Model_Detail();

      $formVoteMulti = $this->createFormVoteMulti();
      $formVoteSingle = $this->createFormVoteSingle();

      $votedPolls = array();
      if(isset ($_COOKIE[VVE_SESSION_NAME.'_polls'])){
         $votedPolls = unserialize($_COOKIE[VVE_SESSION_NAME.'_polls']);
      }
      $cokieExpire = time()+60*60*24*$this->category()->getParam('cookiedays', self::DEFAULT_COOKIE_DAYS);

      // hlasování s jednou volbou
      if($formVoteSingle->isValid()) {
         $poll = $model->getPoll($formVoteSingle->id_poll->getValues());
         $this->view()->id = $formVoteSingle->id_poll->getValues();
         if($poll == false) {
            $this->errMsg()->addMessage($this->_('Hlasování v neexistující anketě'));
         } else if(isset ($votedPolls[$formVoteSingle->id_poll->getValues()])){
            $this->errMsg()->addMessage($this->_('V této anketě jste již hlasoval'));
         } else {
            $votedPolls[$formVoteSingle->id_poll->getValues()] = true;
            setcookie(VVE_SESSION_NAME.'_polls', serialize($votedPolls),$cokieExpire,'/');

            $data = unserialize($poll->{Polls_Model_Detail::COL_DATA});
            $data[$formVoteSingle->answer->getValues()]['count']++;
            $model->savePollData($formVoteSingle->id_poll->getValues(), serialize($data));
            $this->infoMsg()->addMessage($this->_('Váš hlas byl přijat'));
            $this->link()->reload();
         }
      } else
      // hlasování s více volbami
         if($formVoteMulti->isValid()) {
            $this->view()->id = $formVoteMulti->id_poll->getValues();
            $poll = $model->getPoll($formVoteMulti->id_poll->getValues());
            $selected = $formVoteMulti->answer->getValues();
            if($poll == false) {
               $this->errMsg()->addMessage($this->_('Hlasování v neexistující anketě'));
            } else if(isset ($votedPolls[$formVoteMulti->id_poll->getValues()])){
               $this->errMsg()->addMessage($this->_('V této anketě jste již hlasoval'));
            } else if($selected == null){
               $this->errMsg()->addMessage($this->_('Nebyla vybrána žádná volba'));
            } else {
               $votedPolls[$formVoteMulti->id_poll->getValues()] = true;
               setcookie(VVE_SESSION_NAME.'_polls', serialize($votedPolls),$cokieExpire,'/');
               $data = unserialize($poll->{Polls_Model_Detail::COL_DATA});
               foreach ($selected as $id => $dat) {
                  $data[$id]['count']++;
               }
               $model->savePollData($formVoteMulti->id_poll->getValues(), serialize($data));
               $this->infoMsg()->addMessage($this->_('Váš hlas byl přijat'));
               $this->link()->reload();
            }
         }

      if($this->category()->getRights()->isWritable()) {
         $delForm = new Form('poll_');
         $elemId = new Form_Element_Hidden('id');
         $delForm->addElement($elemId);
         $elemSub = new Form_Element_SubmitImage('delete');
         $delForm->addElement($elemSub);

         if($delForm->isValid()) {
            $model->deletePoll($delForm->id->getValues());
            $this->infoMsg()->addMessage($this->_('Anketa byla smazána'));
            $this->link()->rmParam()->reload();
         }
      }

      // pokud je XHR vyskoč
      if(Url_Request::isXHRRequest()) return true;

      $this->view()->formmulti = $formVoteMulti;
      $this->view()->formsingle = $formVoteSingle;
      $this->view()->votedPolls = $votedPolls;

      $scrollComponent = new Component_Scroll();
      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS,
              $model->getCountPolls($this->category()->getId()));

      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE,
              $this->category()->getParam('scroll', self::DEFAULT_POLLS_ON_PAGE));

      $this->view()->polls = $model->getPolls($this->category()->getId(),
              $scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage());

      $this->view()->scrollComp = $scrollComponent;
   }

   public function pollDataController(){
      $this->checkReadableRights();
      $id = $this->getRequestParam('id', null);
      if($id != null){
         $model = new Polls_Model_Detail();
         $this->view()->poll = $model->getPoll($id);
      } else {
         $this->view()->poll = null;
      }
   }

   public function addController() {
      $this->checkWritebleRights();

      $form = $this->createForm();

      if($form->isValid()) {
         $data = array();

         $answers = $form->answer->getValues();
         foreach ($answers as $answer) {
            array_push($data, array('answer' => $answer, 'count' => 0));
         }

         $model = new Polls_Model_Detail();
         $model->savePoll($this->category()->getId(), $form->question->getValues(),
                 $form->multianswer->getValues(), serialize($data));

         $this->infoMsg()->addMessage($this->_('Anketa byla uložena'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
      $this->view()->linkBack = $this->link()->route();
   }

   public function editController() {
      $this->checkWritebleRights();
      $model = new Polls_Model_Detail();
      $poll = $model->getPoll($this->getRequest('id'));

      if($poll == false) return false;

      $form = $this->createForm(true);

      $form->question->setValues($poll->{Polls_Model_Detail::COL_QUESTION});
      $form->multianswer->setValues($poll->{Polls_Model_Detail::COL_IS_MULTI});
      $form->active->setValues($poll->{Polls_Model_Detail::COL_ACTIVE});

      $tmpArr = array();
      $data = unserialize($poll->{Polls_Model_Detail::COL_DATA});
      foreach ($data as $key => $answer) {
         $tmpArr[] = $answer['answer'];
      }
      $form->answer->setValues($tmpArr);
      unset ($tmpArr);


      if($form->isValid()) {
         $clear = $form->clear->getValues();
         $answers = $form->answer->getValues();
         $newData = array();
         $votes = 0;
         foreach ($answers as $key => $answer) {
            if($clear === true) {
               $count = 0;
               $votes = 0;
            } else {
               $count = $data[$key]['count'];
               $votes += $data[$key]['count'];
            }
            array_push($newData, array('answer' => $answer, 'count' => $count));
         }

         $model->savePoll($this->category()->getId(), $form->question->getValues(),
                 $form->multianswer->getValues(), serialize($newData), $votes, $poll->{Polls_Model_Detail::COL_ID});

         $this->infoMsg()->addMessage($this->_('Anketa byla uložena'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
      $this->view()->linkBack = $this->link()->route();
   }

   /**
    * Metoda vytvoří formulář
    * @return Form
    */
   private function createForm($elemClear = false) {
      $form = new Form('poll_');

      $elemQuestion = new Form_Element_Text('question',$this->_('Otázka'));
//      $elemQuestion->setLangs();
      $elemQuestion->addValidation(new Form_Validator_NotEmpty(null, Locale::getDefaultLang()));
      $form->addElement($elemQuestion);

      $elemMulti = new Form_Element_Checkbox('multianswer',$this->_('Více odpovědí'));
      $form->addElement($elemMulti);

      $elemActive = new Form_Element_Checkbox('active',$this->_('Hlasování povoleno'));
      $elemActive->setValues(true);
      $form->addElement($elemActive);

      if($elemClear === true) {
         $elemAnswerClear = new Form_Element_Checkbox('clear', $this->_('Vynulovat'));
         $form->addElement($elemAnswerClear);
      }

      $elemAnswer = new Form_Element_Text('answer',$this->_('Odpověd'));
      $elemAnswer->addValidation(new Form_Validator_NotEmpty());
      $elemAnswer->setDimensional();
      $form->addElement($elemAnswer);

      $elemSubmit = new Form_Element_Submit('send','Odeslat');
      $form->addElement($elemSubmit);

      return $form;
   }

   private function createFormVoteSingle() {
      $formVoteSingle = new Form('vote_');

      $elemAnswer = new Form_Element_Radio('answer');
      $formVoteSingle->addElement($elemAnswer);

      $elemId = new Form_Element_Hidden('id_poll');
      $formVoteSingle->addElement($elemId);

      $elemSubmit = new Form_Element_Submit('vote', $this->_('Hlasovat'));
      $formVoteSingle->addElement($elemSubmit);

      return $formVoteSingle;
   }

   private function createFormVoteMulti() {
      $formVoteMulti = new Form('vote_multi_');

      $elemAnswer = new Form_Element_Checkbox('answer');
      $elemAnswer->setDimensional();
      $formVoteMulti->addElement($elemAnswer);


      $elemId = new Form_Element_Hidden('id_poll');
      $formVoteMulti->addElement($elemId);

      $elemSubmit = new Form_Element_Submit('vote', $this->_('Hlasovat'));
      $formVoteMulti->addElement($elemSubmit);

      return $formVoteMulti;
   }

   /**
    * Metoda pro nastavení modulu
    */
   public static function settingsController(&$settings,Form &$form) {
      $elemScroll = new Form_Element_Text('scroll', 'Počet anket na stránku');
      $elemScroll->setSubLabel('Výchozí: '.self::DEFAULT_POLLS_ON_PAGE.' anket');
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll,'basic');

      if(isset($settings['scroll'])) {
         $form->scroll->setValues($settings['scroll']);
      }

      $elemScroll = new Form_Element_Text('cookiedays', 'Délka platnosti cookie');
      $elemScroll->setSubLabel('Počet dnů platnosti cookie s údaji o hlasování klienta. Výchozí: '.self::DEFAULT_COOKIE_DAYS.' dní');
      $elemScroll->addValidation(new Form_Validator_IsNumber());
      $form->addElement($elemScroll,'basic');

      if(isset($settings['cookiedays'])) {
         $form->cookiedays->setValues($settings['cookiedays']);
      }
      // znovu protože mohl být už jednou validován bez těchto hodnot
      if($form->isValid()) {
         $settings['scroll'] = $form->scroll->getValues();
         $settings['cookiedays'] = $form->cookiedays->getValues();
      }
   }
}

?>