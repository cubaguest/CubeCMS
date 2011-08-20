<?php
class Polls_Panel extends Panel {
   const DEFAULT_NUM_POLLS = 1;

	public function panelController() {
      $model = new Polls_Model();
      $model->where(Polls_Model::COLUMN_ID_CAT.' = :idc', array('idc' => $this->category()->getId()))
         ->order(array(Polls_Model::COLUMN_DATE => Model_ORM::ORDER_DESC))
//         ->limit(0, $this->panelObj()->getParam('num', self::DEFAULT_NUM_POLLS))
            ;
      $this->template()->poll = $model->record();
      if($this->template()->poll === false) return false;

      $this->template()->voted = false;
      $votedPolls = array();
      if(isset ($_COOKIE[VVE_SESSION_NAME.'_polls']) AND !Auth::isAdmin()){
         $votedPolls = explode('|', $_COOKIE[VVE_SESSION_NAME.'_polls']);
         if(in_array($this->template()->poll->{Polls_Model::COLUMN_ID}, $votedPolls)
            OR $this->template()->poll->{Polls_Model::COLUMN_ACTIVE} != true){
            $this->template()->voted = true;
         }
      }
      if($this->template()->voted == false){
         if($this->template()->poll->{Polls_Model::COLUMN_IS_MULTI} == true){
            $this->template()->formmulti = Polls_Controller::createFormVoteMulti();
         } else {
            $this->template()->formsingle = Polls_Controller::createFormVoteSingle();
         }
      }
   }

   public function panelView() {
		$this->template()->addFile("tpl://panel.phtml");
	}
}
?>