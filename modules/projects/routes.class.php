<?php
class Projects_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('checkProjectUrlkey', 'c-url-pr.php', 'checkProjectUrlkey', 'c-url-pr.php', 'XHR_Respond_VVEAPI');
   
      $this->addRoute('addSection', "add-section/", 'addSection', "add-section/");
      
      $this->addRoute('addProject', "::seckey::/add-project/", 'addProject', "{seckey}/add-project/");
      $this->addRoute('editSection', "::seckey::/edit", 'editSection','{seckey}/edit/');
      $this->addRoute('sortSections', "sections-sort/", 'sortSections', '/sections-sort/');
      $this->addRoute('editText', "edit-text/", 'editText','edit-text/');
      $this->addRoute('editProject', "::seckey::/::prkey::/edit/", 'editProject','{seckey}/{prkey}/edit/');
      $this->addRoute('sortProjects', "::seckey::/sort/", 'sortProjects', '{seckey}/sort/');
      
      $this->addRoute('project', "::seckey::/::prkey::/", 'project', '{seckey}/{prkey}/');
      
      $this->addRoute('section', "::seckey::/", 'section', '{seckey}/');
      // cesty z fotogalerie
      $this->registerModule('photogalery', array('itemKey' => array('seckey', 'prkey')));
	}
}
