<?php
/**
 * Třída pro vytvoření objektu tlačítka nástroje (tool) pro toolbox verze 2
 *
 * @copyright  	Copyright (c) 2008-2012 Jakub Matas
 * @version    	$Id: $ VVE 7.18 $Revision: $
 * @author        $Author: $ $Date: $
 *                $LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Nástroje tlačítka toolboxu
 */

class Template_Toolbox2_Tool_Button extends Template_Toolbox2_Tool implements Template_Toolbox2_Tool_Interface {
   protected $data = array();
   
   public function setData($data)
   {
      $this->data = $data;
      return $this;
   }
   
   public function getData()
   {
      return $this->data;
   }
   
   public function addData($key, $value)
   {
      $this->data[$key] = $value;
      return $this;
   }
   
   public function removeData($key)
   {
      if(isset($this->data[$key])){
         unset($this->data[$key]);
      }
      return $this;
   }
}
