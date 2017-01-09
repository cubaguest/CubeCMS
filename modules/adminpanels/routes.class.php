<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class AdminPanels_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('add', "add", 'add', 'add/');
      // ajax pro načtení panelů
      $this->addRoute('getPanels', "panels.json", 'getPanels', 'panels.json');
      // seznam panelů v html (připraveno pro tabulku)
      $this->addRoute('getListPanels', "panels.html", 'getListPanels', 'panels.html');

      $this->addRoute('getPanelInfo', "panelinfo.json", 'getPanelInfo', 'panelinfo.json');
      $this->addRoute('edit', "panel-::id::/edit/", 'edit','panel-{id}/edit/');
      $this->addRoute('settings', "panel-::id::/settings/", 'panelSettings','panel-{id}/settings/');
      $this->addRoute('normal', null, 'main', null);
	}
}