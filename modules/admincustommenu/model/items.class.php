<?php
/*
 * Třída modelu s listem Novinek
*/
class AdminCustomMenu_Model_Items extends Model_ORM_Tree {
   const DB_TABLE = 'custom_menu_items';

   const COLUMN_ID = 'id_custom_menu_item';
   const COLUMN_ID_CATEGORY = 'id_category';
   const COLUMN_BOX = 'menu_item_box';
   const COLUMN_NAME = 'menu_item_name';
   const COLUMN_LINK = 'menu_item_link';
   const COLUMN_NEW_WINDOW = 'menu_item_new_window';
   const COLUMN_ORDER = 'menu_item_order';
   const COLUMN_ACTIVE = 'menu_item_active';
   const COLUMN_LEFT = 'id_lft';
   const COLUMN_RIGHT = 'id_rgt';
   const COLUMN_LEVEL = 'level';
   const COLUMN_IS_TPL_MENU = 'is_tpl_menu';

   protected function  _initTable() {
      $this->setTableName(self::DB_TABLE, 't_cm_items');

      $this->addColumn(self::COLUMN_ID, array('datatype' => 'int', 'ai' => true, 'nn' => true, 'pk' => true));
      $this->addColumn(self::COLUMN_ID_CATEGORY, array('datatype' => 'int', 'nn' => true, 'index' => true, 'default' => 0));
      $this->addColumn(self::COLUMN_BOX, array('datatype' => 'varchar(45)', 'pdoparam' => PDO::PARAM_STR, 'index' => true));

      $this->addColumn(self::COLUMN_NAME, array('datatype' => 'varchar(50)', 'lang' => true, 'pdoparam' => PDO::PARAM_STR));
      $this->addColumn(self::COLUMN_LINK, array('datatype' => 'varchar(200)', 'pdoparam' => PDO::PARAM_STR));

      $this->addColumn(self::COLUMN_NEW_WINDOW, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => false));
      $this->addColumn(self::COLUMN_ACTIVE, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'default' => true));
      $this->addColumn(self::COLUMN_IS_TPL_MENU, array('datatype' => 'tinyint(1)', 'pdoparam' => PDO::PARAM_BOOL, 'index' => true, 'default' => false));

      $this->addColumn(self::COLUMN_ORDER, array('datatype' => 'int', 'pdoparam' => PDO::PARAM_INT, 'default' => 0));

      $this->setPk(self::COLUMN_ID);
      $this->setLimitingColumns(array(self::COLUMN_BOX));
      $this->setLeftColumn(self::COLUMN_LEFT);
      $this->setRightColumn(self::COLUMN_RIGHT);
      $this->setLevelColumn(self::COLUMN_LEVEL);

      $this->addForeignKey(self::COLUMN_ID_CATEGORY, 'Model_Category', Model_Category::COLUMN_ID);
      $this->rowClass = 'AdminCustomMenu_Model_Items_Record';
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
    * Vrazí objek menu podle zadaného id
    * @param $id
    */
   public static function getMenu($id)
   {
      $m = new self();
      
      return $m->where(self::COLUMN_ID_CATEGORY, $id)->records();
   }
   
   protected function beforeSave(\Model_ORM_Record $record, $type = 'U')
   {
      if($record->{self::COLUMN_BOX} == null){
         $record->{self::COLUMN_BOX} = Utils_String::toSafeFileName((string)$record->{self::COLUMN_NAME});
      }
      parent::beforeSave($record, $type);
   }
}

class AdminCustomMenu_Model_Items_Record extends Model_ORM_Tree_Record {
   
   public function getUrl()
   {
      
   }
   
}
