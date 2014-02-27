<?php
/*
 * Třída modelu s detailem galerie
*/
class HPSlideShow_Model extends Model_ORM {
   const DB_TABLE = 'hpslideshow_images';
   /**
    * Názvy sloupců v databázi pro tabulku s obrázky
    * @var string
    */
   const COLUMN_ID 				= 'id_image';
   const COLUMN_ID_CAT        = 'id_category';
   const COLUMN_LABEL         = 'image_label';
   const COLUMN_LINK          = 'image_link';
   const COLUMN_ACTIVE 			= 'image_active';
   const COLUMN_ORDER 			= 'image_order';
   const COLUMN_FILE 			= 'image_file';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_ph_imgs');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CAT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));

      $this->addColumn(self::COLUMN_LABEL, array('datatype' => 'varchar(400)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_LINK, array('datatype' => 'varchar(100)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_FILE, array('datatype' => 'varchar(40)', 'pdoparam' => PDO::PARAM_STR, 'nn' => true));

      $this->setPk(self::COLUMN_ID);
      
      $this->addForeignKey(self::COLUMN_ID_CAT, 'Model_Category', Model_Category::COLUMN_CAT_ID);
   }

   protected function beforeSave(Model_ORM_Record $record, $type = 'U')
   {
      // kontrola jestli je zadána pozice
      if($record->{self::COLUMN_ORDER} < 1){
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
