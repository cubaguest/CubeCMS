<?php
class News_Panel extends Panel {
	public function panelController() {
	}

	public function panelView() {
      $artM = new Articles_Model_List();
      $this->template()->newArticles = $artM->getList($this->category()->getId(),0,
              $this->category()->getModule()->getParam('scrollpanel', 3));
      $this->template()->rssLink = $this->link()->route('export', array('type' => 'rss'));
		$this->template()->addTplFile("panel.phtml");
	}
}
?>