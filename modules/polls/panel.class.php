<?php
class Polls_Panel extends Panel {
   const DEFAULT_NUM_POLLS = 1;

	public function panelController() {
      $model = new Polls_Model_Detail();
      $poll = $model->getPolls($this->category()->getId(), 0, $this->panelObj()->getParam('num', self::DEFAULT_NUM_POLLS));
      $this->template()->poll = $poll->fetch();
      if($this->template()->poll === false) return false;

      $this->template()->voted = false;
      $votedPolls = array();
      if(isset ($_COOKIE[VVE_SESSION_NAME.'_polls'])){
         $votedPolls = unserialize($_COOKIE[VVE_SESSION_NAME.'_polls']);
         if(isset ($votedPolls[$this->template()->poll->{Polls_Model_Detail::COL_ID}])
            OR $this->template()->poll->{Polls_Model_Detail::COL_ACTIVE} != true){
            $this->template()->voted = true;
         }
      }
      if($this->template()->voted == false){
         if($this->template()->poll->{Polls_Model_Detail::COL_IS_MULTI} == true){
            $this->template()->formmulti = $this->createFormVoteMulti();
         } else {
            $this->template()->formsingle = $this->createFormVoteSingle();
         }
      }
   }

   public function panelView() {
		$this->template()->addTplFile("panel.phtml");
		$this->template()->addCssFile("style.css");
	}

   private function createFormVoteSingle() {
      $formVoteSingle = new Form('vote_');
      $formVoteSingle->setAction($this->link());

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
      $formVoteMulti->setAction($this->link());

      $elemAnswer = new Form_Element_Checkbox('answer');
      $elemAnswer->setDimensional();
      $formVoteMulti->addElement($elemAnswer);


      $elemId = new Form_Element_Hidden('id_poll');
      $formVoteMulti->addElement($elemId);

      $elemSubmit = new Form_Element_Submit('vote', $this->_('Hlasovat'));
      $formVoteMulti->addElement($elemSubmit);

      return $formVoteMulti;
   }
}
?>