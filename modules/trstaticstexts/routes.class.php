<?php
/**
 * Třída obsluhující cesty modulu
 *
 */

class TrStaticsTexts_Routes extends Routes {
   protected function  initRoutes() {
      $this->addRoute('translateModule', "trmodule/::module::/::locale::", 'translateModule','trmodule/{module}/{locale}/');
      $this->addRoute('translateLibs', "trlibs/::locale::", 'translateLibs','trlibs/{locale}/');
   }

}

?>