<?php
/*
 * Třída modelu se slidy
*/
class HPSlideShowAdv_Model extends Model_ORM_Ordered {
   const DB_TABLE = 'hpslideshow_adv_slides';
   /**
    * Názvy sloupců v databázi pro tabulku
    * @var string
    */
   const COLUMN_ID 				= 'id_slide';
   const COLUMN_ID_CAT        = 'id_category';
   const COLUMN_LANG          = 'slide_lang';
   const COLUMN_NAME          = 'slide_name';
   const COLUMN_IMAGE         = 'slide_image';
   const COLUMN_LINK          = 'slide_link';
   const COLUMN_ACTIVE 			= 'slide_active';
   const COLUMN_ORDER 			= 'slide_order';
   const COLUMN_ANIMATION 		= 'slide_animation';
   const COLUMN_DELAY 			= 'slide_delay';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_hp_slides');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'smallint', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CAT, array('datatype' => 'smallint', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));
      $this->addColumn(self::COLUMN_LANG, array('datatype' => 'char(2)', 'pdoparam' => PDO::PARAM_STR, 'index' => true));
      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(400)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_LINK, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'smallint', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_ANIMATION, array('datatype' => 'varchar(20)', 'pdoparam' => PDO::PARAM_STR, 'default' => null));
      $this->addColumn(self::COLUMN_DELAY, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 5));

      $this->setPk(self::COLUMN_ID);
      $this->setOrderColumn(self::COLUMN_ORDER);
      
      $this->addForeignKey(self::COLUMN_ID_CAT, 'Model_Category', Model_Category::COLUMN_CAT_ID);
      $this->addRelatioOneToMany(self::COLUMN_ID, 'HPSlideShowAdv_Model_Items', HPSlideShowAdv_Model_Items::COLUMN_ID_SLIDE);
   }
   
   protected function beforeSave(Model_ORM_Record $record, $type = 'U')
   {
      parent::beforeSave($record, $type);
      if($record->{self::COLUMN_LANG} == null){
         $record->{self::COLUMN_LANG} = Locales::getDefaultLang();
      }
         
   }
//   protected function beforeDelete($pk)
//   {
//      parent::beforeDelete($pk);
//      $model = new HPSlideShowAdv_Model_Items();
//      $model
//          ->where(HPSlideShowAdv_Model_Items::COLUMN_ID_SLIDE." = :ids", array('ids' => $pk))
//          ->delete();
//   }
}
class HPSlideShowAdv_Model_Record extends Model_ORM_Ordered_Record {
   public function getItems($order = HPSlideShowAdv_Model_Items::COLUMN_ORDER)
   {
      $m = new HPSlideShowAdv_Model_Items();
      return $m
          ->where(HPSlideShowAdv_Model_Items::COLUMN_ID_SLIDE.' = :ids', array('ids' => $this->getPK()))
          ->order(array($order))
          ->records();
   }
   
   public function getBGImageUrl()
   {
      return $this->{HPSlideShowAdv_Model::COLUMN_IMAGE} != null 
      ? Url_Request::getBaseWebDir().CUBE_CMS_DATA_DIR.'/'.HPSlideShowAdv_Controller::DATA_DIR.'/'.$this->{HPSlideShowAdv_Model::COLUMN_IMAGE}
      : null;
   }
}