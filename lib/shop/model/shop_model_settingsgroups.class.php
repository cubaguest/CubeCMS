<?php

/**
 * Třída shop_model_settingsgroups
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id:  $ VVE 7.3 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
 */
class Shop_Model_SettingsGroups extends Model_ORM {
   protected function _initTable()
   {
      $this->setTableName('shop_settings_groups', 't_art');
   }
}
?>
