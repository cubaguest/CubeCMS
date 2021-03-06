<?php
/*
 * Třída modelu detailem šablon
*/
class Templates_Model extends Model_ORM {
   const TEMPLATE_TYPE_TEXT = 'text';
   const TEMPLATE_TYPE_MAIL = 'mail';

   const DB_TABLE = 'templates';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID         = 'id_template';
   const COLUMN_NAME       = 'name';
   const COLUMN_DESC       = 'description';
   const COLUMN_CONTENT    = 'content';
   const COLUMN_ADD_TIME   = 'time_add';
   const COLUMN_TYPE       = 'type';
   const COLUMN_LANG       = 'lang';

   /**
    * Pole s typy šablon
    * @var <array>
    */
   public static $tplTypes = array(Templates_Model::TEMPLATE_TYPE_TEXT, Templates_Model::TEMPLATE_TYPE_MAIL);

   protected function  _initTable() {
      if(defined('VVE_MAIN_SITE_TABLE_PREFIX') && VVE_MAIN_SITE_TABLE_PREFIX != null){
         $this->setTableName(VVE_MAIN_SITE_TABLE_PREFIX.self::DB_TABLE, 't_us', false);
      } else {
         $this->setTableName(self::DB_TABLE, 't_us');
      }

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR, 'nn' => true));
      $this->addColumn(self::COLUMN_DESC, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CONTENT, array('datatype' => 'text', 'pdoparam' => PDO::PARAM_STR, 'nn' => true, 'default' => false));
      $this->addColumn(self::COLUMN_ADD_TIME, array('datatype' => 'datetime', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_TYPE, array('datatype' => 'varchar(10)', 'pdoparam' => PDO::PARAM_STR, 'default' => self::TEMPLATE_TYPE_TEXT));
      $this->addColumn(self::COLUMN_LANG, array('datatype' => 'varchar(5)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));

      $this->setPk(self::COLUMN_ID);
   }

   /**
    * Metoda vrací šablonu podle zadaného id
    *
    * @param int -- id šablony
    * @return Object -- pole s šablonou
    * @deprecated - Use ORM!
    */
   public static function getTemplate($id, $lang = null) {
      $m = new self();
      if($lang == null){
         return $m->where(self::COLUMN_ID.' = :id', array('id' => $id))->record();
      }
      return $m->where(self::COLUMN_ID.' = :id AND '.self::COLUMN_LANG.' = :lang', array('id' => $id, 'lang' => $lang))->record();
   }
   
   /**
    * Metoda vrací šablony podle typu
    *
    * @param int -- id šablony
    * @return Model_ORM_Record[] -- pole s šablonami
    */
   public static function getTemplates($type = self::TEMPLATE_TYPE_TEXT, $lang = null) {
      $m = new self();
      if($lang == null){
         return $m->where(self::COLUMN_TYPE.' = :type', array('type' => $type))->records();
      }
      return $m->where(self::COLUMN_TYPE.' = :type AND '.self::COLUMN_LANG.' = :lang', array('type' => $type, 'lang' => $lang))->records();
   }
}