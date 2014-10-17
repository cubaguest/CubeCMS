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

class Model_UsersLoginAttempts extends Model_ORM {
   const DB_TABLE = 'cubecms_global_login_attempts';

   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
//   const COLUMN_ID      = 'id_login_attempt';
   const COLUMN_ID_USER = 'id_user';
   const COLUMN_TIME    = 'time_login';
   const COLUMN_IP    = 'login_ip';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_login_at', false);

      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_IP, array('datatype' => 'VARBINARY(16)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'index' => true));
      $this->addColumn(self::COLUMN_TIME, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
   }
   
   public static function getLogins($iduser, $ip, DateTime $fromTime = null)
   {
      $m = new self();
      $whereBinds = array('idu' => $iduser, 'ip' => inet_pton($ip));
      $whereStrings = array("(".self::COLUMN_ID_USER." = :idu OR ".self::COLUMN_IP." = :ip)");
      if($fromTime){
         $whereStrings[] = self::COLUMN_TIME.' > :from';
         $whereBinds['from'] = $fromTime->format(DATE_ISO8601);
      }
      return $m->where(implode(' AND ', $whereStrings), $whereBinds)->count();
   }
   
   public static function clearUserAttempts($iduser, $ip)
   {
      $m = new self();
      return $m->where(self::COLUMN_ID_USER." = :idu OR ".self::COLUMN_IP." = :ip", array('idu' => $iduser, 'ip' => inet_pton($ip)))->delete();
   }
}