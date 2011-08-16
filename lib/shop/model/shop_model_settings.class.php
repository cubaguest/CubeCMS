<?php

/**
 * Třída shop_model
 *
 * @copyright  	Copyright (c) 2008-2011 Jakub Matas
 * @version    	$Id:  $ VVE 7.3 $Revision: $
 * @author        $Author: $ $Date: $
 * @author        cuba
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída 
 */
class Shop_Model_Settings extends Model_ORM {
   protected function _initTable()
   {
      $this->setTableName('shop_settings', 't_art');
   }
}
?>
