<?php
class Actions_Panel extends Panel {
	public function panelController() {
	}
	
	public function panelView() {
      $this->template()->addTplFile("panel.phtml", 'actions');

      $model = new Actions_Model_List();
      $this->template()->actions = $model->getCurrentActions($this->category()->getId());
	}
}
?>