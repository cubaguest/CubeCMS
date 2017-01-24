<?php
class AdminCustomMenu_Routes extends Routes {

   function initRoutes() {
      $this->addRoute('editMenu', "edit-menu-::id::/");
      $this->addRoute('editMenuItem', "edit-item-::id::/");
      $this->addRoute('moveItem', 'moveItem.php', 'moveItem', 'moveItem.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('getTree', 'getTree.php', 'getTree', 'getTree.php', 'XHR_Respond_VVEAPI');
   }
}
