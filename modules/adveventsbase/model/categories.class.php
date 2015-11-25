<?php
/*
 * Třída modelu s detailem uživatele
 */
class AdvEventsBase_Model_Categories extends Model_ORM {
   const DB_TABLE 	   = 'adv_events_categories';
   /**
    * Pole s názvy sloupců v tabulce
    * @var array
    */
   const COLUMN_ID 	   = 'id_ev_category';
	const COLUMN_NAME		= 'ev_category_name';
	const COLUMN_DESC		= 'ev_category_desc';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_events_cats');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_DESC, array('datatype' => 'varchar(200)','pdoparam' => PDO::PARAM_STR));

      $this->setPk(self::COLUMN_ID);
      $this->order(self::COLUMN_NAME);
   }

   /**
    * @param $id
    * @return Model_ORM_Record
    */
   public static function getCategory($id)
   {
      $m = new self();
      return $m->record($id);
   }

   /**
    * @param int $limit
    * @param int $from
    * @param array|string $orderColumn
    * @param string $orderType
    * @return array
    */
   public static function getCategories($limit = 10, $from = 0, $orderColumn = self::COLUMN_NAME, $orderType = Model_ORM::ORDER_ASC)
   {
      $m = new self();
      return $m
         ->order(array($orderColumn => $orderType))
         ->limit($from, $limit)
         ->records();
   }

   /**
    * @param int $limit
    * @param int $from
    * @param array|string $orderColumn
    * @param string $orderType
    * @return array
    */
   public static function getCategoriesByName($name, $limit = 10, $from = 0, $orderColumn = self::COLUMN_NAME, $orderType = Model_ORM::ORDER_ASC)
   {
      $m = new self();
      return $m
         ->where(self::COLUMN_NAME." LIKE :str1",
         array('str1' => '%'.$name.'%'))
         ->order(array($orderColumn => $orderType))
         ->limit($from, $limit)
         ->records();
   }
}
