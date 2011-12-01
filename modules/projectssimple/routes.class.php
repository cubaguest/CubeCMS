<?php
class ProjectsSimple_Routes extends Projects_Routes {
   function initRoutes() {
      parent::initRoutes();
      // remove not allowed routes
      $this->removeRoute('addSection')->removeRoute('editSection')->removeRoute('section');
      // recreate routes
      $this->addRoute('addProject', "add-project/", 'addProject', "add-project/");
      $this->addRoute('editProject', "::prkey::/edit/", 'editProject','{prkey}/edit/');
      $this->addRoute('project', "::prkey::/", 'project', '{prkey}/');
      // tohle patří přesunout do nadřazené třídy
      $this->addRoute('editText', "edit-text/", 'editText','edit-text/');
      // cesty z fotogalerie
      $this->registerModule('photogalery', array('itemKey' => array('prkey')));
	}
}

?>