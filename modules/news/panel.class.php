<?php
class News_Panel extends Panel {
	public function panelController() {
	}

	public function panelView() {
      $artM = new Articles_Model_List();
      $this->template()->newArticles = $artM->getList($this->category()->getId(), 0, 3);
		$this->template()->addTplFile("panel.phtml");
	}
}
?>