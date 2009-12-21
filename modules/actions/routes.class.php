<?php
class Actions_Routes extends Routes {
   function initRoutes() {
//      $this->addRoute('top', "top", 'top', 'top/');
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('edit', "::articlekey::/edit", 'edit','{articlekey}/edit/');
      $this->addRoute('detail', "::articlekey::", 'show','{articlekey}/');

      $this->addRoute('normal', null, 'main', null);
   }
}

?>