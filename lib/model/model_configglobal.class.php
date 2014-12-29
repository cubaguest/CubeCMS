<?php
/**
 * Třída Modelu pro práci s konfiguračními volbami systému (globálními)
 * Třída, která umožňuje pracovet s modelem konfiguračních voleb
 *
 * @copyright  	Copyright (c) 2008-2009 Jakub Matas
 * @version    	$Id: $ Cube CMS 7.3 $Revision: $
 * @author			$Author: $ $Date:  $
 *						$LastChangedBy: $ $LastChangedDate: $
 * @abstract 		Třída pro vytvoření modelu pro práci s moduly
 */

class Model_ConfigGlobal extends Model_ORM {
   /**
 * Tabulka s detaily
 */
   const DB_TABLE = 'cubecms_global_config';

   /**
    * slouce v db
    */
   const COLUMN_ID = 'id_config';
   const COLUMN_ID_GROUP = 'id_group';
   const COLUMN_KEY = 'key';
   const COLUMN_VALUE = 'value';
   const COLUMN_VALUES = 'values';
   const COLUMN_PROTECTED = 'protected';
   const COLUMN_TYPE = 'type';
   const COLUMN_LABEL = 'label';
   const COLUMN_HIDDEN = 'hidden_value';
   const COLUMN_CALLBACK = 'callback_func';

   const TYPE_STRING = 'string';
   const TYPE_NUMBER = 'number';
   const TYPE_BOOL = 'bool';
   const TYPE_LIST = 'list';
   const TYPE_LIST_MULTI = 'listmulti';
   const TYPE_SER_DATA = 'ser_object';
   const TYPE_INT = 'int';
      
   private $globalTable = 'cubecms_global_config';
   private $mainTable = null;
   
   protected function  _initTable() {
      $this->mainTable = $this->getTableName();
      $this->setTableName(self::DB_TABLE, 't_ccfg_g', false);

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_GROUP, array('datatype' => 'smallint', 'nn' => true, 'default' => 1));
      $this->addColumn(self::COLUMN_KEY, array('datatype' => 'varchar(50)', 'nn' => true, 'uq' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_LABEL, array('datatype' => 'varchar(1000)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_VALUE, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_VALUES, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_PROTECTED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_TYPE, array('datatype' => 'varchar(15)', 'pdoparam' => PDO::PARAM_STR, 'default' => 'string')); // ENUM
      $this->addColumn(self::COLUMN_HIDDEN, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_CALLBACK, array('datatype' => 'varchar(40)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));

      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_GROUP, 'Model_ConfigGroups', Model_ConfigGroups::COLUMN_ID);
   }

   /**
    * Metoda nastaví proměnnou konfigurace
    * @param $name
    * @param $value
    * @return bool
    */
   public static function setValue($name, $value, $type = self::TYPE_STRING, $grpId = 3)
   {
      $model = new static();
      $rec = $model->where(self::COLUMN_KEY." = :name", array('name' => $name))->record();
      if($type == self::TYPE_BOOL){
         $value = $value ? 'true' : 'false';
      } else if($type == self::TYPE_LIST_MULTI && is_array($value)){
         $value = implode(';', $value);
      }
      if(!$rec){
         $rec = $model->newRecord();
         $rec->{self::COLUMN_ID_GROUP} = $grpId;
         $rec->{self::COLUMN_TYPE} = $type;
         $rec->{self::COLUMN_KEY} = $name;
      }
      $rec->{self::COLUMN_VALUE} = $value;
      $rec->save();
      return $rec;
   }
   
   /**
    * Metoda vrací proměnnou konfigurace
    * @param $name
    * @param $default
    * @return string
    */
   public static function getValue($name, $default)
   {
      // zkusíme nastavení pro aktuální site
      $m = new Model_Config();
      $type = self::TYPE_STRING;
      $r = $m
         ->where(self::COLUMN_KEY." = :name", array('name' => $name))
         ->record();
      if($r){
         $default = $r->{self::COLUMN_VALUE};
         $type = $r->{self::COLUMN_TYPE};
      } else {
         // zkusíme globální config
         $model = new Model_ConfigGlobal();
         $r = $model
            ->where(self::COLUMN_KEY." = :name", array('name' => $name))
            ->record();
         if($r){
            $default = $r->{self::COLUMN_VALUE};
            $type = $r->{self::COLUMN_TYPE};
         }
      }
      
      if($type == self::TYPE_BOOL){
         $default = $default == 'true' ? true : false;
      }
      
      return $default;
   }


   /**
    * Metoda nastaví vnitřní SQL dotaz na výbě záznamů z obou konfigurací
    */
   public function mergedConfigValues(){
      $m = new Model_Config();
      $this->currentSql = 'SELECT * FROM (SELECT `'.self::COLUMN_KEY.'`, `'.self::COLUMN_VALUE.'` FROM '.$m->getTableName().' UNION ALL SELECT `'.self::COLUMN_KEY.'`, `'.self::COLUMN_VALUE.'`'
         .' FROM `'.self::DB_TABLE.'`) AS t GROUP BY t.`'.self::COLUMN_KEY.'`';
      return $this;
   }
}