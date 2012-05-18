<?php
/*
 * Třída modelu detailem článku
*/
class Forms_Model_Elements extends Model_ORM {
   const DB_TABLE = 'forms_elements';

   /**
    * Názvy sloupců v databázi
    */
   const COLUMN_ID = 'id_form_element';
   const COLUMN_ID_FORM = 'id_form';
   const COLUMN_NAME = 'form_element_name';
   const COLUMN_LABEL = 'form_element_label';
   const COLUMN_TYPE = 'form_element_type';
   const COLUMN_VALUE = 'form_element_value';
   const COLUMN_REQUIRED = 'form_element_required';
   const COLUMN_OPTIONS = 'form_element_options';
   const COLUMN_ORDER = 'form_element_order';
   const COLUMN_VALIDATOR = 'form_element_validator';
   const COLUMN_IS_MULTIPLE = 'form_element_ismultiple';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_videos');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_FORM, array('datatype' => 'smallint', 'nn' => true, 'index' => true));
      
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(400)', 'pdoparam' => PDO::PARAM_STR, 'nn' => true));
      $this->addColumn(self::COLUMN_LABEL, array('datatype' => 'varchar(400)', 'pdoparam' => PDO::PARAM_STR, 'nn' => true));
      
      $this->addColumn(self::COLUMN_TYPE, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR, 'nn' => true));
      $this->addColumn(self::COLUMN_VALUE, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR, "default" => null ));
      $this->addColumn(self::COLUMN_REQUIRED, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      
      $this->addColumn(self::COLUMN_OPTIONS, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR, "default" => null ));
      
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 1));
      $this->addColumn(self::COLUMN_VALIDATOR, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR, "default" => null));
      $this->addColumn(self::COLUMN_IS_MULTIPLE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      
      $this->setPk(self::COLUMN_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID_FORM, 'Forms_Model', Forms_Model::COLUMN_ID);
   }
}

?>