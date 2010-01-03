<?php
class Actions_Routes extends Routes {
   function initRoutes() {
      // přidání akce
      $this->addRoute('add', "add", 'add', "add/");
      // archiv akcí
      $this->addRoute('archive', "archiv", 'archive', "archiv/");
      // akce podle datumů
      $this->addRoute('normaldate',  "(?:(?P<day>[0-3]?[0-9]{1})/(?P<month>[0-1]?[0-9]{1})/(?P<year>[0-9]{4}))?", 'main','{day}/{month}/{year}/');
      // editace akce
      $this->addRoute('edit', "::urlkey::/edit", 'edit','{urlkey}/edit/');
      // detail akce
      $this->addRoute('detail', "::urlkey::", 'show','{urlkey}/');
      // list akcí
      $this->addRoute('normal', null, 'main', null);
   }
}

?>