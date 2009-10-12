<?php
/**
 * Třída s modelem pro práci s uživateli
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: model_module.class.php 625 2009-06-13 16:01:09Z jakub $ VVE 5.1.0 $Revision: 625 $
 * @author			$Author: jakub $ $Date: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 * @abstract 		Třída s modelem pro práci s uživateli
 */

class Model_Users extends Model_PDO {
   /**
    * Název tabulky s uživateli
    */
    const DB_TABLE = 'users';

   /**
    * Název tabulky se skupinami
    */
    const DB_TABLE_GROUPS = 'groups';

	 /**
	  * Názvy sloupců v db tabulce
	  * @var string
	  */
	const COLUMN_ID         = 'id_user';
	const COLUMN_USERNAME   = 'username';
	const COLUMN_PASSWORD   = 'password';
	const COLUMN_ID_GROUP   = 'id_group';
	const COLUMN_NAME       = 'name';
	const COLUMN_SURNAME    = 'surname';
	const COLUMN_MAIL       = 'mail';
	const COLUMN_NOTE       = 'note';
	const COLUMN_BLOCKED    = 'blocked';
	const COLUMN_FOTO_FILE  = 'foto_file';
	const COLUMN_DELETED    = 'deleted';



	const COLUMN_GROUP_NAME    = 'gname';
	const COLUMN_GROUP_LABEL    = 'label';

   /**
    * Metoda načte kategori, pokud je zadán klíč je načtena určitá, pokud ne je
    * načtena kategorie s nejvyšší prioritou
    * @param string $catKey -- (option) klíč kategorie
    */
   public function getUser($username) {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT *, grp.name AS gname FROM ".Db_PDO::table(self::DB_TABLE)." AS user
             JOIN ".Db_PDO::table(self::DB_TABLE_GROUPS)." AS grp ON user.".self::COLUMN_ID_GROUP
             ." = grp.".self::COLUMN_ID_GROUP."
             WHERE (user.".self::COLUMN_USERNAME." = ".$dbc->quote($username).")");
      $dbst->execute();

      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst->fetch();
   }

   /**
    * Metoda vrací seznam skupin v systému
    * @return PDOStatement
    */
   public function getGroups() {
      $dbc = new Db_PDO();
      $dbst = $dbc->query("SELECT *, name AS gname FROM ".Db_PDO::table(self::DB_TABLE_GROUPS));
      $dbst->execute();

//      $dbst->setFetchMode(PDO::FETCH_OBJ);
      return $dbst;
   }
}
?>