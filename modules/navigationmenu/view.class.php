<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class NavigationMenu_View extends View {
   public function mainView() {
      $this->template()->addTplFile('list.phtml');
   }

   public function addView() {
      $this->template()->addTplFile('edit.phtml');
   }

   public function editView() {
      $this->addView();
   }

   public static function listView() {
      $tpl = new Template_ModuleStatic(new Url_Link_ModuleRequest(), 'navigationmenu');
      $tpl->addTplFile('list_only.phtml');
      $tpl->renderTemplate();
   }
}

?>