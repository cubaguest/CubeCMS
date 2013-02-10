<?php
/*
 * Třída modelu detailem článku
 */
class Shop_Model_AttributesGroups extends Model_ORM {
   const DB_TABLE = 'shop_attributes_groups';

   const COLUMN_ID = 'id_attribute_group';
   const COLUMN_NAME = 'atgroup_name';
   const COLUMN_ORDER = 'atgroup_order';

   protected function _initTable() {
      $this->setTableName(self::DB_TABLE, 't_attr_grps');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(100)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'lang' => true));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'dafeult' => 0));

      $this->setPk(self::COLUMN_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'Shop_Model_Attributes', Shop_Model_Attributes::COLUMN_ID_GROUP);
   }

   protected function beforeSave(Model_ORM_Record $record, $type = 'U')
   {
      // kontrola jestli je zadána pozice
      if($record->{self::COLUMN_ORDER} == 0){
         $counter = $this->count();
         $record->{self::COLUMN_ORDER} = $counter+1;
      }
   }

   protected function beforeDelete($pk)
   {
      $m = new self();
      $record = $m->record($pk);

      // reorganizovat pořadí
      $m->where(self::COLUMN_ORDER." > :ord", array( 'ord' => $record->{self::COLUMN_ORDER} ))
         ->update(array( self::COLUMN_ORDER => array('stmt' => self::COLUMN_ORDER." - 1" )) );
   }

   public static function changeOrder($id, $newPos)
   {
      $m = new self();
      $rec = $m->record($id);

      if($newPos > $rec->{self::COLUMN_ORDER}){
         // move down
         $m->where(self::COLUMN_ORDER." > :oldOrder AND ".self::COLUMN_ORDER." <= :newOrder",
            array('oldOrder' => $rec->{self::COLUMN_ORDER}, 'newOrder' => $newPos))
            ->update(array(self::COLUMN_ORDER => array('stmt' => self::COLUMN_ORDER.' - 1')));
      } else {
         // move up
         $m->where(self::COLUMN_ORDER." < :oldOrder AND ".self::COLUMN_ORDER." >= :newOrder",
            array('oldOrder' => $rec->{self::COLUMN_ORDER}, 'newOrder' => $newPos))
            ->update(array(self::COLUMN_ORDER => array('stmt' => self::COLUMN_ORDER.' + 1')));
      }
      // update row
      $rec->{self::COLUMN_ORDER} = $newPos;
      $rec->save();
   }
}
