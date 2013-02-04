<?php
class CustomMenu_Routes extends Routes {

   function initRoutes() {
      $this->addRoute('edit', 'edit.php', 'edit', 'edit.php', 'XHR_Respond_VVEAPI');
   }
}
