<?php
/**
 * Třída slouží pro validaci časů a datumů
 *
 * @copyright  	Copyright (c) 2008-2010 Jakub Matas
 * @version    	$Id$ VVE 6.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro validaci časů a datumů
 */
class Validator_Time extends Validator {
   const TIME = 1;
   const DATE = 2;

   private $type = self::TIME;

   public function  __construct($values = null, $type = self::TIME) {
      parent::__construct($values);
      $this->type = $type;
   }

   public function  validate() {
      switch ($this->type) {
         case self::DATE:
            $this->isValid = $this->checkDate($this->values);
            break;
         case self::TIME:
         default:
            $this->isValid = $this->checkTime($this->values);
            break;
      }
   }
   /**
	 * Metoda testuje zadané datum, pokud je v pořádku je vráceno v ISO podobe YYYY-MM-DD
	 * @param string -- kontrolované datum
	 * @param boolean(option) -- true pokud má být vrácen timestamp
	 * @return bool -- true pokud je validní
	 */
   private function checkDate() {
      $regs = array();
		if (preg_match("/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4})/", $this->values, $regs) AND checkdate ($regs[2], $regs[1], $regs[3]) AND Locales::getLang() != 'en'){
//			$date = $regs[2].self::ISO_DATE_SEPARATOR.$regs[1].self::ISO_DATE_SEPARATOR.$regs[3];
//			$time = mktime(null,null,null,$regs[2],$regs[1],$regs[3]);
         $this->isValid = true;
		} else if (preg_match ("/([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})/",$this->values, $regs) AND checkdate ($regs[1], $regs[2], $regs[3])){
//			$date = $regs[1].self::ISO_DATE_SEPARATOR.$regs[2].self::ISO_DATE_SEPARATOR.$regs[3];
//			$time = mktime(null,null,null,$regs[1],$regs[2],$regs[3]);
         $this->isValid = true;
		}
      $this->isValid = false;
	}

	/**
	 * Metoda zkontroluje zadaný čas (hh:mm)
	 * @return string -- vrací čas v podobě HH:MM nebo false
	 */
   private function checkTime() {
		$nums = explode(self::TIME_SEAPRATOR, $this->values, 2);
//		Jestli se jedná o hodinu
		if(!isset($nums[0]) OR $nums[0] < 0 OR $nums[0] > 23){
         $this->isValid = false;
		}
//		Jestli se jedná o minuty
		if(!isset($nums[1]) OR $nums[1] < 0 OR $nums[1] > 59){
         $this->isValid = false;
		}
      $this->isValid = true;
	}
}
?>