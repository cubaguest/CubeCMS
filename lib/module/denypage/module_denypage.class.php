<?php
/**
 * Třída Core Modulu pro obsluhu chybové stránky
 *
 * @copyright  	Copyright (c) 2008-2012 Jakub Matas Cube Studio
 * @version    	$Id: $ VVE 7.8 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu chybové stránky pro omezení přístupu
 */
class Module_DenyPage extends Module_ErrPage {
   public function __construct(Category_Core $moduleCategory)
   {
      parent::__construct($moduleCategory);
      $this->setCode('403');
   }
}

?>
