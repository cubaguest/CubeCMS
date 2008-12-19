<?php
/**
 * Třída pro práci s článekem (article).
 * Třída obsluhuje parametr článku, který je přenášen v URL. Umožňuje 
 * přístup přímo k názvu článku. Je propojena s Třídou routes, protože 
 * cesta se odvozuje od názvu článku. 
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: article.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro obsluhu článku přenášeného v URL
 */

class Article {
	/**
	 * Oddělovač mezi názvem článku a id
	 * @var char
	 */
	const ARTICLE_URL_SEPARATOR = '-';

	/**
	 * Proměná obsahuje id článku
	 * @var string
	 */
	private static $currentArticleId = null;

	/**
	 * Id modulu ke kterému článek patří
	 * @var integer
	 */
//	private static $currentArticleIdItem = null;

	/**
	 * Konstruktor nastaví klíč článku z url
	 *
	 */
	function __construct() {
	}

	/**
	 * Metoda nastaví id aktuálního článku
	 * @param integer $id -- id článku
	 */
	public static function setCurrentArticle($id){
		self::$currentArticleId = $id;
	}

	/**
	 * Metoda vrací id článku
	 * @return integer
	 */
	public function getArticle() {
		return (int)self::$currentArticleId;
	}

	/**
	 * Metoda vytvoří řetězec článku pro url
	 * @param <type> $label
	 * @param <type> $id
	 */
	public function createUrl($label, $id) {
		$textHelp = new TextCtrlHelper();
		return $textHelp->utf2ascii($label).self::ARTICLE_URL_SEPARATOR.$id;
	}

	/**
	 * Metoda inicializuje objekt Article
	 */
//	public static function factory() {
//		if(self::$currentArticleId != null){
//			$articleFull = urldecode($_GET[self::GET_ARTICLE]);

//			Přeparsování přes routu
//			$article = Routes::parseRoute($articleFull);

//			self::$article = self::parseArticle($article);
//			self::$isArticle = true;
//		}
//	}

	/**
	 * Metoda přeparsuje článek a zjistí jestli bylo zadáno id článku
	 * @param string $article -- id článku
	 */
//	private static function parseArticle($article) {
//		$articleDet = array();
//		if(eregi('^([a-zA-Z0-9\-]+)-([0-9]+)$', $article, $articleDet)){
////				self::$article = $articleDet[1];
//				$article = $articleDet[2];
//			} else {
////				:$article = $article;
//			}
//
//		return $article;
//	}

	/**
	 * Metoda vrací klíč článku (article urlkey)
	 * @return string -- klíč článku
	 */
//	public function getArticle() {
//		return self::$article;
//	}
	
	/**
	 * Metoda vrací true pokud je článek nastaven
	 * @return boolean -- true při nastavení článku
	 */
	public function isArticle() {
		if(self::$currentArticleId != null){
			return true;
		}
		return false;
	}
	
	/**
	 * Magická metoda pro vrácení stringu
	 * @return string -- article
	 */
	public function __toString() {
		return (string)self::$currentArticleId;
	}
	
	/**
	 * Metoda nastavuje že je použita cesta(route) u článku
	 * 
	 * @param boolean -- nastavení cesty
	 */
//	public function setRoute($value) {
//		$this->isRoute = $value;
//	}
	
	/**
	 * Metoda vrací, jesli je nastavena cesta v článku
	 * @return boolean -- true pokud je cesta součástí článku
	 * @deprecated -- je obsažen ve funkci isRoute
	 */
//	public function withRoute() {
//		return $this->isRoute();
//	}
	
	/**
	 * Metoda vrací jestli je article použito s cestou
	 * @return boolean -- true pokud je article použito s cestou
	 */
//	public function isRoute() {
//		return $this->isRoute;
//	}
	
}

?>