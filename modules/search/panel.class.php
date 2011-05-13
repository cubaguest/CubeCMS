<?php
class Search_Panel extends Panel {
	public function panelController() {
	}

	public function panelView() {
      $model = new Search_Model_Api();
      $this->template()->apis = $model->getApis($this->category()->getId());
		$this->template()->addTplFile("panel.phtml");
      if(isset ($_GET['q'])){
         $this->template()->searchString = htmlspecialchars($_GET['q']);
      }
	}
}
?>