<?php
class Products_Panel extends Panel {

	public function panelController() {
	}
	
	public function panelView() {
      $model = new Products_Model_List($this->sys());
      $this->template()->products = $model->getListProducts();

      $jQuery = new  JsPlugin_JQuery();
      $this->template()->addJsPlugin($jQuery);

      $this->template()->addTplFile("panel.phtml");
      $this->template()->addCssFile("style.css");

	}
}
?>