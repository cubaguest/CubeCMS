<?php
/**
 * Abstraktní třída pro aktualizaci záznamů v db.
 * Třída zobrazuje prvky třídy, které musí být použity v jednotlivých implementacích
 * databázových konektorů.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: update.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro aktualizaci záznamů v db
 */

abstract class Db_Update {
	/**
	 * Metoda nastavuje v které tabulce se bude upravovat
	 * klauzule UPDATE table
	 *
	 * @param string -- tabulka která se bude upravovat
	 * 
	 * @return Db_Update -- objekt Db_Update
	 */
	abstract function table($table);
	
	/**
	 * Metoda nastavuje, které hodnoty se upraví
	 * (název sloupce) => (hodnota)
	 *
	 * @param array -- pole s hodnotamik
	 * 
	 * @return Db_Update -- objekt Db_Update
	 */
	abstract function set($values);
	
	/**
	 * Metody vatváří podmínku WHERE
	 *
	 * @param string -- podmínka
	 * @param string -- typ spojení podmínky (AND, OR) (výchozí je AND)
	 * 
	 * @return Db_Update -- objekt Db_Update
	 */
	abstract function where($condition, $operator = self::SQL_AND);
	
	/**
	 * Metoda přiřadí řazení sloupcu v SQL dotazu
	 *
	 * @param string -- sloupec, podle kterého se má řadit
	 * @param string -- (option) jak se má sloupec řadit (ASC, DESC) (default: ASC)
	 * 
	 * @return Db_Update -- objekt Db_Update
	 */
	abstract function order($colum, $order = self::SQL_ASC);
	
	/**
	 * Metoda přidá do SQL dotazu klauzuli LIMIT
	 * @param integer -- počet záznamů
	 * @param integer -- záčátek
	 * 
	 * @return Db_Update -- objekt Db_Update
	 */
	abstract function limit($rowCount, $offset);
}

?>