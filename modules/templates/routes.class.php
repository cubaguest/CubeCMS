<?php
class Templates_Routes extends Routes {
   const ROUTE_EDIT = 'edit';

   function initRoutes() {
      $this->addRoute('content', "content-{id}.html", 'content', 'content-{id}.html');
      
      $this->addRoute('add', "add", 'add', "add/");

      $this->addRoute(self::ROUTE_EDIT, "edit/tpl-(?P<id>[0-9]+)", self::ROUTE_EDIT,'edit/tpl-{id}/');
      $this->addRoute('preview', "preview-(?P<id>[0-9]+)", 'preview' ,'preview-{id}/');

      $this->addRoute('normal', null, 'main', null);
	}
}