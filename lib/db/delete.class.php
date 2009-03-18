<?php
/**
 * Rozhraní pro odstraňování záznamů z db.
 * Rozhraní pro tvorbu třídy k mazání odstraňování z databáze. Implementuje metodu
 * delete z rozhraní Db_Interface
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.2 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Rozhraní pro odstraňování záznamů z db
 */

interface Db_Delete {
	/**
	 * Metoda nastavuje z které tabulky se bude mazat
	 * klauzule FROM
	 *
	 * @param string -- tabulka pro použití
	 * @param boolean -- (option) jestli se májí tabulky zamknout
	 * 
	 * @return Db_Delete -- objekt Db_Delete
	 */
	public function table($table, $lockTable = false);
	
	/**
    * Metody vatváří podmínku WHERE. Pokud je třeba použít více podmínek, je
    * zadáváno pole v tomot poředí např:
    * where(array($cola,$valuea,'='),'AND',array(array($colb,$valueb,'='),'OR',array($colc,$valuec,'=')))
    * WHERE ($cola = $valuea) AND (($colb = $valueb) OR ($colc = $valuec))
    * @param string -- sloupcec
    * @param char/int -- operátor porovnávání konstatna Db::OPERATOR_XXX
    * @param int -- operátor porovnávání Db::COND_OPERATOR_XXX
    *
    * @return Db_Select -- objekt Db_Select
    */
   public function where($column, $value = null, $term = '=', $operator = Db::COND_OPERATOR_AND);
	
	/**
	 * Metoda přiřadí řazení sloupcu v SQL dotazu
	 *
    * @param string -- sloupec, podle kterého se má řadit
    * @param integer -- (option) jak se má sloupec řadit konstanta Db::ORDER_XXX (default: ASC)
	 * 
	 * @return Db_Delete -- objekt Db_Delete
	 */
	public function order($colum, $order = Db::ORDER_ASC);
	
	/**
	 * Metoda přidá do SQL dotazu klauzuli LIMIT
	 * @param integer -- počet záznamů
	 * @param integer -- záčátek
	 * 
	 * @return Db_Delete -- objekt Db_Delete
	 */
	public function limit($rowCount, $offset);
}
?>