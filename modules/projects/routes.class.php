<?php
class Projects_Routes extends Routes {
   function initRoutes() {
      $this->addRoute('addSection', "add-section/", 'addSection', "add-section/");
      
      $this->addRoute('addProject', "::seckey::/add-project/", 'addProject', "{seckey}/add-project/");
      $this->addRoute('editSection', "::seckey::/edit", 'editSection','{seckey}/edit/');
      
      $this->addRoute('editProject', "::seckey::/::prkey::/edit/", 'editProject','{seckey}/{prkey}/edit/');
      
      $this->addRoute('project', "::seckey::/::prkey::/", 'project', '{seckey}/{prkey}/');
      
      $this->addRoute('section', "::seckey::", 'section', '{seckey}');
      // cesty z fotogalerie
      $this->registerModule('photogalery', array('itemKey' => array('seckey', 'prkey')));
	}
}

?>