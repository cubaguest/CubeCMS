<?php
/**
 * Třída se stará o zvolený článek (article)
 *
 */
class Article {
	/**
	 * $GET parametr s článkem
	 * přenáší se z třídy Links
	 * @var string
	 */
	const GET_ARTICLE = Links::GET_ARTICLE;
	
	/**
	 * Proměná obsahuje klíč článku (article urlkey)
	 * @var string
	 */
	private $article = null;
	
	/**
	 * Proměná obsahuje je-li nastaven článek (article)
	 * @var boolean
	 */
	private $isArticle = false;
	
	/**
	 * Proměná obsahuje, je-li součástí článku cesta(route)
	 * @var boolean
	 */
	private $isRoute = false;
	
	/**
	 * Konstruktor nastaví klíč článku z url
	 *
	 */
	function __construct() {
		if(isset($_GET[self::GET_ARTICLE])){
			$this->setArticle($_GET[self::GET_ARTICLE]);
			$this->isArticle = true;
		}
	}
	
	/**
	 * Metoda vrací klíč článku (article urlkey)
	 * @return string -- klíč článku
	 */
	public function getArticle() {
		return $this->article;
	}
	
	/**
	 * Metoda nastavuje klíč článku (article urlkey)
	 * @param string -- klíč článku
	 */
	public function setArticle($articleKey) {
		$this->article = $articleKey;
	}
	
	/**
	 * Metoda vrací true pokud je článek nastaven
	 * @return boolean -- true při nastavení článku
	 */
	public function isArticle() {
		return $this->isArticle;
	}
	
	/**
	 * Metoda rozparsuje článek na dvě části, odělené oddělovačem a vrátí jej jako pole
	 * @param string -- oddělovač
	 * @return array -- číslované pole s hodnotami
	 */
	public function parse($separator) {
		$returnArray=explode($separator, $this->getArticle());
		
		return $returnArray;
	}
	
	/**
	 * Magická metoda pro vrácení stringu
	 * @return string -- article
	 */
	public function __toString() {
		return $this->getArticle();
	}
	
	/**
	 * Metoda nastavuje že je použita cesta(route) u článku
	 * 
	 * @param boolean -- nastavení cesty
	 */
	public function setRoute($value) {
		$this->isRoute = $value;
	}
	
	/**
	 * Metoda vrací, jesli je nastavena cesta v článku
	 * @return boolean -- true pokud je cesta součástí článku
	 */
	public function withRoute() {
		return $this->isRoute;
	}
	
}

?>