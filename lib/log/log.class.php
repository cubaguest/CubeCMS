<?php
/**
 * Třída logování událostí
 * Třída pro zaznámenávání událostí do úložiště
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id: $ VVE 6.4 $Revision:  $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro zaznamenávání událostí systému
 */
class Log {
   const LOG_DIR = 'logs';

   public static function msg($message, $file = null, $user = null, $targetLog = 'core') {
      $str = null;
      if($file != null) $str .= 'source: '.$file;
      if($user != null) $str .= ' user: '.$user;
      $str .= ' msg: '.$message;
      self::save($str, $targetLog);
   }
   
   protected static function save($str, $log) {
      file_put_contents(AppCore::getAppWebDir().self::LOG_DIR.DIRECTORY_SEPARATOR.strtolower($log).'-'.  date('Y-m').'.log', date('c').' '.$str."\n", FILE_APPEND);
   }
}
?>
