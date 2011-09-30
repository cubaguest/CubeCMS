<?php
class ArticlesWGal_Routes extends Articles_Routes {
	function initRoutes() {
      parent::initRoutes();
      $this->registerModule('photogalery', array('itemKey' => 'urlkey'));
	}
}

?>