<?php
/**
 * Description of TimeValidator
 * Třída slouží pro validaci časů a datumů
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract 		Třída pro validaci časů a datumů prvků
 */
class Validator_Time extends Validator {
  	/**
	 * Metoda testuje zadané datum, pokud je v pořádku je vráceno v ISO podobe YYYY-MM-DD
	 * @param string -- kontrolované datum
	 * @param boolean(option) -- true pokud má být vrácen timestamp
	 * @return string -- vrací datum v ISO podobě nebo false
	 */
	public function checkDate($date, $timestam = false) {
      $regs = array();
		if (preg_match("/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4})/",$date, $regs) AND checkdate ($regs[2], $regs[1], $regs[3]) AND Locale::getLang() != 'en'){
			$date = $regs[2].self::ISO_DATE_SEPARATOR.$regs[1].self::ISO_DATE_SEPARATOR.$regs[3];
			$time = mktime(null,null,null,$regs[2],$regs[1],$regs[3]);
		} else if (preg_match ("/([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})/",$date, $regs) AND checkdate ($regs[1], $regs[2], $regs[3])){
			$date = $regs[1].self::ISO_DATE_SEPARATOR.$regs[2].self::ISO_DATE_SEPARATOR.$regs[3];
			$time = mktime(null,null,null,$regs[1],$regs[2],$regs[3]);
		} else {
			$date = $time = false;
		}
		if($timestam){
			return $time;
		} else {
			return $date;
		}
	}

	/**
	 * Metoda zkontroluje zadaný čas (hh:mm)
	 *
	 * @param string -- kontrolovaný čas
	 * @param boolean(option) -- true pokud má být vrácen timestamp
	 * @return string -- vrací čas v podobě HH:MM nebo false
	 */
	public function checkTime($time, $timestamp = false) {
		$nums = explode(self::TIME_SEAPRATOR, $time, 2);
		$allOk = true;
//		Jestli se jedná o hodinu
		if(!isset($nums[0]) OR $nums[0] < 0 OR $nums[0] > 23){
			 $allOk = false;
		}
//		Jestli se jedná o minuty
		if(!isset($nums[1]) OR $nums[1] < 0 OR $nums[1] > 59){
			 $allOk = false;
		}
		if($allOk){
			if(!$timestamp){
				return $nums[0].self::TIME_SEAPRATOR.$nums[1];
			} else {
				return mktime($nums[0], $nums[1]);
			}
		} else {
			return false;
		}
	}
}
?>