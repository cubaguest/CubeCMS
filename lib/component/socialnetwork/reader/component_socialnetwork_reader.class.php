<?php
/** 
 * Třída Komponenty přístup k sociálním sítím
 *
 * @copyright  	Copyright (c) 2008-2012 Jakub Matas
 * @version    	$Id: $ VVE 7.9 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída Komponenty přístup k sociálním sítím
 */

class Component_SocialNetwork_Reader extends Component_SocialNetwork {
   /**
    * Konstruktor třídy, spouští metodu init();
    */
   function __construct($runOnly = false) 
   {
      parent::__construct(true); // nemá žádný vystup přes url adresy
   }
}
?>