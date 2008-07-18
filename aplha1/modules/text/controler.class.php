<?php
/**
 * Třída pro obsluhu akcí a kontrolerů modulu
 *
 */

class TextController extends Controller {
	public function mainController() {
		echo "mainctrl";
		echo $this->getLink()->action($this->getAction()->actionEdittext());
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
	}

	public function edittextController() {
		echo "editujeme";
	}
	
}

?>