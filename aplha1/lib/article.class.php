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
	public function isArticle()
	{
		return $this->isArticle;
	}
	
	
}

?>