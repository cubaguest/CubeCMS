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

class Model_UsersLogins extends Model_ORM {
/**
 * Název tabulky s uživateli
 */
   const DB_TABLE = 'users_logins';

   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
   const COLUMN_ID          = 'id_user_login';
   const COLUMN_ID_USER    = 'id_user';
   const COLUMN_IP_ADDRESS = 'user_login_ip';
   const COLUMN_BROWSER    = 'user_login_browser';
   const COLUMN_TIME    = 'user_login_time';

   protected function  _initTable() {
   if(VVE_USE_GLOBAL_ACCOUNTS === true) {
         $this->setTableName(VVE_GLOBAL_TABLES_PREFIX.self::DB_TABLE, 't_us', false);
      } else {
         $this->setTableName(self::DB_TABLE, 't_us');
      }

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_IP_ADDRESS, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_BROWSER, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));

      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
   }
}
?>