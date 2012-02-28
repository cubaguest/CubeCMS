<?php
/*
 * Třída modelu detailem článku
 */
class Advice_Model_Connections extends Model_ORM {
   const DB_TABLE = 'advice_connections';

   const COLUMN_ID_QUESTION = 'id_advice_question';
   const COLUMN_ID_CAT = 'id_advice_cat';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_adv_cat_conn');

      $this->addColumn(self::COLUMN_ID_QUESTION, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ID_CAT, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT));
      
      $this->addForeignKey(self::COLUMN_ID_QUESTION, 'Advice_Model');
      $this->addForeignKey(self::COLUMN_ID_CAT, 'Advice_Model_Categories');
   }
}

?>