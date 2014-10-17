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

class Model_IPBlocks extends Model_ORM {
/**
 * Název tabulky s uživateli
 */
   const DB_TABLE = 'cubecms_global_ipblock';

   const COLUMN_ID         = 'ip_address';
   const COLUMN_TIME       = 'time_add';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_ipblocks', false);
      $this->addColumn(self::COLUMN_ID, array('datatype' => 'varbinary(16)', 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_TIME, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->setPk(self::COLUMN_ID);
   }
   
   public static function isBlocked($ip)
   {
      $m = new self();
      return (bool)$m->where(self::COLUMN_ID." = :idb", array('idb' => inet_pton($ip)))->count(); 
   }
   
   public static function blockIP($ip)
   {
      $r = self::getNewRecord();
      $r->{self::COLUMN_ID} = inet_pton($ip);
      $r->save();
   }
}

class Model_IPBlocks_Record extends Model_ORM_Record {
   public function getIP()
   {
      return inet_ntop($this->{Model_IPBlocks::COLUMN_ID});
   }
   
   public function setIP($ip)
   {
      $this->{Model_IPBlocks::COLUMN_ID} = inet_pton($ip);
   }
}