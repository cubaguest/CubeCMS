<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Advice_Routes extends Routes {
   protected function initRoutes()
   {
      $this->addRoute('addQuestion', "addquestion/", 'addQuestion', "addquestion/");
      $this->addRoute('editQuestion', "admin/question/(?P<id>[0-9]+)/", 'editQuestion', "admin/question/{id}/");
      $this->addRoute('answers', "admin/answers/", 'answers', "admin/answers/");
      $this->addRoute('editCats', "admin/edit-cats/", 'editCats', "admin/edit-cats/");
      $this->addRoute('stats', "admin/stats/", 'stats', "admin/stats/");
      $this->addRoute('generateStats', "admin/stats/generate.php", 'generateStats', "admin/stats/generate.php");
      // only after import texts
      $this->addRoute('repairTexts', "admin/repair/", 'repairTexts', "admin/repair/");
      
//      $this->addRoute('addCat', "admin/add-cats/", 'addCat', "admin/add-cats/");
      
      $this->addRoute('questionsList', "questions.json", 'questionsList','questions.json');
      $this->addRoute('changeAttribute', "cha.php", 'changeAttribute', "cha.php", 'XHR_Respond_VVEAPI');
   }
}

?>