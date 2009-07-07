<?php
class Actions_Panel extends Panel {
	/**
	 * Počet novinek v panelu
	 * @var integer
	 */
	const PARAM_NUMBER_OF_NEWS = 'scrollpanel';

	public function panelController() {
	}
	
	public function panelView() {
      $this->template()->addTplFile("panel.phtml");
		$this->template()->addCssFile("style.css");

      $model = new Actions_Model_List($this->sys());
      $this->template()->actions = $model->getSelectedListActions(0,
         $this->sys()->module()->getParam(self::PARAM_NUMBER_OF_NEWS, 1));

	}
}
?>