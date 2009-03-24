<?php
/**
 * Abstraktní třída pro mazání záznamů z db.
 * Třída zobrazuje prvky třídy, které musí být použity v jednotlivých implementacích
 * databázových konektorů.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: delete.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro mazání záznamů z db
 */

abstract class Db_Delete {
	/**
	 * Metoda nastavuje z které tabulky se bude mazat
	 * klauzule FROM
	 *
	 * @param string/array -- tabulka ze které se bude vybírat, u pole index označuje alias tabulky
	 * @param string/array -- které sloupce se mají mazat
	 * 
	 * @return Db_Delete -- objekt Db_Delete
	 */
	abstract function from($table, $columsArray = null);
	
	/**
	 * Metody vatváří podmínku WHERE
	 *
	 * @param string -- podmínka
	 * @param string -- typ spojení podmínky (AND, OR) (výchozí je AND)
	 * 
	 * @return Db_Delete -- objekt Db_Delete
	 */
	abstract function where($condition, $operator = self::SQL_AND);
	
	/**
	 * Metoda přiřadí řazení sloupcu v SQL dotazu
	 *
	 * @param string -- sloupec, podle kterého se má řadit
	 * @param string -- (option) jak se má sloupec řadit (ASC, DESC) (default: ASC)
	 * 
	 * @return Db_Delete -- objekt Db_Delete
	 */
	abstract function order($colum, $order = self::SQL_ASC);
	
	/**
	 * Metoda přidá do SQL dotazu klauzuli LIMIT
	 * @param integer -- počet záznamů
	 * @param integer -- záčátek
	 * 
	 * @return Db_Delete -- objekt Db_Delete
	 */
	abstract function limit($rowCount, $offset);
}
?>