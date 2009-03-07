<?php
/**
 * Abstraktní třída pro výběr záznamů z db.
 * Třída zobrazuje prvky třídy, které musí být použity v jednotlivých implementacích
 * databázových konektorů.
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: select.class.php 3.0.0 beta1 29.8.2008
 * @author 		Jakub Matas <jakubmatas@gmail.com>
 * @abstract 		Třída pro výběr záznamů z db
 */

interface Db_Query {
	/**
	 * Metody vatváří podmínku WHERE
	 *
	 * @param string -- podmínka
	 * @param string -- typ spojení podmínky (AND, OR) (výchozí je AND)
	 * 
	 * @return Db_Select -- objekt Db_Select
	 */
	public function where($condition, $operator = self::SQL_AND);

   /**
	 * Metoda přidá do SQL dotazu klauzuli LIMIT
	 * @param integer -- počet záznamů
	 * @param integer -- záčátek
	 *
	 * @return Db_Select -- objekt Db_Select
	 */
   public function limit($rowCount, $offset);
}
?>