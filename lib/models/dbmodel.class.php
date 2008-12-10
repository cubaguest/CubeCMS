<?php
require_once '.' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR. AppCore::ENGINE_MODELS_DIR . DIRECTORY_SEPARATOR . 'model.class.php';

/**
 * Abstraktní třída pro Db Model.
 * Tříta pro vytvoření modelu, přistupujícího k databázi. Umožňuje základní práce
 * s databází.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: dbmodel.class.php 3.0.55 26.9.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Abstraktní třída pro vytvoření modelu pro práci s databází
 */

class DbModel extends Model {
	
	/**
	 * Objekt konektoru k databázi
	 *
	 * @var DbInterface
	 */
	private $dbConnector = null;
	
	/**
	 * Konstruktor třídy
	 *
	 */	
	final function __construct() {
		$this->db = AppCore::getDbConnector();
		
//		Inicializace modelu
		$this->_init();
	}

	/**
	 * Metoda vrací objekt ke konektoru databáze
	 * 
	 * @return Dbinterface
	 */
	final public function getDb() {
		return AppCore::getDbConnector();
	}
	
	
	
}

?>