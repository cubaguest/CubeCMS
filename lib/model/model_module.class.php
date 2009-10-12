<?php
/**
 * Třída Modelu pro práci s moduly.
 * Třída, která umožňuje pracovet s modelem modulů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.2 $Revision$
 * @author			$Author$ $Date$
 *						$LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro vytvoření modelu pro práci s moduly
 */

class Model_Module extends Model {

/**
 * Metoda načte moduly
 * @return array -- pole s moduly
 */
   public function getModules() {
      $handle = opendir(AppCore::getAppWebDir().AppCore::MODULES_DIR);
      if (!$handle) {
         trigger_error(_('Nepodařilo se otevřít adresář s moduly'));
      }

      $directories = array();
      while (false !== ($file = readdir($handle))) {
         if ($file != "." AND $file != ".." AND is_dir(AppCore::getAppWebDir()
               .AppCore::MODULES_DIR.DIRECTORY_SEPARATOR.$file)) {
            array_push($directories, $file);
         }
      }
      closedir($handle);
      return $directories;
   }
}
?>