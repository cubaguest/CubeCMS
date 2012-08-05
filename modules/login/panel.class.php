<?php
class Login_Panel extends Panel {
	
   protected $userRecord;
   
	public function panelController() {
	   if(Auth::isLogin()){
	      $m = new Model_Users();
	      $this->userRecord = $m->record(Auth::getUserId());
	   }
	}
	
	public function panelView() {
      $this->template()->currentLink = (string)new Url_Link();
      $this->template()->user = $this->userRecord;
      $this->template()->addTplFile("panel.phtml");
	}
}
?>