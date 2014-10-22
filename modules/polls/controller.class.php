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

      $model = new Polls_Model();

      $formVoteMulti = $this->createFormVoteMulti();
      $formVoteSingle = $this->createFormVoteSingle();

      $votedPolls = array();
      if(isset ($_COOKIE[VVE_SESSION_NAME.'_polls']) AND VVE_DEBUG_LEVEL < 2 AND !Auth::isAdmin()){
         $votedPolls = explode('|', $_COOKIE[VVE_SESSION_NAME.'_polls']);
      }
      $cokieExpire = time()+60*60*24*$this->category()->getParam('cookiedays', self::DEFAULT_COOKIE_DAYS);

      // hlasování s jednou volbou
      if($formVoteSingle->isValid()) {
         $poll = $model->record($formVoteSingle->id_poll->getValues());
         $this->view()->id = $formVoteSingle->id_poll->getValues();
         if($poll == false) {
            $this->errMsg()->addMessage($this->tr('Hlasování v neexistující anketě'));
         } else if(in_array($formVoteSingle->id_poll->getValues(), $votedPolls)){
            $this->errMsg()->addMessage($this->tr('V této anketě jste již hlasoval'));
         } else if($formVoteSingle->answer->getValues() == null){
            $this->errMsg()->addMessage($this->tr('Nebyl odeslán hlas'));
         } else {
            $votedPolls[] = $formVoteSingle->id_poll->getValues();
            if(VVE_DEBUG_LEVEL < 2){
               setcookie(VVE_SESSION_NAME.'_polls', implode('|', $votedPolls),$cokieExpire,'/');
            }

            $data = unserialize($poll->{Polls_Model::COLUMN_DATA});
            $data[$formVoteSingle->answer->getValues()]['count']++;
            $poll->{Polls_Model::COLUMN_DATA} = serialize($data);
            $poll->{Polls_Model::COLUMN_VOTES} = $poll->{Polls_Model::COLUMN_VOTES} + 1;
            $model->save($poll);
            
            $this->infoMsg()->addMessage($this->tr('Váš hlas byl přijat'));
            $this->link()->reload();
         }
      } else if($formVoteMulti->isValid()) {
      // hlasování s více volbami
         $this->view()->id = $formVoteMulti->id_poll->getValues();
         $poll = $model->record($formVoteMulti->id_poll->getValues());
         $selected = $formVoteMulti->answer->getValues();
         if($poll == false) {
            $this->errMsg()->addMessage($this->tr('Hlasování v neexistující anketě'));
         } else if(in_array ($formVoteMulti->id_poll->getValues(), $votedPolls)){
            $this->errMsg()->addMessage($this->tr('V této anketě jste již hlasoval'));
         } else if($selected == null){
            $this->errMsg()->addMessage($this->tr('Nebyla vybrána žádná volba'));
         } else {
            $votedPolls[] = $formVoteMulti->id_poll->getValues();
            if(VVE_DEBUG_LEVEL < 2){
               setcookie(VVE_SESSION_NAME.'_polls', implode('|', $votedPolls),$cokieExpire,'/');
            }
            $data = unserialize($poll->{Polls_Model::COLUMN_DATA});
            foreach ($selected as $id => $dat) {
               $data[$id]['count']++;
            }
            $poll->{Polls_Model::COLUMN_DATA} = serialize($data);
            $poll->{Polls_Model::COLUMN_VOTES} = $poll->{Polls_Model::COLUMN_VOTES} + 1;
            $model->save($poll);
            $this->infoMsg()->addMessage($this->tr('Váš hlas byl přijat'));
            $this->link()->reload();
         }
      }

      if($this->category()->getRights()->isWritable()) {
         $formDelete = new Form('poll_');
         $elemId = new Form_Element_Hidden('id');
         $formDelete->addElement($elemId);
         $elemSub = new Form_Element_Submit('delete', $this->tr('Smazat anketu'));
         $formDelete->addElement($elemSub);

         if($formDelete->isValid()) {
            $model->delete($formDelete->id->getValues());
            
            $this->infoMsg()->addMessage($this->tr('Anketa byla smazána'));
            $this->link()->rmParam()->reload();
         }
         $this->view()->formDelete = $formDelete;
      }

      // pokud je XHR vyskoč, není nutné načítat ankety a další blbosti
      if(Url_Request::isXHRRequest()) return true;

      $this->view()->formmulti = $formVoteMulti;
      $this->view()->formsingle = $formVoteSingle;
      $this->view()->votedPolls = $votedPolls;

      $model = new Polls_Model();
      $model->where(Polls_Model::COLUMN_ID_CAT.' = :idc', array('idc' => $this->category()->getId()))
         ->order(array(Polls_Model::COLUMN_DATE => Model_ORM::ORDER_DESC));
      
      $scrollComponent = new Component_Scroll();
      $scrollComponent->setConfig(Component_Scroll::CONFIG_CNT_ALL_RECORDS, $model->count());

      $scrollComponent->setConfig(Component_Scroll::CONFIG_RECORDS_ON_PAGE, 
         $this->category()->getParam('scroll', self::DEFAULT_POLLS_ON_PAGE));

      $this->view()->polls = $model->limit($scrollComponent->getStartRecord(), $scrollComponent->getRecordsOnPage())->records();

      $this->view()->scrollComp = $scrollComponent;
   }

   public function pollDataController(){
      $this->checkReadableRights();
      $id = $this->getRequestParam('id', null);
      if($id != null){
         $model = new Polls_Model();
         $this->view()->poll = $model->record($id);
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
         $answersCount = $form->answerCount->getValues();
         $votes = 0; 
         foreach ($answers as $key => $answer) {
            $count = 0;
            if(isset ($answersCount[$key]) && $answersCount[$key] != null){
               $count = (int)$answersCount[$key];
               $votes += (int)$answersCount[$key];
            }
            array_push($data, array('answer' => $answer, 'count' => $count));
         }

         $model = new Polls_Model();
         $poll = $model->newRecord();
         $poll->{Polls_Model::COLUMN_ID_CAT} = $this->category()->getId();
         $poll->{Polls_Model::COLUMN_QUESTION} = $form->question->getValues();
         $poll->{Polls_Model::COLUMN_IS_MULTI} = $form->multianswer->getValues();
         $poll->{Polls_Model::COLUMN_ACTIVE} = (bool)$form->active->getValues();
         $poll->{Polls_Model::COLUMN_DATA} = serialize($data);
         $poll->{Polls_Model::COLUMN_VOTES} = $votes;
         
         $model->save($poll);
         
         $this->infoMsg()->addMessage($this->tr('Anketa byla uložena'));
         $this->link()->route()->reload();
      }

      $this->view()->form = $form;
      $this->view()->linkBack = $this->link()->route();
   }

   public function editController() {
      $this->checkWritebleRights();
      $model = new Polls_Model();
      $poll = $model->record($this->getRequest('id'));

      if($poll == false) return false;

      $form = $this->createForm(true);

      $form->question->setValues($poll->{Polls_Model::COLUMN_QUESTION});
      $form->multianswer->setValues($poll->{Polls_Model::COLUMN_IS_MULTI});
      $form->active->setValues($poll->{Polls_Model::COLUMN_ACTIVE});

      $tmpArr = $tmpCountArr = array();
      $data = unserialize($poll->{Polls_Model::COLUMN_DATA});
      foreach ($data as $key => $answer) {
         $tmpArr[] = $answer['answer'];
         $tmpCountArr[] = $answer['count'];
      }
      $form->answer->setValues($tmpArr);
      $form->answerCount->setValues($tmpCountArr);
      unset ($tmpArr);

      if($form->isValid()) {
         $clear = $form->clear->getValues();
         $answers = $form->answer->getValues();
         $answersCount = $form->answerCount->getValues();
         $newData = array();
         $votes = 0;
         foreach ($answers as $key => $answer) {
            if($clear === true) {
               $count = 0;
               $votes = 0;
            } else {
               $count = (int)$answersCount[$key];
               $votes += (int)$answersCount[$key];
            }
            array_push($newData, array('answer' => $answer, 'count' => $count));
         }
         
         $poll->{Polls_Model::COLUMN_QUESTION} = $form->question->getValues();
         $poll->{Polls_Model::COLUMN_IS_MULTI} = $form->multianswer->getValues();
         $poll->{Polls_Model::COLUMN_DATA} = serialize($newData);
         $poll->{Polls_Model::COLUMN_VOTES} = $votes;
         
         $model->save($poll);

         $this->infoMsg()->addMessage($this->tr('Anketa byla uložena'));
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

      $elemQuestion = new Form_Element_Text('question',$this->tr('Otázka'));
//      $elemQuestion->setLangs();
      $elemQuestion->addValidation(new Form_Validator_NotEmpty(null, Locales::getDefaultLang()));
      $form->addElement($elemQuestion);

      $elemMulti = new Form_Element_Checkbox('multianswer',$this->tr('Více odpovědí'));
      $form->addElement($elemMulti);

      $elemActive = new Form_Element_Checkbox('active',$this->tr('Hlasování povoleno'));
      $elemActive->setValues(true);
      $form->addElement($elemActive);

      if($elemClear === true) {
         $elemAnswerClear = new Form_Element_Checkbox('clear', $this->tr('Vynulovat'));
         $form->addElement($elemAnswerClear);
      }

      $elemAnswer = new Form_Element_Text('answer',$this->tr('Odpověd'));
      $elemAnswer->addValidation(new Form_Validator_NotEmpty());
      $elemAnswer->setDimensional();
      $form->addElement($elemAnswer);
      
      $elemAnswerCount = new Form_Element_Text('answerCount',$this->tr('počet odpovědí'));
      $elemAnswerCount->addValidation(new Form_Validator_IsNumber());
      $elemAnswerCount->setDimensional();
      $elemAnswerCount->setValues(0);
      $form->addElement($elemAnswerCount);

      $elemSubmit = new Form_Element_SaveCancel('send');
      $form->addElement($elemSubmit);

      if($form->isSend() AND $form->send->getValues() == false ){
         $this->infoMsg()->addMessage($this->tr('Změny byly zrušeny'));
         $this->link()->route()->reload();
      }
      
      return $form;
   }

   public static function createFormVoteSingle() {
      $tr = new Translator_Module('polls');
      $formVoteSingle = new Form('vote_');

      $elemAnswer = new Form_Element_Radio('answer');
      $elemAnswer->setCheckOptions(false);
      $formVoteSingle->addElement($elemAnswer);

      $elemId = new Form_Element_Hidden('id_poll');
      $formVoteSingle->addElement($elemId);

      $elemSubmit = new Form_Element_Submit('vote', $tr->tr('Hlasovat'));
      $formVoteSingle->addElement($elemSubmit);

      return $formVoteSingle;
   }

   public static function createFormVoteMulti() {
      $tr = new Translator_Module('polls');
      $formVoteMulti = new Form('vote_multi_');

      $elemAnswer = new Form_Element_Checkbox('answer');
      $elemAnswer->setDimensional();
      $formVoteMulti->addElement($elemAnswer);


      $elemId = new Form_Element_Hidden('id_poll');
      $formVoteMulti->addElement($elemId);

      
      $elemSubmit = new Form_Element_Submit('vote', $tr->tr('Hlasovat'));
      $formVoteMulti->addElement($elemSubmit);

      return $formVoteMulti;
   }

   /**
    * Metoda pro nastavení modulu
    */
   protected function settings(&$settings,Form &$form) {
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