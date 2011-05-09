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

interface Component_Uploader_Handler_Interface {
   public function save($path);
   public function getName();
   public function getSize();
}
?>
