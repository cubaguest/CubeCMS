<?php

/*
 * Třída modelu s bannery
 */

class Banners_Model extends Model_ORM {
   const DB_TABLE = "banners";
   
   /**
    * Názvy sloupců v databázi
    * @var string
    */
   const COLUMN_ID = 'id_banner';
   const COLUMN_NAME = 'banner_name';
   const COLUMN_FILE = 'banner_file';
   const COLUMN_ACTIVE = 'banner_active';
   const COLUMN_BOX = 'banner_box';
   const COLUMN_ORDER = 'banner_order';
   const COLUMN_URL = 'banner_url';
   const COLUMN_TEXT = 'banner_text';
   const COLUMN_TIME_ADD = 'banner_time_add';
   const COLUMN_NEW_WINDOW = 'banner_new_window';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_banner');
   
      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_FILE, array('datatype' => 'varchar(50)', 'nn' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_BOX, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_URL, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TEXT, array('datatype' => 'varchar(300)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_TIME_ADD, array('datatype' => 'timestamp', 'pdoparam' => PDO::PARAM_STR, 'default' => 'CURRENT_TIMESTAMP'));
      $this->addColumn(self::COLUMN_NEW_WINDOW, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      
      $this->setPk(self::COLUMN_ID);
   }

   public static function move($idBanner, $newPos)
   {
      $model = new self();
      $model->lock(Model_ORM::LOCK_WRITE);
      $banner = $model->record($idBanner);
      if($newPos > $banner->{Banners_Model::COLUMN_ORDER}){
         // přesun dolů
         $model->where(
            Banners_Model::COLUMN_ORDER.' > :opos AND '.Banners_Model::COLUMN_ORDER." <= :npos AND ".Banners_Model::COLUMN_BOX." = :box",
            array( 'npos' => $newPos, 'opos' => $banner->{Banners_Model::COLUMN_ORDER}, 'box' => $banner->{Banners_Model::COLUMN_BOX} )
         )
            ->update(array(Banners_Model::COLUMN_ORDER => array( 'stmt' => Banners_Model::COLUMN_ORDER.'-1') ));

      } else if($newPos < $banner->{Banners_Model::COLUMN_ORDER}){
         $model->where(
            Banners_Model::COLUMN_ORDER.' >= :npos AND '.Banners_Model::COLUMN_ORDER." < :opos AND ".Banners_Model::COLUMN_BOX." = :box",
            array( 'npos' => $newPos, 'opos' => $banner->{Banners_Model::COLUMN_ORDER}, 'box' => $banner->{Banners_Model::COLUMN_BOX} )
         )
            ->update(array(Banners_Model::COLUMN_ORDER => array( 'stmt' => Banners_Model::COLUMN_ORDER.'+1') ));
      }
      $banner->{Banners_Model::COLUMN_ORDER} = $newPos;
      $model->save($banner);
      $model->unLock();
      return $banner;
   }

   public static function moveToNewBox($idBanner, $boxName, $newPos)
   {
      $model = new self();
      $model->lock(Model_ORM::LOCK_WRITE);
      $banner = $model->record($idBanner);

      $oldPos = $banner->{self::COLUMN_ORDER};
      $oldBoxName = $banner->{self::COLUMN_BOX};

      // nový box - vytvořit místo pro banner
      $model->where( self::COLUMN_ORDER.' >= :npos AND '.self::COLUMN_BOX." = :box",
         array( 'npos' => $newPos, 'box' => $boxName ))
         ->update(array(self::COLUMN_ORDER => array( 'stmt' => self::COLUMN_ORDER.'+1') ));

      // přesun do jiného boxu a update pozice
      $banner->{self::COLUMN_BOX} = $boxName;
      $banner->{self::COLUMN_ORDER} = $newPos;
      $model->save($banner);

      // starý box - přesunutí všech pozic pod banerem nahoru
      $model->where( self::COLUMN_ORDER.' >= :opos AND '.self::COLUMN_BOX." = :box",
         array( 'opos' => $oldPos, 'box' => $oldBoxName ))
         ->update(array(self::COLUMN_ORDER => array( 'stmt' => self::COLUMN_ORDER.'-1') ));
      return $banner;
   }

   protected function beforeSave(Model_ORM_Record $record, $type = 'U')
   {
      // doplníme max order
      if($record->isNew() || $record->{self::COLUMN_ORDER} == 0){
         $model = new self();
         $lastPos = $model->where(self::COLUMN_BOX." = :box",
            array('box' => $record->{self::COLUMN_BOX}))->count();
         $record->{Banners_Model::COLUMN_ORDER} = $lastPos+1;
      }
      parent::beforeSave($record, $type);
   }

   protected function beforeDelete($pk)
   {
      $model = new self();
      $record = $model->record($pk);

      // aktualizujeme všechny pozice co jsou pod banerem
      $model->where(self::COLUMN_ORDER.' > :ord AND '.self::COLUMN_BOX." = :box",
         array('ord' => $record->{self::COLUMN_ORDER}, 'box' => $record->{self::COLUMN_BOX}))
         ->update(array(self::COLUMN_ORDER => array( 'stmt' => self::COLUMN_ORDER.'-1') ));

      parent::beforeDelete($pk);
   }

}
