<?php
class Contact_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('edit', "edit", 'edit', 'edit/');
      $this->addRoute('normal', null, 'main', null);
   }
}
?>