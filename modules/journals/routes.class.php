<?php
class Journals_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('add', "add", 'add', "add/");
      $this->addRoute('showLast', "last/", 'showLast', 'last/');
      $this->addRoute('edit', "(?P<year>[0-9]+)/(?P<number>[0-9]+)/edit/", 'edit', '{year}/{number}/edit/');
      $this->addRoute('show', "(?P<year>[0-9]+)/(?P<number>[0-9]+)/", 'show', '{year}/{number}/');
      $this->addRoute('showYear', "(?P<year>[0-9]+)/", 'main', '{year}/');
	}
}

?>