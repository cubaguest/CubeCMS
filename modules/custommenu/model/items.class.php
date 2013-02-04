<?php
/*
 * Třída modelu s listem Novinek
*/
class CustomMenu_Model_Items extends Model_ORM {
   const DB_TABLE = 'custom_menu_items';

   const COLUMN_ID = 'id_custom_menu_item';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_BOX = 'menu_item_box';
   const COLUMN_NAME = 'menu_item_name';
   const COLUMN_LINK = 'menu_item_link';
   const COLUMN_NEW_WINDOW = 'menu_item_new_window';
   const COLUMN_ORDER = 'menu_item_order';
   const COLUMN_ACTIVE = 'menu_item_active';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_markets_params');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'int', 'nn' => true, 'index' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_BOX, array('datatype' => 'varchar(45)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR, 'index' => true));

      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'nn' => true, 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_LINK, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_NEW_WINDOW, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));

      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));

      $this->setPk(self::COLUMN_ID);

      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category', Model_Category::COLUMN_ID);
   }

   protected function beforeSave(Model_ORM_Record $record, $type = 'U')
   {
      $recInDB = $this->record($record->getPK());
      if($record->{self::COLUMN_BOX} != $recInDB->{self::COLUMN_BOX}){
         // změna poředí na utomatické generování
         $record->{CustomMenu_Model_Items::COLUMN_ORDER} = 0;
         // update včech pozic
         $this->where(self::COLUMN_BOX." = :boxOld AND ".self::COLUMN_ORDER." > :ordOld",
            array('boxOld' => $recInDB->{self::COLUMN_BOX}, 'ordOld' => $recInDB->{self::COLUMN_ORDER}))
            ->update(array( self::COLUMN_ORDER => array('stmt' => self::COLUMN_ORDER." - 1" )) );
      }
      // kontrola jestli je zadána pozice
      if($record->{self::COLUMN_ORDER} == 0){
         $counter = $this->where( self::COLUMN_BOX." = :box", array('box' => $record->{self::COLUMN_BOX}))->count();
         $record->{self::COLUMN_ORDER} = $counter+1;
      }

   }

   protected function beforeDelete($pk)
   {
      $m = new self();
      $record = $m->record($pk);

      // reorganizovat pořadí
      $m->where(self::COLUMN_BOX." = :box AND ".self::COLUMN_ORDER." > :ord",
         array(
            'box' => $record->{self::COLUMN_BOX},
            'ord' => $record->{self::COLUMN_ORDER},
         ))
         ->update(array( self::COLUMN_ORDER => array('stmt' => self::COLUMN_ORDER." - 1" )) );
   }

   public static function changeState($id, $active = null)
   {
      $m = new self();
      if($active === null){
         $r = $m->record($id);
         $active = !$r->{self::COLUMN_ACTIVE};
      }

      $m->where(self::COLUMN_ID." = :id", array('id' => $id))
         ->update(array(self::COLUMN_ACTIVE => $active));
   }

   /**
    * Změna pozice zadaného prvku
    * @param $id
    * @param $newPos
    */
   public static function changeOrder($id, $newPos)
   {
      $m = new self();
      $rec = $m->record($id);

      if($newPos > $rec->{self::COLUMN_ORDER}){
         // move down
         $m->where(self::COLUMN_ORDER." > :oldOrder AND ".self::COLUMN_ORDER." <= :newOrder AND ".self::COLUMN_BOX." = :box",
            array('oldOrder' => $rec->{self::COLUMN_ORDER}, 'newOrder' => $newPos, 'box' => $rec->{self::COLUMN_BOX}))
            ->update(array(self::COLUMN_ORDER => array('stmt' => self::COLUMN_ORDER.' - 1')));
      } else {
         // move up
         $m->where(self::COLUMN_ORDER." < :oldOrder AND ".self::COLUMN_ORDER." >= :newOrder AND ".self::COLUMN_BOX." = :box",
            array('oldOrder' => $rec->{self::COLUMN_ORDER}, 'newOrder' => $newPos, 'box' => $rec->{self::COLUMN_BOX}))
            ->update(array(self::COLUMN_ORDER => array('stmt' => self::COLUMN_ORDER.' + 1')));
      }
      // update row
      $rec->{self::COLUMN_ORDER} = $newPos;
      $rec->save();
   }
}
