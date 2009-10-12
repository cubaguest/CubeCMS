<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define('VVE_TPL_FILE_IMAGE', 'image');

function vve_get_tpl_file($file, $type){
   switch ($type) {
      case 'image':
      default:
         if(file_exists(AppCore::getAppWebDir().Template::face().Template::IMAGES_DIR.DIRECTORY_SEPARATOR.$file)){
            return Template::face().Template::IMAGES_DIR.DIRECTORY_SEPARATOR.$file;
         } else {
            return Template::IMAGES_DIR.DIRECTORY_SEPARATOR.$file;
         }
         break;
   }
}

?>
