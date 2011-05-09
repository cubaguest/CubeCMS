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
class Component_Uploader_Handler_XHR extends Component_Uploader_Handler implements Component_Uploader_Handler_Interface {

   /**
    * Save the file to the specified path
    * @return boolean TRUE on success
    */
   function save($path)
   {
      $input = fopen("php://input", "r");
      $temp = tmpfile();
      $realSize = stream_copy_to_stream($input, $temp);
      fclose($input);

      if ($realSize != $this->getSize()) {
         return false;
      }

      $target = fopen($path, "w");
      fseek($temp, 0, SEEK_SET);
      stream_copy_to_stream($temp, $target);
      fclose($target);

      return true;
   }

   function getName()
   {
      return $_GET[$this->requestName];
   }

   function getSize()
   {
      if (isset($_SERVER["CONTENT_LENGTH"])) {
         return (int) $_SERVER["CONTENT_LENGTH"];
      } else {
         throw new Exception('Getting content length is not supported.');
      }
   }

}
?>
