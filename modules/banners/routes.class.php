<?php
class Banners_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('clicksList', 'clicks.json', 'clicksList', 'clicks.json', 'XHR_Respond_VVEAPI');
      $this->addRoute('moveBanner', 'move.php', 'moveBanner', 'move.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('edit', "banner-::id::/edit/", 'edit', 'banner-{id}/edit/');
      $this->addRoute('show', "banner-::id::/", 'show', 'banner-{id}/');
      $this->addRoute('add', "add", 'add', 'add/');
   }
}
?>