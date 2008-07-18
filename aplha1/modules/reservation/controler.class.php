<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class ReservationController extends Controller {
	public function mainController() {
//		$this->createModel("NewsList");
//		print_r($this->getModel());
//		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable());
		
		//Doplnění pole s novinkami do modelu
//		$this->getModel()->allNewsArray=$this->getDb()->fetchAssoc($sqlSelect);
		
//		echo $this->getModule()->getDir()->getDataDir();
//		
//		echo "<br />".$this->getLink()."<br />";
//		echo "<br />".$this->getLink()->article("article-pokus")->action($this->getAction()->actionAddphoto())."<br />";
//		echo "<br />".$this->getLink()->article("novinka")->action()."<br />";
//		echo "<br />Akce editace: ".$this->getLink()->article()->action($this->getAction()->actionEdit())."<br />";
//		echo "<br />Akce vytvořená dinamicky: ".$this->getLink()->article()->action($this->getAction()->actionEditnews())."<br />";
//		echo "skupina uživatele: ".$this->getAuth()->getUserName();
	
		
		$this->createModel("CoursesList");
		
		$temp= $this->getDb()
					->select()
					->from($this->getModule()
					->getDbTable(2))
					->where("id_item = ".$this
					->getModule()
					->getId());
		
		$this->getModel()->coursesArray = $this->getDb()->fetchAssoc($temp);
	}

	
}

?>