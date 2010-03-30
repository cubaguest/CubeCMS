<?php
class NewsLetter_Panel extends Panel {
	
	public function panelController() {
      $form = $this->createRegisterForm();
      $this->template()->form = $form;
   }
	
	public function panelView() {
//      $model = new Polls_Model_Detail();
//      $poll = $model->getPolls($this->category()->getId(), 0, $this->panelObj()->getParam('num', self::DEFAULT_NUM_POLLS));
//      $this->template()->poll = $poll->fetch();
//      if($this->template()->poll === false) return false;
//      $this->template()->voted = false;
//      $votedPolls = array();
//      if(isset ($_COOKIE[VVE_SESSION_NAME.'_polls'])){
//         $votedPolls = unserialize($_COOKIE[VVE_SESSION_NAME.'_polls']);
//         if(isset ($votedPolls[$this->template()->poll->{Polls_Model_Detail::COL_ID}])){
//            $this->template()->voted = true;
//         }
//      }

		$this->template()->addTplFile("panel.phtml");
	}

   private function createRegisterForm() {
      $form = new Form('regmail_');

      $elemMail = new Form_Element_Text('mail', $this->_('E-mail'));
      $elemMail->addValidation(new Form_Validator_NotEmpty());
      $elemMail->addValidation(new Form_Validator_Email());
      $form->addElement($elemMail);

      $elemSend = new Form_Element_Submit('send', $this->_('Registrovat'));
      $form->addElement($elemSend);
      $form->setAction($this->link());

      return $form;
   }
}
?>