<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class Panels_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('add', "add", 'add', null);
      // ajax pro načtení panelů
      $this->addRoute('getPanels', "panels.json", 'getPanels', 'panels.json');
      $this->addRoute('getPanelInfo', "panelinfo.json", 'getPanelInfo', 'panelinfo.json');
      $this->addRoute('edit', "panel-::id::/edit/", 'edit','panel-{id}/edit');
//      $this->addRoute('detail', "category-::categoryid::/", 'show','category-{categoryid}');

      $this->addRoute('normal', null, 'main', null);
	}
}

?>