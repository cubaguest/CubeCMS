<?php
/**
 * Interface pro třídu Db
 */
interface DbInterface{

	/**
	 * Metoda provede daný sql dotaz na databázi
	 *
	 * @param string -- sql dotaz
	 */
	public function query($sqlQuery);
	
	/**
	 * Metoda vrací počet ovlivněných záznamů příkazy INSERT, UPDATE, DELETE, REPLACE
	 * @return integer -- počet ovlivněných záznamu
	 */
	public function getAffectedRows();
	
	/**
	 * Metoda vrací počet vrácených záznamů příkazy SELECT
	 * @return integer -- počet vrácených záznamu
	 */
	public function getNumRows();
	
	/**
	 * Metoda vrací id posledníího vloženého záznamu pomocí INSERT
	 * @return integer -- id posledního záznamu
	 */
	public function getLastInsertedId();
	
	/**
	 * Metoda pro výběr prvků z db
	 * @return Db_Select -- objekt pro přístup k db;
	 */
	public function select();

	/**
	 * Metoda vrací počet záznamů v zadané tabulce
	 *
	 * @param string -- název tabulky
	 * @param string -- podmínka
	 * @return integer -- počet záznamů v tabulce
	 */
	public function count($table,$condition = null);
	
	/**
	 * Metoda pro vložení prvků do db
	 * @return Db_Insert -- objekt pro přístup k db;
	 */
	public function insert();
	
	/**
	 * Metoda pro smazání prvků z db
	 * @return Db_Delete -- objekt pro přístup k db;
	 */
	public function delete();
	
	/**
	 * Metoda pro úpravu prvků z db
	 * @return Db_Update -- objekt pro přístup k db;
	 */
	public function update();
	
	/**
	 * Metoda provede sql dotaz a výstup doplní do asociativního pole
	 *
	 * @param string -- SQL dotaz
	 * @param boolean -- jestli se má vrátit pouze pole s prvky, nebo pole s poly prvků
	 * @return array/boolean -- asociativní pole s výsledky sql dotazu nebo false při prázdném výsledku
	 */
	public function fetchAssoc($sqlQuery, $oneArray = false);
	
	/**
	 * Metoda provede sql dotaz a výstup přiřadí do pole objektů mysqli
	 *
	 * @param string -- SQL dotaz
	 * @return array -- pole objektů MySQLi_Result
	 */
	public function fetchObjectArray($sqlQuery);
	
	/**
	 * Metoda vrací řádek z db jako objekt
	 * @param string -- sql dotaz
	 * @return MySQLi_Result -- objekt s prvky z db
	 */
	public function fetchObject($sqlQuery);
}
?>