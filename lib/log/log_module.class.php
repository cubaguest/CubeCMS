<?php
/**
 * Třída logování událostí z modulů
 * Třída pro zaznámenávání událostí do úložiště z modulů
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro zaznamenávání událostí systému
 */
class Log_Module extends Log {

   public static function msg($message, $module, $categorName = null) {
      self::save('m: '.$module.' cat: '.$categorName.' user: '.Auth::getUserName().' msg: '.$message, 'module');
   }
}
?>
