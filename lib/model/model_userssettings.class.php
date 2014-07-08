<?php
/**
 * Třída s modelem pro práci s uživateli
 *
 * @copyright     Copyright (c) 2008-2009 Jakub Matas
 * @version       $Id: model_module.class.php 625 2009-06-13 16:01:09Z jakub $ VVE 5.1.0 $Revision: 625 $
 * @author        $Author: jakub $ $Date: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 *                $LastChangedBy: jakub $ $LastChangedDate: 2009-06-13 16:01:09 +0000 (So, 13 čen 2009) $
 * @abstract      Třída s modelem pro práci s uživateli
 */

class Model_UsersSettings extends Model_ORM {
/**
 * Název tabulky s uživateli
 */
   const DB_TABLE = 'users_settings';

   /**
    * Názvy sloupců v db tabulce
    * @var string
    */
   const COLUMN_ID         = 'id_user_setting';
   const COLUMN_ID_USER    = 'id_user';
   const COLUMN_NAME       = 'setting_name';
   const COLUMN_VALUE      = 'setting_value';

   protected static $settingsCache = array();

   protected function  _initTable() {
      if(VVE_USE_GLOBAL_ACCOUNTS === true) {
         $this->setTableName(VVE_GLOBAL_TABLES_PREFIX.self::DB_TABLE, 't_uss', false);
      } else {
         $this->setTableName(self::DB_TABLE, 't_us');
      }

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_USER, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_VALUE, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));

      $this->setPk(self::COLUMN_ID);
      $this->addForeignKey(self::COLUMN_ID_USER, 'Model_Users');
   }

   public static function getSettings($name, $defaultValue = null, $idUser = null)
   {
      $idUser = $idUser == null ? Auth::getUserId() : $idUser;
      
      self::checkSettingsLoaded($idUser);
      if(isset(self::$settingsCache[$idUser]) && isset(self::$settingsCache[$idUser][$name])){
         return self::$settingsCache[$idUser][$name];
      }
   }
   
   protected static function checkSettingsLoaded($idUser)
   {
      if(!isset(self::$settingsCache[$idUser])){
         self::$settingsCache[$idUser] = array();
         $m = new self();
         $all = $m
            ->where(self::COLUMN_ID_USER.' = :idu', array('idu' => $idUser))
            ->records(PDO::FETCH_OBJ);
         foreach ($all as $value) {
            self::$settingsCache[$idUser][$value->{self::COLUMN_NAME}] = $value->{self::COLUMN_VALUE};
         }
      }
   }

   public static function setSettings($name, $value = null, $idUser = null)
   {
      $m = new self();
      $idUser = $idUser == null ? Auth::getUserId() : $idUser;
      if($value == null){
         $m
            ->where(self::COLUMN_ID_USER.' = :idu AND '.self::COLUMN_NAME.' = :name', array('idu' => $idUser, 'name' => $name))
            ->delete();
      } else {
         $setting = $m
            ->where(self::COLUMN_ID_USER.' = :idu AND '.self::COLUMN_NAME.' = :name', array('idu' => $idUser, 'name' => $name))
            ->record();
         if(!$setting){
            $setting = $m->newRecord();
            $setting->{self::COLUMN_ID_USER} = $idUser;
            $setting->{self::COLUMN_NAME} = $name;
         }
         $setting->{self::COLUMN_VALUE} = $value;
         $setting->save();
      }
      if(isset(self::$settingsCache[$idUser])){
         unset(self::$settingsCache[$idUser]);
      }
   }
}