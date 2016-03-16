<?php
/*
 * Třída modelu s položkami slidu
*/
class HPSlideShowAdv_Model_Items extends Model_ORM_Ordered {
   const DB_TABLE = 'hpslideshow_adv_slides_items';
   
   /**
    * Názvy sloupců v databázi pro tabulku
    * @var string
    */
   const COLUMN_ID               = 'id_slide_item';
   const COLUMN_ID_SLIDE         = 'id_slide';
   const COLUMN_CONTENT          = 'slide_item_content';
   const COLUMN_LINK             = 'slide_item_link';
   const COLUMN_IMAGE            = 'slide_item_image';
   const COLUMN_CLASSES          = 'slide_item_classes';
   const COLUMN_STYLES           = 'slide_item_styles';
   const COLUMN_ANIMATION        = 'slide_item_animation';
   const COLUMN_ANIMATION_OUT    = 'slide_item_animation_out';
   const COLUMN_ANIMATION_SPEED  = 'slide_item_animation_speed';
   const COLUMN_ANIMATION_SPEED_OUT  = 'slide_item_animation_speed_out';
   const COLUMN_DELAY            = 'slide_item_delay';
   const COLUMN_POS_X            = 'slide_item_pos_x';
   const COLUMN_POS_Y            = 'slide_item_pos_y';
   const COLUMN_WIDTH            = 'slide_item_width';
   const COLUMN_HEIGHT           = 'slide_item_height';
   
   const COLUMN_ORDER            = 'slide_item_order';
   
   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_hp_sitems');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_SLIDE, array('datatype' => 'int', 'nn' => true, 'pdoparam' => PDO::PARAM_INT, 'index' => true));

      $this->addColumn(self::COLUMN_CONTENT, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_LINK, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_IMAGE, array('datatype' => 'varchar(100)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_CLASSES, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_STYLES, array('datatype' => 'varchar(500)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ANIMATION, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ANIMATION_OUT, array('datatype' => 'varchar(50)', 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_ANIMATION_SPEED, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_ANIMATION_SPEED_OUT, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_DELAY, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT));
      $this->addColumn(self::COLUMN_POS_X, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_POS_Y, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_WIDTH, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));
      $this->addColumn(self::COLUMN_HEIGHT, array('datatype' => 'int',  'pdoparam' => PDO::PARAM_INT, 'default' => 0));

      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));

      $this->setPk(self::COLUMN_ID);
      $this->setOrderColumn(self::COLUMN_ORDER);
      
      $this->addForeignKey(self::COLUMN_ID_SLIDE, 'HPSlideShowAdv_Model', HPSlideShowAdv_Model::COLUMN_ID);
   }
}

class HPSlideShowAdv_Model_Items_Record extends Model_ORM_Ordered_Record {
//   public function getImageURL()
//   {
//      if($this->{HPSlideShowAdv_Model_Items::COLUMN_IMAGE} == null){
//         return null;
//      }
//      return Url_Request::getBaseWebDir().CUBE_CMS_DATA_DIR.'/'.HPSlideShowAdv_Controller::DATA_DIR.'/'.$this->{HPSlideShowAdv_Model_Items::COLUMN_IMAGE};
//      
//   }
}