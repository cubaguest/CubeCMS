<?php

/**
 * Třída pro upload souborů na server
 * Třída slouží pro nahrávání souborů na server (kromě IE je podporováno multiple
 * a progressbar) implementace AJAX uploadu od Andrew Valums
 *
 * @copyright  	Copyright (c) 2011 Jakub Matas Js Copyright (c) 2010 Andrew Valums
 * @version    	$Id: $ VVE 7.3.0 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro nahrávání souborů
 * @see           http://valums.com/ajax-upload/
 */
class Component_Uploader_Handler_Form extends Component_Uploader_Handler implements Component_Uploader_Handler_Interface {

   /**
    * Save the file to the specified path
    * @return boolean TRUE on success
    */
   public function save($path)
   {
      if (!move_uploaded_file($_FILES[$this->requestName]['tmp_name'], $path)) {
         return false;
      }
      return true;
   }

   public function getName()
   {
      return $_FILES[$this->requestName]['name'];
   }

   public function getSize()
   {
      return $_FILES[$this->requestName]['size'];
   }

}
?>
