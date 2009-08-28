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

class Model_Users extends Model_Db {
   /**
    * Název tabulky s uživateli
    */
    const DB_TABLE = 'users';

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
}
?>