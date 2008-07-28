<?php
interface DbInterface{

	public function query($sqlQuery);
	public function getAffectedRows();
	public function getNumRows();
	public function getLastInsertedId();
	/**
	 * Metoda pro výběr prvků z db
	 * @return Db_Select -- objekt pro přístup k db;
	 */
	public function select();

	public function count($table);
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
	public function fetchAssoc($sqlQuery);
	public function fetchObjectArray($sqlQuery);
	public function fetchObject($sqlQuery);
}
?>