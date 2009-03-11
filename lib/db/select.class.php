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

interface Db_Select{
   /**
	 * Metoda nastavuje která tabulka se bude používat
	 *
	 * @param string -- tabulka pro použití
	 * @param string -- alias tabulky pro použití
	 * @param boolean -- (option) jestli se májí tabulky zamknout
	 * @return Db_Select
	 */
	public function table($table, $alias = null, $lockTable = false);

	/**
	 * Metoda nastavuje které sloupce su budou načítat
	 *
	 * @param string/array -- sloupce, které se budou vybírat, intex označuje alias
	 * 
	 * @return Db_Select -- objekt Db_Select
	 */
   public function colums($columsArray = '*');
	
	/**
	 * Metody vatváří podmínku WHERE
	 *
	 * @param string/array -- sloupec nebo pole podmínek
	 * @param string/mixed -- hodnota
	 * @param string/mixed -- typ porovnávání
	 * 
	 * @return Db_Select -- objekt Db_Select
	 */
   public function where($column, $value = null, $term = '=', $operator = self::SQL_AND);
	
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
	public function join($tableArray, $condition, $joinType = null, $columsArray = "*");
	
	/**
	 * Metoda přiřadí řazení sloupcu v SQL dotazu
	 *
	 * @param string -- sloupec, podle kterého se má řadit
	 * @param string -- (option) jak se má sloupec řadit (ASC, DESC) (default: ASC)
	 * 
	 * @return Db_Select -- objekt Db_Select
	 */
	public function order($colum, $order = self::SQL_ASC);
	
	/**
	 * Metoda přiřadí slouření sloupců v SQL dotazu pomocí klauzule GROUP BY
	 *
	 * @param string -- sloupec, podle kterého se má řadit
	 * @param string -- (option) WITH ROLLUP false(default)/true
	 * 
	 * @return Db_Select -- objekt Db_Select
	 */
   public function group($colum, $withRollup = false);
	
	/**
	 * Metoda přidává do dotazu sloupce s počem záznamů
	 * @param string -- alias pod kterým má být vrácena hodnota
	 * @param string -- které sloupce se mají vybrat (option) default: všechny
	 *
	 * @return Db_Select -- objekt Db_Select
	 */
   public function count($alias = null, $colum = self::SQL_ALL_VALUES);

   /**
	 * Metoda přidá do SQL dotazu klauzuli LIMIT
	 * @param integer -- počet záznamů
	 * @param integer -- záčátek
	 *
	 * @return Db_Select -- objekt sebe
	 */
	public function limit($rowCount, $offset);
}

?>