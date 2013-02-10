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

class Model_Tokens extends Model_ORM {
   /**
    * Název tabulky s registrovanými moduly
    */
   const DB_TABLE = 'secure_tokens';

   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
   const COLUMN_ID         = 'id_secure_token';
   const COLUMN_ID_USER    = 'id_user';
   const COLUMN_TOKEN      = 'secure_token';
   const COLUMN_TIME_ADD   = 'secure_token_created';

   protected function  _initTable() {
      if(VVE_USE_GLOBAL_ACCOUNTS === true) {
         $this->setTableName(VVE_GLOBAL_TABLES_PREFIX.self::DB_TABLE, 't_stokens', false);
      } else {
         $this->setTableName(self::DB_TABLE, 't_stokens');
      }

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'int', 'nn' => true, 'index' => array(self::COLUMN_ID_USER, self::COLUMN_TOKEN, self::COLUMN_TIME_ADD)));
      $this->addColumn(self::COLUMN_TOKEN, array('datatype' => 'varchar(40)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));

      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users', Model_Users::COLUMN_ID);
   }
}
