<?php
class Courses_Routes extends Routes {
   const FEEDS = true; // podle tohohle se generují feedy
   const FEED_FILE = '{type}.xml';

   function initRoutes() {
      $this->addRoute('addCourse', "add", 'addCourse', "add/");
      $this->addRoute('listAllCourses', "list", 'listAllCourses', "list/");
      $this->addRoute('editCourse', "::urlkey::/edit", 'editCourse','{urlkey}/edit/');
      $this->addRoute('detailCourse', "::urlkey::", 'showCourse','{urlkey}/');
      $this->addRoute('registrationsCourse', "::urlkey::/registrations", 'registrationsCourse','{urlkey}/registrations/');
      // exporty
      $this->addRoute('exportFeed', "(?P<type>(?:rss)|(?:atom)).xml", 'exportFeed', self::FEED_FILE);
      $this->addRoute('detailExport', "::urlkey::\.(?P<output>(?:pdf)|(?:xml)|(?:html))", 'exportArticle','{urlkey}.{output}');


      $this->addRoute('placesList', "places.json", 'placesList','places.json');



      $this->addRoute('normal', null, 'main', null);
	}
}

?>