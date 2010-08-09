<?php
class TitlePage_Routes extends Routes {

   function initRoutes() {
      $this->addRoute('addSelectItem', "add", 'addSelectItem','add/');
      $this->addRoute('addItem', "add/::type::", 'addItem','add/{type}/');
      $this->addRoute('editItem', "edit/id-::id::", 'editItem','edit/id-{id}/');
      $this->addRoute('editList', "edit", 'editList','edit/');
      $this->addRoute('changePosition', "changepos.php", 'changePosition','changepos.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('deleteItem', "deleteitem.php", 'deleteItem','deleteitem.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('getArticlesList', "getartlist.php", 'getArticlesList','getartlist.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('normal', null, 'main', null);
	}
}

?>