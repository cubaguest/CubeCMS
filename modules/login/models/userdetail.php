<?php
/*
 * Třída modelu s detailem uživatele
 */
class UserDetailModel extends DbModel {
	/**
	 * Pole s názvy sloupců v tabulce
	 * @var array
	 */
	const COLUMN_ID 			= 'id_user';
	const COLUMN_USER_NAME		= 'name';
	const COLUMN_USER_SURNAME	= 'surname';
	const COLUMN_USER_USERNAME	= 'username';
	const COLUMN_USER_PASSWORD	= 'password';

	/**
	 * Metoda načte heslo uživatele
	 *
	 * @param integer -- id uživatele
	 * @return string -- heslo s databáze
	 */
	public function getPasswd($idUser) {	
//			načtení fotky z db
			$sqlSelect = $this->getDb()->select()->from(array('user'=>$this->getModule()->getDbTable()), self::COLUMN_USER_PASSWORD)
				->where(self::COLUMN_ID." = '".$idUser."'");
												 
			$user = $this->getDb()->fetchAssoc($sqlSelect, true);	
		
		return $user[self::COLUMN_USER_PASSWORD];
	}

	/**
	 * Metoda uloží heslo uživatele
	 *
	 * @param integer -- id uživatele
	 * @param string -- heslo uživatele (nejčastěji zašifrované)
	 * @return boolean -- pokud se záznam podařilo uložit
	 */
	public function setPasswd($idUser, $password) {	
//			načtení fotky z db
			$sqlUpdate = $this->getDb()->update()->table($this->getModule()->getDbTable())
									   ->set(array(self::COLUMN_USER_PASSWORD => $password))
									   ->where(self::COLUMN_ID.' = '.$idUser);
		
		return $this->getDb()->query($sqlUpdate);
	}
}

?>