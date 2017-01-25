<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class ShopProductVariants_Routes extends Routes {
	function initRoutes() {
      // úprava skupiny
      $this->addRoute('editGroup', 'edit-group.php', 'editGroup', 'edit-group.php', 'XHR_Respond_VVEAPI');
      // úprava hodnoty
      $this->addRoute('editVariant', 'edit-variant.php', 'editVariant', 'edit-variant.php', 'XHR_Respond_VVEAPI');
      // list položek
      $this->addRoute('groupsList', "groups-list.php", 'groupsList', 'groups-list.php');
      $this->addRoute('variantsList', "group-(?P<idg>[0-9]+)/variants-list.php", 'variantsList', 'group-{idg}/variants-list.php');
	}
}

