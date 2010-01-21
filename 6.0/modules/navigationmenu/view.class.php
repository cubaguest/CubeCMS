<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class NavigationMenu_View extends View {
   public function mainView() {
   }

   public function showView() {
   }

   public static function listView() {
      $tpl = new Template_ModuleStatic(new Url_Link_ModuleRequest(), 'navigationmenu');
      $tpl->addTplFile('list_only.phtml');
      $tpl->addCssFile('style-nav.css');
      $tpl->renderTemplate();
   }
}

?>