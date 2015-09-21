<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class UserQuestions_Routes extends Routes {
	function initRoutes()
   {
      $this->addRoute('addQuestion');
      $this->addRoute('editQuestion', "edit-question/::id::/");
   }
}
