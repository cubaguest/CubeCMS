<?php
/**
 * Třída pro práci s článekem (article).
 * Třída obsluhuje parametr článku, který je přenášen v URL. Umožňuje 
 * přístup přímo k názvu článku. Je propojena s Třídou routes, protože 
 * cesta se odvozuje od názvu článku. 
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id$ VVE3.9.4 $Revision$
 * @author        $Author$ $Date$
 *                $LastChangedBy$ $LastChangedDate$
 * @abstract      Třída pro obsluhu článku přenášeného v URL
 */

class Article {
	/**
	 * Oddělovač mezi názvem článku a id
	 * @var char
	 */
	const ARTICLE_URL_SEPARATOR = '-';

	/**
	 * Proměná obsahuje id článku
	 * @var integer
	 */
	private static $currentArticleId = null;

	/**
	 * Konstruktor
	 */
	function __construct() {}

	/**
	 * Metoda nastaví id aktuálního článku
	 * @param integer $id -- id článku
	 */
	public static function setCurrentArticleId($id){
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
	 * @param string $label -- název článku
	 * @param integer $id -- id článku
	 */
	public function createUrl($label, $id) {
		$textHelp = new TextCtrlHelper();
		return $textHelp->utf2ascii($label).self::ARTICLE_URL_SEPARATOR.$id;
	}

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
}
?>