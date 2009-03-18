<?php
/**
 * Rozhraní pro úpravu záznamů z db.
 * Rozhraní pro tvorbu třídy k úpravě záznamů v databáze. Implementuje metodu
 * update z rozhraní Db_Interface
 *
 * @copyright  	Copyright (c) 2008 Jakub Matas
 * @version    	$Id: $ VVE3.9.2 $Revision: $
 * @author			$Author: $ $Date:$
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro úpravu záznamů v db
 */

interface Db_Update {
   /**
	 * Metoda nastavuje která tabulka se bude používat
	 *
	 * @param string -- tabulka pro použití
	 * @param boolean -- (option) jestli se májí tabulky zamknout
	 * @return Db_Select
	 */
	public function table($table, $lockTable = false);
	
	/**
    * Metoda nastavuje, které hodnoty se upraví
    * (název sloupce) => (hodnota)
    *
    * @param array $values -- pole s hodnotami array((název sloupce) => (hodnota))
    *
    * @return Db_Update -- objekt Db_Update
    */
   public function set($values);

   /**
    * Metody vatváří podmínku WHERE. Pokud je třeba použít více podmínek, je
    * zadáváno pole v tomot poředí např:
    * where(array($cola,$valuea,'='),'AND',array(array($colb,$valueb,'='),'OR',array($colc,$valuec,'=')))
    * WHERE ($cola = $valuea) AND (($colb = $valueb) OR ($colc = $valuec))
    * @param string -- sloupcec
    * @param char/int -- operátor porovnávání konstatna Db::OPERATOR_XXX
    * @param int -- operátor porovnávání Db::COND_OPERATOR_XXX
    *
    * @return Db_Update -- objekt Db_Update
    */
   public function where($column, $value = null, $term = '=', $operator = Db::COND_OPERATOR_AND);
	
	/**
	 * Metoda přiřadí řazení sloupcu v SQL dotazu
	 *
    * @param string -- sloupec, podle kterého se má řadit
    * @param integer -- (option) jak se má sloupec řadit konstanta Db::ORDER_XXX (default: ASC)
	 *
	 * @return Db_Update -- objekt Db_Update
	 */
	public function order($colum, $order = Db::ORDER_ASC);
	
   /**
	 * Metoda přidá do SQL dotazu klauzuli LIMIT
	 * @param integer -- počet záznamů
	 * @param integer -- záčátek
	 *
	 * @return Db_Update -- objekt sebe
	 */
	public function limit($rowCount, $offset);
}
?>