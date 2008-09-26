<?php
abstract class Db_Select {
	/**
	 * Metoda nastavuje z které tabulky se bude načítat
	 * klauzule FROM
	 *
	 * @param string/array -- tabulka ze které se bude vybírat, u pole index označuje alias tabulky
	 * @param string/array -- které sloupce mají být vabrány (option)
	 * 
	 * @return Db_Select -- objekt Db_Select
	 */
	abstract function from($tableArray, $columsArray = "*");
	
	/**
	 * Metody vatváří podmínku WHERE
	 *
	 * @param string -- podmínka
	 * @param string -- typ spojení podmínky (AND, OR) (výchozí je AND)
	 * 
	 * @return Db_Select -- objekt Db_Select
	 */
	abstract function where($condition, $operator = self::SQL_AND);
	
	/**
	 * Metody vytvoří část pro klauzuli JOIN
	 *
	 * @param string/array -- název tabulky (alias je generován z prvních dvou písmen) nebo pole kde index je alias tabulky
	 * @param string -- podmínka v klauzuli ON, je třeba zadat i s aliasy
	 * @param string -- typ JOIN operace hodnoty jsou: JOIN, LEFT, RIGHT, INNER
	 * @param string/array -- název sloupců, které se mají vypsatm, výchozí jsou všechny ("*").
	 * 						  U pole označuje index alias prvku. Pokud je zadáno null, nebude načten žádný sloupec
	 * 
	 * @return Db_Select -- objekt Db_Select
	 */
	abstract function join($tableArray, $condition, $joinType = null, $columsArray = "*");
	
	/**
	 * Metoda přiřadí řazení sloupcu v SQL dotazu
	 *
	 * @param string -- sloupec, podle kterého se má řadit
	 * @param string -- (option) jak se má sloupec řadit (ASC, DESC) (default: ASC)
	 * 
	 * @return Db_Select -- objekt Db_Select
	 */
	abstract function order($colum, $order = self::SQL_ASC);
	
	/**
	 * Metoda přiřadí slouření sloupců v SQL dotazu pomocí klauzule GROUP BY
	 *
	 * @param string -- sloupec, podle kterého se má řadit
	 * @param string -- (option) WITH ROLLUP false(default)/true
	 * 
	 * @return Db_Select -- objekt Db_Select
	 */
	abstract function group($colum, $withRollup = false);
	
	/**
	 * Metoda přidá do SQL dotazu klauzuli LIMIT
	 * @param integer -- počet záznamů
	 * @param integer -- záčátek
	 * 
	 * @return Db_Select -- objekt Db_Select
	 */
	abstract function limit($rowCount, $offset);
	
	/**
	 * Metoda přidává do dotazu sloupce s počem záznamů
	 * @param string -- alias pod kterým má být vrácena hodnota
	 * @param string -- které sloupce se mají vybrat (option) default: všechny
	 * 
	 * @return Db_Select -- objekt Db_Select
	 */
	abstract function count($alias = null, $colum = self::SQL_ALL_VALUES);
}

?>