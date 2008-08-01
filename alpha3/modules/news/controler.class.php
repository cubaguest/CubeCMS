<?php
class NewsController extends Controller {
	public function mainController() {
	
//		$this->infoMsg()->addMessage("pokusná zpráva");
//		$this->errMsg()->addMessage("chyba!!! nezadány všechny parametry!!");
//		new	CoreException("test", 9);
		
//		echo $this->getModule()->getDir()->getDataDir();

//		Inicializace epluginu
//		$scroll = $this->addEPlugin("scroll", "scroll");		
//		
//		echo $sel = $this->getDb()->select()->from("test")->where("id = 5", "OR")->where("id < 5");
		
//		$ins=$this->getDb()->insert()->into($this->getModule()->getDbTable())
//								->colums("id_item", "key", "id_user", "label", "text", "time")
//								->values($this->getModule()->getId(), "pokusekkkkk", 3, "Pokůůsek", "pokusná zpráva", time());
//		echo $ins."<br />";
//		echo "INSERT INTO `vypecky_news` ( `id_item` , `key` , `id_user` , `label` , `text` , `time` ) VALUES ( '5', 'pokuuuuues', '3', 'Pokusek1', 'Pokusný text 1', '123456789');";
//		$this->getDb()->query($ins);

//		echo "lastID: ".$this->getDb()->getLastInsertedId();
		
//		$del = $this->getDb()->delete()->from("news")->where("id_new = 22", "OR")->where("id_new = 23", "OR");
//		echo $del."<br />";

//		$upd = $this->getDb()->update()->table("news")->set(array("text"=>"novy text a ještě delší"))->where("id_new = 25");
//		echo $upd;
//		$this->getDb()->query($upd);
		
		
		$this->createModel("NewsList");
//		print_r($this->getModel());

		
//		Scrolovátka
		$scroll = $this->eplugin()->scroll();
		$scroll->setCountRecordsOnPage($this->getModule()->getRecordsOnPage());
		$scroll->setCountAllRecords($this->getDb()->count($this->getModule()->getDbTable()));

		$sqlSelect = $this->getDb()->select()->from($this->getModule()->getDbTable())
											 ->limit($scroll->getStartRecord(), $scroll->getCountRecords());
		$this->getModel()->allNewsArray=$this->getDb()->fetchAssoc($sqlSelect);
		
		//Doplnění pole s novinkami do modelu
		$this->getModel()->scroll = $scroll;
//		echo $this->getLink()->action()->reload();
		
//		$this->changeActionView("test");
//		echo "<pre>";
//		print_r($scroll);
//		echo "</pre>";
//		$scroll->initial();
		
//		echo "<br />".$this->getLink()."<br />";
//		echo "<br />".$this->getLink()->article("article-pokus")->action($this->getAction()->actionEdit())."<br />";
//		echo "<br />s article a action".$this->getLink()->article("novinka")->action()."<br />";
//		echo "<br />s kat ".$this->getLink()->category("pokusek")."<br />";
//		echo "<br />bez article ".$this->getLink()->article()."<br />";
//		echo "<br />bez article a params ".$this->getLink()->article()->params()."<br />";
//		echo "<br />this ".$this->getLink()."<br />";
//		echo "<br />clear ".$this->getLink(true)."<br />";
//		echo "<br />Akce editace: ".$this->getLink()->article()->action($this->getAction()->actionEdit())."<br />";
//		echo "<br />Akce vytvořená dinamicky: ".$this->getLink()->article()->action($this->getAction()->actionEditnews())."<br />";
//		echo "skupina uživatele: ".$this->getAuth()->getUserName();
	}

	public function showController()
	{
		$pole = $this->getArticle()->parse('-');
		$this->getDb()->select()->from();
//		echo $pole[0]."<br />";
//		echo $pole[1];
	}
	
	
}

?>