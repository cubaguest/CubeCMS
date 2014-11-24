<?php
/**
 * Třída Modelu pro práci s konfiguračními volbami systému
 * Třída, která umožňuje pracovet s modelem konfiguračních voleb
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: model_module.class.php 648 2009-09-16 16:20:04Z jakub $ VVE3.9.2 $Revision: 648 $
 * @author			$Author: jakub $ $Date: 2009-09-16 18:20:04 +0200 (St, 16 zář 2009) $
 *						$LastChangedBy: jakub $ $LastChangedDate: 2009-09-16 18:20:04 +0200 (St, 16 zář 2009) $
 * @abstract 		Třída pro vytvoření modelu pro práci s moduly
 */

class Model_Config extends Model_ConfigGlobal {
   const DB_TABLE = 'config';
   protected function  _initTable() {
      parent::_initTable();
      $this->mainTable = $this->getTableName();
      $this->setTableName(self::DB_TABLE);
   }
   
   public static function setSiteConfigValue($dbPrefix, $name, $value, $idGroup = 1, $type = self::TYPE_STRING)
   {
      $db = $this->getDb();
      
      $stmt = $db->prepare('INSERT INTO `'.$dbPrefix.'_config` '
          . '('.self::COLUMN_ID_GROUP.', '.self::COLUMN_KEY.', '.self::COLUMN_VALUE.', '.self::COLUMN_TYPE.') '
          . 'VALUES (:idg, :key, :value, :type) ON DUPLICATE KEY UPDATE '
          . '`'.self::COLUMN_VALUE.'`= :valueu;');
      
      // bind insert parametrů
      $stmt->bindValue(':idg', $idGroup, PDO::PARAM_INT);
      $stmt->bindValue(':key', $name, PDO::PARAM_STR);
      $stmt->bindValue(':value', $value, $type == self::TYPE_INT ? PDO::PARAM_INT : PDO::PARAM_STR);
      $stmt->bindValue(':type', $type, PDO::PARAM_STR);
      
      $stmt->bindValue(':valueu', $value, $type == self::TYPE_INT ? PDO::PARAM_INT : PDO::PARAM_STR);
      // bind update
      return $stmt->execute();
   }
}
