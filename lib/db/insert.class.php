<?php
/**
 * Abstraktní třída pro vkládání záznamů do db.
 * Třída zobrazuje prvky třídy, které musí být použity v jednotlivých implementacích
 * databázových konektorů.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: insert.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro vkládání záznamů do db
 */

abstract class Db_Insert {
	/**
	 * Metoda nastavuje do které tabulky se bude zapisovat
	 * klauzule INTO
	 *
	 * @param string -- tabulka do které se bude zapisovat
	 * 
	 * @return Db_Insert -- objekt Db_Insert
	 */
	abstract function into($table);
	
	/**
	 * Metody vatváří sloupce, které se budou zapisovat 
	 *
	 * @param mixed -- sloupce (string nebo array)
	 * @param string -- sloupce (neomezený počet parametrů)
	 * 
	 * @return Db_Insert -- objekt Db_Insert
	 */
	abstract function colums($colums);
	
	/**
	 * Metoda přiřadí hodnoty sloupscům
	 *
	 * @param string -- hodnota sloupce (proměnný počet parametrů)
	 * 
	 * @return Db_Insert -- objekt Db_Insert
	 */
	abstract function values($value);
	
	
}
?>