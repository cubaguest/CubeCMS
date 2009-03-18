<?php
/**
 * Rozhraní pro vkládání záznamů do db.
 * Rozhraní pro tvorbu třídy k vkládání záznamů do databáze. Implementuje metodu
 * insert z rozhraní Db_Interface
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.2 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vkládání záznamů do db
 */

interface Db_Insert {
	/**
	 * Metoda nastavuje která tabulka se bude používat
	 *
	 * @param string -- tabulka pro použití
	 * @param boolean -- (option) jestli se májí tabulky zamknout
	 * @return Db_Insert
	 */
	public function table($table, $lockTable = false);
	
	/**
	 * Metoda nastavuje do kterých sloupců se budou vkládat záznamy
    *
	 * @param string/array -- sloupce, do kterých se má vkládat
	 * @return Db_Insert -- objekt Db_Insert
	 */
   public function colums($columsArray);
	
	/**
	 * Metoda přiřadí hodnoty sloupcům
    *
	 * @param string -- hodnota sloupce (proměnný počet parametrů)
	 * @return Db_Insert -- objekt Db_Insert
	 */
	public function values($value);
}
?>