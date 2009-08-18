<?php
class Products_Panel extends Panel {
   private static $links = array();

	public function panelController() {
	}
	
	public function panelView() {
      $model = new Products_Model_List($this->sys());
      $this->template()->products = $model->getListProducts();

      $jQuery = new  JsPlugin_JQuery();
      $this->template()->addJsPlugin($jQuery);

      $this->template()->addTplFile("panel.phtml");
      $this->template()->addCssFile("style.css");

      // odkaz do hlavičky
      array_push(self::$links, $this->link());
      $this->template()->setPVar("productsUrl", self::$links);
	}
}
?>