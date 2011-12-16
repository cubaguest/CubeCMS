<?php
/**
 * Interface pro třídy obsluhy souborů.
 *
 * @copyright  	Copyright (c) 2011 Jakub Matas
 * @version    	$Id: $ CubeCMS 7.7 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Interface pro třídy souborů
 */

interface File_Interface {
   
   public function __construct($name = null, $path = null);

   /* info o souboru*/

   public function setName($name);
   
   public function getName();
   
   public function setPath($path);
   
   public function getPath();
   
   public function getSize();
   
   public function getChangeTime();
   
   public function getMimeType();
   
   /**
    * Metoda vrací příponu souboru
    * @return string
    */
   public function getExtension();
   
   public function setRights($mode);
   
   public function __toString();
   
   /* Obsah souboru podle typu */
   
   public function setData($data);
   
   public function getData();
   
   public function save();

   
   /* Modoty pro práci se souborem */
   
   public function exist();
   
   public function copy($path);
   
   public function rename($newName);
   
   public function move($dstDir);
   
   public function send();
   

}
?>
