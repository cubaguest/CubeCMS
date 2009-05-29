<?php
/**
 * Třída pro práci s šablonami modulu.
 * Třida obsahuje vrstvu mezi šablonovacím systémem a samotným pohledem (viewrem).
 * Umožňuje všechny základní operace při volbě a plnění šablony a jejímu zobrazení.
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: template.class.php 576 2009-04-15 10:52:59Z jakub $ VVE3.9.4 $Revision: 576 $
 * @author        $Author: jakub $ $Date: 2009-04-15 10:52:59 +0000 (St, 15 dub 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-04-15 10:52:59 +0000 (St, 15 dub 2009) $
 * @abstract 		Třída pro obsluhu šablony
 */

class TplVar {

   private $name = null;
   
   private $value = null;

   public function  __construct($name, $value = null) {
      $this->name = $name;
      $this->value = $value;
   }

   public function get() {
      return $this->value;
   }

   public function  __toString() {
      return (string)$this->value;
   }
}
?>