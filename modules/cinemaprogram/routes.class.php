<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class CinemaProgram_Routes extends Routes {
	function initRoutes() {
      // přidání filmu
      $this->addRoute('add', "add", 'add', null);

      // list nadcházejiících filmů v xml
      $this->addRoute('feturedlist', "featuredlist.(?P<output>(?:xml))", 'featuredList', 'featuredlist.{output}');

      $this->addRoute('detailExport', "movie-(?P<id>[0-9]+)\.(?P<output>(?:pdf)|(?:xml))", 'showData','movie-{id}.{output}');
      // list s právě probíhajícím filmem
      $this->addRoute('current', "current.(?P<output>(?:xml))", 'currentMovie', 'current.{output}');
      // uprava
      $this->addRoute('detail', "(?P<id>[0-9]+)-[a-zA-Z0-9_-]+", 'detail','{id}-{name}/');
      $this->addRoute('edit', "edit-(?P<id>[0-9]+)", 'edit','edit-{id}/');
      //standard
      $this->addRoute('normaldate',  "(?P<day>[0-3]?[0-9]{1})/(?P<month>[0-1]?[0-9]{1})/(?P<year>[0-9]{4})", 'main','{day}/{month}/{year}/');
      $this->addRoute('normal', null, 'main', null);
	}
}

?>