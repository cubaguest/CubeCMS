<?php
/**
 * Třída Core Modulu pro obsluhu chybové se zakázaným přístupem
 *
 * @copyright  	Copyright (c) 2008-2012 Jakub Matas Cube Studio
 * @version    	$Id: $ VVE 7.18 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu chybové stránky pro omezení přístupu
 */
class Module_UnauthorizedAccess extends Module_ErrPage {
   public function __construct(Category_Core $moduleCategory)
   {
      parent::__construct($moduleCategory);
      $this->setCode('401');
   }
}
