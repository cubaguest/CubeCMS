<?php
/**
 * Třída Core Modulu pro obsluhu mapy stránek
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ VVE 6.2 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro obsluhu mapy stránek
 */
class Module_Cookieinfo extends Module_Core {
   public function runController() {
      $this->template()->text = Text_Model::getText($this->category()->getId(), Text_Model::TEXT_MAIN_KEY);
   }

   public function runView() {
      $this->template()->addFile('tpl://cookieinfo.phtml');
   }
}
