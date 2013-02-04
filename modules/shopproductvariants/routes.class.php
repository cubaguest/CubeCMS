<?php
/**
 * Třída obsluhující cesty modulu
 *
 */
class ShopProductVariants_Routes extends Routes {
	function initRoutes() {
      $this->addRoute('attrGroupsList', "attr-groups-list.php", 'attrGroupsList','attr-groups-list.php');
      $this->addRoute('attrList', "attr-list.php", 'attrList','attr-list.php');
      
      $this->addRoute('editAttrGroup', "edit-attr-group.php", 'editAttrGroup', 'edit-attr-group.php', 'XHR_Respond_VVEAPI');
      $this->addRoute('editAttr', "edit-attr.php", 'editAttr', 'edit-attr.php', 'XHR_Respond_VVEAPI');
	}
}

?>