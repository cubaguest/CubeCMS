<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class FAQ_Routes extends Routes {
	function initRoutes()
   {
      $this->addRoute('addQuestion');
      $this->addRoute('sortQuestions');
      $this->addRoute('editQuestion', "edit-question/::id::/");
   }
}
