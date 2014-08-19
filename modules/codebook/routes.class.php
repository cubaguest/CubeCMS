<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class CodeBook_Routes extends Routes {
	function initRoutes()
   {
      $this->addRoute('add');
      $this->addRoute('edit', "edit/::id::/");
      $this->addRoute('sort', "sort.php",'sort', "sort.php", "XHR_Respond_VVEAPI");
   }
}
