<?php
/**
 * Třída pro vytvoření a obsluhu pohledů
 *
 */

class ReservationView extends View {
	public function mainView() {
		$this->template()->addTpl("ReservationForm.tpl");
//		$this->template()->addTpl("scroll.tpl");
//		$this->template()->addCss("pokus.css");
//		$this->template()->addJS("pokus.js");
		//print_r($this->getModel()->coursesArray);
		
		$tmpArray = array();
		foreach ($this->getModel()->coursesArray as $value){
			$tmpArray[$value["id_course"]] = $value["name"]." ".$value["price"]." ".date("j.n.Y",$value["from"])." - ".date("j.n.Y",$value["to"]);
		}
		
		
		$this->template()->addVar("COURSES_ARRAY", $tmpArray);
//		$this->template()->addCss("style.css");
	}
}
	
?>